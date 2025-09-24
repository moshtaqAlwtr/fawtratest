<?php


namespace Modules\Stock\Http\Controllers\Stock;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorehouseRequest;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\JobRole;
use App\Models\Log as ModelsLog;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\StoreHouse;
use App\Models\WarehousePermits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StorehouseController extends Controller
{
    public function index()
    {
        $storehouses = StoreHouse::orderBy('id', 'DESC')->get();
        return view('stock::storehouse.index', compact('storehouses'));
    }

    public function create()
    {
        $branches = Branch::select('id', 'name')->get();
        $employees = Employee::select()->get();
        $job_roles = JobRole::select('id', 'role_name')->get();
        return view('stock::storehouse.crate', compact('employees', 'branches', 'job_roles'));
    }

public function store(StorehouseRequest $request)
{
    DB::beginTransaction();

    try {
        // التحقق من وجود الحساب الأب (المخزون)
        $inventoryParentAccount = Account::find(16);
        if (!$inventoryParentAccount) {
            throw new \Exception('حساب المخزون الرئيسي غير موجود');
        }

        // إنشاء المستودع
        $storehouse = new StoreHouse();
        $storehouse->name = $request->name;
        $storehouse->shipping_address = $request->shipping_address;
        $storehouse->status = $request->status;
        $storehouse->view_permissions = $request->view_permissions;
        $storehouse->crate_invoices_permissions = $request->crate_invoices_permissions;
        $storehouse->edit_stock_permissions = $request->edit_stock_permissions;

        // التعامل مع المستودع الرئيسي
        if ($request->has('major')) {
            Storehouse::where('major', 1)->update(['major' => 0]);
            $storehouse->major = 1;
        } else {
            $storehouse->major = 0;
        }

        // إعداد الصلاحيات
        $this->setPermissions($storehouse, $request);

        $storehouse->save();
        // إنشاء الحساب المحاسبي للمستودع
        $account = new Account();

        $account->name = $request->name;
        $account->type_accont = 0; // نوع الحساب (مستودع)
        $account->is_active = $request->is_active ?? 1;
        $account->parent_id = 16; // حساب المخزون الرئيسي
        $account->balance_type = 'debit'; // نوع الرصيد (مدين)
        $account->balance = 0; // الرصيد الابتدائي
        $account->code = 0; // سيتم تحديثه لاحقاً
        $account->storehouse_id = $storehouse->id; // ربط الحساب بالمستودع
        $account->save();

        // تحديث رمز الحساب
        $account->code = $this->generateAccountCode($account->id, $account->parent_id);
        $account->save();

        // ربط المستودع بالحساب
        $storehouse->account_id = $account->id;
        $storehouse->save();

        // تسجيل Log
        ModelsLog::create([
            'type' => 'storehouse_log',
            'type_id' => $storehouse->id,
            'type_log' => 'create',
            'description' => 'تم إضافة المستودع: **' . $storehouse->name . '** وإنشاء الحساب المحاسبي رقم: **' . $account->code . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()
            ->route('storehouse.index')
            ->with('success', 'تم إضافة المستودع والحساب المحاسبي بنجاح!');

    } catch (\Exception $e) {
        DB::rollback();

        Log::error('خطأ في إضافة المستودع: ' . $e->getMessage());

        return redirect()
            ->route('storehouse.index')
            ->with('error', 'حدث خطأ أثناء إضافة المستودع: ' . $e->getMessage());
    }
}

/**
 * إعداد صلاحيات المستودع
 */
private function setPermissions($storehouse, $request)
{
    // صلاحيات العرض
    switch ($request->view_permissions) {
        case 1: // موظف محدد
            $storehouse->value_of_view_permissions = $request->v_employee_id;
            break;
        case 2: // دور وظيفي
            $storehouse->value_of_view_permissions = $request->v_functional_role_id;
            break;
        default: // فرع
            $storehouse->value_of_view_permissions = $request->v_branch_id;
            break;
    }

    // صلاحيات إنشاء الفواتير
    switch ($request->crate_invoices_permissions) {
        case 1: // موظف محدد
            $storehouse->value_of_crate_invoices_permissions = $request->c_employee_id;
            break;
        case 2: // دور وظيفي
            $storehouse->value_of_crate_invoices_permissions = $request->c_functional_role_id;
            break;
        default: // فرع
            $storehouse->value_of_crate_invoices_permissions = $request->c_branch_id;
            break;
    }

    // صلاحيات تعديل المخزون
    switch ($request->edit_stock_permissions) {
        case 1: // موظف محدد
            $storehouse->value_of_edit_stock_permissions = $request->e_employee_id;
            break;
        case 2: // دور وظيفي
            $storehouse->value_of_edit_stock_permissions = $request->e_functional_role_id;
            break;
        default: // فرع
            $storehouse->value_of_edit_stock_permissions = $request->e_branch_id;
            break;
    }
}

/**
 * توليد رمز الحساب بناءً على التسلسل الهرمي
 */
private function generateAccountCode($accountId, $parentId)
{
    $parent = Account::find($parentId);

    if (!$parent) {
        return $accountId;
    }

    // إنشاء رمز فرعي بناءً على الحساب الأب
    $childrenCount = Account::where('parent_id', $parentId)->count();
    $parentCode = $parent->code ?: $parent->id;

    return $parentCode . str_pad($childrenCount, 2, '0', STR_PAD_LEFT);
}

/**
 * تحديث رصيد المستودع
 */
public function updateStorehouseBalance($storehouseId, $amount, $operation = 'add')
{
    $storehouse = StoreHouse::with('account')->find($storehouseId);

    if (!$storehouse || !$storehouse->account) {
        throw new \Exception('المستودع أو الحساب المحاسبي غير موجود');
    }

    $storehouse->account->updateBalance($amount, $operation);

    return $storehouse->account->balance;
}
    public function edit($id)
    {
        $storehouse = StoreHouse::findOrFail($id);
        $branches = Branch::select('id', 'name')->get();
        $employees = Employee::select()->get();
        $job_roles = JobRole::select('id', 'role_name')->get();
        return view('stock::storehouse.edit', compact('storehouse', 'employees', 'branches', 'job_roles'));
    }

    public function show($id)
    {
        $storehouse = StoreHouse::findOrFail($id);
        $actives_logs = Log::where('type_log', 'log')->where('type', 'product_log')->where('type_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()->unique('id')
            ->filter(function ($log) {
                return !is_null($log) && !is_bool($log); // التأكد من أن السجل ليس null أو false
            })
            ->groupBy(function ($log) {
                return optional($log->created_at)->format('Y-m-d'); // التأكد أن created_at ليس null
            });

        return view('stock::storehouse.show', compact('storehouse','actives_logs'));
    }

public function update(StorehouseRequest $request, $id)
{
    DB::beginTransaction();

    try {
        $storehouse = StoreHouse::findOrFail($id);
        $oldName = $storehouse->name;

        // تحديث بيانات المستودع
        $storehouse->name = $request->name;
        $storehouse->shipping_address = $request->shipping_address;
        $storehouse->status = $request->status;
        $storehouse->view_permissions = $request->view_permissions;
        $storehouse->crate_invoices_permissions = $request->crate_invoices_permissions;
        $storehouse->edit_stock_permissions = $request->edit_stock_permissions;

        // التعامل مع المستودع الرئيسي
        if ($request->has('major')) {
            Storehouse::where('major', 1)->update(['major' => 0]);
            $storehouse->major = 1;
        } else {
            $storehouse->major = 0;
        }

        // إعداد الصلاحيات
        $this->setPermissions($storehouse, $request);

        $storehouse->save();

        // التحقق من الحساب المحاسبي المرتبط
        $account = Account::where('storehouse_id', $storehouse->id)->first();

        if ($account) {
            // تحديث الحساب الحالي
            $account->name = $request->name;
            $account->is_active = $request->is_active ?? 1;
            $account->save();
        } else {
            // إنشاء حساب جديد إذا ماكان موجود
            $account = new Account();
            $account->name = $request->name;
            $account->type_accont = 0; // نوع الحساب (مستودع)
            $account->is_active = $request->is_active ?? 1;
            $account->parent_id = 16; // حساب المخزون الرئيسي
            $account->balance_type = 'debit';
            $account->balance = 0;
            $account->code = 0; // سيتم تحديثه لاحقاً
            $account->storehouse_id = $storehouse->id;
            $account->save();

            // تحديث رمز الحساب
            $account->code = $this->generateAccountCode($account->id, $account->parent_id);
            $account->save();
        }

        // تسجيل Log
        ModelsLog::create([
            'type' => 'storehouse_log',
            'type_id' => $storehouse->id,
            'type_log' => 'update',
            'description' => sprintf(
                'تم تعديل المستودع من **%s** إلى **%s**',
                $oldName,
                $storehouse->name
            ),
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()
            ->route('storehouse.index')
            ->with('success', 'تم تحديث المستودع والحساب المحاسبي بنجاح!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('خطأ في تعديل المستودع: ' . $e->getMessage());

        return redirect()
            ->route('storehouse.index')
            ->with('error', 'حدث خطأ أثناء تعديل المستودع: ' . $e->getMessage());
    }
}


    public function delete($id)
    {
        $storehouse = StoreHouse::findOrFail($id);
        Log::create([
            'type' => 'product_log',
            'type_id' => $storehouse->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم  حذف المستودع :  :  **' . $storehouse->name . '**', // النص المنسق
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        // التحقق مما إذا كان المستودع يحتوي على أصناف
        if ($storehouse->productDetails()->count() > 0) {
            return back()->with('error', 'هذا المستودع لديه معاملات، يمكنك إلغاء تفعيله فقط لا حذفه');
        }

        // التحقق مما إذا كان المستودع مستخدمًا في عمليات تحويل
        if ($storehouse->transfersFrom()->count() > 0 || $storehouse->transfersTo()->count() > 0) {
            return back()->with('error', 'هذا المستودع لديه معاملات، يمكنك إلغاء تفعيله فقط لا حذفه');
        }

        $storehouse->delete();
        return redirect()->route('storehouse.index')->with(key: ['error' => 'تم حذف المستودع بنجاج !!']);
    }

    // public function summary_inventory_operations($id)
    // {
    //     $storehouse = StoreHouse::findOrFail($id);
    //     $warehousePermits = WarehousePermits::where('store_houses_id', $id)->with('warehousePermitsProducts')->get();

    //     $allProducts = collect();

    //     foreach ($warehousePermits as $storePermit) {
    //         foreach ($storePermit['products'] as $product) {
    //             $allProducts->push($product);
    //         }
    //     }

    //     $uniqueProducts = $allProducts->unique('id');

    //     $uniqueProductsArray = $uniqueProducts->values()->all();
    //     return $warehousePermits;

    //     return view('stock.storehouse.summary_inventory_operations', [
    //         'warehousePermits' => $warehousePermits,
    //         'storehouse' => $storehouse,
    //     ]);
    // }

    public function summary_inventory_operations($id)
    {
        // جلب بيانات المستودع
        $storehouse = StoreHouse::findOrFail($id);

        // جلب جميع التصاريح الخاصة بالمستودع
        $warehousePermits = WarehousePermits::where('store_houses_id', $id)
            ->orWhere('from_store_houses_id', $id)
            ->orWhere('to_store_houses_id', $id)
            ->with('warehousePermitsProducts')
            ->get();

        // جلب الفواتير مع عناصرها
        $invoices = Invoice::with('items')->get();

        // مصفوفات لتخزين الكميات
        $normalSalesArray = []; // الكميات المباعة (فواتير عادية)
        $salesReturnsArray = []; // الكميات المرتجعة (فواتير مرتجع)

        // معالجة الفواتير
        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                $productId = $item->product_id;

                // إذا كانت الفاتورة من نوع "عادي" (normal)
                if ($invoice->type == 'normal') {
                    $normalSalesArray[$productId] = ($normalSalesArray[$productId] ?? 0) + $item->quantity;
                }

                // إذا كانت الفاتورة من نوع "مرتجع" (return)
                if ($invoice->type == 'return') {
                    $salesReturnsArray[$productId] = ($salesReturnsArray[$productId] ?? 0) + $item->quantity;
                }
            }
        }

        // مصفوفة لتخزين بيانات المنتجات
        $productsData = [];

        // معالجة التصاريح
        foreach ($warehousePermits as $permit) {
            foreach ($permit->warehousePermitsProducts as $product) {
                $productId = $product->product_id;
                $productInfo = Product::find($productId);

                // إذا لم يتم العثور على المنتج، يتم تخطيه
                if (!$productInfo) {
                    continue;
                }

                // إذا لم يتم إضافة المنتج إلى المصفوفة، يتم إضافته
                if (!isset($productsData[$productId])) {
                    $productsData[$productId] = [
                        'name' => $productInfo->name,
                        'id' => $productId,
                        'incoming_manual' => 0,
                        'incoming_transfer' => 0,
                        'outgoing_manual' => 0,
                        'outgoing_transfer' => 0,
                        'incoming_total' => 0,
                        'outgoing_total' => 0,
                        'movement_total' => 0,
                        'sold_quantity' => $normalSalesArray[$productId] ?? 0, // الكمية المباعة
                        'sales_return_quantity' => $salesReturnsArray[$productId] ?? 0, // مرتجع المبيعات
                    ];
                }

                $quantity = $product->quantity;

                // حساب القيم بناءً على نوع الإذن
                switch ($permit->permission_type) {
                    case 1: // إضافة (يدوي - وارد)
                        $productsData[$productId]['incoming_manual'] += $quantity;
                        break;
                    case 2: // صرف (يدوي - منصرف)
                        $productsData[$productId]['outgoing_manual'] += $quantity;
                        break;
                    case 3: // تحويل
                        if ($permit->to_store_houses_id == $id) {
                            $productsData[$productId]['incoming_transfer'] += $quantity;
                        }
                        if ($permit->from_store_houses_id == $id) {
                            $productsData[$productId]['outgoing_transfer'] += $quantity;
                        }
                        break;
                }

                // حساب الإجماليات
                $productsData[$productId]['incoming_total'] =
                    $productsData[$productId]['incoming_manual'] +
                    $productsData[$productId]['incoming_transfer'];

                $productsData[$productId]['outgoing_total'] =
                    $productsData[$productId]['outgoing_manual'] +
                    $productsData[$productId]['outgoing_transfer'];

                $productsData[$productId]['movement_total'] =
                    $productsData[$productId]['incoming_total'] -
                    $productsData[$productId]['outgoing_total'];
            }
        }

        $categories = Category::all();
        // إرجاع البيانات إلى الواجهة
        return view('stock::storehouse.summary_inventory_operations', [
            'products' => $productsData,
            'storehouse' => $storehouse,
            'categories' => $categories,
        ]);
        }
    public function inventory_value($id)
    {
        $products = ProductDetails::where('store_house_id', $id)->select('product_id', DB::raw('SUM(quantity) as quantity'))->with('product')->groupBy('product_id')->get();

        $storehouse = StoreHouse::findOrFail($id);


        return view('stock::storehouse.inventory_value', compact('products', 'storehouse'));
    }

    public function inventory_sheet($id)
    {
        $products = ProductDetails::where('store_house_id', $id)->get();

        $storehouse = StoreHouse::findOrFail($id);

        return view('stock::storehouse.inventory_sheet', compact('products', 'storehouse'));
    }
} # End of Controller
