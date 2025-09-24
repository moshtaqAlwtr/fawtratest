<?php

namespace Modules\Client\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Client\Entities\Client;
use App\Models\User;
use App\Models\Employee;
use App\Models\CategoriesClient;
use App\Models\Statuses;
use App\Models\Neighborhood;
use App\Models\Region_groub;
use App\Models\Branch;
use App\Models\Location;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\PaymentsProcess;
use App\Models\Receipt;
use App\Models\Target;
use App\Models\EmployeeClientVisit;
use Carbon\Carbon;

class ClientApiController extends Controller
{
    /**
     * Get clients data with pagination via AJAX
     */
    public function getClients(Request $request)
    {
        $user = auth()->user();
        $baseQuery = Client::with(['employee', 'status:id,name,color', 'locations', 'neighborhood.region', 'branch:id,name', 'account', 'categoriesClient']);
        $noClients = false;

        // حسابات التاريخ والأسبوع
        $currentDate = now();
        $currentDayOfWeek = $currentDate->dayOfWeek;
        $adjustedDayOfWeek = ($currentDayOfWeek + 1) % 7;
        $englishDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $currentDayNameEn = $englishDays[$adjustedDayOfWeek];

        $startOfYear = now()->copy()->startOfYear();
        $startOfYearDay = ($startOfYear->dayOfWeek + 1) % 7;
        $daysSinceStart = $startOfYear->diffInDays($currentDate);
        $currentWeek = (int) ceil(($daysSinceStart + $startOfYearDay + 1) / 7);
        $currentYear = now()->year;

        // فلترة حسب دور المستخدم
        if ($user->role === 'employee') {
            $clientVisits = EmployeeClientVisit::with('client')
                ->where('employee_id', $user->id)
                ->where('day_of_week', $currentDayNameEn)
                ->where('year', $currentYear)
                ->where('week_number', $currentWeek)
                ->get();

            if ($clientVisits->isNotEmpty()) {
                $clientIds = $clientVisits->pluck('client_id');
                $baseQuery->whereIn('id', $clientIds);
            } else {
                $noClients = true;
            }
        } elseif ($user->branch_id) {
            $mainBranchName = Branch::where('is_main', true)->value('name');
            $currentBranchName = Branch::find($user->branch_id)->name;

            if ($currentBranchName !== $mainBranchName) {
                $baseQuery->where('branch_id', $user->branch_id);
            }
        }

        // تطبيق الفلاتر
        $this->applyFilters($baseQuery, $request);

        if ($noClients) {
            return response()->json([
                'clients' => [],
                'pagination' => null,
                'total' => 0
            ]);
        }

        // حساب المسافات
        $userLocation = Location::where('employee_id', $user->id)->latest()->first();
        $clientDistances = [];

        // جلب العملاء مع الترقيم
        $perPage = $request->get('perPage', 50);
        $clients = $baseQuery->paginate($perPage);

        // حساب المسافات لكل عميل
        foreach ($clients as $client) {
            $clientLocation = Location::where('client_id', $client->id)->latest()->first();

            if ($userLocation && $clientLocation) {
                $distanceKm = $this->calculateDistance(
                    $userLocation->latitude,
                    $userLocation->longitude,
                    $clientLocation->latitude,
                    $clientLocation->longitude
                );

                $clientDistances[$client->id] = [
                    'distance' => $distanceKm,
                    'message' => 'تم الحساب بنجاح',
                    'within_range' => $distanceKm !== null && $distanceKm <= 0.3,
                ];
            } else {
                $clientDistances[$client->id] = [
                    'distance' => null,
                    'message' => $userLocation ? 'موقع العميل غير معروف' : 'موقعك غير معروف',
                    'within_range' => false,
                ];
            }
        }

        // ترتيب العملاء حسب المسافة
        $sortedClients = $clients->getCollection()->sortBy(function ($client) use ($clientDistances) {
            return $clientDistances[$client->id]['distance'] ?? INF;
        })->values();

        $clients->setCollection($sortedClients);

        // إرجاع البيانات
        return response()->json([
            'clients' => $clients->items(),
            'pagination' => [
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
                'per_page' => $clients->perPage(),
                'total' => $clients->total(),
                'from' => $clients->firstItem(),
                'to' => $clients->lastItem(),
                'has_more_pages' => $clients->hasMorePages(),
                'prev_page_url' => $clients->previousPageUrl(),
                'next_page_url' => $clients->nextPageUrl(),
            ],
            'client_distances' => $clientDistances,
            'total' => $clients->total()
        ]);
    }

