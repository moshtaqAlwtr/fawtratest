<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientGiftOffer;
use App\Models\EmployeeGiftOffer;
use App\Models\GiftOffer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class GiftOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = GiftOffer::all();
        return view('sales::gift_offers.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $clients = Client::all();
        $users = User::whereIn('role', ['manager', 'employee'])
            ->select('id', 'name', 'email', 'role')
            ->get();

        return view('sales::gift_offers.create', compact('products', 'clients', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'target_product_id' => 'nullable|exists:products,id',
            'min_quantity' => 'required|integer|min:1',
            'gift_product_id' => 'nullable|exists:products,id',
            'gift_quantity' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_for_all_clients' => 'required|boolean',
            'is_for_all_employees' => 'required|boolean',
            'clients' => 'array',
            'clients.*' => 'exists:clients,id',

            // ✅ إضافة الموظفين
            'employees' => 'nullable|array',
            'employees.*' => 'exists:users,id',

            'excluded_clients' => 'nullable|array',
            'excluded_clients.*' => 'exists:clients,id',
        ]);

        // ✅ إنشاء عرض الهدية
        $giftOffer = GiftOffer::create([
            'name' => $request->name,
            'target_product_id' => $request->target_product_id,
            'min_quantity' => $request->min_quantity,
            'gift_product_id' => $request->gift_product_id,
            'gift_quantity' => $request->gift_quantity,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_for_all_clients' => $request->is_for_all_clients,
            'is_for_all_employees' =>  $request->is_for_all_employees,
        ]);

        // ✅ ربط العملاء
        if (!$request->is_for_all_clients && $request->has('clients')) {
            foreach ($request->clients as $clientId) {
                ClientGiftOffer::create([
                    'gift_offer_id' => $giftOffer->id,
                    'client_id' => $clientId,
                ]);
            }
        }

        // ✅ ربط الموظفين إذا تم تحديدهم
        if (!$request->is_for_all_employees && $request->has('employees')) {
            foreach ($request->employees as $userId) {
                EmployeeGiftOffer::create([
                    'gift_id' => $giftOffer->id,
                    'user_id' => $userId,
                ]);
            }
        }

         // ربط العملاء المستثنون
    if ($request->has('excluded_clients')) {
        foreach ($request->excluded_clients as $clientId) {
            \App\Models\ExcludedClientGiftOffer::create([
                'gift_offer_id' => $giftOffer->id,
                'client_id' => $clientId,
            ]);
        }
    }

        return redirect()->route('Offers.index')->with('success', 'تم إنشاء عرض الهدية بنجاح');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(GiftOffer $giftOffer)
    {
        $giftOffer->load('clients', 'users'); // ⬅️ تحميل الموظفين المرتبطين أيضًا

        $products = Product::all();
        $clients = Client::all();
        $users = User::whereIn('role', ['manager', 'employee'])
            ->select('id', 'name', 'email', 'role')
            ->get();

        return view('sales::gift_offers.create', compact('products', 'clients', 'users', 'giftOffer'));
    }






    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'target_product_id' => 'nullable|exists:products,id',
            'min_quantity' => 'required|integer|min:1',
            'gift_product_id' => 'nullable|exists:products,id',
            'gift_quantity' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_for_all_clients' => 'required|boolean',
            'is_for_all_employees' => 'required|boolean',
            'clients' => 'array',
            'clients.*' => 'exists:clients,id',
            'employees' => 'array',
            'employees.*' => 'exists:users,id',

             // الفاليديشن للعملاء المستثنون
        'excluded_clients' => 'nullable|array',
        'excluded_clients.*' => 'exists:clients,id',

        ]);

        $giftOffer = GiftOffer::findOrFail($id);

        // ✅ تحديث بيانات العرض
        $giftOffer->update([
            'name' => $request->name,
            'target_product_id' => $request->target_product_id,
            'min_quantity' => $request->min_quantity,
            'gift_product_id' => $request->gift_product_id,
            'gift_quantity' => $request->gift_quantity,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_for_all_clients' => $request->is_for_all_clients,
        ]);

        // ✅ تحديث العملاء المرتبطين بالعرض
        if (!$request->is_for_all_clients && $request->has('clients')) {
            // حذف العملاء الحاليين المرتبطين بالعرض
            $giftOffer->clients()->sync($request->clients);
        } else {
            // إذا كان العرض لكل العملاء، نحذف كل الربط السابق
            $giftOffer->clients()->detach();
        }

        // ✅ تحديث الموظفين المرتبطين بالعرض
        if (!$request->is_for_all_employees && $request->has('employees')) {
            $giftOffer->users()->sync($request->employees);
        } else {
            $giftOffer->users()->detach();
        }

 // ✅ تحديث العملاء المستثنون
    if ($request->has('excluded_clients')) {
        $giftOffer->excludedClients()->sync($request->excluded_clients);
    } else {
        $giftOffer->excludedClients()->detach();
    }
        return redirect()->route('Offers.index')->with('success', 'تم تحديث عرض الهدية بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
  public function destroy(GiftOffer $giftOffer)
{
    $giftOffer->update(['is_active' => false]);
    return redirect()->route('Offers.index')->with('success', 'تم تعطيل العرض بنجاح');
}
public function status($id)
{

    $giftOffer = GiftOffer::findOrFail($id);

    $giftOffer->is_active = !$giftOffer->is_active;
    $giftOffer->save();

    return redirect()->back()->with('success', 'تم تحديث حالة العرض بنجاح');
}



}
