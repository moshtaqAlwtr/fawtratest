<?php

namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Employee;
use App\Models\SupplyOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SupplyOrdersReportController extends Controller
{
    public function index()
    {
        return view('reports::supply.index');
    }

    public function supplyOrdersReport()
    {
        // جلب البيانات الأساسية للفلاتر
        $clients = Client::all();
        $employees = Employee::all();
        $statuses = [
            'pending' => 'في الانتظار',
            'approved' => 'معتمد',
            'in_progress' => 'قيد التنفيذ',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            'completed' => 'مكتمل',
        ];

        $currencies = ['SAR', 'USD', 'EUR', 'AED'];
        $tags = SupplyOrder::distinct('tag')->whereNotNull('tag')->pluck('tag');

        return view('reports::supply.rport_supply', compact('clients', 'employees', 'statuses', 'currencies', 'tags'));
    }

    /**
     * جلب بيانات تقرير أوامر التوريد عبر AJAX
     */
    public function supplyOrdersReportAjax(Request $request)
    {
        try {
            // بناء الاستعلام الأساسي
            $query = SupplyOrder::query()
                ->with(['client', 'employee'])
                ->select('supply_orders.*');

            // تطبيق الفلاتر
            $this->applySupplyOrderFilters($query, $request);

            // تطبيق الترتيب
            $this->applySorting($query, $request);

            // جلب البيانات
            $supplyOrders = $query->get();

            // معالجة البيانات للعرض
            $processedOrders = $this->processSupplyOrderData($supplyOrders, $request);

            // حساب الإجماليات
            $totals = $this->calculateSupplyOrderTotals($processedOrders);

            // إعداد بيانات الرسم البياني
            $chartData = $this->prepareChartData($processedOrders, $request);

            return response()->json([
                'success' => true,
                'orders' => $processedOrders,
                'totals' => $totals,
                'chart_data' => $chartData,
                'group_by' => $this->getGroupByLabel($request->group_by),
                'message' => 'تم تحميل البيانات بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * تطبيق فلاتر أوامر التوريد
     */
    private function applySupplyOrderFilters($query, Request $request)
    {
        // فلتر العميل
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // فلتر الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // فلتر الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلتر العملة
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        // فلتر العلامة
        if ($request->filled('tag')) {
            $query->where('tag', $request->tag);
        }

        // فلتر التاريخ من
        if ($request->filled('start_date_from')) {
            $query->where('start_date', '>=', $request->start_date_from);
        }

        // فلتر التاريخ إلى
        if ($request->filled('start_date_to')) {
            $query->where('start_date', '<=', $request->start_date_to);
        }

        // فلتر تاريخ الانتهاء من
        if ($request->filled('end_date_from')) {
            $query->where('end_date', '>=', $request->end_date_from);
        }

        // فلتر تاريخ الانتهاء إلى
        if ($request->filled('end_date_to')) {
            $query->where('end_date', '<=', $request->end_date_to);
        }

        // فلتر الميزانية من
        if ($request->filled('budget_from')) {
            $query->where('budget', '>=', $request->budget_from);
        }

        // فلتر الميزانية إلى
        if ($request->filled('budget_to')) {
            $query->where('budget', '<=', $request->budget_to);
        }

        // البحث النصي
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // إظهار الموظفين فقط
        if ($request->boolean('show_employee_only')) {
            $query->where('show_employee', true);
        }

        // فلتر الأوامر المنتهية الصلاحية
        if ($request->boolean('show_expired_only')) {
            $query->where('end_date', '<', now());
        }

        // فلتر الأوامر النشطة فقط
        if ($request->boolean('show_active_only')) {
            $query->whereIn('status', ['pending', 'approved', 'in_progress', 'shipped']);
        }
    }

    /**
     * تطبيق الترتيب
     */
    private function applySorting($query, Request $request)
    {
        switch ($request->sort_by) {
            case 'budget_asc':
                $query->orderBy('budget', 'asc');
                break;
            case 'budget_desc':
                $query->orderBy('budget', 'desc');
                break;
            case 'start_date_asc':
                $query->orderBy('start_date', 'asc');
                break;
            case 'start_date_desc':
                $query->orderBy('start_date', 'desc');
                break;
            case 'end_date_asc':
                $query->orderBy('end_date', 'asc');
                break;
            case 'end_date_desc':
                $query->orderBy('end_date', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'order_number_asc':
                $query->orderBy('order_number', 'asc');
                break;
            case 'order_number_desc':
                $query->orderBy('order_number', 'desc');
                break;
            case 'created_at_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * معالجة بيانات أوامر التوريد للعرض
     */
    private function processSupplyOrderData($supplyOrders, Request $request)
    {
        $processedData = [];

        foreach ($supplyOrders as $order) {
            // إعداد بيانات الأمر
            $orderData = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'name' => $order->name,
                'client' => $order->client ? $order->client->trade_name : null,
                'employee' => $order->employee ? $order->employee->full_name : null,
                'status' => $order->status,
                'status_label' => $this->getStatusLabel($order->status),
                'start_date' => $order->start_date ? $order->start_date->format('d/m/Y') : null,
                'end_date' => $order->end_date ? $order->end_date->format('d/m/Y') : null,
                'budget' => $order->budget,
                'currency' => $order->currency,
                'tag' => $order->tag,
                'tracking_number' => $order->tracking_number,
                'description' => $order->description,
                'shipping_address' => $order->shipping_address,
                'show_employee' => $order->show_employee,
                'days_remaining' => $this->calculateDaysRemaining($order),
                'is_expired' => $this->isOrderExpired($order),
                'is_urgent' => $this->isOrderUrgent($order),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ];

            // تطبيق التجميع إذا كان مطلوباً
            if ($request->filled('group_by')) {
                $this->groupOrderData($processedData, $orderData, $request->group_by);
            } else {
                $processedData[] = $orderData;
            }
        }

        return $processedData;
    }

    /**
     * تجميع بيانات أوامر التوريد
     */
    private function groupOrderData(&$processedData, $orderData, $groupBy)
    {
        $groupKey = '';

        switch ($groupBy) {
            case 'client':
                $groupKey = $orderData['client'] ?? 'غير محدد';
                break;
            case 'employee':
                $groupKey = $orderData['employee'] ?? 'غير محدد';
                break;
            case 'status':
                $groupKey = $orderData['status_label'] ?? 'غير محدد';
                break;
            case 'currency':
                $groupKey = $orderData['currency'] ?? 'غير محدد';
                break;
            case 'tag':
                $groupKey = $orderData['tag'] ?? 'غير محدد';
                break;
            case 'month':
                $groupKey = $orderData['created_at'] ? Carbon::parse($orderData['created_at'])->format('Y-m') : 'غير محدد';
                break;
            default:
                $groupKey = 'عام';
        }

        if (!isset($processedData[$groupKey])) {
            $processedData[$groupKey] = [
                'group_name' => $groupKey,
                'items' => [],
                'total_budget' => 0,
                'total_orders' => 0,
                'avg_budget' => 0,
            ];
        }

        $processedData[$groupKey]['items'][] = $orderData;
        $processedData[$groupKey]['total_budget'] += $orderData['budget'] ?? 0;
        $processedData[$groupKey]['total_orders']++;
        $processedData[$groupKey]['avg_budget'] = $processedData[$groupKey]['total_orders'] > 0 ? $processedData[$groupKey]['total_budget'] / $processedData[$groupKey]['total_orders'] : 0;
    }

    /**
     * حساب الإجماليات
     */
    private function calculateSupplyOrderTotals($orders)
    {
        $totals = [
            'total_orders' => 0,
            'total_budget' => 0,
            'avg_budget' => 0,
            'total_clients' => 0,
            'total_employees' => 0,
            'status_counts' => [],
            'expired_orders' => 0,
            'urgent_orders' => 0,
        ];

        $clients = [];
        $employees = [];
        $statusCounts = [];

        foreach ($orders as $order) {
            if (isset($order['group_name'])) {
                // للبيانات المجمعة
                $totals['total_orders'] += $order['total_orders'];
                $totals['total_budget'] += $order['total_budget'];

                foreach ($order['items'] as $item) {
                    if ($item['client']) {
                        $clients[$item['client']] = true;
                    }
                    if ($item['employee']) {
                        $employees[$item['employee']] = true;
                    }

                    $status = $item['status'];
                    $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;

                    if ($item['is_expired']) {
                        $totals['expired_orders']++;
                    }
                    if ($item['is_urgent']) {
                        $totals['urgent_orders']++;
                    }
                }
            } else {
                // للبيانات غير المجمعة
                $totals['total_orders']++;
                $totals['total_budget'] += $order['budget'] ?? 0;

                if ($order['client']) {
                    $clients[$order['client']] = true;
                }
                if ($order['employee']) {
                    $employees[$order['employee']] = true;
                }

                $status = $order['status'];
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;

                if ($order['is_expired']) {
                    $totals['expired_orders']++;
                }
                if ($order['is_urgent']) {
                    $totals['urgent_orders']++;
                }
            }
        }

        $totals['total_clients'] = count($clients);
        $totals['total_employees'] = count($employees);
        $totals['avg_budget'] = $totals['total_orders'] > 0 ? $totals['total_budget'] / $totals['total_orders'] : 0;
        $totals['status_counts'] = $statusCounts;

        return $totals;
    }

    /**
     * إعداد بيانات الرسم البياني
     */
    private function prepareChartData($orders, Request $request)
    {
        $chartData = [
            'labels' => [],
            'budgets' => [],
            'counts' => [],
        ];

        // تحديد نوع الرسم البياني حسب التجميع
        if ($request->group_by === 'status') {
            $statusData = [];

            foreach ($orders as $order) {
                if (isset($order['group_name'])) {
                    $statusData[$order['group_name']] = $order['total_orders'];
                }
            }

            foreach ($statusData as $status => $count) {
                $chartData['labels'][] = $status;
                $chartData['counts'][] = $count;
            }
        } else {
            // رسم بياني للميزانيات (أعلى 10)
            $budgetData = [];

            foreach ($orders as $order) {
                if (isset($order['group_name'])) {
                    $budgetData[] = [
                        'name' => $order['group_name'],
                        'budget' => $order['total_budget'],
                    ];
                } else {
                    $budgetData[] = [
                        'name' => $order['name'] ?? $order['order_number'],
                        'budget' => $order['budget'] ?? 0,
                    ];
                }
            }

            // ترتيب حسب الميزانية (تنازلي) وأخذ أعلى 10
            usort($budgetData, function ($a, $b) {
                return $b['budget'] <=> $a['budget'];
            });

            $budgetData = array_slice($budgetData, 0, 10);

            foreach ($budgetData as $item) {
                $chartData['labels'][] = $item['name'];
                $chartData['budgets'][] = $item['budget'];
            }
        }

        return $chartData;
    }

    /**
     * الحصول على تسمية الحالة
     */
    private function getStatusLabel($status)
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'approved' => 'معتمد',
            'in_progress' => 'قيد التنفيذ',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            'completed' => 'مكتمل',
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * حساب الأيام المتبقية
     */
    private function calculateDaysRemaining($order)
    {
        if (!$order->end_date) {
            return null;
        }

        $endDate = Carbon::parse($order->end_date);
        $today = Carbon::now();

        return $today->diffInDays($endDate, false);
    }

    /**
     * تحديد إذا كان الأمر منتهي الصلاحية
     */
    private function isOrderExpired($order)
    {
        if (!$order->end_date) {
            return false;
        }

        return Carbon::parse($order->end_date)->isPast();
    }

    /**
     * تحديد إذا كان الأمر عاجل
     */
    private function isOrderUrgent($order)
    {
        $daysRemaining = $this->calculateDaysRemaining($order);

        return $daysRemaining !== null && $daysRemaining >= 0 && $daysRemaining <= 7;
    }

    /**
     * الحصول على تسمية التجميع
     */
    private function getGroupByLabel($groupBy)
    {
        $labels = [
            'client' => 'العميل',
            'employee' => 'الموظف',
            'status' => 'الحالة',
            'currency' => 'العملة',
            'tag' => 'العلامة',
            'month' => 'الشهر',
        ];

        return $labels[$groupBy] ?? '';
    }
}