    /**
     * Get map data for clients
     */
    public function getMapData(Request $request)
    {
        $user = auth()->user();
        $baseQuery = Client::with(['status:id,name,color', 'locations:id,client_id,latitude,longitude', 'branch:id,name']);

        // تطبيق نفس فلاتر الدور
        if ($user->role === 'employee') {
            $currentDate = now();
            $currentDayOfWeek = $currentDate->dayOfWeek;
            $adjustedDayOfWeek = ($currentDayOfWeek + 1) % 7;
            $englishDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $currentDayNameEn = $englishDays[$adjustedDayOfWeek];

            $startOfYear = now()->copy()->startOfYear();
            $startOfYearDay = ($startOfYear->dayOfWeek + 1) % 7;
            $daysSinceStart = $startOfYear->diffInDays($currentDate);
            $currentWeek = (int) ceil(($daysSinceStart + $startOfYearDay + 1) / 7);
            $currentYear = now()->year;

            $clientVisits = EmployeeClientVisit::where('employee_id', $user->id)
                ->where('day_of_week', $currentDayNameEn)
                ->where('year', $currentYear)
                ->where('week_number', $currentWeek)
                ->get();

            if ($clientVisits->isNotEmpty()) {
                $clientIds = $clientVisits->pluck('client_id');
                $baseQuery->whereIn('id', $clientIds);
            } else {
                return response()->json(['clients' => []]);
            }
        } elseif ($user->branch_id) {
            $mainBranchName = Branch::where('is_main', true)->value('name');
            $currentBranchName = Branch::find($user->branch_id)->name;

            if ($currentBranchName !== $mainBranchName) {
                $baseQuery->where('branch_id', $user->branch_id);
            }
        }

        // تطبيق الفلاتر
        $this->applyFilters($baseQuery, $request);

        // جلب العملاء مع المواقع فقط
        $clients = $baseQuery->whereHas('locations', function($q) {
            $q->whereNotNull('latitude')->whereNotNull('longitude');
        })->get();

        $mapData = [];
        $userLocation = Location::where('employee_id', $user->id)->latest()->first();

        foreach ($clients as $client) {
            if ($client->locations && $client->locations->latitude && $client->locations->longitude) {
                $distance = null;
                if ($userLocation) {
                    $distance = $this->calculateDistance(
                        $userLocation->latitude,
                        $userLocation->longitude,
                        $client->locations->latitude,
                        $client->locations->longitude
                    );
                }

                $mapData[] = [
                    'id' => $client->id,
                    'lat' => (float) $client->locations->latitude,
                    'lng' => (float) $client->locations->longitude,
                    'trade_name' => $client->trade_name,
                    'code' => $client->code,
                    'phone' => $client->phone,
                    'address' => $client->locations->address ?? '',
                    'status' => $client->status->name ?? 'غير محدد',
                    'statusColor' => $client->status->color ?? '#4CAF50',
                    'branch' => $client->branch->name ?? '',
                    'distance' => $distance ? round($distance, 2) : null,
                    'balance' => $client->account->balance ?? 0
                ];
            }
        }

        return response()->json([
            'clients' => $mapData,
            'user_location' => $userLocation ? [
                'lat' => (float) $userLocation->latitude,
                'lng' => (float) $userLocation->longitude
            ] : null
        ]);
    }

    /**
     * Get client financial data
     */
    public function getClientFinancialData(Request $request)
    {
        $clientIds = $request->get('client_ids', []);

        if (empty($clientIds)) {
            return response()->json(['clients_data' => []]);
        }

        $currentYear = now()->year;
        $monthlyTarget = 648;

        // جلب البيانات المالية
        $returnedInvoiceIds = Invoice::whereNotNull('reference_number')->pluck('reference_number')->toArray();
        $excludedInvoiceIds = array_unique(array_merge($returnedInvoiceIds, Invoice::where('type', 'returned')->pluck('id')->toArray()));

        $invoices = Invoice::whereIn('client_id', $clientIds)
            ->where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds)
            ->get();

        $invoiceIdsByClient = $invoices->groupBy('client_id')->map->pluck('id');
        $payments = PaymentsProcess::whereIn('invoice_id', $invoices->pluck('id'))
            ->whereYear('created_at', $currentYear)
            ->get()
            ->groupBy('invoice_id');

        $receipts = Receipt::with('account')
            ->whereHas('account', fn($q) => $q->whereIn('client_id', $clientIds))
            ->whereYear('created_at', $currentYear)
            ->get()
            ->groupBy(fn($receipt) => $receipt->account->client_id);

        $months = [
            'يناير' => 1, 'فبراير' => 2, 'مارس' => 3, 'أبريل' => 4,
            'مايو' => 5, 'يونيو' => 6, 'يوليو' => 7, 'أغسطس' => 8,
            'سبتمبر' => 9, 'أكتوبر' => 10, 'نوفمبر' => 11, 'ديسمبر' => 12
        ];

