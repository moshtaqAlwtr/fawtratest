<?php


namespace Modules\Stock\Http\Controllers\Stock;
use App\Http\Controllers\Controller;
use App\Models\InventoryAdjustment;
use App\Models\InventoryItem;
use App\Models\PermissionSource;
use App\Models\StoreHouse;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use Illuminate\Http\Request;
use DB;

class InventoryManagementController extends Controller
{
     public function index()
    {
        $adjustments = InventoryAdjustment::with(['storeHouse'])->get();
        return view('stock::inventory_management.index', compact('adjustments'));
    }

    public function create()
    {
        $storehouses = StoreHouse::orderBy('id', 'DESC')->get();
        return view('stock::inventory_management.create_step1',compact('storehouses'));
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'storehouse_id' => 'required|exists:store_houses,id',
        'inventory_time' => 'required|date',
        'calculation_type' => 'required|in:dated,undated',
    ]);

    $adjustment = InventoryAdjustment::create([
        'stock_id' => $validated['storehouse_id'], // سيتم ملؤها لاحقًا حسب نوع الجرد
        'inventory_time' => $validated['inventory_time'],

        'status' => 'draft',
        'calculation_type' => $validated['calculation_type'],
    ]);


    return redirect()->route('inventory.do_stock', $adjustment->id);
}


public function doStock($id)
{
    $adjustment = InventoryAdjustment::findOrFail($id);



    // أو إذا كنت تريد جميع المنتجات مع الكميات (حتى لو كانت صفر)
    $products = Product::leftJoin('product_details', function($join) use ($adjustment) {
            $join->on('products.id', '=', 'product_details.product_id')
                 ->where('product_details.store_house_id', $adjustment->stock_id);
        })
        ->select('products.*', DB::raw('COALESCE(product_details.quantity, 0) as current_quantity'))
        ->get();



    return view('stock::inventory_management.doStock', compact('adjustment', 'products'));
}

