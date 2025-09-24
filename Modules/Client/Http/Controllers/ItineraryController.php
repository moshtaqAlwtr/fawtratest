<?php

namespace Modules\Client\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Region_groub;
use App\Models\Client;
use App\Models\EmployeeClientVisit;
use App\Models\Log;
use App\Models\Neighborhood;
use Illuminate\Support\Facades\DB;

class ItineraryController extends Controller
{
    /**
     * Display the itinerary planning interface.
     */
    public function create()
    {
        $user = auth()->user();
        $employees = collect();
        $groups = collect();

        if ($user->role === 'employee') {
            // إذا كان المستخدم موظفًا، اعرض بياناته فقط
            $employees = User::where('id', $user->id)->get();
            $groups = $user->regionGroups()->get(); // جلب المجموعات الخاصة به
        } else {
            // إذا كان المستخدم مديرًا أو أي دور آخر، اسمح له باختيار الموظف
            $employees = User::where('role', 'employee')->get();
        }

        return view('client::Itinerary.create', compact('employees', 'groups'));
    }

    /**
     * Store the weekly itinerary for an employee.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'employee_id' => 'required|exists:users,id',
        'visits' => 'required|array',
        'year' => 'required|integer|min:2020|max:2030',
        'week_number' => 'required|integer|min:1|max:53',
        'visits.*' => 'nullable|array',
        'visits.*.*' => 'nullable|integer|exists:clients,id',
    ]);

    DB::beginTransaction();
    try {
        $employeeId = $validated['employee_id'];
        $year = $validated['year'];
        $weekNumber = $validated['week_number'];

        // تحويل البيانات المتداخلة إلى مصفوفة مسطحة
        $visitData = [];
        foreach ($validated['visits'] as $day => $clientIds) {
            if (!is_array($clientIds)) continue;

            foreach (array_filter($clientIds, 'is_numeric') as $clientId) {
                $visitData[] = [
                    'employee_id' => $employeeId,
                    'client_id' => $clientId,
                    'day_of_week' => strtolower($day),
                    'year' => $year,
                    'week_number' => $weekNumber,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (empty($visitData)) {
            throw new \Exception('لا توجد زيارات صالحة للحفظ');
        }

        // إدراج البيانات دون حذف القديمة
        EmployeeClientVisit::insert($visitData);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'تم الحفظ بنجاح',
            'inserted_count' => count($visitData)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage(),
            'error_details' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
}
    public function edit($employeeId)
    {
        $employee = User::findOrFail($employeeId);

        // Get the week and year from the request, or default to the current week and year.
        $currentWeek = request('week', now()->weekOfYear);
        $currentYear = request('year', now()->year);

        // Get the itinerary data for this employee and week
        $itinerary = EmployeeClientVisit::with([
            'client' => function ($query) {
                $query->select('id', 'trade_name', 'code', 'city');
            },
        ])
            ->where('employee_id', $employeeId)
            ->where('year', $currentYear)
            ->where('week_number', $currentWeek)
            ->get()
            ->groupBy('day_of_week');

        // تحويل البيانات لتكون متاحة في الـ view
        $visitsByDay = [];
        $days = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($days as $day) {
            $visitsByDay[$day] = $itinerary->get($day, collect());
        }

        // We just need to pass the context. The view will fetch data via AJAX.
        $groups = $employee->regionGroups()->get();

        return view('client::Itinerary.edit', [
            'employee' => $employee,
            'groups' => $groups,
            'currentWeek' => (int) $currentWeek,
            'currentYear' => (int) $currentYear,
            'itinerary' => $itinerary,
            'visitsByDay' => $visitsByDay,
        ]);
    }

    public function update(Request $request, $employeeId)
    {
        $request->validate([
            'visits' => 'required|array',
            'year' => 'required|integer',
            'week_number' => 'required|integer',
            'visits.*' => 'nullable|array',
            'visits.*.*' => 'nullable|integer|exists:clients,id',
        ]);

        $year = $request->input('year');
        $weekNumber = $request->input('week_number');
        $visitsByDay = $request->input('visits');

        DB::beginTransaction();
        try {
            // جلب جميع الزيارات الحالية لهذا الأسبوع (للموظف والسنة ورقم الأسبوع)
            $existingVisits = EmployeeClientVisit::where('employee_id', $employeeId)->where('year', $year)->where('week_number', $weekNumber)->get()->groupBy('day_of_week');

            foreach ($visitsByDay as $day => $clientIds) {
                if (is_array($clientIds)) {
                    // تحويل المصفوفة إلى أرقام فقط (للتأكد من عدم وجود قيم فارغة)
                    $clientIds = array_filter($clientIds, 'is_numeric');
                    $day = strtolower($day);

                    // جلب الزيارات الحالية لهذا اليوم
                    $currentDayVisits = $existingVisits[$day] ?? collect();
                    $currentClientIds = $currentDayVisits->pluck('client_id')->toArray();

                    // تحديد العملاء الجدد الذين يجب إضافتهم لهذا اليوم
                    $newClientIds = array_diff($clientIds, $currentClientIds);

                    // إضافة العملاء الجدد لهذا اليوم (دون التحقق من وجودهم في أيام أخرى)
                    foreach ($newClientIds as $clientId) {
                        EmployeeClientVisit::create([
                            'employee_id' => $employeeId,
                            'client_id' => $clientId,
                            'day_of_week' => $day,
                            'year' => $year,
                            'week_number' => $weekNumber,
                        ]);
                    }

                    // تحديد العملاء الذين يجب حذفهم (الذين تم إزالتهم من الواجهة)
                    $removedClientIds = array_diff($currentClientIds, $clientIds);
                    if (!empty($removedClientIds)) {
                        EmployeeClientVisit::where('employee_id', $employeeId)->where('year', $year)->where('week_number', $weekNumber)->where('day_of_week', $day)->whereIn('client_id', $removedClientIds)->delete();
                    }

                    // تحديث العملاء المتبقين (الموجودين في كلا المصفوفتين)
                    $remainingClientIds = array_intersect($clientIds, $currentClientIds);
                    foreach ($remainingClientIds as $clientId) {
                        EmployeeClientVisit::where('employee_id', $employeeId)
                            ->where('client_id', $clientId)
                            ->where('year', $year)
                            ->where('week_number', $weekNumber)
                            ->where('day_of_week', $day)
                            ->update([
                                'updated_at' => now(),
                            ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'تم تحديث خط السير بنجاح.']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Itinerary Update Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تحديث البيانات.'], 500);
        }
    }
    // دالة جديدة لجلب البيانات المحفوظة للأسبوع المحدد
    public function getWeekItinerary($employeeId)
    {
        $year = request('year', now()->year);
        $week = request('week', now()->weekOfYear);

        $itinerary = EmployeeClientVisit::with([
            'client' => function ($query) {
                $query->select('id', 'trade_name', 'code', 'city')->with([
                    'visits' => function ($q) {
                        $q->latest()->limit(1);
                    },
                    'invoices' => function ($q) {
                        $q->latest()->limit(1);
                    },
                    'appointmentNotes' => function ($q) {
                        $q->latest()->limit(1);
                    },
                ]);
            },
        ])
            ->where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('week_number', $week)
            ->get();

        return response()->json($itinerary);
    }

    public function destroyVisit($visitId)
    {
        try {
            $visit = EmployeeClientVisit::findOrFail($visitId);
            $visit->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الزيارة بنجاح',
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في حذف الزيارة: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function getItineraryForWeek(Request $request, $employeeId)
    {
        $request->validate([
            'year' => 'required|integer',
            'week' => 'required|integer',
        ]);

        $year = $request->query('year');
        $week = $request->query('week');

        $itinerary = EmployeeClientVisit::with([
            'client' => function ($query) {
                $query->with([
                    'visits' => function ($q) {
                        $q->latest()->limit(1);
                    },
                    'invoices' => function ($q) {
                        $q->latest()->limit(1);
                    },
                    'appointmentNotes' => function ($q) {
                        $q->latest()->limit(1);
                    },
                ]);
            },
        ])
            ->where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('week_number', $week)
            ->get();

        return response()->json($itinerary);
    }

    public function getGroupsForEmployee(User $employee)
    {
        // The 'regionGroups' relationship on the User model fetches the groups.
        $groups = $employee->regionGroups()->get();
        return response()->json($groups);
    }

    public function getClientsForGroup($id)
    {
        $clients = Client::with([
            'visits' => function ($query) {
                $query->latest()->limit(1);
            },
            'invoices' => function ($query) {
                $query->latest()->limit(1);
            },
            'appointmentNotes' => function ($query) {
                $query->latest()->limit(1);
            },
            'account.receipts' => function ($query) {
                $query->latest()->limit(1);
            },
        ])
            ->whereHas('neighborhood', function ($query) use ($id) {
                $query->where('region_id', $id); // ✅ هذا هو التعديل الصحيح
            })
            ->get(['id', 'trade_name', 'code', 'city']);

        return response()->json($clients);
    }

    public function listAll()
    {
        $itineraries = EmployeeClientVisit::with(['employee', 'client'])
            ->orderBy('year', 'desc')
            ->orderBy('week_number', 'desc')
            ->orderBy('employee_id')
            ->get()
            ->groupBy('employee_id')
            ->map(function ($employeeVisits) {
                // الحصول على أحدث ثلاثة أسابيع (بعد ترتيبهم مسبقًا)
                $latestWeeks = $employeeVisits
                    ->map(function ($visit) {
                        return $visit->year . '-W' . str_pad($visit->week_number, 2, '0', STR_PAD_LEFT);
                    })
                    ->unique()
                    ->take(4); // أحدث ثلاثة أسابيع فقط

                // تصفية الزيارات التي تنتمي لهذه الأسابيع الثلاثة
                $latestWeekVisits = $employeeVisits->filter(function ($visit) use ($latestWeeks) {
                    $visitKey = $visit->year . '-W' . str_pad($visit->week_number, 2, '0', STR_PAD_LEFT);
                    return $latestWeeks->contains($visitKey);
                });

                // تجميع الزيارات حسب الأسبوع > اليوم
                $weeksGrouped = $latestWeekVisits
                    ->groupBy(function ($visit) {
                        return $visit->year . '-W' . str_pad($visit->week_number, 2, '0', STR_PAD_LEFT);
                    })
                    ->map(function ($visits) {
                        return $this->groupVisitsByDay($visits);
                    });

                return [
                    'employee' => $employeeVisits->first()->employee,
                    'weeks' => $weeksGrouped,
                ];
            });

        $newClientsTodayCount = Client::whereDate('created_at', today())->count();

        return view('client::Itinerary.list', compact('itineraries', 'newClientsTodayCount'));
    }

    // دالة مساعدة لتجميع الزيارات حسب اليوم
    private function groupVisitsByDay($weekVisits)
    {
        // A mapping from day name (as stored in the DB) to Carbon day constants
        $dayConstantMapping = [
            'saturday' => \Carbon\Carbon::SATURDAY,
            'sunday' => \Carbon\Carbon::SUNDAY,
            'monday' => \Carbon\Carbon::MONDAY,
            'tuesday' => \Carbon\Carbon::TUESDAY,
            'wednesday' => \Carbon\Carbon::WEDNESDAY,
            'thursday' => \Carbon\Carbon::THURSDAY,
            'friday' => \Carbon\Carbon::FRIDAY,
        ];

        // Initialize the days structure
        $days = [];
        foreach (array_keys($dayConstantMapping) as $dayName) {
            $days[$dayName] = ['visits' => [], 'new_clients_count' => 0];
        }

        if ($weekVisits->isEmpty()) {
            return $days;
        }

        // Get year and week number from the first visit
        $firstVisit = $weekVisits->first();
        $year = $firstVisit->year;
        $week = $firstVisit->week_number;

        // Calculate the start of the week (Saturday)
        $startOfWeekDate = \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek(\Carbon\Carbon::SATURDAY);

        // Group visits by day of the week
        foreach ($weekVisits->groupBy('day_of_week') as $dayName => $dayVisits) {
            $dayName = strtolower($dayName);
            if (array_key_exists($dayName, $dayConstantMapping)) {
                // Calculate the actual date of this day
                $dayOffset = array_search($dayName, array_keys($dayConstantMapping));
                $visitDate = $startOfWeekDate->copy()->addDays($dayOffset)->startOfDay();

                $uniqueVisits = $dayVisits->unique('client_id');

                // Add a flag to each visit's client object to indicate if they are new for that specific visit date
                $uniqueVisits->each(function ($visit) use ($visitDate) {
                    if ($visit->client) {
                        $visit->client->is_new_for_visit_date = $visit->client->created_at->isSameDay($visitDate);
                    }
                });

                // Count new clients for this specific day
                $newClientsCount = $uniqueVisits
                    ->filter(function ($visit) {
                        return $visit->client && $visit->client->is_new_for_visit_date;
                    })
                    ->count();

                $days[$dayName] = [
                    'visits' => $uniqueVisits->values()->all(),
                    'new_clients_count' => $newClientsCount,
                ];
            }
        }

        return $days;
    }
}