        $getClassification = function ($percentage, $collected = 0) {
            if ($collected == 0) {
                return ['group' => 'D', 'class' => 'secondary'];
            }
            if ($percentage > 100) {
                return ['group' => 'A++', 'class' => 'primary'];
            }
            if ($percentage >= 60) {
                return ['group' => 'A', 'class' => 'success'];
            }
            if ($percentage >= 30) {
                return ['group' => 'B', 'class' => 'warning'];
            }
            return ['group' => 'C', 'class' => 'danger'];
        };

        $clientsData = [];

        foreach ($clientIds as $clientId) {
            $invoiceIds = $invoiceIdsByClient[$clientId] ?? collect();

            $clientData = [
                'id' => $clientId,
                'monthly' => [],
                'invoices_count' => $invoiceIds->count(),
                'payments_count' => $invoiceIds->sum(fn($id) => isset($payments[$id]) ? $payments[$id]->count() : 0),
                'receipts_count' => isset($receipts[$clientId]) ? $receipts[$clientId]->count() : 0,
                'total_collected' => 0,
            ];

            $totalYearlyCollected = 0;

            foreach ($months as $monthName => $monthNumber) {
                $paymentsTotal = 0;
                if ($invoiceIds->isNotEmpty()) {
                    foreach ($invoiceIds as $invoiceId) {
                        if (isset($payments[$invoiceId])) {
                            $paymentsTotal += $payments[$invoiceId]->filter(function($payment) use ($currentYear, $monthNumber) {
                                return Carbon::parse($payment->created_at)->year == $currentYear &&
                                       Carbon::parse($payment->created_at)->month == $monthNumber;
                            })->sum('amount');
                        }
                    }
                }

                $receiptsTotal = isset($receipts[$clientId]) ?
                    $receipts[$clientId]->filter(function($receipt) use ($currentYear, $monthNumber) {
                        return Carbon::parse($receipt->created_at)->year == $currentYear &&
                               Carbon::parse($receipt->created_at)->month == $monthNumber;
                    })->sum('amount') : 0;

                $monthlyCollected = $paymentsTotal + $receiptsTotal;
                $totalYearlyCollected += $monthlyCollected;

                $percentage = $monthlyTarget > 0 ? round(($monthlyCollected / $monthlyTarget) * 100, 2) : 0;
                $classification = $getClassification($percentage, $monthlyCollected);

                $clientData['monthly'][$monthName] = [
                    'collected' => $monthlyCollected,
                    'payments_total' => $paymentsTotal,
                    'receipts_total' => $receiptsTotal,
                    'target' => $monthlyTarget,
                    'percentage' => $percentage,
                    'group' => $classification['group'],
                    'group_class' => $classification['class'],
                    'month_number' => $monthNumber,
                ];
            }

            $clientData['total_collected'] = $totalYearlyCollected;
            $clientsData[$clientId] = $clientData;
        }

        return response()->json(['clients_data' => $clientsData]);
    }

    /**
     * Get filter options for dropdowns
     */
    public function getFilterOptions()
    {
        $user = auth()->user();

        return response()->json([
            'statuses' => Statuses::select('id', 'name', 'color')->get(),
            'categories' => CategoriesClient::select('id', 'name')->get(),
            'employees' => Employee::select('id', 'full_name')->get(),
            'users' => User::select('id', 'name')->get(),
            'neighborhoods' => Neighborhood::select('id', 'name')->get(),
            'region_groups' => $user->role === 'employee' ?
                $user->regionGroups()->select('id', 'name')->get() :
                Region_groub::select('id', 'name')->get(),
        ]);
    }

    /**
     * Apply filters to the base query
     */
    private function applyFilters($baseQuery, $request)
    {
        if ($request->filled('client')) {
            $baseQuery->where('id', $request->client);
        }

        if ($request->filled('name')) {
            $baseQuery->where('trade_name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('status')) {
            $baseQuery->where('status_id', $request->status);
        }

        if ($request->filled('region')) {
            $baseQuery->whereHas('Neighborhoodname.Region', function ($q) use ($request) {
                $q->where('id', $request->region);
            });
        }

        if ($request->filled('neighborhood')) {
            $baseQuery->whereHas('Neighborhoodname', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->neighborhood . '%')
                  ->orWhere('id', $request->neighborhood);
            });
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $baseQuery->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_from')) {
            $baseQuery->where('created_at', '>=', $request->date_from . ' 00:00:00');
        } elseif ($request->filled('date_to')) {
            $baseQuery->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('categories')) {
            $baseQuery->where('category_id', $request->categories);
        }

        if ($request->filled('user')) {
            $baseQuery->where('created_by', $request->user);
        }

        if ($request->filled('type')) {
            $baseQuery->where('type', $request->type);
        }

        if ($request->filled('employee')) {
            $baseQuery->where('employee_id', $request->employee);
        }
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometers = $miles * 1.609344;

        return round($kilometers, 2);
    }
}