public function edit($id)
{
    $item = InventoryItem::where('adjustment_id',$id)->get();

    return view('stock::inventory_management.edit', compact('item'));
}
public function saveFinal(Request $request, $id)
{
    DB::beginTransaction();

    try {
        $adjustment = InventoryAdjustment::findOrFail($id);

        $items = $request->input('items', []);

        if (empty($items)) {
            return back()->with('error', 'لا توجد عناصر لحفظها');
        }

        foreach ($items as $item) {
$imagePath = null;

if ($request->hasFile('items.' . $item['product_id'] . '.image')) {
    $image = $request->file('items.' . $item['product_id'] . '.image');

    $filename = uniqid() . '.' . $image->getClientOriginalExtension(); // اسم فريد

    $destinationPath = public_path('inventory_images');

    // إنشاء المجلد إذا لم يكن موجود
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    // نقل الصورة إلى المجلد المطلوب
    $image->move($destinationPath, $filename);

    // حفظ المسار في قاعدة البيانات إن أردت (نسبي إلى public)
    $imagePath = 'inventory_images/' . $filename;
}


            $adjustment->items()->create([
                'product_id' => $item['product_id'],
                'quantity_in_system' => $item['quantity_in_system'],
                'quantity_in_stock' => $item['quantity_in_stock'],
                'quantity_difference' => $item['quantity_in_stock'] - $item['quantity_in_system'],
                'image' => $imagePath,
                'note' => $item['note'] ?? null,
            ]);
        }

        // $adjustment->update(['status' => 'completed']);
        DB::commit();

      return redirect()->route('inventory.show', $id)->with('success', 'تم حفظ الجرد بنجاح');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
}

public function show($id)
{


    $adjustment = InventoryAdjustment::with(['storeHouse', 'items.product'])
        ->findOrFail($id);

    return view('stock::inventory_management.show', compact('adjustment'));
}
    public function adjustment($id)
{

    $adjustment = InventoryAdjustment::with('items')->findOrFail($id); // جلب الجرد مع العناصر

    if ($adjustment->status === 'adjusted') {
        return redirect()->back()->with('error', 'تمت تسوية هذا الجرد مسبقاً.');
    }

    DB::beginTransaction();

    // try {
        foreach ($adjustment->items as $item) {
            $productDetails = ProductDetails::where('product_id', $item->product_id)
                ->where('store_house_id', $adjustment->stock_id)
                ->first();

            if (!$productDetails) {
                continue;
            }

            $stock_before = $productDetails->quantity;
            $difference = $item->quantity_difference;

            if ($difference > 0) {
                $productDetails->increment('quantity', $difference);
                $permissionName = 'ورقة جرد وارد';
            } elseif ($difference < 0) {
                $productDetails->decrement('quantity', abs($difference));
                $permissionName = 'ورقة جرد منصرف';
            } else {
                continue;
            }

            $stock_after = $productDetails->quantity;

            $permissionSource = PermissionSource::where('name', $permissionName)->first();
            // if (!$permissionSource) {
            //     throw new \Exception("مصدر إذن '$permissionName' غير موجود.");
            // }

            $permit = WarehousePermits::create([
                'permission_source_id' => $permissionSource->id,
                'permission_date' => now(),
                'number' => $adjustment->id,
                'grand_total' => 0,
                'store_houses_id' => $adjustment->stock_id,
                'created_by' => auth()->id(),
            ]);

            WarehousePermitsProducts::create([
                'quantity' => abs($difference),
                'total' => 0,
                'unit_price' => 0,
                'product_id' => $item->product_id,
                'stock_before' => $stock_before,
                'stock_after' => $stock_after,
                'warehouse_permits_id' => $permit->id,
            ]);
        }

        $adjustment->status = 'adjusted';
        $adjustment->save();

        DB::commit();
        return redirect()->back()->with('success', 'تمت التسوية بنجاح.');

    // } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'حدث خطأ أثناء التسوية: ' . $e->getMessage());
    // }
}



public function Canceladjustment($id)
{
    $adjustment = InventoryAdjustment::with('items')->findOrFail($id);

    if ($adjustment->status !== 'adjusted') {
        return redirect()->back()->with('error', 'لا يمكن إلغاء تسوية غير منفذة.');
    }

    DB::beginTransaction();

    try {
        // جلب جميع الإذونات المرتبطة بهذا الجرد
        $permits = WarehousePermits::where('number', $adjustment->id)
            ->where('store_houses_id', $adjustment->stock_id)
            ->get();

        foreach ($permits as $permit) {
            foreach ($permit->warehousePermitsProducts as $permitProduct) {
                $productDetails = ProductDetails::where('product_id', $permitProduct->product_id)
                    ->where('store_house_id', $adjustment->stock_id)
                    ->first();

                if (!$productDetails) continue;

                // عكس العملية
                $difference = $permitProduct->quantity;

                // حسب نوع الإذن (وارد أو منصرف)
                $permissionTypeName = $permit->permissionSource->name ?? ''; // يفترض علاقة source() موجودة

                if ($permissionTypeName === 'ورقة جرد وارد') {
                    // كان هناك زيادة، الآن ننقص
                    $productDetails->decrement('quantity', $difference);
                } elseif ($permissionTypeName === 'ورقة جرد منصرف') {
                    // كان هناك نقصان، الآن نزيد
                    $productDetails->increment('quantity', $difference);
                }

                // حذف السطر
                $permitProduct->delete();
            }

            // حذف الإذن نفسه
            $permit->delete();
        }

        // تعديل حالة الجرد
        $adjustment->status = 'draft'; // أو 'not_adjusted' حسب النظام
        $adjustment->save();

        DB::commit();
        return redirect()->back()->with('success', 'تم إلغاء التسوية بنجاح.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'فشل في إلغاء التسوية: ' . $e->getMessage());
    }
}

}
