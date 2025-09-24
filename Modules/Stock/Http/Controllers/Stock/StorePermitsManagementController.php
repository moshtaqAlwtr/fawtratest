<?php

namespace Modules\Stock\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehousePermitsRequest;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Client;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Log as ModelsLogs;
use App\Models\PermissionSource;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\StoreHouse;
use App\Models\Supplier;

use App\Models\notifications;
use App\Models\User;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StorePermitsManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = WarehousePermits::query()->orderBy('id', 'DESC');

        // فلترة حسب الفرع
        if ($request->filled('branch')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('branch_id', $request->branch);
            });
        }

        // فلترة حسب كلمات البحث (الاسم أو الكود)
        if ($request->filled('keywords')) {
            $keywords = '%' . $request->keywords . '%';
            $query->where(function ($q) use ($keywords) {
                $q->where('number', 'like', $keywords)->orWhere('details', 'like', $keywords);
            });
        }

        // فلترة حسب نوع الإذن (مصدر الإذن)
        if ($request->filled('permission_type')) {
            $query->where('permission_type', $request->permission_type);
        }

        // فلترة حسب الرقم المعرف
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // فلترة حسب المستودع
        if ($request->filled('store_house')) {
            $query->where('store_houses_id', $request->store_house);
        }

        // فلترة حسب العميل
        if ($request->filled('client')) {
            $query->where('sub_account', $request->client);
        }

        // فلترة حسب المورد
        if ($request->filled('supplier')) {
            $query->where('sub_account', $request->supplier);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب المستخدم الذي أضاف الإذن
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        // فلترة حسب المنتج
        if ($request->filled('product')) {
            $query->whereHas('products', function ($q) use ($request) {
                $q->where('product_id', $request->product);
            });
        }

        // فلترة حسب التاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('permission_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('permission_date', '<=', $request->to_date);
        }

        $wareHousePermits = $query->paginate(30);
        $storeHouses = StoreHouse::where('status', 0)->select('id', 'name')->get();
        $branches = Branch::where('status', 0)->select('id', 'name')->get();
        $clients = Client::all();
        $permissionSources = PermissionSource::all();
        $suppliers = Supplier::all();
        $users = User::where('role', 'employee')->get();
        $products = Product::all();

        // إحصائيات سريعة
        $stats = [
            'pending' => WarehousePermits::where('status', 'pending')->count(),
            'approved' => WarehousePermits::where('status', 'approved')->count(),
            'rejected' => WarehousePermits::where('status', 'rejected')->count(),
            'total' => WarehousePermits::count(),
        ];

        return view('stock::store_permits_management.index', compact('wareHousePermits', 'permissionSources', 'storeHouses', 'branches', 'clients', 'suppliers', 'users', 'products', 'stats'));
    }

    public function show($id)
    {
        $permit = WarehousePermits::with(['items.product', 'reference', 'storeHouse', 'branch']) // eager loading
            ->findOrFail($id);

        $logs = ModelsLogs::where('type', 'warehouse_log')
            ->where('type_id', $id)
            ->whereHas('warehouse_log') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
        return view('stock::store_permits_management.show', compact('permit', 'logs'));
    }

    public function create()
    {
        $storeHouses = StoreHouse::where('status', 0)->select('id', 'name')->get();
        $products = Product::select()->get();

        $record_count = DB::table('warehouse_permits')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);

        return view('stock::store_permits_management.create', compact('storeHouses', 'products', 'serial_number'));
    }

    public function manual_disbursement()
    {
        $storeHouses = StoreHouse::where('status', 0)->select('id', 'name')->get();
        $products = Product::select()->get();

        $record_count = DB::table('warehouse_permits')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);

        return view('stock::store_permits_management.manual_disbursement', compact('storeHouses', 'products', 'serial_number'));
    }

    public function manual_conversion()
    {
        $storeHouses = StoreHouse::where('status', 0)->select('id', 'name')->get();
        $products = Product::select()->get();

        $record_count = DB::table('warehouse_permits')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);

        return view('stock::store_permits_management.manual_conversion', compact('storeHouses', 'products', 'serial_number'));
    }

    public function store(WarehousePermitsRequest $request)
    {
        DB::beginTransaction();
        try {
            $wareHousePermits = new WarehousePermits();

            if ($request->hasFile('attachments')) {
                $wareHousePermits->attachments = $this->UploadImage('assets/uploads/warehouse', $request->attachments);
            }

            if ($request->permission_source_id == 13) {
                $wareHousePermits->store_houses_id = $request->from_store_houses_id;
            } else {
                $wareHousePermits->store_houses_id = $request->store_houses_id;
            }

            $wareHousePermits->permission_source_id = 13;

            $wareHousePermits->permission_date = $request->permission_date;
            $wareHousePermits->sub_account = $request->sub_account;
            $wareHousePermits->number = $request->number;
            $wareHousePermits->details = $request->details;
            $wareHousePermits->grand_total = $request->grand_total;
            $wareHousePermits->from_store_houses_id = $request->from_store_houses_id;
            $wareHousePermits->to_store_houses_id = $request->to_store_houses_id;
            $wareHousePermits->created_by = auth()->user()->id;
            $wareHousePermits->status = 'approved';

            $wareHousePermits->save();

            // إنشاء بنود الإذن
            foreach ($request['quantity'] as $index => $quantity) {
                WarehousePermitsProducts::create([
                    'quantity' => $quantity,
                    'total' => $request['total'][$index],
                    'unit_price' => $request['unit_price'][$index],
                    'product_id' => $request['product_id'][$index],
                    'stock_before' => $request['stock_before'][$index] ?? 0,
                    'stock_after' => $request['stock_after'][$index] ?? 0,
                    'warehouse_permits_id' => $wareHousePermits->id,
                ]);
            }
            $this->createWarehouseTransferEntries($wareHousePermits);

            ModelsLogs::create([
                'type' => 'warehouse_log',
                'type_id' => $wareHousePermits->id,
                'type_log' => 'log',
                'description' => sprintf('تم إنشاء إذن مخزني رقم %s من النوع %s - بانتظار الموافقة', $wareHousePermits->number, $this->getPermissionTypeName($wareHousePermits->permission_type)),
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()
                ->route('store_permits_management.index')
                ->with(['success' => 'تم انشاء اذن المخزن وقيده المحاسبي بنجاح - بانتظار الموافقة']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('store_permits_management.index')
                ->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا: ' . $e->getMessage()]);
        }
    }


    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $wareHousePermit = WarehousePermits::with(['items'])
                ->lockForUpdate()
                ->findOrFail($id);

            // التحقق من حالة الإذن - حماية إضافية
            if ($wareHousePermit->status !== 'pending') {
                DB::rollBack();

                $statusText = $wareHousePermit->status === 'approved' ? 'تمت الموافقة عليه مسبقاً' : 'تم رفضه مسبقاً';

                if (request()->ajax()) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'هذا الإذن ' . $statusText,
                        ],
                        400,
                    );
                }
                return redirect()
                    ->back()
                    ->with('error', 'هذا الإذن ' . $statusText);
            }

            // تحديث حالة الإذن أولاً لمنع التكرار
            $wareHousePermit->update([
                'status' => 'processing', // حالة مؤقتة أثناء المعالجة
                'updated_at' => now(),
            ]);

            // الحصول على المستودع المحدد في الإذن أو المستودع الرئيسي
            $targetStoreHouse = null;

            if ($wareHousePermit->permission_type == 3) {
                // إذن تحويل - استخدم المستودع المحدد في الإذن
                $targetStoreHouse = StoreHouse::find($wareHousePermit->to_store_houses_id);
            } else {
                // إذن إضافة أو صرف - استخدم المستودع المحدد في الإذن أو الرئيسي
                $targetStoreHouse = StoreHouse::find($wareHousePermit->store_houses_id);

                // إذا لم يجد المستودع المحدد، استخدم الرئيسي
                if (!$targetStoreHouse) {
                    $targetStoreHouse = StoreHouse::where('major', 1)->first();
                }
            }

            if (!$targetStoreHouse) {
                DB::rollBack();

                if (request()->ajax()) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'لم يتم العثور على المستودع المطلوب',
                        ],
                        400,
                    );
                }
                return redirect()->back()->with('error', 'لم يتم العثور على المستودع المطلوب');
            }

            // معالجة كل بند في الإذن
            foreach ($wareHousePermit->items as $item) {
                $quantity = $item->quantity;
                $productId = $item->product_id;

                // التحقق من وجود المنتج
                $product = Product::find($productId);
                if (!$product) {
                    DB::rollBack();

                    if (request()->ajax()) {
                        return response()->json(
                            [
                                'success' => false,
                                'message' => 'المنتج غير موجود',
                            ],
                            400,
                        );
                    }
                    return redirect()->back()->with('error', 'المنتج غير موجود');
                }

                if ($wareHousePermit->permission_type == 1) {
                    // 🟢 إذن إضافة للمستودع
                    $result = $this->addToWarehouse($targetStoreHouse->id, $productId, $quantity, $wareHousePermit);

                    if (!$result['success']) {
                        DB::rollBack();

                        if (request()->ajax()) {
                            return response()->json(
                                [
                                    'success' => false,
                                    'message' => $result['message'],
                                ],
                                400,
                            );
                        }
                        return redirect()->back()->with('error', $result['message']);
                    }
                } elseif ($wareHousePermit->permission_type == 2) {
                    // 🔴 إذن صرف من المستودع
                    $result = $this->removeFromWarehouse($targetStoreHouse->id, $productId, $quantity, $wareHousePermit);

                    if (!$result['success']) {
                        DB::rollBack();

                        if (request()->ajax()) {
                            return response()->json(
                                [
                                    'success' => false,
                                    'message' => $result['message'],
                                ],
                                400,
                            );
                        }
                        return redirect()->back()->with('error', $result['message']);
                    }
                } elseif ($wareHousePermit->permission_type == 3) {
                    // 🔄 إذن تحويل من المستودع الرئيسي إلى المستودع المحدد
                    $fromStoreHouse = StoreHouse::find($wareHousePermit->from_store_houses_id);

                    if (!$fromStoreHouse) {
                        $fromStoreHouse = StoreHouse::where('major', 1)->first();
                    }

                    $result = $this->transferBetweenWarehouses($fromStoreHouse->id, $targetStoreHouse->id, $productId, $quantity, $wareHousePermit);

                    if (!$result['success']) {
                        DB::rollBack();

                        if (request()->ajax()) {
                            return response()->json(
                                [
                                    'success' => false,
                                    'message' => $result['message'],
                                ],
                                400,
                            );
                        }
                        return redirect()->back()->with('error', $result['message']);
                    }
                }

                // إنشاء سجل ProductDetails واحد فقط لكل منتج
                $existingDetail = ProductDetails::where([
                    'product_id' => $productId,
                    'store_house_id' => $targetStoreHouse->id,
                ])->first();

                if (!$existingDetail) {
                    ProductDetails::create([
                        'product_id' => $productId,
                        'store_house_id' => $targetStoreHouse->id,
                        'quantity' => $this->getQuantityDirection($wareHousePermit->permission_type) * $quantity,
                        'unit_price' => floatval($item->unit_price ?? 0),
                        'date' => Carbon::parse($wareHousePermit->permission_date),
                        'time' => now()->format('H:i:s'),
                        'type_of_operation' => $wareHousePermit->permission_type,
                        'type' => $wareHousePermit->permission_type,
                        'comments' => 'موافقة على إذن مخزني رقم ' . $wareHousePermit->number . ' - تم في المستودع: ' . $targetStoreHouse->name,
                    ]);
                }
            }

            // ✅ تحديث حالة الإذن إلى موافق عليه - التعديل هنا
            $wareHousePermit->status = 'approved';

            $wareHousePermit->save(); // 🔥 هذا المهم - حفظ التغييرات

            // ✅ تحديث حالة الفاتورة المرتبطة إذا وجدت - التعديل هنا
            if ($wareHousePermit->reference_type === 'purchase_invoice' && $wareHousePermit->reference_id) {
                $purchaseInvoice = PurchaseInvoice::find($wareHousePermit->reference_id);
                if ($purchaseInvoice && $purchaseInvoice->receiving_status !== 'received') {
                    $purchaseInvoice->receiving_status = 'received';
                    $purchaseInvoice->received_date = now()->format('Y-m-d');
                    $purchaseInvoice->save(); // 🔥 هذا المهم - حفظ التغييرات
                }
            }

            notifications::create([
                'user_id' => $wareHousePermit->user_id,
                'receiver_id' => $wareHousePermit->user_id,
                'title' => 'تمت الموافقة على الإذن المخزني رقم ' . $wareHousePermit->number,
                'description' => 'تمت الموافقة على الإذن المخزني رقم ' . $wareHousePermit->number,
                'type' => 'success',
            ]);
            DB::commit();

            $successMessage = 'تمت الموافقة على الإذن المخزني رقم ' . $wareHousePermit->number . ' بنجاح وتم تنفيذه في المستودع: ' . $targetStoreHouse->name;

            // إذا كان الطلب AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'new_status' => 'approved', // إضافة الحالة الجديدة للـ AJAX
                ]);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في الموافقة على الإذن المخزني: ' . $e->getMessage(), [
                'permit_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = 'عذراً، حدث خطأ أثناء الموافقة على الإذن المخزني: ' . $e->getMessage();

            // إذا كان الطلب AJAX
            if (request()->ajax()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => $errorMessage,
                    ],
                    500,
                );
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    private function addToWarehouse($storeHouseId, $productId, $quantity, $wareHousePermit)
    {
        try {
            // إضافة سجل جديد في ProductDetails (لا نبحث عن سجل موجود)
            // لأن ProductDetails هو سجل تاريخي لكل عملية
            ProductDetails::create([
                'product_id' => $productId,
                'store_house_id' => $storeHouseId, // تأكد من اسم الحقل الصحيح
                'quantity' => $quantity, // كمية موجبة للإضافة
                'unit_price' => 0, // سيتم تحديثه لاحقاً من بيانات الإذن
                'date' => Carbon::parse($wareHousePermit->permission_date),
                'time' => now()->format('H:i:s'),
                'type_of_operation' => 1, // نوع العملية: إضافة
                'type' => 1,
                // 'warehouse_permit_id' => $wareHousePermit->id,
                'comments' => 'إضافة للمستودع - إذن رقم ' . $wareHousePermit->number,
                'created_by' => auth()->id(),
            ]);

            return ['success' => true, 'message' => 'تم إضافة المنتج بنجاح'];
        } catch (\Exception $e) {
            Log::error('خطأ في إضافة المنتج للمستودع: ' . $e->getMessage(), [
                'store_house_id' => $storeHouseId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'trace' => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'message' => 'خطأ في إضافة المنتج للمستودع: ' . $e->getMessage()];
        }
    }

    /**
     * صرف منتج من المستودع
     */
    private function removeFromWarehouse($storeHouseId, $productId, $quantity, $wareHousePermit)
    {
        try {
            // حساب إجمالي الكمية المتاحة في المستودع
            $totalQuantity = ProductDetails::where([
                'store_house_id' => $storeHouseId,
                'product_id' => $productId,
            ])->sum('quantity');

            if ($totalQuantity < $quantity) {
                return [
                    'success' => false,
                    'message' => 'الكمية المطلوبة غير متوفرة. الكمية المتاحة: ' . $totalQuantity,
                ];
            }

            ProductDetails::create([
                'product_id' => $productId,
                'store_house_id' => $storeHouseId,
                'quantity' => -$quantity, // كمية سالبة للصرف
                'unit_price' => 0,
                'date' => Carbon::parse($wareHousePermit->permission_date),
                'time' => now()->format('H:i:s'),
                'type_of_operation' => 2, // نوع العملية: صرف
                'type' => 2,
                'comments' => 'صرف من المستودع - إذن رقم ' . $wareHousePermit->number,
                'created_by' => auth()->id(),
            ]);

            return ['success' => true, 'message' => 'تم صرف المنتج بنجاح'];
        } catch (\Exception $e) {
            Log::error('خطأ في صرف المنتج من المستودع: ' . $e->getMessage(), [
                'store_house_id' => $storeHouseId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'trace' => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'message' => 'خطأ في صرف المنتج من المستودع'];
        }
    }

    public function getCurrentStock($productId, $storeHouseId)
    {
        return ProductDetails::where([
            'product_id' => $productId,
            'store_house_id' => $storeHouseId,
        ])->sum('quantity');
    }

    /**
     * إضافة دالة لحساب إجمالي المخزون للمنتج في جميع المستودعات
     */
    public function getTotalStock($productId)
    {
        return ProductDetails::where('product_id', $productId)->sum('quantity');
    }

    /**
     * تحويل منتج بين المستودعات
     */
    private function transferBetweenWarehouses($fromStoreHouseId, $toStoreHouseId, $productId, $quantity, $wareHousePermit)
    {
        try {
            // صرف من المستودع المصدر
            $removeResult = $this->removeFromWarehouse($fromStoreHouseId, $productId, $quantity, $wareHousePermit);
            if (!$removeResult['success']) {
                return $removeResult;
            }

            // إضافة للمستودع الهدف
            $addResult = $this->addToWarehouse($toStoreHouseId, $productId, $quantity, $wareHousePermit);
            if (!$addResult['success']) {
                // في حالة فشل الإضافة، استرجع الكمية للمستودع المصدر
                $this->addToWarehouse($fromStoreHouseId, $productId, $quantity, $wareHousePermit);
                return $addResult;
            }

            return ['success' => true, 'message' => 'تم التحويل بنجاح'];
        } catch (\Exception $e) {
            Log::error('خطأ في تحويل المنتج بين المستودعات: ' . $e->getMessage());
            return ['success' => false, 'message' => 'خطأ في تحويل المنتج بين المستودعات'];
        }
    }

    /**
     * الحصول على اتجاه الكمية (موجب أم سالب)
     */
    private function getQuantityDirection($permissionType)
    {
        switch ($permissionType) {
            case 1: // إضافة
                return 1;
            case 2: // صرف
                return -1;
            case 3: // تحويل
                return 0; // سيتم التعامل معه بشكل منفصل
            default:
                return 0;
        }
    }

    /**
     * الحصول على اسم نوع الإذن
     */
    private function getPermissionTypeName($permissionType)
    {
        switch ($permissionType) {
            case 1:
                return 'إذن إضافة';
            case 2:
                return 'إذن صرف';
            case 3:
                return 'إذن تحويل';
            default:
                return 'غير محدد';
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $wareHousePermit = WarehousePermits::with(['items'])
                ->lockForUpdate()
                ->findOrFail($id);

            // التحقق من حالة الإذن - حماية إضافية
            if ($wareHousePermit->status !== 'pending') {
                DB::rollBack();

                $statusText = $wareHousePermit->status === 'approved' ? 'تمت الموافقة عليه مسبقاً' : 'تم رفضه مسبقاً';

                if (request()->ajax()) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'هذا الإذن ' . $statusText,
                        ],
                        400,
                    );
                }
                return redirect()
                    ->back()
                    ->with('error', 'هذا الإذن ' . $statusText);
            }

            // التحقق من وجود سبب الرفض (اختياري)
            $rejectionReason = $request->input('rejection_reason', 'لم يتم تحديد سبب الرفض');

            // تحديث حالة الإذن إلى مرفوض
            $wareHousePermit->update([
                'status' => 'rejected',
                'rejection_reason' => $rejectionReason,
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'updated_at' => now(),
            ]);

            // تحديث حالة الفاتورة المرتبطة إذا وجدت (في حالة الرفض)
            if ($wareHousePermit->reference_type === 'purchase_invoice' && $wareHousePermit->reference_id) {
                $purchaseInvoice = PurchaseInvoice::find($wareHousePermit->reference_id);
                if ($purchaseInvoice && $purchaseInvoice->receiving_status !== 'rejected') {
                    $purchaseInvoice->receiving_status = 'rejected';
                    $purchaseInvoice->rejection_date = now()->format('Y-m-d');
                    $purchaseInvoice->rejection_reason = $rejectionReason;
                    $purchaseInvoice->save();
                }
            }

            // إنشاء إشعار للمستخدم الذي أنشأ الإذن
            notifications::create([
                'user_id' => $wareHousePermit->user_id,
                'receiver_id' => $wareHousePermit->user_id,
                'title' => 'تم رفض الإذن المخزني رقم ' . $wareHousePermit->number,
                'description' => 'تم رفض الإذن المخزني رقم ' . $wareHousePermit->number . ($rejectionReason ? ' - السبب: ' . $rejectionReason : ''),
                'type' => 'error',
            ]);

            // إنشاء سجل في تاريخ العمليات (اختياري)
            ProductDetails::create([
                'product_id' => null, // أو يمكن تسجيل المنتج الأول من الإذن
                'store_house_id' => $wareHousePermit->store_houses_id,
                'quantity' => 0, // لا توجد حركة فعلية للمخزون
                'unit_price' => 0,
                'date' => now()->format('Y-m-d'),
                'time' => now()->format('H:i:s'),
                'type_of_operation' => 99, // رمز خاص لعمليات الرفض
                'type' => 99,
                'comments' => 'رفض الإذن المخزني رقم ' . $wareHousePermit->number . ' - السبب: ' . $rejectionReason,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            $successMessage = 'تم رفض الإذن المخزني رقم ' . $wareHousePermit->number . ' بنجاح';

            // إذا كان الطلب AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'new_status' => 'rejected',
                ]);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('خطأ في رفض الإذن المخزني: ' . $e->getMessage(), [
                'permit_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = 'عذراً، حدث خطأ أثناء رفض الإذن المخزني: ' . $e->getMessage();

            // إذا كان الطلب AJAX
            if (request()->ajax()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => $errorMessage,
                    ],
                    500,
                );
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }
    public function edit($id)
    {
        $permit = WarehousePermits::findOrFail($id);

        // منع التعديل إذا تمت الموافقة على الإذن
        if ($permit->status === 'approved') {
            return redirect()->back()->with('error', 'لا يمكن تعديل إذن تمت الموافقة عليه');
        }

        $products = Product::select()->get();
        $storeHouses = StoreHouse::where('status', 0)->select('id', 'name')->get();

        if ($permit->permission_type == 1) {
            return view('stock.store_permits_management.edit', compact('permit', 'storeHouses', 'products'));
        }

        if ($permit->permission_type == 2) {
            return view('stock.store_permits_management.manual_disbursement_edit', compact('permit', 'storeHouses', 'products'));
        }

        if ($permit->permission_type == 3) {
            return view('stock.store_permits_management.manual_conversion_edit', compact('permit', 'storeHouses', 'products'));
        }
    }

    public function update(WarehousePermitsRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $wareHousePermits = WarehousePermits::findOrFail($id);

            // حفظ البيانات القديمة للمقارنة
            $oldGrandTotal = $wareHousePermits->grand_total;
            $oldFromStoreId = $wareHousePermits->from_store_houses_id;
            $oldToStoreId = $wareHousePermits->to_store_houses_id;

            // تحديث الملف إذا تم رفع ملف جديد
            if ($request->hasFile('attachments')) {
                // حذف الملف القديم إذا وُجد
                if ($wareHousePermits->attachments) {
                    $oldFilePath = public_path('assets/uploads/warehouse/' . $wareHousePermits->attachments);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $wareHousePermits->attachments = $this->UploadImage('assets/uploads/warehouse', $request->attachments);
            }

            // تحديث بيانات الإذن
            if ($request->permission_source_id == 13) {
                $wareHousePermits->store_houses_id = $request->from_store_houses_id;
            } else {
                $wareHousePermits->store_houses_id = $request->store_houses_id;
            }

            $wareHousePermits->permission_source_id = 13;
            $wareHousePermits->permission_date = $request->permission_date;
            $wareHousePermits->sub_account = $request->sub_account;
            $wareHousePermits->number = $request->number;
            $wareHousePermits->details = $request->details;
            $wareHousePermits->grand_total = $request->grand_total;
            $wareHousePermits->from_store_houses_id = $request->from_store_houses_id;
            $wareHousePermits->to_store_houses_id = $request->to_store_houses_id;
            $wareHousePermits->updated_by = auth()->user()->id;
            $wareHousePermits->status = 'approved';

            $wareHousePermits->save();

            // حذف البنود القديمة
            WarehousePermitsProducts::where('warehouse_permits_id', $wareHousePermits->id)->delete();

            // إنشاء البنود الجديدة
            foreach ($request['quantity'] as $index => $quantity) {
                WarehousePermitsProducts::create([
                    'quantity' => $quantity,
                    'total' => $request['total'][$index],
                    'unit_price' => $request['unit_price'][$index],
                    'product_id' => $request['product_id'][$index],
                    'stock_before' => $request['stock_before'][$index] ?? 0,
                    'stock_after' => $request['stock_after'][$index] ?? 0,
                    'warehouse_permits_id' => $wareHousePermits->id,
                ]);
            }

            // إنشاء قيد محاسبي جديد (بدون تعديل القيد القديم)
            $this->createWarehouseTransferEntries($wareHousePermits);

            // تسجيل لوج التحديث
            ModelsLogs::create([
                'type' => 'warehouse_log',
                'type_id' => $wareHousePermits->id,
                'type_log' => 'update',
                'description' => sprintf('تم تحديث إذن مخزني رقم %s من النوع %s - تم إنشاء قيد محاسبي جديد', $wareHousePermits->number, $this->getPermissionTypeName($wareHousePermits->permission_type)),
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()
                ->route('store_permits_management.index')
                ->with(['success' => 'تم تحديث إذن المخزن وإنشاء قيد محاسبي جديد بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('store_permits_management.index')
                ->with(['error' => 'حدث خطأ ما برجاء المحاولة لاحقاً: ' . $e->getMessage()]);
        }
    }

    // دالة إنشاء القيود المحاسبية (نفس الدالة من store ولكن مع تحديثات طفيفة)
    private function createWarehouseTransferEntries($warehousePermit)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // ✅ حساب المستودع المحول منه
        $fromStoreAccount = Account::where('storehouse_id', $warehousePermit->from_store_houses_id)->first();
        if (!$fromStoreAccount) {
            $fromStore = StoreHouse::find($warehousePermit->from_store_houses_id);
            $fromStoreAccount = Account::create([
                'name' => 'حساب المستودع - ' . ($fromStore->name ?? 'مستودع غير معروف'),
                'storehouse_id' => $warehousePermit->from_store_houses_id,
                'account_type' => 'storehouse',
                'balance' => 0,
                'status' => 1,
            ]);
        }

        // ✅ حساب المستودع المستلم
        $toStoreAccount = Account::where('storehouse_id', $warehousePermit->to_store_houses_id)->first();
        if (!$toStoreAccount) {
            $toStore = StoreHouse::find($warehousePermit->to_store_houses_id);
            $toStoreAccount = Account::create([
                'name' => 'حساب المستودع - ' . ($toStore->name ?? 'مستودع غير معروف'),
                'storehouse_id' => $warehousePermit->to_store_houses_id,
                'account_type' => 'storehouse',
                'balance' => 0,
                'status' => 1,
            ]);
        }

        // ✅ إنشاء قيد التحويل الجديد
        $journalEntry = JournalEntry::create([
            'reference_number' => $warehousePermit->number . '_UPDATE_' . now()->timestamp,
            'warehouse_permit_id' => $warehousePermit->id,
            'date' => now(),
            'description' => 'تحديث تحويل من مستودع #' . $warehousePermit->from_store_houses_id . ' إلى مستودع #' . $warehousePermit->to_store_houses_id,
            'status' => 1,
            'currency' => 'SAR',
            'created_by_employee' => Auth::id(),
        ]);

        // 1. دائن: المستودع المحول منه
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $fromStoreAccount->id,
            'description' => 'خروج بضاعة من المستودع ' . $fromStoreAccount->name . ' (تحديث)',
            'debit' => 0,
            'credit' => $warehousePermit->grand_total,
            'is_debit' => false,
        ]);

        // 2. مدين: المستودع المستلم
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $toStoreAccount->id,
            'description' => 'دخول بضاعة إلى المستودع ' . $toStoreAccount->name . ' (تحديث)',
            'debit' => $warehousePermit->grand_total,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // ✅ تحديث الأرصدة
        $fromStoreAccount->balance -= $warehousePermit->grand_total; // خروج
        $fromStoreAccount->save();

        $toStoreAccount->balance += $warehousePermit->grand_total; // دخول
        $toStoreAccount->save();

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
    public function delete($id)
    {
        $wareHousePermits = WarehousePermits::findOrFail($id);

        // منع الحذف إذا تمت الموافقة على الإذن
        if ($wareHousePermits->status === 'approved') {
            return redirect()->back()->with('error', 'لا يمكن حذف إذن تمت الموافقة عليه');
        }

        WarehousePermitsProducts::where('warehouse_permits_id', $id)->delete();
        $wareHousePermits->delete();

        return redirect()
            ->route('store_permits_management.index')
            ->with(['success' => 'تم حذف أذن المخزن بنجاح']);
    }

    public function getProductStock($storeId, $productId)
    {
        $stock = DB::table('product_details')->where('store_house_id', $storeId)->where('product_id', $productId)->value('quantity');

        return response()->json(['stock' => $stock ?? 0]);
    }

    # Helper Function
    function uploadImage($folder, $image)
    {
        $fileExtension = $image->getClientOriginalExtension();
        $fileName = time() . rand(1, 99) . '.' . $fileExtension;
        $image->move($folder, $fileName);

        return $fileName;
    }
}
