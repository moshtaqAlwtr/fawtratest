<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Offer;
use App\Models\Product;
use App\Models\GiftOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;

class OffersController extends Controller
{
    public function index()
    {
        $offers = Offer::all();
        $gift_offers  = GiftOffer::all();
        return view('sales::sitting.offers.index', compact('offers','gift_offers'));
    }

    public function create()
    {
        $product = Product::all();
        $category = Category::all();
        $clients = Client::all();
        return view('sales::sitting.offers.create', compact('clients', 'product', 'category'));
    }
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after:valid_from',
            'type' => 'required|integer|in:1,2',
            'quantity' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|integer|in:1,2',
            'discount_value' => 'nullable|numeric|min:0',
            'category' => 'nullable|string',
            'client_id' => 'nullable|array',
            'client_id.*' => 'exists:clients,id',
            'unit_type' => 'nullable|integer|in:1,2,3',
            'product_id' => 'required_if:unit_type,3|nullable|array',
            'product_id.*' => 'exists:products,id',
            'category_id' => 'required_if:unit_type,2|nullable|array',
            'category_id.*' => 'exists:categories,id',
            'is_active' => 'boolean',
        ]);

        try {
            // إضافة القيمة الافتراضية لـ is_active إذا لم يتم إرسالها
            $validated['is_active'] = $request->has('is_active') ? true : false;

            // إنشاء العرض الأساسي
            $offer = Offer::create([
                'name' => $validated['name'],
                'valid_from' => $validated['valid_from'],
                'valid_to' => $validated['valid_to'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'discount_type' => $validated['discount_type'],
                'discount_value' => $validated['discount_value'],
                'category' => $validated['category'] ?? null,
                'unit_type' => $validated['unit_type'],
                'is_active' => $validated['is_active'],
            ]);

            // إرفاق العملاء (Many-to-Many)
            if (!empty($validated['client_id'])) {
                $offer->clients()->attach($validated['client_id']);
            }

            // إرفاق التصنيفات (Many-to-Many)
            if (!empty($validated['category_id'])) {
                $offer->categories()->attach($validated['category_id']);
            }

            // إرفاق المنتجات (Many-to-Many)
            if (!empty($validated['product_id'])) {
                $offer->products()->attach($validated['product_id']);
            }

            return redirect()->route('Offers.index')->with('success', 'تم إضافة العرض بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة العرض: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $offer = Offer::with(['clients', 'products', 'categories'])->findOrFail($id);
        $products = Product::all();
        $categories = Category::all();
        $clients = Client::all();

        return view('sales::sitting.offers.edit', compact('offer', 'products', 'categories', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'type' => 'required|integer|in:1,2',
            'quantity' => 'required|numeric|min:1',
            'discount_type' => 'required|integer|in:1,2',
            'discount_value' => 'required|numeric|min:0',
            'client_id' => 'nullable|array',
            'client_id.*' => 'exists:clients,id',
            'unit_type' => 'required|integer|in:1,2,3',
            'product_id' => 'nullable|array',
            'product_id.*' => 'exists:products,id',
            'category_id' => 'nullable|array',
            'category_id.*' => 'exists:categories,id',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // تحديث البيانات الأساسية
            $offer->update([
                'name' => $validated['name'],
                'valid_from' => $validated['valid_from'],
                'valid_to' => $validated['valid_to'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'discount_type' => $validated['discount_type'],
                'discount_value' => $validated['discount_value'],
                'unit_type' => $validated['unit_type'],
                'is_active' => $request->has('is_active'),
            ]);

            // مزامنة العلاقات
            if (isset($validated['client_id'])) {
                $offer->clients()->sync($validated['client_id']);
            }

            if (isset($validated['product_id'])) {
                $offer->products()->sync($validated['product_id']);
            }

            if (isset($validated['category_id'])) {
                $offer->categories()->sync($validated['category_id']);
            }

            DB::commit();

            return redirect()->route('Offers.index')->with('success', 'تم تحديث العرض بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating offer: ' . $e->getMessage());
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث العرض');
        }
    }

    public function destroy($id)
    {
        try {
            $offer = Offer::findOrFail($id);
            $offer->delete();

            return redirect()->route('Offers.index')->with('success', 'تم حذف العرض بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف العرض: ' . $e->getMessage());
        }
    }
    public function show($id)
    {
        $offer = Offer::with(['clients', 'products', 'categories'])->findOrFail($id);
        return view('sales::sitting.offers.show', compact('offer'));
    }
    public function updateStatus($id)
    {
        $offer = Offer::find($id);

        if (!$offer) {
            return redirect()->route('offer.show',$id)->with(['error' => ' العرض  غير موجود!']);
        }

        $offer->update(['status' => !$offer->status]);

        return redirect()->route('offer.show',$id)->with(['success' => 'تم تحديث حالة العرض بنجاح!']);
    }
}
