<?php

namespace Modules\Purchases\Http\Controllers;

use App\Exports\QuotationsExport;
use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use App\Models\Log as ModelsLog;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\PurchaseQuotation;
use App\Models\ProductDetails;
use App\Models\PurchaseQuotationSupplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class QuotationsController extends Controller
{
    public function index(Request $request)
{
    $suppliers = Supplier::all();

    // إذا كان الطلب Ajax، نعيد البيانات فقط
    if ($request->ajax()) {
        return $this->getFilteredData($request);
    }

    // في البداية نعيد الصفحة مع البيانات الأولية
    $purchaseQuotation = $this->getFilteredData($request, false);

    return view('purchases::purchases.quotations.index', compact('suppliers', 'purchaseQuotation'));
}

private function getFilteredData(Request $request, $returnJson = true)
{
    $query = PurchaseQuotation::query();

    // البحث بالكود
    if ($request->filled('code')) {
        $query->where('code', 'LIKE', '%' . $request->code . '%');
    }

    // البحث بالمورد
    if ($request->filled('supplier_id')) {
        $query->whereHas('suppliers', function ($q) use ($request) {
            $q->where('supplier_id', $request->supplier_id);
        });
    }

    // البحث بتاريخ الطلب (من)
    if ($request->filled('order_date_from')) {
        $query->whereDate('order_date', '>=', $request->order_date_from);
    }

    // البحث بتاريخ الطلب (إلى)
    if ($request->filled('order_date_to')) {
        $query->whereDate('order_date', '<=', $request->order_date_to);
    }

    // البحث بتاريخ الاستحقاق (من)
    if ($request->filled('due_date_from')) {
        $query->whereDate('due_date', '>=', $request->due_date_from);
    }

    // البحث بتاريخ الاستحقاق (إلى)
    if ($request->filled('due_date_to')) {
        $query->whereDate('due_date', '<=', $request->due_date_to);
    }

    // البحث بالحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // البحث بحالة المتابعة
    if ($request->filled('follow_status')) {
        $query->where('follow_status', $request->follow_status);
    }

    // تنفيذ الاستعلام وترتيب النتائج
    $purchaseQuotation = $query->latest()->paginate(10);

    if ($returnJson) {
        return response()->json([
            'success' => true,
            'data' => view('purchases::purchases.quotations.partials.table', compact('purchaseQuotation'))->render(),
            'pagination' => view('purchases::purchases.quotations.partials.pagination', compact('purchaseQuotation'))->render(),
            'total' => $purchaseQuotation->total(),
            'current_page' => $purchaseQuotation->currentPage(),
            'last_page' => $purchaseQuotation->lastPage(),
        ]);
    }

    return $purchaseQuotation;
}

// دالة للتعامل مع طلبات الـ pagination عبر Ajax
public function paginate(Request $request)
{
    if ($request->ajax()) {
        return $this->getFilteredData($request);
    }

    return redirect()->route('Quotations.index');
}


    public function show($id)
{
    $productDetails = InvoiceItem::where('purchase_quotation_id', $id)->get();

    $purchaseQuotation = PurchaseQuotation::with([
        'suppliers', 'creator', 'updater',
        'generatedQuotations' // <-- نضيف هذه
    ])->findOrFail($id);

    $suppliers = Supplier::all();

    $logs = ModelsLog::where('type', 'purchase_quotation')
        ->where('type_id', $id)
        ->whereHas('purchase_quotation')
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

    return view('purchases::purchases.quotations.show', compact('purchaseQuotation', 'productDetails', 'suppliers', 'logs'));
}


    public function create(Request $request)
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        $order_id = $request->query('order_id');
        return view('purchases::purchases.quotations.create', compact('suppliers', 'products', 'order_id'));
    }

    public function store(Request $request)
{
    try {
        // التحقق من البيانات المدخلة
        $validatedData = $request->validate(
            [
                'title' => 'required|string|max:255',
                'code' => 'nullable|string|max:50|unique:purchase_quotations,code',
                'order_date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:order_date',
                'supplier_id' => 'required|array|min:1',
                'supplier_id.*' => 'exists:suppliers,id',
                'notes' => 'nullable|string',
                'attachments' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:1',
                'order_id' => 'nullable|exists:purchase_orders,id',
            ],
            [
                'supplier_id.required' => 'يجب اختيار مورد واحد على الأقل',
                'supplier_id.min' => 'يجب اختيار مورد واحد على الأقل',
                'supplier_id.*.exists' => 'أحد الموردين المحددين غير موجود',
                'items.required' => 'يجب إضافة بند واحد على الأقل',
                'items.*.product_id.required' => 'يجب اختيار المنتج',
                'items.*.quantity.required' => 'يجب تحديد الكمية',
            ],
        );

        DB::beginTransaction();

        // إنشاء عرض السعر
        $purchaseQuotation = PurchaseQuotation::create([
            'title' => $validatedData['title'],
       'code' => $validatedData['code'] ?? ((PurchaseQuotation::max('id') ?? 0) + 1),
'order_date' => $validatedData['order_date'],
            'due_date' => $validatedData['due_date'] ?? null,
            'notes' => $validatedData['notes'] ?? null,
            'total_amount' => array_reduce(
                $validatedData['items'],
                function ($carry, $item) {
                    $product = Product::find($item['product_id']);
                    return $carry + ($item['quantity'] * ($product->purchase_price ?? 0));
                },
                0,
            ),

            'created_by' => auth()->id(),
            'order_id' => $validatedData['order_id'] ?? null,
        ]);

        // رفع المرفقات إن وُجدت
        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('purchase_quotations/attachments', $fileName, 'public');
            $purchaseQuotation->update(['attachments' => $filePath]);
        }

        // ربط الموردين
        foreach ($validatedData['supplier_id'] as $supplierId) {
            PurchaseQuotationSupplier::create([
                'purchase_quotation_id' => $purchaseQuotation->id,
                'supplier_id' => $supplierId,
                'created_by' => auth()->id(),
            ]);
        }

        // إضافة البنود
        foreach ($validatedData['items'] as $item) {
            $product = Product::find($item['product_id']);

            InvoiceItem::create([
                'purchase_quotation_id' => $purchaseQuotation->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'item' => $product->name,
                'price' => $product->purchase_price ?? 0,
                'total' => $item['quantity'] * ($product->purchase_price ?? 0),
            ]);
        }

        // تسجيل العملية في سجل النظام
        ModelsLog::create([
            'type' => 'purchase_quotation',
            'type_id' => $purchaseQuotation->id,
            'type_log' => 'log',
            'description' => 'تم انشاء عرض السعر **' . ($validatedData['code'] ?? $purchaseQuotation->code) . '**',
            'created_by' => auth()->id(),
        ]);

        // ✅ تحديث حالة طلب الشراء إن وُجد
        if (!empty($validatedData['order_id'])) {
            $purchaseOrder = PurchaseOrder::find($validatedData['order_id']);
            if ($purchaseOrder) {
                $purchaseOrder->status = 'Convert to Quotation'; // أو رقم مثل: 2
                $purchaseOrder->save();
            }
        }

        DB::commit();

        return redirect()->route('Quotations.show', $purchaseQuotation->id)->with('success', 'تم إنشاء عرض السعر بنجاح.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()
            ->withInput()
            ->with('error', 'حدث خطأ أثناء إنشاء عرض السعر: ' . $e->getMessage());
    }
}

    public function update(Request $request, $id)
{
    try {
        // التحقق من البيانات المدخلة
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'order_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:order_date',
            'supplier_id' => 'required|array|min:1',
            'supplier_id.*' => 'exists:suppliers,id',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ], [
            'supplier_id.required' => 'يجب اختيار مورد واحد على الأقل',
            'supplier_id.min' => 'يجب اختيار مورد واحد على الأقل',
            'supplier_id.*.exists' => 'أحد الموردين المحددين غير موجود',
            'items.required' => 'يجب إضافة بند واحد على الأقل',
            'items.*.product_id.required' => 'يجب اختيار المنتج',
            'items.*.quantity.required' => 'يجب تحديد الكمية',
        ]);

        DB::beginTransaction();

        $purchaseQuotation = PurchaseQuotation::findOrFail($id);

        // تحديث البيانات الأساسية
        $purchaseQuotation->update([
            'title' => $validatedData['title'],
            'order_date' => $validatedData['order_date'],
            'due_date' => $validatedData['due_date'] ?? null,
            'notes' => $validatedData['notes'] ?? null,
            'total_amount' => array_reduce(
                $validatedData['items'],
                function ($carry, $item) {
                    $product = Product::find($item['product_id']);
                    return $carry + ($item['quantity'] * ($product->purchase_price ?? 0));
                },
                0,
            ),
            'updated_by' => auth()->id(),
        ]);

        // معالجة المرفقات
        if ($request->hasFile('attachments')) {
            // حذف المرفق القديم إذا كان موجوداً
            if ($purchaseQuotation->attachments) {
                Storage::disk('public')->delete($purchaseQuotation->attachments);
            }

            $file = $request->file('attachments');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('purchase_quotations/attachments', $fileName, 'public');
            $purchaseQuotation->update(['attachments' => $filePath]);
        }

        // تحديث علاقات الموردين
        PurchaseQuotationSupplier::where('purchase_quotation_id', $id)->delete();
        foreach ($validatedData['supplier_id'] as $supplierId) {
            PurchaseQuotationSupplier::create([
                'purchase_quotation_id' => $purchaseQuotation->id,
                'supplier_id' => $supplierId,
                'created_by' => auth()->id(),
            ]);
        }

        // تحديث تفاصيل الفاتورة
        InvoiceItem::where('purchase_quotation_id', $id)
                 ->where('type', 'purchase_quotation')
                 ->delete();

        foreach ($validatedData['items'] as $item) {
            $product = Product::find($item['product_id']);

            InvoiceItem::create([
                'purchase_quotation_id' => $purchaseQuotation->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'item' => $product->name,
                'price' => $product->purchase_price ?? 0,
                'total' => $item['quantity'] * ($product->purchase_price ?? 0),
                'type' => 'purchase_quotation',
            ]);
        }

        // تسجيل التعديل في السجلات
        ModelsLog::create([
            'type' => 'purchase_quotation',
            'type_id' => $purchaseQuotation->id,
            'type_log' => 'log',
            'description' => 'تم تحديث عرض السعر **' . $purchaseQuotation->code . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()->route('Quotations.show', $purchaseQuotation->id)
            ->with('success', 'تم تحديث عرض السعر بنجاح.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()
            ->withInput()
            ->with('error', 'حدث خطأ أثناء تحديث عرض السعر: ' . $e->getMessage());
    }
}

    public function edit($id)
    {
        $purchaseQuotation = PurchaseQuotation::findOrFail($id);

        // جلب الموردين المرتبطين

        // جلب قوائم البيانات المطلوبة
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('purchases::purchases.quotations.edit', compact('purchaseQuotation', 'products', 'suppliers'));
    }
    public function destroy($id)
    {
        $purchaseQuotation = PurchaseQuotation::findOrFail($id);
        ModelsLog::create([
            'type' => 'purchase_quotation',
            'type_id' => $purchaseQuotation->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف  عرض سعر رقم **' . $purchaseQuotation->code . '**', // النص المنسق
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        $purchaseQuotation->items()->delete();
        $purchaseQuotation->delete();
        return redirect()->route('Quotations.index')->with('success', 'تم حذف عرض السعر بنجاح!');
    }
    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $purchaseQuotation = PurchaseQuotation::findOrFail($id);

            // تحديث حالة الطلب
            $purchaseQuotation->update([
                'status' => 2, // موافق عليه
                'updated_by' => Auth::id(),
            ]);

            // إضافة ملاحظة الموافقة إذا وجدت
            if ($request->has('note')) {
                $purchaseQuotation->activities()->create([
                    'description' => 'تمت الموافقة على الطلب. ملاحظة: ' . $request->note,
                    'created_by' => Auth::id(),
                ]);
            } else {
                $purchaseQuotation->activities()->create([
                    'description' => 'تمت الموافقة على الطلب',
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'تمت الموافقة على طلب عرض السعر بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء الموافقة على الطلب: ' . $e->getMessage());
        }
    }

    /**
     * رفض طلب عرض السعر
     */
    public function reject(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'note' => 'required|string|max:500',
                ],
                [
                    'note.required' => 'يجب إدخال سبب الرفض',
                ],
            );

            DB::beginTransaction();

            $purchaseQuotation = PurchaseQuotation::findOrFail($id);

            // تحديث حالة الطلب
            $purchaseQuotation->update([
                'status' => 3, // مرفوض
                'updated_by' => Auth::id(),
            ]);

            // إضافة سبب الرفض
            $purchaseQuotation->activities()->create([
                'description' => 'تم رفض الطلب. السبب: ' . $request->note,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم رفض طلب عرض السعر بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء رفض الطلب: ' . $e->getMessage());
        }
    }

    /**
     * نسخ طلب عرض السعر
     */
    public function duplicate($id)
    {
        try {
            DB::beginTransaction();

            $originalQuotation = PurchaseQuotation::with(['items', 'suppliers'])->findOrFail($id);

            // إنشاء نسخة جديدة
            $newQuotation = $originalQuotation->replicate();
            $newQuotation->code = $this->generateQuotationCode();
            $newQuotation->status = 1; // تحت المراجعة
            $newQuotation->created_by = Auth::id();
            $newQuotation->updated_by = null;
            $newQuotation->save();

            // نسخ المنتجات
            foreach ($originalQuotation->items as $item) {
                $newItem = $item->replicate();
                $newItem->purchase_quotation_id = $newQuotation->id;
                $newItem->save();
            }

            // نسخ الموردين
            $newQuotation->suppliers()->attach($originalQuotation->suppliers->pluck('id')->toArray());

            DB::commit();
            return redirect()->route('Quotations.edit', $newQuotation->id)->with('success', 'تم نسخ طلب عرض السعر بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء نسخ الطلب: ' . $e->getMessage());
        }
    }

public function generatePdf($id)
{
    try {
        // جلب طلب الشراء مع العناصر والمنتجات
        $purchaseOrder = PurchaseOrder::with(['items.product', 'creator'])
            ->findOrFail($id);

        // حساب المجموع الكلي
        $totalAmount = $purchaseOrder->items->sum('total');

        // إعداد البيانات للـ PDF
        $data = [
            'purchaseOrder' => $purchaseOrder,
            'items' => $purchaseOrder->items,
            'totalAmount' => $totalAmount,
            'company' => [
                'name' => config('app.name', 'اسم الشركة'),
                'address' => 'عنوان الشركة',
                'phone' => 'رقم الهاتف',
                'email' => 'info@company.com'
            ]
        ];

        // إنشاء PDF
        $pdf = Pdf::loadView('purchases::ordersPurchase..pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultPaperSize' => 'a4',
                'dpi' => 150,
                'enable_php' => true
            ]);

        // تسجيل في السجل
        ModelsLog::create([
            'type' => 'OrdersPurchases',
            'type_id' => $purchaseOrder->id,
            'type_log' => 'log',
            'description' => 'تم تحميل PDF لطلب الشراء **' . ($purchaseOrder->code ?? 'بدون كود') . '**',
            'created_by' => auth()->id(),
        ]);

        return $pdf->download('purchase_order_' . ($purchaseOrder->code ?? $purchaseOrder->id) . '.pdf');

    } catch (\Exception $e) {
        return back()->with('error', 'حدث خطأ أثناء إنشاء PDF: ' . $e->getMessage());
    }
}

// إضافة route في web.php
//
}
