<?php

namespace Modules\Stock\Http\Controllers\Manufacturing;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndirectCostsRequest;
use App\Models\Account;
use App\Models\IndirectCost;
use App\Models\Log as ModelsLog;
use App\Models\IndirectCostItem;
use App\Models\ManufacturOrders;
use App\Models\JournalEntry;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndirectCostsController extends Controller
{
    public function index()
    {
        // جلب البيانات الأساسية للفلاتر
        $accounts = Account::select('id', 'name', 'code')->orderBy('name')->get();
        $products = Product::select('id', 'name')->orderBy('name')->get();
        $manufacturing_orders = ManufacturOrders::select('id', 'name')->orderBy('name')->get();

        return view('stock::manufacturing.indirectCosts.index', compact(
            'accounts',
            'products',
            'manufacturing_orders'
        ));
    }

    /**
     * جلب البيانات مع الفلترة عبر Ajax
     */
    public function getData(Request $request)
    {
        $query = IndirectCost::with(['account', 'indirectCostItems.manufacturingOrder', 'indirectCostItems.journalEntry']);

        // فلترة حسب الحساب
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // فلترة حسب المنتج (من خلال أوامر التصنيع)
        if ($request->filled('product_id')) {
            $query->whereHas('indirectCostItems.manufacturingOrder', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // فلترة حسب أمر التصنيع
        if ($request->filled('manufacturing_order_id')) {
            $query->whereHas('indirectCostItems', function($q) use ($request) {
                $q->where('manufacturing_order_id', $request->manufacturing_order_id);
            });
        }

        // فلترة حسب تاريخ الإنشاء (from_date)
        if ($request->filled('date_from_start')) {
            $query->where('from_date', '>=', $request->date_from_start);
        }

        if ($request->filled('date_from_end')) {
            $query->where('from_date', '<=', $request->date_from_end);
        }

        // فلترة حسب تاريخ الإنتهاء (to_date)
        if ($request->filled('date_to_start')) {
            $query->where('to_date', '>=', $request->date_to_start);
        }

        if ($request->filled('date_to_end')) {
            $query->where('to_date', '<=', $request->date_to_end);
        }

        // فلترة حسب النوع
        if ($request->filled('based_on')) {
            $query->where('based_on', $request->based_on);
        }

        // فلترة حسب المجموع
        if ($request->filled('total_min')) {
            $query->where('total', '>=', $request->total_min);
        }

        if ($request->filled('total_max')) {
            $query->where('total', '<=', $request->total_max);
        }

        // ترتيب النتائج
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // تطبيق التصفح
        $perPage = $request->get('per_page', 10);
        $indirectCosts = $query->paginate($perPage);

        // إرجاع البيانات كـ JSON
        return response()->json([
            'success' => true,
            'data' => $indirectCosts->items(),
            'pagination' => [
                'current_page' => $indirectCosts->currentPage(),
                'last_page' => $indirectCosts->lastPage(),
                'per_page' => $indirectCosts->perPage(),
                'total' => $indirectCosts->total(),
                'from' => $indirectCosts->firstItem(),
                'to' => $indirectCosts->lastItem(),
            ],
            'summary' => [
                'total_records' => $indirectCosts->total(),
                'total_amount' => $query->sum('total'),
                'avg_amount' => $query->avg('total'),
            ]
        ]);
    }

    /**
     * إحصائيات سريعة للداشبورد
     */
    public function getStats()
    {
        $stats = [
            'total_costs' => IndirectCost::count(),
            'total_amount' => IndirectCost::sum('total'),
            'avg_amount' => IndirectCost::avg('total'),
            'this_month' => IndirectCost::whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->sum('total'),
            'last_month' => IndirectCost::whereMonth('created_at', now()->subMonth()->month)
                                      ->whereYear('created_at', now()->subMonth()->year)
                                      ->sum('total'),
        ];

        return response()->json(['success' => true, 'stats' => $stats]);
    }

    /**
     * تصدير البيانات
     */
    public function export(Request $request)
    {
        // نفس منطق الفلترة من getData
        $query = IndirectCost::with(['account', 'indirectCostItems.manufacturingOrder', 'indirectCostItems.journalEntry']);

        // تطبيق نفس الفلاتر...
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        // ... باقي الفلاتر

        $indirectCosts = $query->get();

        // يمكن استخدام مكتبة مثل Laravel Excel للتصدير
        return response()->json([
            'success' => true,
            'message' => 'سيتم تصدير ' . $indirectCosts->count() . ' سجل',
            'download_url' => route('manufacturing.indirectcosts.downloadExport', ['filters' => $request->all()])
        ]);
    }

    // باقي الدوال الموجودة...
    public function create()
    {
        $accounts = Account::select('id', 'name')->get();
        $manufacturing_orders = ManufacturOrders::select('id', 'name')->get();
        $restrictions = collect();

        return view('stock::manufacturing.indirectCosts.create', compact(
            'accounts',
            'manufacturing_orders',
            'restrictions'
        ));
    }

    public function getRestrictionsByDate(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date'
        ]);

        $restrictions = JournalEntry::select('id', 'reference_number', 'description', 'date')
            ->where('status', 1)
            ->whereBetween('date', [$request->from_date, $request->to_date])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'restrictions' => $restrictions->map(function($restriction) {
                return [
                    'id' => $restriction->id,
                    'reference_number' => $restriction->reference_number,
                    'description' => $restriction->description,
                    'date' => $restriction->date->format('Y-m-d'),
                    'display_text' => $restriction->reference_number . ' - ' . \Str::limit($restriction->description, 50) . ' (' . $restriction->date->format('Y-m-d') . ')'
                ];
            })
        ]);
    }

    public function store(IndirectCostsRequest $request)
    {
        DB::beginTransaction();
        try {
            $indirectCost = IndirectCost::create([
                'account_id' => $request->account_id,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'based_on' => $request->based_on,
                'total' => $request->total
            ]);

            if ($request->has('restriction_id') && is_array($request->restriction_id)) {
                foreach ($request->restriction_id as $index => $restriction_id) {
                    if (empty($restriction_id) && empty($request->manufacturing_order_id[$index])) {
                        continue;
                    }

                    IndirectCostItem::create([
                        'indirect_costs_id' => $indirectCost->id,
                        'restriction_id' => !empty($restriction_id) ? $restriction_id : null,
                        'restriction_total' => $request->restriction_total[$index] ?? 0,
                        'manufacturing_order_id' => !empty($request->manufacturing_order_id[$index]) ? $request->manufacturing_order_id[$index] : null,
                        'manufacturing_price' => $request->manufacturing_price[$index] ?? 0,
                    ]);
                }
            }

            ModelsLog::create([
                'type' => 'indirect_cost',
                'type_id' => $indirectCost->id,
                'type_log' => 'create',
                'description' => 'تم اضافة تكاليف غير مباشرة في التصنيع',
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->route('manufacturing.indirectcosts.index')
                ->with(['success' => 'تمت إضافة التكاليف غير المباشرة بنجاح.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $indirectCost = IndirectCost::with('indirectCostItems')->findOrFail($id);
        $accounts = Account::select('id', 'name')->get();
        $manufacturing_orders = ManufacturOrders::select('id', 'name')->get();

        $restrictions = JournalEntry::select('id', 'reference_number', 'description', 'date')
            ->where('status', 1)
            ->whereBetween('date', [$indirectCost->from_date, $indirectCost->to_date])
            ->orderBy('date', 'desc')
            ->get();

        return view('stock::manufacturing.indirectCosts.edit', compact(
            'indirectCost',
            'accounts',
            'manufacturing_orders',
            'restrictions'
        ));
    }

    public function update(IndirectCostsRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $indirectCost = IndirectCost::findOrFail($id);

            $indirectCost->update([
                'account_id' => $request->account_id,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'based_on' => $request->based_on,
                'total' => $request->total
            ]);

            $indirectCost->indirectCostItems()->delete();

            if ($request->has('restriction_id') && is_array($request->restriction_id)) {
                foreach ($request->restriction_id as $index => $restriction_id) {
                    if (empty($restriction_id) && empty($request->manufacturing_order_id[$index])) {
                        continue;
                    }

                    IndirectCostItem::create([
                        'indirect_costs_id' => $indirectCost->id,
                        'restriction_id' => !empty($restriction_id) ? $restriction_id : null,
                        'restriction_total' => $request->restriction_total[$index] ?? 0,
                        'manufacturing_order_id' => !empty($request->manufacturing_order_id[$index]) ? $request->manufacturing_order_id[$index] : null,
                        'manufacturing_price' => $request->manufacturing_price[$index] ?? 0,
                    ]);
                }
            }

            ModelsLog::create([
                'type' => 'indirect_cost',
                'type_id' => $indirectCost->id,
                'type_log' => 'update',
                'description' => 'تم تحديث تكاليف غير مباشرة في التصنيع',
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->route('manufacturing.indirectcosts.index')
                ->with(['success' => 'تم التحديث بنجاح.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['error' => 'حدث خطاء في التحديث: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $indirectCost = IndirectCost::with(['indirectCostItems.journalEntry', 'indirectCostItems.manufacturingOrder'])
            ->findOrFail($id);
                            $logs = ModelsLog::where('type', 'indirect_cost')
            ->where('type_id', $id)
            ->whereHas('indirect_cost') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('stock::manufacturing.indirectCosts.show', compact('indirectCost','logs'));
    }

    public function destroy($id)
    {
        $indirectCost = IndirectCost::findOrFail($id);
        $indirectCost->indirectCostItems()->delete();
        $indirectCost->delete();

        ModelsLog::create([
            'type' => 'indirect_cost',
            'type_id' => $id,
            'type_log' => 'delete',
            'description' => 'تم حذف تكاليف غير مباشرة في التصنيع',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('manufacturing.indirectcosts.index')
            ->with(['success' => 'تم حذف التكاليف بنجاح.']);
    }
}