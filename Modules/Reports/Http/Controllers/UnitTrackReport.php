<?php

namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Employee;
use App\Models\SupplyOrder;
use App\Models\Unit;
use App\Models\UnitType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UnitTrackReport extends Controller
{
    public function index()
    {
        return view('reports::unitTrack.index');
    }
public function unitsReport()
{
    // جلب البيانات الأساسية للفلاتر
    $unitTypes = UnitType::where('status', 'active')->get();

    $statuses = [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'maintenance' => 'قيد الصيانة',
    ];

    $priorities = [
        'high' => 'عالية',
        'medium' => 'متوسطة',
        'low' => 'منخفضة',
    ];

    return view('reports::unitTrack.unit_track', compact('unitTypes', 'statuses', 'priorities'));
}

/**
 * جلب بيانات تقرير تتبع الوحدات عبر AJAX
 */
public function unitsReportAjax(Request $request)
{
    try {
        // بناء الاستعلام الأساسي
        $query = Unit::query()
            ->with(['unitType', 'unitType.pricingRule'])
            ->select('units.*');

        // تطبيق الفلاتر
        $this->applyUnitsFilters($query, $request);

        // تطبيق الترتيب
        $this->applyUnitsSorting($query, $request);

        // جلب البيانات
        $units = $query->get();

        // معالجة البيانات للعرض
        $processedUnits = $this->processUnitsData($units, $request);

        // حساب الإجماليات
        $totals = $this->calculateUnitsTotals($processedUnits);

        // إعداد بيانات الرسم البياني
        $chartData = $this->prepareUnitsChartData($processedUnits, $request);

        return response()->json([
            'success' => true,
            'units' => $processedUnits,
            'totals' => $totals,
            'chart_data' => $chartData,
            'group_by' => $this->getUnitsGroupByLabel($request->group_by),
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
 * تطبيق فلاتر الوحدات
 */
private function applyUnitsFilters($query, Request $request)
{
    // فلتر نوع الوحدة
    if ($request->filled('unit_type_id')) {
        $query->where('unit_type_id', $request->unit_type_id);
    }

    // فلتر الحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // فلتر الأولوية
    if ($request->filled('priority')) {
        $query->where('priority', $request->priority);
    }

    // البحث النصي
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // فلتر الوحدات النشطة فقط
    if ($request->boolean('show_active_only')) {
        $query->where('status', 'active');
    }
}

/**
 * تطبيق الترتيب
 */
private function applyUnitsSorting($query, Request $request)
{
    switch ($request->sort_by) {
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        case 'priority_desc':
            $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
            break;
        case 'priority_asc':
            $query->orderByRaw("FIELD(priority, 'low', 'medium', 'high')");
            break;
        case 'created_at_desc':
            $query->orderBy('created_at', 'desc');
            break;
        case 'created_at_asc':
            $query->orderBy('created_at', 'asc');
            break;
        default:
            $query->orderBy('created_at', 'desc');
    }
}

/**
 * معالجة بيانات الوحدات للعرض
 */
private function processUnitsData($units, Request $request)
{
    $processedData = [];

    foreach ($units as $unit) {
        // إعداد بيانات الوحدة
        $unitData = [
            'id' => $unit->id,
            'name' => $unit->name,
            'unit_type' => $unit->unitType ? $unit->unitType->name : null,
            'unit_type_id' => $unit->unit_type_id,
            'status' => $unit->status,
            'status_label' => $this->getUnitsStatusLabel($unit->status),
            'priority' => $unit->priority,
            'priority_label' => $this->getUnitsPriorityLabel($unit->priority),
            'description' => $unit->description,
            'created_at' => $unit->created_at ? $unit->created_at->format('d/m/Y') : null,
            'updated_at' => $unit->updated_at ? $unit->updated_at->format('d/m/Y') : null,
            'pricing_rule' => $unit->unitType && $unit->unitType->pricingRule ? $unit->unitType->pricingRule->name : null,
            'check_in_time' => $unit->unitType ? $unit->unitType->check_in_time : null,
            'check_out_time' => $unit->unitType ? $unit->unitType->check_out_time : null,
            'tax1' => $unit->unitType ? $unit->unitType->tax1 : null,
            'tax2' => $unit->unitType ? $unit->unitType->tax2 : null,
        ];

        // تطبيق التجميع إذا كان مطلوباً
        if ($request->filled('group_by')) {
            $this->groupUnitsData($processedData, $unitData, $request->group_by);
        } else {
            $processedData[] = $unitData;
        }
    }

    return $processedData;
}

/**
 * تجميع بيانات الوحدات
 */
private function groupUnitsData(&$processedData, $unitData, $groupBy)
{
    $groupKey = '';

    switch ($groupBy) {
        case 'unit_type':
            $groupKey = $unitData['unit_type'] ?? 'غير محدد';
            break;
        case 'status':
            $groupKey = $unitData['status_label'] ?? 'غير محدد';
            break;
        case 'priority':
            $groupKey = $unitData['priority_label'] ?? 'غير محدد';
            break;
        default:
            $groupKey = 'عام';
    }

    if (!isset($processedData[$groupKey])) {
        $processedData[$groupKey] = [
            'group_name' => $groupKey,
            'items' => [],
            'total_units' => 0,
            'active_units' => 0,
            'inactive_units' => 0,
            'maintenance_units' => 0,
            'high_priority_units' => 0,
            'medium_priority_units' => 0,
            'low_priority_units' => 0,
        ];
    }

    $processedData[$groupKey]['items'][] = $unitData;
    $processedData[$groupKey]['total_units']++;

    // حساب إحصائيات المجموعة
    switch ($unitData['status']) {
        case 'active':
            $processedData[$groupKey]['active_units']++;
            break;
        case 'inactive':
            $processedData[$groupKey]['inactive_units']++;
            break;
        case 'maintenance':
            $processedData[$groupKey]['maintenance_units']++;
            break;
    }

    switch ($unitData['priority']) {
        case 'high':
            $processedData[$groupKey]['high_priority_units']++;
            break;
        case 'medium':
            $processedData[$groupKey]['medium_priority_units']++;
            break;
        case 'low':
            $processedData[$groupKey]['low_priority_units']++;
            break;
    }
}

/**
 * حساب الإجماليات
 */
private function calculateUnitsTotals($units)
{
    $totals = [
        'total_units' => 0,
        'active_units' => 0,
        'inactive_units' => 0,
        'maintenance_units' => 0,
        'high_priority_units' => 0,
        'medium_priority_units' => 0,
        'low_priority_units' => 0,
        'total_unit_types' => 0,
        'status_counts' => [],
        'priority_counts' => [],
        'unit_type_counts' => [],
    ];

    $unitTypes = [];
    $statusCounts = [];
    $priorityCounts = [];
    $unitTypeCounts = [];

    foreach ($units as $unit) {
        if (isset($unit['group_name'])) {
            // للبيانات المجمعة
            $totals['total_units'] += $unit['total_units'];
            $totals['active_units'] += $unit['active_units'];
            $totals['inactive_units'] += $unit['inactive_units'];
            $totals['maintenance_units'] += $unit['maintenance_units'];
            $totals['high_priority_units'] += $unit['high_priority_units'];
            $totals['medium_priority_units'] += $unit['medium_priority_units'];
            $totals['low_priority_units'] += $unit['low_priority_units'];

            foreach ($unit['items'] as $item) {
                if ($item['unit_type']) {
                    $unitTypes[$item['unit_type']] = true;
                    $unitTypeCounts[$item['unit_type']] = ($unitTypeCounts[$item['unit_type']] ?? 0) + 1;
                }

                $status = $item['status'];
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;

                $priority = $item['priority'];
                $priorityCounts[$priority] = ($priorityCounts[$priority] ?? 0) + 1;
            }
        } else {
            // للبيانات غير المجمعة
            $totals['total_units']++;

            switch ($unit['status']) {
                case 'active':
                    $totals['active_units']++;
                    break;
                case 'inactive':
                    $totals['inactive_units']++;
                    break;
                case 'maintenance':
                    $totals['maintenance_units']++;
                    break;
            }

            switch ($unit['priority']) {
                case 'high':
                    $totals['high_priority_units']++;
                    break;
                case 'medium':
                    $totals['medium_priority_units']++;
                    break;
                case 'low':
                    $totals['low_priority_units']++;
                    break;
            }

            if ($unit['unit_type']) {
                $unitTypes[$unit['unit_type']] = true;
                $unitTypeCounts[$unit['unit_type']] = ($unitTypeCounts[$unit['unit_type']] ?? 0) + 1;
            }

            $status = $unit['status'];
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;

            $priority = $unit['priority'];
            $priorityCounts[$priority] = ($priorityCounts[$priority] ?? 0) + 1;
        }
    }

    $totals['total_unit_types'] = count($unitTypes);
    $totals['status_counts'] = $statusCounts;
    $totals['priority_counts'] = $priorityCounts;
    $totals['unit_type_counts'] = $unitTypeCounts;

    return $totals;
}

/**
 * إعداد بيانات الرسم البياني
 */
private function prepareUnitsChartData($units, Request $request)
{
    $chartData = [
        'labels' => [],
        'values' => [],
    ];

    // تحديد نوع الرسم البياني حسب التجميع
    if ($request->group_by === 'status') {
        $statusData = [];

        foreach ($units as $unit) {
            if (isset($unit['group_name'])) {
                $statusData[$unit['group_name']] = $unit['total_units'];
            }
        }

        foreach ($statusData as $status => $count) {
            $chartData['labels'][] = $status;
            $chartData['values'][] = $count;
        }
    } elseif ($request->group_by === 'priority') {
        $priorityData = [];

        foreach ($units as $unit) {
            if (isset($unit['group_name'])) {
                $priorityData[$unit['group_name']] = $unit['total_units'];
            }
        }

        foreach ($priorityData as $priority => $count) {
            $chartData['labels'][] = $priority;
            $chartData['values'][] = $count;
        }
    } elseif ($request->group_by === 'unit_type') {
        $unitTypeData = [];

        foreach ($units as $unit) {
            if (isset($unit['group_name'])) {
                $unitTypeData[$unit['group_name']] = $unit['total_units'];
            }
        }

        foreach ($unitTypeData as $unitType => $count) {
            $chartData['labels'][] = $unitType;
            $chartData['values'][] = $count;
        }
    } else {
        // رسم بياني افتراضي للحالات
        $statusCounts = [];
        foreach ($units as $unit) {
            $status = isset($unit['status_label']) ? $unit['status_label'] : 'غير محدد';
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }

        foreach ($statusCounts as $status => $count) {
            $chartData['labels'][] = $status;
            $chartData['values'][] = $count;
        }
    }

    return $chartData;
}

/**
 * الحصول على تسمية الحالة
 */
private function getUnitsStatusLabel($status)
{
    $statuses = [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'maintenance' => 'قيد الصيانة',
    ];

    return $statuses[$status] ?? $status;
}

/**
 * الحصول على تسمية الأولوية
 */
private function getUnitsPriorityLabel($priority)
{
    $priorities = [
        'high' => 'عالية',
        'medium' => 'متوسطة',
        'low' => 'منخفضة',
    ];

    return $priorities[$priority] ?? $priority;
}

/**
 * الحصول على تسمية التجميع
 */
private function getUnitsGroupByLabel($groupBy)
{
    $labels = [
        'unit_type' => 'نوع الوحدة',
        'status' => 'الحالة',
        'priority' => 'الأولوية',
    ];

    return $labels[$groupBy] ?? '';
}

/**
 * تحديث حالة الوحدة
 */
public function updateUnitStatus(Request $request, $unitId)
{
    try {
        $request->validate([
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        $unit = Unit::findOrFail($unitId);
        $unit->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الوحدة بنجاح',
            'unit' => $unit
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحديث حالة الوحدة: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * تحديث أولوية الوحدة
 */
public function updateUnitPriority(Request $request, $unitId)
{
    try {
        $request->validate([
            'priority' => 'required|in:high,medium,low'
        ]);

        $unit = Unit::findOrFail($unitId);
        $unit->update([
            'priority' => $request->priority
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث أولوية الوحدة بنجاح',
            'unit' => $unit
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحديث أولوية الوحدة: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * الحصول على تفاصيل الوحدة
 */
public function getUnitDetails($unitId)
{
    try {
        $unit = Unit::with(['unitType', 'unitType.pricingRule'])->findOrFail($unitId);

        $unitDetails = [
            'id' => $unit->id,
            'name' => $unit->name,
            'unit_type' => $unit->unitType ? [
                'id' => $unit->unitType->id,
                'name' => $unit->unitType->name,
                'status' => $unit->unitType->status,
                'check_in_time' => $unit->unitType->check_in_time,
                'check_out_time' => $unit->unitType->check_out_time,
                'tax1' => $unit->unitType->tax1,
                'tax2' => $unit->unitType->tax2,
                'description' => $unit->unitType->description,
                'pricing_rule' => $unit->unitType->pricingRule ? [
                    'id' => $unit->unitType->pricingRule->id,
                    'name' => $unit->unitType->pricingRule->name,
                ] : null
            ] : null,
            'priority' => $unit->priority,
            'priority_label' => $this->getUnitsPriorityLabel($unit->priority),
            'status' => $unit->status,
            'status_label' => $this->getUnitsStatusLabel($unit->status),
            'description' => $unit->description,
            'created_at' => $unit->created_at->format('d/m/Y H:i'),
            'updated_at' => $unit->updated_at->format('d/m/Y H:i'),
        ];

        return response()->json([
            'success' => true,
            'unit' => $unitDetails
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في جلب تفاصيل الوحدة: ' . $e->getMessage()
        ], 500);
    }
}

}