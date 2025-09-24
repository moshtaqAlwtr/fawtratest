<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Models\Quote;
use App\Models\Account;
use App\Models\TaxInvoice;
use App\Models\SerialSetting;
use App\Models\Product;
use App\Models\InvoiceItem;
use App\Models\TaxSitting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\AccountSetting;
use Modules\Api\Http\Resources\QuoteResource;
use Modules\Api\Http\Resources\QuoteFullResource;


class QuoteController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    try {
        $query = Quote::with(['client', 'creator', 'items']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('id')) {
            $query->where('id', 'LIKE', '%' . $request->id . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', intval($request->status));
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('total_from')) {
            $query->where('grand_total', '>', $request->total_from);
        }

        if ($request->filled('total_to')) {
            $query->where('grand_total', '<', $request->total_to);
        }

        if ($request->filled('from_date_1') && $request->filled('to_date_1')) {
            $from = Carbon::parse($request->from_date_1)->startOfDay();
            $to = Carbon::parse($request->to_date_1)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('date_type_2')) {
            switch ($request->date_type_2) {
                case 'monthly':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'weekly':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'daily':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                default:
                    if ($request->filled('from_date_2') && $request->filled('to_date_2')) {
                        $from = Carbon::parse($request->from_date_2)->startOfDay();
                        $to = Carbon::parse($request->to_date_2)->endOfDay();
                        $query->whereBetween('created_at', [$from, $to]);
                    }
            }
        }

        if ($request->filled('item_search')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('item', 'LIKE', '%' . $request->item_search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->item_search . '%');
            });
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('sales_representative')) {
            $query->where('created_by', $request->sales_representative);
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(10);

        return $this->paginatedResponse(QuoteResource::collection($quotes), 'تم جلب عروض الأسعار بنجاح');

    } catch (\Exception $e) {
        return $this->errorResponse('حدث خطأ أثناء جلب عروض الأسعار', 500, $e->getMessage());
    }
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('api::create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validated = validator($request->all(), [
        'client_id' => 'required|exists:clients,id',
        'quote_date' => 'required|date_format:Y-m-d',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|numeric|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0',
        'items.*.discount_type' => 'nullable|in:amount,percentage',
        'items.*.tax_1' => 'nullable|numeric|min:0',
        'items.*.tax_2' => 'nullable|numeric|min:0',
        'items.*.tax_1_id' => 'nullable|exists:tax_sittings,id',
        'items.*.tax_2_id' => 'nullable|exists:tax_sittings,id',
        'shipping_cost' => 'nullable|numeric|min:0',
        'discount_type' => 'nullable|in:amount,percentage',
        'discount_amount' => 'nullable|numeric|min:0',
        'tax_type' => 'required|in:1,2,3',
        'tax_rate' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string',
    ])->validate();

    DB::beginTransaction();
    try {
        $serialSetting = SerialSetting::where('section', 'quotation')->first();
        $currentNumber = $serialSetting ? $serialSetting->current_number : 1;

        while (Quote::where('id', $currentNumber)->exists()) {
            $currentNumber++;
        }

        if ($serialSetting) {
            $serialSetting->update(['current_number' => $currentNumber + 1]);
        } else {
            SerialSetting::create(['section' => 'quotation', 'current_number' => $currentNumber + 1]);
        }

        $total_amount = 0;
        $total_discount = 0;
        $items_data = [];

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $quantity = floatval($item['quantity']);
            $unit_price = floatval($item['unit_price']);
            $item_total = $quantity * $unit_price;

            $item_discount = 0;
            if (!empty($item['discount'])) {
                $item_discount = $item['discount_type'] == 'percentage'
                    ? ($item_total * $item['discount']) / 100
                    : $item['discount'];
            }

            $total_amount += $item_total;
            $total_discount += $item_discount;

            $items_data[] = [
                'quotation_id' => null,
                'product_id' => $item['product_id'],
                'item' => $product->name,
                'description' => $item['description'] ?? null,
                'quantity' => $quantity,
                'unit_price' => $unit_price,
                'discount' => $item_discount,
                'discount_type' => $item['discount_type'] === 'percentage' ? 2 : 1,
                'tax_1' => floatval($item['tax_1'] ?? 0),
                'tax_2' => floatval($item['tax_2'] ?? 0),
                'tax_1_id' => $item['tax_1_id'] ?? null,
                'tax_2_id' => $item['tax_2_id'] ?? null,
                'total' => $item_total - $item_discount,
            ];
        }

        $quote_discount = floatval($validated['discount_amount'] ?? 0);
        $discountType = $validated['discount_type'] ?? 'amount';
        if ($discountType === 'percentage') {
            $quote_discount = ($total_amount * $quote_discount) / 100;
        }

        $final_total_discount = $total_discount + $quote_discount;
        $amount_after_discount = $total_amount - $final_total_discount;

        $tax_total = 0;
        foreach ($items_data as $item) {
            $item_subtotal = $item['unit_price'] * $item['quantity'];
            $item_tax = ($item_subtotal * $item['tax_1']) / 100 + ($item_subtotal * $item['tax_2']) / 100;
            $tax_total += $item_tax;
        }

        $shipping_cost = floatval($validated['shipping_cost'] ?? 0);
        $shipping_tax = 0;
        if ($validated['tax_type'] == 1) {
            $shipping_tax = ($shipping_cost * ($validated['tax_rate'] ?? 0)) / 100;
        }

        $tax_total += $shipping_tax;
        $total_with_tax = $amount_after_discount + $tax_total + $shipping_cost;

        $quote = Quote::create([
            'id' => $currentNumber,
            'client_id' => $validated['client_id'],
            'quotes_number' => $currentNumber,
            'quote_date' => $validated['quote_date'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
            'discount_amount' => $quote_discount,
            'discount_type' => $discountType === 'percentage' ? 2 : 1,
            'shipping_cost' => $shipping_cost,
            'shipping_tax' => $shipping_tax,
            'tax_type' => $validated['tax_type'],
            'tax_rate' => $validated['tax_type'] == 1 ? ($validated['tax_rate'] ?? 0) : null,
            'subtotal' => $total_amount,
            'total_discount' => $final_total_discount,
            'tax_total' => $tax_total,
            'grand_total' => $total_with_tax,
            'status' => 1,
        ]);

        foreach ($items_data as $item) {
            $item['quotation_id'] = $quote->id;
            InvoiceItem::create($item);

            foreach (['tax_1_id', 'tax_2_id'] as $tax_key) {
                if (!empty($item[$tax_key])) {
                    $tax = TaxSitting::find($item[$tax_key]);
                    if ($tax) {
                        $tax_value = ($tax->tax / 100) * ($item['quantity'] * $item['unit_price']);
                        TaxInvoice::create([
                            'name' => $tax->name,
                            'invoice_id' => $quote->id,
                            'type' => $tax->type,
                            'rate' => $tax->tax,
                            'value' => $tax_value,
                            'type_invoice' => 'quote',
                        ]);
                    }
                }
            }
        }

        DB::commit();
        return response()->json(['status' => true, 'message' => 'تم إنشاء عرض السعر بنجاح', 'data' => $quote], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('API Quote Store Error: ' . $e->getMessage());
        return response()->json(['status' => false, 'message' => 'حدث خطأ أثناء إنشاء عرض السعر', 'error' => $e->getMessage()], 500);
    }
}

    /**
     * Show the specified resource.
     */
   

public function show($id)
{
    try {
        $quote = Quote::with(['client', 'employee', 'items.product'])->findOrFail($id);

        // جلب الضرائب المرتبطة بعرض السعر
        $taxes = TaxInvoice::where('invoice_id', $id)
                           ->where('type_invoice', 'quote')
                           ->get();

        // تحميل إعدادات الحساب (لو احتجتها في الواجهة أو منطق معين)
        $account_setting = AccountSetting::where('user_id', auth()->id())->first();

        // إرجاع الريسورس
        return response()->json([
            'status' => true,
            'message' => 'تم جلب بيانات عرض السعر بنجاح',
            'data' => (new QuoteFullResource($quote->setRelation('taxes', $taxes))),
            'account_setting' => $account_setting, // إذا تحتاجها للعرض
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء جلب عرض السعر',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('api::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    try {
        $quote = Quote::findOrFail($id);
        $quote->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف عرض السعر بنجاح'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء حذف عرض السعر',
            'error' => $e->getMessage()
        ], 500);
    }
}

}






