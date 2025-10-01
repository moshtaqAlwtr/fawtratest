<?php

namespace Modules\Pos\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PosSession;
use App\Models\StoreHouse;
use App\Models\PosSessionDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\CashierDevice;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        return view('pos.reports.index');
    }
   
    /**
 * تقرير مبيعات التصنيفات
 */
public function Category()
{
    // جلب IDs للتصنيفات التي لها مبيعات POS
    $categoryIds = DB::table('invoices')
        ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->join('products', 'invoice_items.product_id', '=', 'products.id')
        ->where('invoices.type', 'pos')
        ->distinct()
        ->pluck('products.category_id');
    
    // جلب التصنيفات بناءً على IDs المفلترة
    $categories = Category::whereIn('id', $categoryIds)
        ->orderBy('name')
        ->get(['id', 'name']);
    
    // جلب IDs للجلسات التي لها فواتير POS
    $sessionIds = DB::table('invoices')
        ->where('type', 'pos')
        ->whereNotNull('session_id')
        ->distinct()
        ->pluck('session_id');
    
    // جلب الجلسات بناءً على IDs المفلترة
    $sessions = PosSession::whereIn('id', $sessionIds)
        ->with('user:id,name')
        ->orderBy('id', 'desc')
        ->get(['id', 'session_number', 'user_id', 'started_at']);
    
    // جلب IDs للمخازن التي لها عناصر فواتير POS
    $storeHouseIds = DB::table('invoices')
        ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->where('invoices.type', 'pos')
        ->whereNotNull('invoice_items.store_house_id')
        ->distinct()
        ->pluck('invoice_items.store_house_id');
    
    // جلب المخازن بناءً على IDs المفلترة
    $storeHouses = StoreHouse::whereIn('id', $storeHouseIds)
        ->orderBy('name')
        ->get(['id', 'name']);
    
    return view('pos.reports.category_sales', compact(
        'categories', 
        'sessions', 
        'storeHouses'
    ));
}

/**
 * جلب بيانات تقرير مبيعات التصنيفات
 */
/**
 * دالة مُصححة لتقرير مبيعات التصنيفات
 */


   public function Product()
{
    // جلب التصنيفات التي لها منتجات في فواتير POS فقط
    $categoryIds = DB::table('invoices')
        ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->join('products', 'invoice_items.product_id', '=', 'products.id')
        ->where('invoices.type', 'pos')
        ->distinct()
        ->pluck('products.category_id');
    
    $categories = Category::whereIn('id', $categoryIds)
        ->orderBy('name')
        ->get(['id', 'name']);
    
    // جلب المنتجات التي لها مبيعات POS فقط
    $productIds = DB::table('invoices')
        ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->where('invoices.type', 'pos')
        ->distinct()
        ->pluck('invoice_items.product_id');
    
    $products = Product::whereIn('id', $productIds)
        ->with('category:id,name')
        ->orderBy('name')
        ->get(['id', 'name', 'barcode', 'category_id']);
    
    // جلب الجلسات التي لها فواتير POS فقط
    $sessionIds = DB::table('invoices')
        ->where('type', 'pos')
        ->whereNotNull('session_id')
        ->distinct()
        ->pluck('session_id');
    
    $sessions = PosSession::whereIn('id', $sessionIds)
        ->with('user:id,name')
        ->orderBy('id', 'desc')
        ->get(['id', 'session_number', 'user_id', 'started_at']);
    
    // جلب المخازن التي لها عناصر فواتير POS فقط
    $storeHouseIds = DB::table('invoices')
        ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->where('invoices.type', 'pos')
        ->whereNotNull('invoice_items.store_house_id')
        ->distinct()
        ->pluck('invoice_items.store_house_id');
    
    $storeHouses = StoreHouse::whereIn('id', $storeHouseIds)
        ->orderBy('name')
        ->get(['id', 'name']);
    
    return view('pos.reports.product_sales', compact(
        'categories', 
        'products',
        'sessions', 
        'storeHouses'
    ));
}
    

/**
 * عرض تقرير إحصائيات الورديات
 */
