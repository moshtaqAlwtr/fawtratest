<?php

namespace Modules\Purchases\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\InvoiceItem;
use App\Models\Log as ModelsLog;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\PurchaseInvoiceSetting;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrdersPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $employees = User::whereIn('role', ['employee', 'manager'])->get();

        // إذا كان الطلب Ajax، نعيد البيانات فقط
        if ($request->ajax()) {
            return $this->getFilteredData($request);
        }

        // في البداية نعيد الصفحة مع البيانات الأولية
        $purchaseOrders = $this->getFilteredData($request, false);

        return view('purchases::purchases.ordersPurchase.index', compact('employees', 'purchaseOrders'));
    }

    private function getFilteredData(Request $request, $returnJson = true)
    {
        // بناء الاستعلام
        $query = PurchaseOrder::query()->withCount('productDetails');

        // البحث حسب حالة المتابعة
        if ($request->filled('follow_status')) {
            $query->where('follow_status', $request->follow_status);
        }
        if ($request->filled('title')) {
            $query->where('title', $request->title);
        }

        // البحث حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('created_by', $request->employee_id);
        }

        // البحث حسب الكود
        if ($request->filled('code')) {
            $query->where('code', 'LIKE', '%' . $request->code . '%');
        }

        // البحث حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // البحث حسب تاريخ الطلب (من)
        if ($request->filled('order_date_from')) {
            $query->whereDate('order_date', '>=', $request->order_date_from);
        }

        // البحث حسب تاريخ الطلب (إلى)
        if ($request->filled('order_date_to')) {
            $query->whereDate('order_date', '<=', $request->order_date_to);
        }

        // البحث حسب تاريخ الاستحقاق (من)
        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }

        // البحث حسب تاريخ الاستحقاق (إلى)
        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        // تنفيذ الاستعلام وترتيب النتائج
        $purchaseOrders = $query->latest()->paginate(10);

        if ($returnJson) {
            return response()->json([
                'success' => true,
                'data' => view('purchases::purchases.ordersPurchase.partials.table', compact('purchaseOrders'))->render(),
                'pagination' => view('purchases::purchases.ordersPurchase.partials.pagination', compact('purchaseOrders'))->render(),
                'total' => $purchaseOrders->total(),
                'current_page' => $purchaseOrders->currentPage(),
                'last_page' => $purchaseOrders->lastPage(),
            ]);
        }

        return $purchaseOrders;
    }

    // دالة للتعامل مع طلبات الـ pagination عبر Ajax
    public function paginate(Request $request)
    {
        if ($request->ajax()) {
            return $this->getFilteredData($request);
        }

        return redirect()->route('OrdersPurchases.index');
    }
    public function show($id)
    {
        $productDetails = InvoiceItem::where('purchase_order_id', $id)->get();
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $logs = ModelsLog::where('type', 'OrdersPurchases')
            ->where('type_id', $id)
            ->whereHas('OrdersPurchases') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
        return view('purchases::purchases.ordersPurchase.show', compact('purchaseOrder', 'productDetails', 'logs'));
    }

    public function create()
    {
        $products = Product::all();
        $employees = Employee::all();
        $code = $this->generatePurchaseOrderCode(); // توليد الكود الجديد
        return view('purchases::purchases.ordersPurchase.create', compact('employees', 'products', 'code'));
    }

    public function store(Request $request)
    {
        try {
            // التحقق من البيانات المدخلة
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',

                'code' => 'required|string|max:50|unique:purchase_orders',

                'order_date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:order_date',
                'notes' => 'nullable|string',
                'attachments' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:1',
            ]);

            DB::beginTransaction();

            // إنشاء طلب الشراء
            $purchaseOrder = PurchaseOrder::create([
                'title' => $validatedData['title'],
                'code' => $validatedData['code'] ?? $this->generatePurchaseOrderCode(),
                'order_date' => $validatedData['order_date'],
                'due_date' => $validatedData['due_date'] ?? null,
                'notes' => $validatedData['notes'] ?? null,

                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('purchase_orders/attachments', $fileName, 'public');
                $purchaseOrder->update(['attachments' => $filePath]);
            }

            // معالجة العناصر (items)
            foreach ($validatedData['items'] as $item) {
                $product = Product::find($item['product_id']);
                // إنشاء عنصر الفاتورة
                InvoiceItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'item' => $product->name ?? 'Product ' . $item['product_id'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    // أو أي نوع آخر تريده
                ]);
            }
            ModelsLog::create([
                'type' => 'OrdersPurchases',
                'type_id' => $purchaseOrder->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط

                'description' => 'تم انشاء طلب شراء **' . $validatedData['code'] . '**', // النص المنسق
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

            DB::commit();

            return redirect()->route('OrdersPurchases.show', $purchaseOrder->id)->withInput()->with('success', 'تم إنشاء طلب الشراء بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء طلب الشراء: ' . $e->getMessage());
        }
    }

    private function generatePurchaseOrderCode()
    {
        $latest = PurchaseOrder::orderBy('id', 'desc')->first();
        $number = $latest ? (int) $latest->code + 1 : 1;
        return str_pad($number, 5, '0', STR_PAD_LEFT); // مثل: 00001
    }

    public function edit($id)
    {
        if (!PurchaseInvoiceSetting::isSettingActive('manual_quote_orders')) {
            return redirect()->route('OrdersPurchases.index')->with('error', 'عذراً، ميزة طلبات الشراء يدوي غير مفعلة حالياً.');
        }
        $purchaseOrder = PurchaseOrder::with('productDetails')->findOrFail($id);

        // السماح بالتعديل فقط إذا كانت الحالة "تحت المعالجة"
        if ($purchaseOrder->status !== 'Under Review') {
            return redirect()->back()->with(
                'error',
                '
لا يمكنك تغيير طلب شراء تم تفعيله أو رفضه',
            );
        }

        $products = Product::all();
        $employees = Employee::all();

        return view('purchases::purchases.ordersPurchase.edit', compact('purchaseOrder', 'products', 'employees'));
    }

    public function update(Request $request, $id)
    {
        try {
            // التحقق من البيانات المدخلة بنفس طريقة الـ store
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:purchase_orders,code,' . $id,
                'order_date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:order_date',
                'notes' => 'nullable|string',
                'attachments' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:1',
            ]);

            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::findOrFail($id);

            // تحديث بيانات طلب الشراء بنفس هيكل الـ store
            $updateData = [
                'title' => $validatedData['title'],
                'code' => $validatedData['code'],
                'order_date' => $validatedData['order_date'],
                'due_date' => $validatedData['due_date'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'updated_by' => auth()->id(),
            ];

            $purchaseOrder->update($updateData);

            // معالجة المرفقات بنفس طريقة الـ store
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('purchase_orders/attachments', $fileName, 'public');

                // حذف الملف القديم إذا وجد
                if ($purchaseOrder->attachments) {
                    Storage::disk('public')->delete($purchaseOrder->attachments);
                }

                $purchaseOrder->update(['attachments' => $filePath]);
            }

            // حذف العناصر القديمة وإضافة الجديدة بنفس هيكل الـ store
            $purchaseOrder->invoiceItems()->delete();

            foreach ($validatedData['items'] as $item) {
                $product = Product::find($item['product_id']);
                InvoiceItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'item' => $product->name ?? 'Product ' . $item['product_id'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                ]);
            }

            // تسجيل السجل بنفس طريقة الـ store
            ModelsLog::create([
                'type' => 'OrdersPurchases',
                'type_id' => $purchaseOrder->id,
                'type_log' => 'log',
                'description' => 'تم تحديث طلب شراء **' . $validatedData['code'] . '**',
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('OrdersPurchases.show', $purchaseOrder->id)->with('success', 'تم تحديث طلب الشراء بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في تحديث طلب الشراء: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث طلب الشراء: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        // تسجيل اشعار نظام جديد
        ModelsLog::create([
            'type' => 'OrdersPurchases',
            'type_id' => $purchaseOrder->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف طلب شراء **' . $purchaseOrder->title . '**', // النص المنسق
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        $purchaseOrder->productDetails()->delete();
        $purchaseOrder->delete();
        return redirect()->route('OrdersPurchases.index')->with('success', 'تم حذف طلب الشراء بنجاح!');
    }
    public function approve(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        DB::beginTransaction();
        try {
            $purchaseOrder->update([
                'status' => 'approval',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_note' => $request->note,
            ]);

            // إضافة سجل في النشاطات

            DB::commit();
            return redirect()->back()->with('success', 'تمت الموافقة على الطلب بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء الموافقة على الطلب');
        }
    }

    // دالة رفض الطلب
    public function reject(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        DB::beginTransaction();
        try {
            $purchaseOrder->update([
                'status' => 'disagree',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
            ]);

            // إضافة سجل في النشاطات

            DB::commit();
            return redirect()->back()->with('success', 'تم رفض الطلب بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء رفض الطلب');
        }
    }

    // دالة إلغاء الموافقة
    public function cancelApproval(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        // التحقق من أن الطلب معتمد أصلاً
        if ($purchaseOrder->status !== 'approval') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء الموافقة، الطلب غير معتمد');
        }

        DB::beginTransaction();
        try {
            $purchaseOrder->update([
                'status' => 'Under Review',
                'approved_by' => null,
                'approved_at' => null,
                'approval_note' => null,
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now(),
            ]);

            // إضافة سجل في النشاطات

            DB::commit();
            return redirect()->back()->with('success', 'تم إلغاء الموافقة بنجاح وإعادة الطلب للمراجعة');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء الموافقة');
        }
    }

    // دالة التراجع عن الرفض
    public function undoRejection(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        // التحقق من أن الطلب مرفوض أصلاً
        if ($purchaseOrder->status !== 'disagree') {
            return redirect()->back()->with('error', 'لا يمكن التراجع عن الرفض، الطلب غير مرفوض');
        }

        DB::beginTransaction();
        try {
            $purchaseOrder->update([
                'status' => 'Under Review',
                'rejected_by' => null,
                'rejected_at' => null,
                'restored_by' => auth()->id(),
                'restored_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم التراجع عن الرفض بنجاح وإعادة الطلب للمراجعة');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التراجع عن الرفض');
        }
    }
    // تعديل دالة index لتجلب البيانات المحدثة
}