public function Shift(Request $request)
{
    
    // try {
        // جلب المرشحات من الطلب
        $filters = [
            'session_number' => $request->input('session_number'),
            'category' => $request->input('category'),
            'pos_shift' => $request->input('pos_shift'),
            'pos_shift_device' => $request->input('pos_shift_device'),
            'order_source' => $request->input('order_source'),
            'store' => $request->input('store'),
            'date_from' => $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
            'currency' => $request->input('currency', 'SAR'),
            'group_by' => $request->input('group_by'),
            'sort_by' => $request->input('sort_by', 'session_number')
        ];

        // استعلام قاعدة البيانات للحصول على بيانات الورديات
        $sessionsQuery = PosSession::with(['user', 'device', 'details'])
            ->leftJoin('users as opener', 'pos_sessions.user_id', '=', 'opener.id')
            ->leftJoin('users as closer', 'pos_sessions.user_id', '=', 'closer.id')
            ->leftJoin('cashier_devices', 'pos_sessions.device_id', '=', 'cashier_devices.id')
            ->select([
                'pos_sessions.*',
                'opener.name as opener_name',
                'closer.name as closer_name',
                'cashier_devices.device_name as device_name',
                'cashier_devices.store_id'
            ]);

        // تطبيق المرشحات
        if ($filters['date_from']) {
            $sessionsQuery->whereDate('pos_sessions.started_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $sessionsQuery->whereDate('pos_sessions.started_at', '<=', $filters['date_to']);
        }

        if ($filters['session_number']) {
            $sessionsQuery->where('pos_sessions.session_number', $filters['session_number']);
        }

        if ($filters['store']) {
            $sessionsQuery->where('cashier_devices.store_id', $filters['store']);
        }

        if ($filters['pos_shift_device']) {
            $sessionsQuery->where('pos_sessions.device_id', $filters['pos_shift_device']);
        }

        // ترتيب النتائج
        switch ($filters['sort_by']) {
            case 'session_number':
                $sessionsQuery->orderBy('pos_sessions.session_number', 'desc');
                break;
            case 'date':
                $sessionsQuery->orderBy('pos_sessions.started_at', 'desc');
                break;
            case 'sales':
                $sessionsQuery->orderBy('pos_sessions.total_sales', 'desc');
                break;
            default:
                $sessionsQuery->orderBy('pos_sessions.session_number', 'desc');
        }

        $sessions = $sessionsQuery->get();

        // حساب الإحصائيات لكل وردية
        $shiftData = $sessions->map(function ($session) {
            // جلب بيانات المبيعات والمرتجعات من الفواتير
            $salesData = Invoice::where('session_id', $session->id)
                ->where('type', 'pos')
                ->selectRaw('
                    COUNT(*) as sales_count,
                    SUM(CASE WHEN type = "pos" THEN grand_total ELSE 0 END) as total_sales,
                    SUM(CASE WHEN type = "returned" THEN grand_total ELSE 0 END) as total_returns,
                    SUM(tax_total) as total_tax,
                    SUM(discount_amount) as total_discount
                ')
                ->first();

            $returnsData = Invoice::where('session_id', $session->id)
                ->where('type', 'returned')
                ->selectRaw('
                    COUNT(*) as returns_count,
                    SUM(grand_total) as total_returns
                ')
                ->first();

            // حساب الصافي
            $totalSales = $salesData->total_sales ?? 0;
            $totalReturns = $returnsData->total_returns ?? 0;
            $netTotal = $totalSales - $totalReturns;

            // حساب المتوسط
            $salesCount = $salesData->sales_count ?? 0;
            $average = $salesCount > 0 ? $netTotal / $salesCount : 0;

            return [
                'code' => $session->session_number,
                'shift_name' => $session->shift->name ?? 'وردية..',
                'opening_time' => $session->started_at ? $session->started_at->format('d/m/Y H:i:s') : '-',
                'closing_time' => $session->ended_at ? $session->ended_at->format('d/m/Y H:i:s') : '-',
                'user_id' => $session->opener_name ?? 'غير محدد',
                'user_id' => $session->closer_name ?? 'غير محدد',
                'branch' => $session->store_id ?? 'الفرع الرئيسي',
                'sales_count' => $salesCount,
                'returns_count' => $returnsData->returns_count ?? 0,
                'net_total' => number_format($netTotal, 2),
                'average' => number_format($average, 2),
                'total_tax' => number_format($salesData->total_tax ?? 0, 2),
                'total_discount' => number_format($salesData->total_discount ?? 0, 2),
                'grand_total' => number_format($totalSales, 2),
                'status' => $session->status,
                'device_name' => $session->device_name ?? 'غير محدد'
            ];
        });

        // حساب الإجماليات العامة
        $totals = [
            'total_sessions' => $shiftData->count(),
            'total_sales_count' => $shiftData->sum('sales_count'),
            'total_returns_count' => $shiftData->sum('returns_count'),
            'total_net' => $shiftData->sum(function($item) {
                return (float)str_replace(',', '', $item['net_total']);
            }),
            'total_tax' => $shiftData->sum(function($item) {
                return (float)str_replace(',', '', $item['total_tax']);
            }),
            'total_discount' => $shiftData->sum(function($item) {
                return (float)str_replace(',', '', $item['total_discount']);
            }),
            'grand_total' => $shiftData->sum(function($item) {
                return (float)str_replace(',', '', $item['grand_total']);
            })
        ];

        // جلب بيانات إضافية للفلاتر
        $devices = CashierDevice::orderBy('device_name')->get(['id', 'device_name']);
        $stores = StoreHouse::orderBy('name')->get(['id', 'name']);
        $sessionNumbers = PosSession::distinct()->pluck('session_number')->sort();

        return view('pos.reports.shift_sales', compact(
            'shiftData',
            'totals',
            'filters',
            'devices',
            'stores',
            'sessionNumbers'
        ));

    // } catch (\Exception $e) {
    //     Log::error('خطأ في تحميل تقرير الورديات: ' . $e->getMessage());
        
    //     return view('pos.reports.shift_sales', [
    //         'shiftData' => collect([]),
    //         'totals' => [
    //             'total_sessions' => 0,
    //             'total_sales_count' => 0,
    //             'total_returns_count' => 0,
    //             'total_net' => 0,
    //             'total_tax' => 0,
    //             'total_discount' => 0,
    //             'grand_total' => 0
    //         ],
    //         'filters' => $filters ?? [],
    //         'devices' => collect([]),
    //         'stores' => collect([]),
    //         'sessionNumbers' => collect([])
    //     ])->with('error', 'حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.');
    // }
}

/**
 * تصدير تقرير الورديات إلى Excel
 */
public function exportShiftReport(Request $request)
{
    try {
        // استخدام نفس منطق الفلترة من دالة Shift
        $filters = [
            'date_from' => $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
            // باقي المرشحات...
        ];

        // جلب البيانات
        $sessionsQuery = PosSession::with(['user', 'device'])
            ->leftJoin('users as opener', 'pos_sessions.user_id', '=', 'opener.id')
            ->leftJoin('users as closer', 'pos_sessions.user_id', '=', 'closer.id')
            ->leftJoin('cashier_devices', 'pos_sessions.device_id', '=', 'cashier_devices.id');

        // تطبيق نفس المرشحات...
        if ($filters['date_from']) {
            $sessionsQuery->whereDate('pos_sessions.started_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $sessionsQuery->whereDate('pos_sessions.started_at', '<=', $filters['date_to']);
        }

        $sessions = $sessionsQuery->get();

        // تحضير البيانات للتصدير
        $exportData = $sessions->map(function ($session) {
            // نفس منطق حساب البيانات من دالة Shift
            return [
                'كود الوردية' => $session->session_number,
                'اسم الوردية' => $session->session_name ?? 'وردية..',
                'وقت الفتح' => $session->started_at ? $session->started_at->format('d/m/Y H:i:s') : '-',
                'وقت الإغلاق' => $session->ended_at ? $session->ended_at->format('d/m/Y H:i:s') : '-',
                'فتحت بواسطة' => $session->opener_name ?? 'غير محدد',
                'أغلقت بواسطة' => $session->closer_name ?? 'غير محدد',
                'الفرع' => $session->store_id ?? 'الفرع الرئيسي',
                'المبيعات' => $session->total_sales ?? 0,
                'المردود' => $session->total_returns ?? 0,
                'الصافي' => ($session->total_sales ?? 0) - ($session->total_returns ?? 0),
                'الضرائب' => $session->total_tax ?? 0,
                'الخصم' => $session->total_discount ?? 0,
                'الإجمالي (SAR)' => $session->total_sales ?? 0
            ];
        });

        // إنشاء ملف Excel وإرجاعه للتحميل
        // يمكنك استخدام مكتبة مثل Laravel Excel هنا
        
        return response()->json([
            'success' => true,
            'message' => 'تم تصدير التقرير بنجاح',
            'data' => $exportData
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في تصدير تقرير الورديات: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تصدير التقرير'
        ], 500);
    }
}

/**
 * جلب بيانات الورديات للتحديث التلقائي (AJAX)
 */
public function getShiftData(Request $request)
{
    try {
        // نفس منطق دالة Shift ولكن إرجاع JSON
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            // باقي المرشحات...
        ];

        // تنفيذ الاستعلام وإرجاع البيانات
        // ... نفس منطق دالة Shift

        return response()->json([
            'success' => true,
            'data' => $shiftData,
            'totals' => $totals
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في جلب بيانات الورديات: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب البيانات'
        ], 500);
    }
}
   

/**
 * عرض التقرير التفصيلي لحركة الورديات
 */
public function Detailed(Request $request)
{
    // try {
        // جلب المرشحات من الطلب
        $filters = [
            'session_number' => $request->input('session_number'),
            'category' => $request->input('category'),
            'pos_shift' => $request->input('pos_shift'),
            'pos_shift_device' => $request->input('pos_shift_device'),
            'order_source' => $request->input('order_source'),
            'store' => $request->input('store'),
            'date_from' => $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
            'currency' => $request->input('currency', 'SAR'),
            'group_by' => $request->input('group_by'),
            'sort_by' => $request->input('sort_by', 'session_number')
        ];

        // استعلام قاعدة البيانات للحصول على بيانات الورديات التفصيلية
        $sessionsQuery = PosSession::with(['user', 'device', 'details'])
            ->leftJoin('users as opener', 'pos_sessions.user_id', '=', 'opener.id')
            ->leftJoin('users as closer', 'pos_sessions.user_id', '=', 'closer.id')
            ->leftJoin('cashier_devices', 'pos_sessions.device_id', '=', 'cashier_devices.id')
            ->leftJoin('store_houses', 'cashier_devices.store_id', '=', 'store_houses.id')
            ->select([
                'pos_sessions.*',
                'opener.name as opener_name',
                'closer.name as closer_name',
                'cashier_devices.device_name as device_name',
                'store_houses.name as store_name',
                'store_houses.id as store_id'
            ]);

        // تطبيق المرشحات
        if ($filters['date_from']) {
            $sessionsQuery->whereDate('pos_sessions.started_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $sessionsQuery->whereDate('pos_sessions.started_at', '<=', $filters['date_to']);
        }

        if ($filters['session_number']) {
            $sessionsQuery->where('pos_sessions.session_number', $filters['session_number']);
        }

        if ($filters['store']) {
            $sessionsQuery->where('store_houses.id', $filters['store']);
        }

        if ($filters['pos_shift_device']) {
            $sessionsQuery->where('pos_sessions.device_id', $filters['pos_shift_device']);
        }

        if ($filters['pos_shift']) {
            $sessionsQuery->where('pos_sessions.status', $filters['pos_shift']);
        }

        // ترتيب النتائج
        switch ($filters['sort_by']) {
            case 'session_number':
                $sessionsQuery->orderBy('pos_sessions.session_number', 'desc');
                break;
            case 'date':
                $sessionsQuery->orderBy('pos_sessions.started_at', 'desc');
                break;
            case 'sales':
                $sessionsQuery->orderBy('pos_sessions.total_sales', 'desc');
                break;
            default:
                $sessionsQuery->orderBy('pos_sessions.session_number', 'desc');
        }

        $sessions = $sessionsQuery->get();

        // حساب البيانات التفصيلية لكل وردية
        $detailedData = $sessions->map(function ($session) {
            // جلب بيانات المبيعات والمرتجعات من الفواتير
            $salesData = Invoice::where('session_id', $session->id)
                ->where('type', 'pos')
                ->selectRaw('
                    COUNT(*) as sales_count,
                    SUM(grand_total) as total_sales,
                    SUM(tax_total) as total_tax,
                    SUM(discount_amount) as total_discount,
                    SUM(CASE WHEN JSON_EXTRACT(payment_method, "$[0].method_id") = 1 THEN grand_total ELSE 0 END) as cash_sales,
                    SUM(CASE WHEN JSON_EXTRACT(payment_method, "$[0].method_id") != 1 THEN grand_total ELSE 0 END) as non_cash_sales
                ')
                ->first();

            $returnsData = Invoice::where('session_id', $session->id)
                ->where('type', 'returned')
                ->selectRaw('
                    COUNT(*) as returns_count,
                    SUM(grand_total) as total_returns,
                    SUM(CASE WHEN JSON_EXTRACT(payment_method, "$[0].method_id") = 1 THEN grand_total ELSE 0 END) as cash_returns,
                    SUM(CASE WHEN JSON_EXTRACT(payment_method, "$[0].method_id") != 1 THEN grand_total ELSE 0 END) as non_cash_returns
                ')
                ->first();

            // جلب بيانات الدفعات من تفاصيل الجلسة
            $sessionDetails = PosSessionDetail::where('session_id', $session->id)
                ->selectRaw('
                    SUM(CASE WHEN transaction_type = "sale" THEN cash_amount ELSE 0 END) as total_cash_received,
                    SUM(CASE WHEN transaction_type = "sale" THEN card_amount ELSE 0 END) as total_card_received,
                    SUM(CASE WHEN transaction_type = "return" THEN cash_amount ELSE 0 END) as total_cash_paid,
                    SUM(CASE WHEN transaction_type = "return" THEN card_amount ELSE 0 END) as total_card_paid,
                    SUM(CASE WHEN transaction_type = "expense" THEN amount ELSE 0 END) as total_expenses,
                    SUM(CASE WHEN transaction_type = "income" THEN amount ELSE 0 END) as total_additional_income
                ')
                ->first();

            // الحسابات الأساسية
            $totalSales = $salesData->total_sales ?? 0;
            $totalReturns = $returnsData->total_returns ?? 0;
            $netSales = $totalSales - $totalReturns;
            
            // النقدي وغير النقدي
            $netCash = ($salesData->cash_sales ?? 0) - ($returnsData->cash_returns ?? 0);
            $netNonCash = ($salesData->non_cash_sales ?? 0) - ($returnsData->non_cash_returns ?? 0);
            
            // المستلمات والمدفوعات
            $totalCashReceived = $sessionDetails->total_cash_received ?? 0;
            $totalCardReceived = $sessionDetails->total_card_received ?? 0;
            $totalCashPaid = $sessionDetails->total_cash_paid ?? 0;
            $totalExpenses = $sessionDetails->total_expenses ?? 0;
            
            // الحسابات النظرية والفعلية
            $theoreticalCash = $session->opening_balance + $netCash - $totalCashPaid - $totalExpenses;
            $actualCashReceived = $session->closing_balance ?? 0;
            $actualNonCashReceived = $totalCardReceived;
            $totalActualReceived = $actualCashReceived + $actualNonCashReceived;
            
            // الفرق
            $difference = $totalActualReceived - $netSales;
            
            // المبالغ الآجلة (من الفواتير غير المدفوعة)
            $creditAmount = Invoice::where('session_id', $session->id)
                ->where('type', 'pos')
                ->where('payment_status', '!=', 1)
                ->sum('due_value') ?? 0;

            return [
                'code' => $session->session_number,
                'shift_name' => $session->session_name ?? 'Main POS Shift',
                'opening_time' => $session->started_at ? $session->started_at->format('d/m/Y H:i') : '-',
                'closing_time' => $session->ended_at ? $session->ended_at->format('d/m/Y H:i') : '-',
                'treasury_employee' => $session->opener_name ?? 'OWNER',
                'confirmed_by' => $session->closer_name ?? 'OWNER',
                'branch' => $session->store_name ?? 'Main Branch',
                'total_sales' => number_format($totalSales, 2),
                'total_returns' => number_format($totalReturns, 2),
                'net_sales' => number_format($netSales, 2),
                'net_cash' => number_format($netCash, 2),
                'net_non_cash' => number_format($netNonCash, 2),
                'total_cash_received' => number_format($totalCashReceived, 2),
                'credit_amount' => number_format($creditAmount, 2),
                'total_cash_collection' => number_format($totalCashReceived, 2),
                'total_cash_paid' => number_format($totalCashPaid + $totalExpenses, 2),
                'theoretical_total' => number_format($theoreticalCash, 2),
                'actual_cash_received' => number_format($actualCashReceived, 2),
                'actual_non_cash_received' => number_format($actualNonCashReceived, 2),
                'total_actual_received' => number_format($totalActualReceived, 2),
                'difference' => number_format($difference, 2),
                'status' => $session->status,
                'opening_balance' => $session->opening_balance ?? 0,
                'closing_balance' => $session->closing_balance ?? 0,
                
                // القيم الخام للحسابات
                'raw_total_sales' => $totalSales,
                'raw_total_returns' => $totalReturns,
                'raw_net_sales' => $netSales,
                'raw_net_cash' => $netCash,
                'raw_net_non_cash' => $netNonCash,
                'raw_total_cash_received' => $totalCashReceived,
                'raw_credit_amount' => $creditAmount,
                'raw_total_cash_paid' => $totalCashPaid + $totalExpenses,
                'raw_theoretical_total' => $theoreticalCash,
                'raw_actual_cash_received' => $actualCashReceived,
                'raw_actual_non_cash_received' => $actualNonCashReceived,
                'raw_total_actual_received' => $totalActualReceived,
                'raw_difference' => $difference
            ];
        });

        // حساب الإجماليات العامة
        $totals = [
            'total_sessions' => $detailedData->count(),
            'total_sales' => $detailedData->sum('raw_total_sales'),
            'total_returns' => $detailedData->sum('raw_total_returns'),
            'net_sales' => $detailedData->sum('raw_net_sales'),
            'net_cash' => $detailedData->sum('raw_net_cash'),
            'net_non_cash' => $detailedData->sum('raw_net_non_cash'),
            'total_cash_received' => $detailedData->sum('raw_total_cash_received'),
            'credit_amount' => $detailedData->sum('raw_credit_amount'),
            'total_cash_paid' => $detailedData->sum('raw_total_cash_paid'),
            'theoretical_total' => $detailedData->sum('raw_theoretical_total'),
            'actual_cash_received' => $detailedData->sum('raw_actual_cash_received'),
            'actual_non_cash_received' => $detailedData->sum('raw_actual_non_cash_received'),
            'total_actual_received' => $detailedData->sum('raw_total_actual_received'),
            'difference' => $detailedData->sum('raw_difference')
        ];

        // جلب بيانات إضافية للفلاتر
        $devices = CashierDevice::with('store')->orderBy('device_name')->get(['id', 'device_name', 'store_id']);
        $stores = StoreHouse::orderBy('name')->get(['id', 'name']);
        $sessionNumbers = PosSession::distinct()->pluck('session_number')->sort();

        return view('pos.reports.detailed_shift_transactions', compact(
            'detailedData',
            'totals',
            'filters',
            'devices',
            'stores',
            'sessionNumbers'
        ));

    // } catch (\Exception $e) {
    //     Log::error('خطأ في تحميل التقرير التفصيلي للورديات: ' . $e->getMessage());
        
    //     return view('pos.reports.detailed_shift_transactions', [
    //         'detailedData' => collect([]),
    //         'totals' => [
    //             'total_sessions' => 0,
    //             'total_sales' => 0,
    //             'total_returns' => 0,
    //             'net_sales' => 0,
    //             'net_cash' => 0,
    //             'net_non_cash' => 0,
    //             'total_cash_received' => 0,
    //             'credit_amount' => 0,
    //             'total_cash_paid' => 0,
    //             'theoretical_total' => 0,
    //             'actual_cash_received' => 0,
    //             'actual_non_cash_received' => 0,
    //             'total_actual_received' => 0,
    //             'difference' => 0
    //         ],
    //         'filters' => $filters ?? [],
    //         'devices' => collect([]),
    //         'stores' => collect([]),
    //         'sessionNumbers' => collect([])
    //     ])->with('error', 'حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.');
    // }
}

/**
 * تصدير التقرير التفصيلي للورديات إلى Excel
 */
public function exportDetailedShiftReport(Request $request)
{
    try {
        // استخدام نفس منطق الفلترة من دالة Detailed
        $filters = [
            'date_from' => $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
            // باقي المرشحات...
        ];

        // جلب البيانات باستخدام نفس المنطق
        // ... (نسخ كود الاستعلام من دالة Detailed)

        // تحضير البيانات للتصدير
        $exportData = $detailedData->map(function ($shift) {
            return [
                'الكود' => $shift['code'],
                'الوردية' => $shift['shift_name'],
                'وقت الفتح' => $shift['opening_time'],
                'وقت الإغلاق' => $shift['closing_time'],
                'موظف الخزينة' => $shift['treasury_employee'],
                'مؤكدة بواسطة' => $shift['confirmed_by'],
                'الفرع' => $shift['branch'],
                'المبيعات' => $shift['total_sales'],
                'المردود' => $shift['total_returns'],
                'الصافي' => $shift['net_sales'],
                'صافي نقدي' => $shift['net_cash'],
                'صافي غير نقدي' => $shift['net_non_cash'],
                'إجمالي استلام نقدي' => $shift['total_cash_received'],
                'الآجل' => $shift['credit_amount'],
                'إجمالي استلام نقدي' => $shift['total_cash_collection'],
                'إجمالي صرف نقدي' => $shift['total_cash_paid'],
                'إجمالي نظري' => $shift['theoretical_total'],
                'إجمالي المستلم نقدي' => $shift['actual_cash_received'],
                'إجمالي المستلم غير نقدي' => $shift['actual_non_cash_received'],
                'إجمالي المستلم' => $shift['total_actual_received'],
                'الفرق' => $shift['difference']
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'تم تصدير التقرير التفصيلي بنجاح',
            'data' => $exportData
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في تصدير التقرير التفصيلي للورديات: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تصدير التقرير'
        ], 500);
    }
}

/**
 * جلب بيانات التقرير التفصيلي للتحديث التلقائي (AJAX)
 */
public function getDetailedShiftData(Request $request)
{
    try {
        // نفس منطق دالة Detailed ولكن إرجاع JSON
        // ... (تنفيذ الاستعلام وإرجاع البيانات)

        return response()->json([
            'success' => true,
            'data' => $detailedData,
            'totals' => $totals
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في جلب البيانات التفصيلية للورديات: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب البيانات'
        ], 500);
    }
}
    public function Prof()
    {
        return view('pos_Reports.Shift_Profitability');
    }
    public function Cate()
    {
        return view('pos_Reports.Category_Profitability');
    }
    public function Prod()
    {
        return view('pos_Reports.Product_Profitability');
    }
    public function quickTest()
{
    // اختبار 1: عدد فواتير POS
    $posInvoicesCount = Invoice::where('type', 'pos')->count();
    echo "عدد فواتير POS: {$posInvoicesCount}<br>";

    // اختبار 2: عينة من فواتير POS
    $sampleInvoice = Invoice::where('type', 'pos')->first();
    if ($sampleInvoice) {
        echo "عينة فاتورة POS: ID={$sampleInvoice->id}, Code={$sampleInvoice->code}<br>";
    } else {
        echo "لا توجد فواتير POS<br>";
    }

    // اختبار 3: عناصر الفواتير
    $invoiceItemsCount = InvoiceItem::whereHas('invoice', function($q) {
        $q->where('type', 'pos');
    })->count();
    echo "عدد عناصر فواتير POS: {$invoiceItemsCount}<br>";

    // اختبار 4: المنتجات والتصنيفات
    $productsWithCategories = Product::whereNotNull('category_id')->count();
    echo "عدد المنتجات المرتبطة بتصنيفات: {$productsWithCategories}<br>";

    // اختبار 5: البيانات المدمجة
    try {
        $joinedCount = DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('invoices.type', 'pos')
            ->count();
        echo "عدد السجلات المدمجة: {$joinedCount}<br>";
    } catch (\Exception $e) {
        echo "خطأ في الاستعلام المدمج: {$e->getMessage()}<br>";
    }

    // اختبار 6: استعلام بسيط للنتائج
    try {
        $simpleResults = DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('invoices.type', 'pos')
            ->select('categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('categories.name')
            ->get();

        echo "نتائج بسيطة: <pre>" . print_r($simpleResults->toArray(), true) . "</pre>";
    } catch (\Exception $e) {
        echo "خطأ في النتائج البسيطة: {$e->getMessage()}<br>";
    }

    echo "<hr>";
    echo "اختبار انتهى";
}

}

