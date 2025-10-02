<?php

namespace Modules\Client\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientRelation;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\notifications;
use App\Models\PaymentsProcess;
use App\Models\Receipt;
use App\Models\Region_groub;
use App\Models\Statuses;
use App\Models\User;
use App\Models\Branch;
use App\Models\Visit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use TCPDF;


class VisitController extends Controller
{
    /**
     * Display the itinerary planner page.
     *
     * @return \Illuminate\View\View
     */
    public function showItineraryPlanner()
    {
        // Fetch users who are sales representatives based on their role.
        $employees = User::where('role', 'employee')->get();
        return view('client.Itinerary.index', compact('employees'));
    }

    /**
     * Get the data for the itinerary planner.
     * Fetches available clients and planned visits for a given employee and date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItineraryData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employeeId = $request->input('employee_id');
        $visitDate = $request->input('visit_date');

        $employee = User::with('regionGroups.clients')->find($employeeId);
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $allClients = $employee->regionGroups->flatMap(fn($group) => $group->clients)->unique('id');
        $plannedClientIds = Visit::where('employee_id', $employeeId)->whereDate('visit_date', $visitDate)->pluck('client_id');
        $availableClients = $allClients->whereNotIn('id', $plannedClientIds);

        $plannedVisits = Visit::with('client')->whereIn('client_id', $plannedClientIds)->orderBy('visit_order', 'asc')->get();

        return response()->json([
            'available_clients' => array_values($availableClients->all()),
            'planned_visits' => $plannedVisits,
        ]);
    }

    /**
     * Save the itinerary plan, ordered by proximity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveItinerary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'clients' => 'present|array',
            'clients.*' => 'exists:clients,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employeeId = $request->input('employee_id');
        $visitDate = $request->input('visit_date');
        $clientIds = $request->input('clients');

        DB::beginTransaction();
        try {
            // Clear existing plan for the day
            Visit::where('employee_id', $employeeId)->whereDate('visit_date', $visitDate)->delete();

            if (empty($clientIds)) {
                DB::commit();
                return response()->json(['message' => 'تم مسح الخطة بنجاح.']);
            }

            $employee = User::with('branch')->find($employeeId);
            $clients = Client::whereIn('id', $clientIds)->get();

            // Sort clients by distance from the employee's branch
            $sortedClients = $this->sortClientsByDistance($clients, $employee->branch);

            foreach ($sortedClients as $index => $client) {
                Visit::create([
                    'employee_id' => $employeeId,
                    'client_id' => $client->id,
                    'visit_date' => $visitDate,
                    'visit_order' => $index + 1,
                    'status' => 'pending',
                    'client_latitude' => $client->latitude,
                    'client_longitude' => $client->longitude,
                    'recording_method' => 'planned',
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'تم حفظ خط السير وترتيبه حسب الأقرب بنجاح!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save itinerary: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء حفظ خط السير. تأكد من وجود إحداثيات للفرع والعملاء.'], 500);
        }
    }

    /**
     * Sorts a collection of clients based on their distance from a starting branch.
     *
     * @param \Illuminate\Database\Eloquent\Collection $clients
     * @param \App\Models\Branch|null $branch
     * @return array
     */
    private function sortClientsByDistance($clients, $branch)
    {
        if (!$branch || !$branch->location) {
            return $clients->all();
        }

        list($startLat, $startLon) = explode(',', $branch->location);

        $clientDistances = $clients->map(function ($client) use ($startLat, $startLon) {
            if ($client->latitude && $client->longitude) {
                // Use the existing calculateDistance method (returns meters)
                $client->distance = $this->calculateDistance($startLat, $startLon, $client->latitude, $client->longitude);
            } else {
                $client->distance = PHP_INT_MAX; // Put clients without location at the end
            }
            return $client;
        });

        return $clientDistances->sortBy('distance')->values()->all();
    }



    // ثوابت النظام المعدلة
    private const ARRIVAL_DISTANCE = 100; // مسافة الوصول بالمتر (تم تخفيضها)
    private const DEPARTURE_DISTANCE = 150; // مسافة الانصراف بالمتر (تم تخفيضها)
    private const MIN_DEPARTURE_MINUTES = 3; // أقل مدة للانصراف (تم تخفيضها)
    private const AUTO_DEPARTURE_TIMEOUT = 10; // مهلة الانصراف التلقائي (تم تعديلها إلى 10 دقائق)
    private const VISIT_COOLDOWN = 30; // مدة الانتظار بين الزيارات (دقيقة)
    private const FORCE_AUTO_DEPARTURE = true; // إضافة خاصية تفعيل الانصراف التلقائي

    // عرض جميع الزيارات
    public function index()
    {
        $visits = Visit::with(['employee', 'client'])
            ->orderBy('visit_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $visits,
            'count' => $visits->count(),
        ]);
    }

    // عرض تفاصيل زيارة
    public function show($id)
    {
        $visit = Visit::with(['employee', 'client'])->find($id);

        if (!$visit) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'الزيارة غير موجودة',
                ],
                404,
            );
        }

        return response()->json([
            'success' => true,
            'data' => $visit,
        ]);
    }

    // تخزين موقع الموظف تلقائياً (محدثة)
    public function storeLocationEnhanced(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'isExit' => 'nullable|boolean',
        ]);

        $employeeId = Auth::id();
        $now = now();

        try {
            // تسجيل موقع الموظف
            $location = Location::updateOrCreate(
                ['employee_id' => $employeeId],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'accuracy' => $request->accuracy,
                    'recorded_at' => $now,
                ],
            );

            Log::info('Employee location updated', [
                'employee_id' => $employeeId,
                'location' => $location,
                'isExit' => $request->isExit,
            ]);

            // معالجة الزيارات التي تحتاج انصراف تلقائي
            $this->processAutoDepartures($employeeId, $request->latitude, $request->longitude);

            // التحقق من الانصراف في جميع الحالات
            $this->checkForDepartures($employeeId, $request->latitude, $request->longitude);

            // إذا كانت نقاط خروج
            if ($request->isExit) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل موقع الخروج بنجاح',
                    'location' => $location,
                    'departures_checked' => true,
                ]);
            }

            // البحث عن العملاء القريبين (فقط إذا لم تكن نقاط خروج)
            $nearbyClients = $this->getNearbyClients($request->latitude, $request->longitude, self::ARRIVAL_DISTANCE);

            Log::info('Nearby clients found', [
                'count' => count($nearbyClients),
                'clients' => $nearbyClients->pluck('id'),
            ]);

            // تسجيل الزيارات للعملاء القريبين
            $recordedVisits = [];
            foreach ($nearbyClients as $client) {
                $visit = $this->recordVisitAutomatically($employeeId, $client->id, $request->latitude, $request->longitude);

                if ($visit) {
                    // جدولة الانصراف التلقائي للزيارة الجديدة
                    $this->scheduleAutoDeparture($visit);
                    $recordedVisits[] = $visit;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الموقع بنجاح',
                'nearby_clients' => count($nearbyClients),
                'recorded_visits' => $recordedVisits,
                'location' => $location,
                'departures_checked' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update location: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تحديث الموقع: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // تسجيل زيارة تلقائية (محدثة)
    private function recordVisitAutomatically($employeeId, $clientId, $latitude, $longitude)
    {
        $now = now();
        $today = $now->toDateString();

        $lastVisit = Visit::where('employee_id', $employeeId)->where('client_id', $clientId)->whereDate('visit_date', $today)->orderBy('visit_date', 'desc')->first();

        if (!$lastVisit) {
            return $this->createNewVisit($employeeId, $clientId, $latitude, $longitude, 'زيارة تلقائية - أول زيارة اليوم');
        }

        if (!$lastVisit->departure_time) {
            Log::info('Skipping new visit - previous visit has no departure', [
                'visit_id' => $lastVisit->id,
                'arrival_time' => $lastVisit->arrival_time,
            ]);
            return null;
        }

        $minutesSinceDeparture = $now->diffInMinutes($lastVisit->departure_time);

        if ($minutesSinceDeparture > self::VISIT_COOLDOWN) {
            return $this->createNewVisit($employeeId, $clientId, $latitude, $longitude, 'زيارة تلقائية - عودة بعد انصراف');
        }

        Log::info('Skipping new visit - recent departure', [
            'visit_id' => $lastVisit->id,
            'minutes_since_departure' => $minutesSinceDeparture,
        ]);

        return null;
    }

    // إنشاء زيارة جديدة
    private function createNewVisit($employeeId, $clientId, $latitude, $longitude, $notes)
    {
        $client = Client::find($clientId);

        $visit = Visit::create([
            'employee_id' => $employeeId,
            'client_id' => $clientId,
            'visit_date' => now(),
            'status' => 'present',
            'employee_latitude' => $latitude,
            'employee_longitude' => $longitude,
            'arrival_time' => now(),
            'notes' => $notes,
            'departure_notification_sent' => false,
        ]);

        Log::info('New visit created automatically', [
            'visit_id' => $visit->id,
            'client_id' => $clientId,
            'employee_id' => $employeeId,
        ]);

        $this->sendVisitNotifications($visit, 'arrival');
        $this->sendEmployeeNotification($employeeId, 'تم تسجيل وصولك للعميل ' . $client->trade_name, 'وصول تلقائي');

        return $visit;
    }

    // جدولة الانصراف التلقائي (دالة جديدة)
    private function scheduleAutoDeparture($visit)
    {
        // إضافة معلومات للسجل
        Log::info('Auto departure scheduled', [
            'visit_id' => $visit->id,
            'client_id' => $visit->client_id,
            'employee_id' => $visit->employee_id,
            'scheduled_time' => now()->addMinutes(self::AUTO_DEPARTURE_TIMEOUT)->format('Y-m-d H:i:s'),
        ]);
    }

    // معالجة الانصراف التلقائي للزيارات (دالة جديدة)
    private function processAutoDepartures($employeeId, $latitude, $longitude)
    {
        $activeVisits = Visit::where('employee_id', $employeeId)
            ->whereDate('visit_date', now()->toDateString())
            ->whereNotNull('arrival_time')
            ->whereNull('departure_time')
            ->get();

        Log::info('Processing auto departures', [
            'employee_id' => $employeeId,
            'active_visits_count' => $activeVisits->count(),
            'current_time' => now()->format('Y-m-d H:i:s'),
        ]);

        foreach ($activeVisits as $visit) {
            $minutesSinceArrival = now()->diffInMinutes($visit->arrival_time);

            Log::info('Checking visit for auto departure', [
                'visit_id' => $visit->id,
                'arrival_time' => $visit->arrival_time,
                'minutes_since_arrival' => $minutesSinceArrival,
                'auto_departure_timeout' => self::AUTO_DEPARTURE_TIMEOUT,
            ]);

            if ($minutesSinceArrival >= self::AUTO_DEPARTURE_TIMEOUT) {
                $this->recordDeparture($visit, $latitude, $longitude, $minutesSinceArrival, 'auto_timeout');
            }
        }
    }
    // التحقق من الانصراف (محدثة)
    private function checkForDepartures($employeeId, $latitude, $longitude)
    {
        $activeVisits = Visit::where('employee_id', $employeeId)
            ->whereDate('visit_date', now()->toDateString())
            ->whereNotNull('arrival_time')
            ->whereNull('departure_time')
            ->with(['client.locations'])
            ->get();

        foreach ($activeVisits as $visit) {
            try {
                // حساب الوقت المنقضي
                $minutesSinceArrival = now()->diffInMinutes($visit->arrival_time);

                // التحقق من المسافة
                $clientLocation = $visit->client->locations()->latest()->first();
                $distance = $this->calculateDistance($clientLocation->latitude, $clientLocation->longitude, $latitude, $longitude);

                // تسجيل الانصراف في أي من الحالتين:
                if ($minutesSinceArrival >= 10 || $distance >= 100) {
                    $reason = $minutesSinceArrival >= 10 ? 'بعد 10 دقائق' : 'بعد الابتعاد بمسافة 100 متر';

                    $this->recordDeparture($visit, $latitude, $longitude, $minutesSinceArrival, $reason);
                }
            } catch (\Exception $e) {
                Log::error('Error processing visit departure', [
                    'visit_id' => $visit->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    // تسجيل الانصراف
    private function recordDeparture($visit, $latitude, $longitude, $value, $reason)
    {
        if ($visit->departure_time) {
            return;
        }

        $visit->update([
            'departure_time' => now(),
            'departure_latitude' => $latitude,
            'departure_longitude' => $longitude,
            'departure_notification_sent' => true,
            'notes' => ($visit->notes ?? '') . "\nانصراف تلقائي: $reason",
        ]);

        // إرسال الإشعارات
        $this->sendVisitNotifications($visit, 'departure');
        $this->sendEmployeeNotification($visit->employee_id, 'تم تسجيل انصرافك من العميل ' . $visit->client->trade_name, 'انصراف تلقائي');
    }

    // البحث عن العملاء القريبين
    private function getNearbyClients($latitude, $longitude, $radius)
    {
        return Client::with('locations')
            ->whereHas('locations', function ($query) use ($latitude, $longitude, $radius) {
                $query->whereRaw(
                    "
                    ST_Distance_Sphere(
                        POINT(longitude, latitude),
                        POINT(?, ?)
                    ) <= ?
                ",
                    [$longitude, $latitude, $radius],
                );
            })
            ->get();
    }

    // التحقق من قرب الموظف من العميل



    // إرسال إشعارات الزيارة
    private function sendVisitNotifications($visit, $type)
    {
        $employeeName = $visit->employee->name ?? 'غير معروف';
        $clientName = $visit->client->trade_name ?? 'غير معروف';
        $time = $type === 'arrival' ? Carbon::parse($visit->arrival_time)->format('H:i') : Carbon::parse($visit->departure_time)->format('H:i');

        // إرسال إشعار داخلي
        notifications::create([
            'user_id' => $visit->employee_id,
            'type' => 'visit',
            'title' => $type === 'arrival' ? 'وصول إلى عميل' : 'انصراف من عميل',
            'message' => $type === 'arrival' ? "تم تسجيل وصولك إلى العميل: $clientName" : "تم تسجيل انصرافك من العميل: $clientName",
            'read' => false,
            'data' => [
                'visit_id' => $visit->id,
                'client_id' => $visit->client_id,
                'type' => $type,
            ],
        ]);

        // إرسال إشعار إلى المدير
        $managers = User::role('manager')->get();
        foreach ($managers as $manager) {
            notifications::create([
                'user_id' => $manager->id,
                'type' => 'visit',
                'title' => $type === 'arrival' ? 'وصول موظف إلى عميل' : 'انصراف موظف من عميل',
                'message' => $type === 'arrival' ? "الموظف $employeeName وصل إلى العميل $clientName" : "الموظف $employeeName انصرف من العميل $clientName",
                'read' => false,
                'data' => [
                    'visit_id' => $visit->id,
                    'employee_id' => $visit->employee_id,
                    'client_id' => $visit->client_id,
                    'type' => $type,
                ],
            ]);
        }

        // إرسال إشعار عبر التليجرام
        $this->sendTelegramNotification($visit, $type);
    }

    // إرسال إشعار للموظف
    private function sendEmployeeNotification($employeeId, $message, $title)
    {
        notifications::create([
            'user_id' => $employeeId,
            'type' => 'visit_notification',
            'title' => $title,
            'message' => $message,
            'read' => false,
            'data' => [
                'type' => 'visit_update',
            ],
        ]);
    }

    // إرسال إشعار التليجرام
    private function sendTelegramNotification($visit, $type)
    {
        $employeeName = $visit->employee->name ?? 'غير معروف';
        $clientName = $visit->client->trade_name ?? 'غير معروف';
        $time = $type === 'arrival' ? Carbon::parse($visit->arrival_time)->format('H:i') : Carbon::parse($visit->departure_time)->format('H:i');

        $message = "🔄 *حركة زيارة عملاء*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= $type === 'arrival' ? '✅ *وصول*' : "🛑 *انصراف*\n";
        $message .= "👤 *الموظف:* `$employeeName`\n";
        $message .= "🏢 *العميل:* `$clientName`\n";
        $message .= "⏱ *الوقت:* `$time`\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";

        try {
            $telegramApiUrl = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage';

            Http::post($telegramApiUrl, [
                'chat_id' => env('TELEGRAM_CHANNEL_ID'),
                'text' => $message,
                'parse_mode' => 'Markdown',
                'timeout' => 60,
            ]);
        } catch (\Exception $e) {
            Log::error('فشل إرسال إشعار التليجرام: ' . $e->getMessage());
        }
    }

    // تحديث زيارة
    public function update(Request $request, $id)
    {
        $visit = Visit::find($id);

        if (!$visit) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'الزيارة غير موجودة',
                ],
                404,
            );
        }

        $request->validate([
            'status' => 'sometimes|in:present,absent',
            'arrival_time' => 'sometimes|date',
            'departure_time' => 'sometimes|date|after:arrival_time',
            'notes' => 'sometimes|string',
        ]);

        if ($visit->employee_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'غير مصرح لك بتعديل هذه الزيارة',
                ],
                403,
            );
        }

        $visit->update($request->all());

        if ($request->has('departure_time')) {
            $this->sendVisitNotifications($visit, 'departure');
            $this->sendEmployeeNotification($visit->employee_id, 'تم تحديث وقت انصرافك من العميل ' . $visit->client->trade_name, 'تحديث انصراف');
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الزيارة بنجاح',
            'data' => $visit,
        ]);
    }

    // حذف زيارة
    public function destroy($id)
    {
        $visit = Visit::find($id);

        if (!$visit) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'الزيارة غير موجودة',
                ],
                404,
            );
        }

        if ($visit->employee_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'غير مصرح لك بحذف هذه الزيارة',
                ],
                403,
            );
        }

        $visit->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الزيارة بنجاح',
        ]);
    }

    // زيارات الموظف الحالي
    public function myVisits()
    {
        $visits = Visit::with('client')->where('employee_id', Auth::id())->orderBy('visit_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $visits,
            'count' => $visits->count(),
        ]);
    }

    // زيارات اليوم
    public function getTodayVisits()
    {
        $today = now()->toDateString();

        $visits = Visit::with(['employee', 'client'])
            ->whereDate('visit_date', $today)
            ->orderBy('visit_date', 'desc')
            ->get()
            ->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'client_name' => $visit->client->trade_name ?? 'غير معروف',
                    'employee_name' => $visit->employee->name ?? 'غير معروف',
                    'arrival_time' => $visit->arrival_time ? $visit->arrival_time->format('H:i') : '--:--',
                    'departure_time' => $visit->departure_time ? $visit->departure_time->format('H:i') : '--:--',
                    'status' => $visit->status,
                    'created_at' => $visit->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'success' => true,
            'visits' => $visits,
            'count' => $visits->count(),
        ]);
    }

    // تحليلات حركة الزيارات
public function tracktaff(Request $request)
{
    $currentYear = $request->get('year', now()->year);

    // توليد أسابيع السنة
    $allWeeks = array_reverse($this->generateYearWeeks($currentYear, true));

    // تحديد الأسبوع الحالي
    $currentWeekNumber = now()->weekOfYear;
    $isCurrentYear = $currentYear == now()->year;

    // تصفية الأسابيع - إظهار الأسابيع حتى الأسبوع الحالي فقط
    if ($isCurrentYear) {
        $allWeeks = array_filter($allWeeks, function($week) use ($currentWeekNumber) {
            return $week['week_number'] <= $currentWeekNumber;
        });
        $allWeeks = array_values($allWeeks);
    }

    // جلب الفروع والمجموعات والأحياء والعملاء فقط بدون علاقات ثقيلة
    $branches = Branch::with([
        'regionGroups.neighborhoods.client' => function ($query) {
            $query->select('id', 'trade_name', 'code', 'status_id')
                  ->with('status_client:id,name,color');
        }
    ])->get();

    // جهز جميع العملاء
    $clients = [];
    $clientIds = [];
    foreach ($branches as $branch) {
        foreach ($branch->regionGroups as $group) {
            foreach ($group->neighborhoods as $neigh) {
                if ($neigh->client) {
                    $clients[$neigh->client->id] = $neigh->client;
                    $clientIds[] = $neigh->client->id;
                }
            }
        }
    }

    // حساب الفواتير المستثناة
    $excludedInvoiceIds = \App\Models\Invoice::whereNotNull('reference_number')
        ->pluck('reference_number')
        ->merge(
            \App\Models\Invoice::where('type', 'returned')->pluck('id')
        )
        ->unique()
        ->toArray();

    // إحصائيات أسبوعية للعملاء
    $clientWeeklyStats = [];

    foreach ($allWeeks as $week) {
        $weekStart = $week['start']->copy()->startOfDay();
        $weekEnd = $week['end']->copy()->endOfDay();
        $weekNumber = $week['week_number'];

        // الفواتير لهذا الأسبوع
        $invoices = DB::table('invoices')
            ->where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds)
            ->whereIn('client_id', $clientIds)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->select('client_id', DB::raw('COUNT(*) as invoice_count'))
            ->groupBy('client_id')
            ->get();

        foreach ($invoices as $row) {
            $clientWeeklyStats[$row->client_id][$weekNumber]['invoice_count'] = $row->invoice_count;
        }

        // الملاحظات لهذا الأسبوع مع التفاصيل
        $notes = DB::table('client_relations')
            ->whereIn('client_id', $clientIds)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->select('client_id', 'status', 'process', 'time', 'date', 'description', 'created_at')
            ->get();

        foreach ($notes as $row) {
            if (!isset($clientWeeklyStats[$row->client_id][$weekNumber]['notes'])) {
                $clientWeeklyStats[$row->client_id][$weekNumber]['notes'] = [];
            }
            $clientWeeklyStats[$row->client_id][$weekNumber]['notes'][] = [
                'status' => $row->status,
                'process' => $row->process,
                'time' => $row->time,
                'date' => $row->date,
                'description' => $row->description,
                'created_at' => $row->created_at,
            ];
            $clientWeeklyStats[$row->client_id][$weekNumber]['note_count'] =
                ($clientWeeklyStats[$row->client_id][$weekNumber]['note_count'] ?? 0) + 1;
        }

        // الزيارات من employee_client_visits
        $visits = DB::table('employee_client_visits')
            ->whereIn('client_id', $clientIds)
            ->where('year', $currentYear)
            ->where('week_number', $weekNumber)
            ->where('status', 1)
            ->select('client_id', 'employee_id', DB::raw('COUNT(DISTINCT day_of_week) as visit_days'))
            ->groupBy('client_id', 'employee_id')
            ->get();

        foreach ($visits as $row) {
            if (!isset($clientWeeklyStats[$row->client_id][$weekNumber]['visits'])) {
                $clientWeeklyStats[$row->client_id][$weekNumber]['visits'] = [];
            }
            $clientWeeklyStats[$row->client_id][$weekNumber]['visits'][] = [
                'employee_id' => $row->employee_id,
                'days' => $row->visit_days
            ];
        }

        // 🟢 المدفوعات لهذا الأسبوع (للتحصيل)
        $payments = DB::table('payments_process')
            ->join('invoices', 'payments_process.invoice_id', '=', 'invoices.id')
            ->where('invoices.type', 'normal')
            ->whereNotIn('invoices.id', $excludedInvoiceIds)
            ->whereIn('invoices.client_id', $clientIds)
            ->whereBetween('payments_process.created_at', [$weekStart, $weekEnd])
            ->select('invoices.client_id', DB::raw('SUM(payments_process.amount) as payment_total'))
            ->groupBy('invoices.client_id')
            ->get();

        foreach ($payments as $row) {
            $clientWeeklyStats[$row->client_id][$weekNumber]['collection'] =
                ($clientWeeklyStats[$row->client_id][$weekNumber]['collection'] ?? 0) + $row->payment_total;
        }

        // 🟢 سندات القبض لهذا الأسبوع (للتحصيل)
        $receipts = DB::table('receipts')
            ->join('accounts', 'receipts.account_id', '=', 'accounts.id')
            ->whereIn('accounts.client_id', $clientIds)
            ->whereBetween('receipts.created_at', [$weekStart, $weekEnd])
            ->select('accounts.client_id', DB::raw('SUM(receipts.amount) as receipt_total'))
            ->groupBy('accounts.client_id')
            ->get();

        foreach ($receipts as $row) {
            $clientWeeklyStats[$row->client_id][$weekNumber]['collection'] =
                ($clientWeeklyStats[$row->client_id][$weekNumber]['collection'] ?? 0) + $row->receipt_total;
        }
    }

    // جلب بيانات الموظفين للزيارات
    $employeeIds = [];
    foreach ($clientWeeklyStats as $clientStats) {
        foreach ($clientStats as $weekStats) {
            if (isset($weekStats['visits'])) {
                foreach ($weekStats['visits'] as $visit) {
                    $employeeIds[] = $visit['employee_id'];
                }
            }
        }
    }

    $employees = \App\Models\User::select('id', 'name', 'role')
        ->with('employee:id,user_id,Job_role_id')
        ->whereIn('id', array_unique($employeeIds))
        ->get()
        ->keyBy('id');

    // إجمالي العملاء
    $totalClients = count($clients);

    return view('reports::sals.traffic_analytics', [
        'branches' => $branches,
        'weeks' => $allWeeks,
        'totalClients' => $totalClients,
        'clientWeeklyStats' => $clientWeeklyStats,
        'currentYear' => $currentYear,
        'clients' => $clients,
        'employees' => $employees,
    ]);
}

public function generateYearWeeks( $currentYear = null)
{
     $currentYear =  $currentYear ?? now()->year;
    $start = Carbon::createFromDate( $currentYear, 1, 1)->startOfWeek();
    $end = Carbon::createFromDate( $currentYear, 12, 31)->endOfWeek();

    $weeks = [];
    $weekNumber = 1;
    while ($start->lte($end)) {
        $weeks[] = [
            'week_number' => $weekNumber,
            'start' => $start->copy(),
            'end' => $start->copy()->endOfWeek(),
        ];
        $start->addWeek();
        $weekNumber++;
    }

    return $weeks;
}


public function getWeeksData(Request $request)
{
    $offset = $request->input('offset', 0);
    $limit = $request->input('limit', 8);

    // جلب بيانات الأسابيع
    $weeks = Week::orderBy('start_date', 'DESC')
                ->skip($offset)
                ->take($limit)
                ->get()
                ->toArray();

    // جلب بيانات العملاء والأنشطة
    $clients = Client::with(['activities' => function($query) use ($weeks) {
                    $query->whereIn('week_id', array_column($weeks, 'id'));
                }])
                ->get()
                ->map(function($client) use ($weeks) {
                    $activities = [];
                    foreach ($client->activities as $activity) {
                        $activities[$activity->week_id] = true;
                    }

                    return [
                        'id' => $client->id,
                        'name' => $client->name,
                        'area' => $client->area,
                        'status' => $client->status,
                        'activities' => $activities,
                        'total_activities' => count($client->activities)
                    ];
                })
                ->toArray();

    return response()->json([
        'success' => true,
        'weeks' => $weeks,
        'clients' => $clients
    ]);
}
    public function getTrafficData(Request $request)
    {
        $weeks = $request->input('weeks');
        $groupIds = $request->input('group_ids', []);

        // هنا يمكنك تنفيذ الاستعلامات للحصول على البيانات حسب الأسابيع المحددة
        // هذا مثال مبسط، يجب تعديله حسب هيكل قاعدة البيانات الخاص بك

        $groups = Region_groub::when(!empty($groupIds), function ($query) use ($groupIds) {
            return $query->whereIn('id', $groupIds);
        })
            ->with([
                'neighborhoods.client' => function ($query) use ($weeks) {
                    $query->with([
                        'invoices' => function ($q) use ($weeks) {
                            $q->whereBetween('created_at', [$weeks[0]['start'], end($weeks)['end']]);
                        },
                        'payments' => function ($q) use ($weeks) {
                            $q->whereBetween('created_at', [$weeks[0]['start'], end($weeks)['end']]);
                        },
                        'appointmentNotes' => function ($q) use ($weeks) {
                            $q->whereBetween('created_at', [$weeks[0]['start'], end($weeks)['end']]);
                        },
                        'visits' => function ($q) use ($weeks) {
                            $q->whereBetween('created_at', [$weeks[0]['start'], end($weeks)['end']]);
                        },
                        'accounts.receipts' => function ($q) use ($weeks) {
                            $q->whereBetween('created_at', [$weeks[0]['start'], end($weeks)['end']]);
                        },
                    ]);
                },
            ])
            ->get();

        return response()->json([
            'groups' => $groups,
            'weeks' => $weeks,
        ]);
    }

    public function sendDailyReport()
{
    $date = Carbon::today();
    $users = User::where('role', 'employee')->get();

    foreach ($users as $user) {
        $invoices = Invoice::with('client')->where('created_by', $user->id)->whereDate('created_at', $date)->get();

        $normalInvoiceIds = $invoices
            ->where('type', '!=', 'returned')
            ->reject(function ($invoice) use ($invoices) {
                return $invoices->where('type', 'returned')->where('reference_number', $invoice->id)->isNotEmpty();
            })
            ->pluck('id')
            ->toArray();

        $payments = PaymentsProcess::whereIn('invoice_id', $normalInvoiceIds)->whereDate('payment_date', $date)->get();
        $visits = Visit::with('client')->where('employee_id', $user->id)->whereDate('created_at', $date)->get();
        $receipts = Receipt::where('created_by', $user->id)->whereDate('created_at', $date)->get();
        $expenses = Expense::where('created_by', $user->id)->whereDate('created_at', $date)->get();
        $notes = ClientRelation::with('client')->where('employee_id', $user->id)->whereDate('created_at', $date)->get();

        // حساب المجاميع
        $totalNormalInvoices = $invoices
            ->where('type', '!=', 'returned')
            ->reject(function ($invoice) use ($invoices) {
                return $invoices->where('type', 'returned')->where('reference_number', $invoice->id)->isNotEmpty();
            })
            ->sum('grand_total');

        $totalReturnedInvoices = $invoices->where('type', 'returned')->sum('grand_total');
        $netSales = $totalNormalInvoices - $totalReturnedInvoices;
        $totalPayments = $payments->sum('amount');
        $totalReceipts = $receipts->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netCollection = $totalPayments + $totalReceipts - $totalExpenses;

        // التحقق من وجود أي أنشطة للموظف
        $hasActivities = $invoices->isNotEmpty() ||
                        $visits->isNotEmpty() ||
                        $payments->isNotEmpty() ||
                        $receipts->isNotEmpty() ||
                        $expenses->isNotEmpty() ||
                        $notes->isNotEmpty();

        if (!$hasActivities) {
            Log::info('لا يوجد أنشطة مسجلة للموظف: ' . $user->name . ' في تاريخ: ' . $date->format('Y-m-d') . ' - تم تخطي إنشاء التقرير');
            continue; // تخطي هذا الموظف والمتابعة مع الموظف التالي
        }

        // إنشاء التقرير فقط إذا كان هناك أنشطة
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor($user->name);
        $pdf->SetTitle('التقرير اليومي للموظف - ' . $user->name . ' - ' . $date->format('Y-m-d'));
        $pdf->SetSubject('التقرير اليومي');
        $pdf->AddPage();

        $html = view('reports.daily_employee_single', [
            'user' => $user,
            'invoices' => $invoices,
            'visits' => $visits,
            'payments' => $payments,
            'receipts' => $receipts,
            'expenses' => $expenses,
            'notes' => $notes,
            'total_normal_invoices' => $totalNormalInvoices,
            'total_returned_invoices' => $totalReturnedInvoices,
            'net_sales' => $netSales,
            'total_payments' => $totalPayments,
            'total_receipts' => $totalReceipts,
            'total_expenses' => $totalExpenses,
            'net_collection' => $netCollection,
            'date' => $date->format('Y-m-d'),
        ])->render();

        $pdf->writeHTML($html, true, false, true, false, 'R');

        $pdfPath = storage_path('app/public/daily_report_' . $user->id . '_' . $date->format('Y-m-d') . '.pdf');
        $pdf->Output($pdfPath, 'F');

        $caption = "📊 التقرير اليومي للموظف\n" . '👤 اسم الموظف: ' . $user->name . "\n" . '📅 التاريخ: ' . $date->format('Y-m-d') . "\n" . '🛒 إجمالي الفواتير: ' . number_format($netSales, 2) . " ر.س\n" . '💵 صافي التحصيل: ' . number_format($netCollection, 2) . " ر.س\n" . '🔄 الفواتير المرتجعة: ' . number_format($totalReturnedInvoices, 2) . ' ر.س';

        $botToken = '7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU';
        $chatId = '@Salesfatrasmart';

        $response = Http::attach('document', file_get_contents($pdfPath), 'daily_report_' . $user->name . '.pdf')->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
            'chat_id' => $chatId,
            'caption' => '📊 تقرير الموظف اليومي - ' . $user->name . ' - ' . $date->format('Y-m-d')
            . '💰 صافي المبيعات: ' . number_format($netSales, 2) . " ر.س\n"
            . '🔄 المرتجعات: ' . number_format($totalReturnedInvoices, 2) . ' ر.س' .
             '💰 صافي التحصيل: ' . number_format($netCollection, 2) . " ر.س\n",
        ]);

        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        if ($response->successful()) {
            Log::info('تم إرسال التقرير اليومي بنجاح للموظف: ' . $user->name);
        } else {
            Log::error('فشل إرسال التقرير اليومي للموظف: ' . $user->name, [
                'error' => $response->body(),
            ]);
        }
    }

    return true;
}
    public function sendWeeklyReport()
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(6);

        $users = User::where('role', 'employee')->get();

        foreach ($users as $user) {
            // جلب جميع الفواتير (العادية والمرتجعة) للأسبوع
            $invoices = Invoice::with('client')
                ->where('created_by', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // حساب الفواتير العادية الصافية (باستثناء التي لها مرتجع)
            $normalInvoices = $invoices->where('type', '!=', 'returned')->reject(function ($invoice) use ($invoices) {
                return $invoices->where('type', 'returned')->where('reference_number', $invoice->id)->isNotEmpty();
            });

            // حساب الفواتير المرتجعة فقط
            $returnedInvoices = $invoices->where('type', 'returned');

            // المدفوعات للفواتير العادية الصافية فقط
            $payments = PaymentsProcess::whereIn('invoice_id', $normalInvoices->pluck('id')->toArray())
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->get();

            // باقي البيانات كما هي بدون تغيير
            $visits = Visit::with('client')
                ->where('employee_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $receipts = Receipt::where('created_by', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $expenses = Expense::where('created_by', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $notes = ClientRelation::with('client')
                ->where('employee_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // الحسابات المالية بنفس طريقة التقرير اليومي بالضبط
            $totalSales = $normalInvoices->sum('grand_total');
            $totalReturns = $returnedInvoices->sum('grand_total');
            $netSales = $totalSales - $totalReturns;
            $totalPayments = $payments->sum('amount');
            $totalReceipts = $receipts->sum('amount');
            $totalExpenses = $expenses->sum('amount');
            $netCollection = $totalPayments + $totalReceipts - $totalExpenses;

            // باقي الكود كما هو...
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator(config('app.name'));
            $pdf->SetAuthor($user->name);
            $pdf->SetTitle('التقرير الأسبوعي للموظف - ' . $user->name);
            $pdf->AddPage();

            $html = view('reports.weekly_employee', [
                'user' => $user,
                'invoices' => $invoices,
                'visits' => $visits,
                'payments' => $payments,
                'receipts' => $receipts,
                'expenses' => $expenses,
                'notes' => $notes,
                'totalSales' => $totalSales,
                'totalReturns' => $totalReturns,
                'netSales' => $netSales,
                'total_payments' => $totalPayments,
                'total_receipts' => $totalReceipts,
                'total_expenses' => $totalExpenses,
                'net_collection' => $netCollection,
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
            ])->render();

            $pdf->writeHTML($html, true, false, true, false, 'R');

            $pdfPath = storage_path('app/public/weekly_report_' . $user->id . '_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf');
            $pdf->Output($pdfPath, 'F');

            // إرسال التقرير عبر Telegram
            $botToken = '7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU';
            $chatId = '@Salesfatrasmart';

            $response = Http::attach('document', file_get_contents($pdfPath), 'weekly_report_' . $user->name . '.pdf')->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => '📊 التقرير الأسبوعي - ' . $user->name . "\n" . '📅 من ' . $startDate->format('Y-m-d') . ' إلى ' . $endDate->format('Y-m-d') . "\n" . '💰 صافي المبيعات: ' . number_format($netSales, 2) . " ر.س\n" . '💰 صافي  التحصيل : ' . number_format($netCollection, 2) . " ر.س\n" . '🔄 المرتجعات: ' . number_format($totalReturns, 2) . ' ر.س',
            ]);

            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }
    }

    public function sendMonthlyReport()
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->startOfMonth();

        $users = User::where('role', 'employee')->get();

        foreach ($users as $user) {
            // جلب جميع الفواتير (العادية والمرتجعة) للشهر
            $invoices = Invoice::with('client')
                ->where('created_by', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // حساب الفواتير العادية الصافية (باستثناء التي لها مرتجع)
            $normalInvoices = $invoices->where('type', '!=', 'returned')->reject(function ($invoice) use ($invoices) {
                return $invoices->where('type', 'returned')->where('reference_number', $invoice->id)->isNotEmpty();
            });

            // حساب الفواتير المرتجعة فقط
            $returnedInvoices = $invoices->where('type', 'returned');

            // المدفوعات للفواتير العادية الصافية فقط
            $payments = PaymentsProcess::whereIn('invoice_id', $normalInvoices->pluck('id')->toArray())
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->get();

            // باقي البيانات كما هي بدون تغيير
            $visits = Visit::with('client')
                ->where('employee_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $clientVisitsCount = $visits->groupBy('client_id')->map->count();

            $receipts = Receipt::where('created_by', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $expenses = Expense::where('created_by', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $notes = ClientRelation::with('client')
                ->where('employee_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // الحسابات المالية بنفس طريقة التقرير اليومي بالضبط
            $totalSales = $normalInvoices->sum('grand_total');
            $totalReturns = $returnedInvoices->sum('grand_total');
            $netSales = $totalSales - $totalReturns;
            $totalPayments = $payments->sum('amount');
            $totalReceipts = $receipts->sum('amount');
            $totalExpenses = $expenses->sum('amount');
            $netCollection = $totalPayments + $totalReceipts - $totalExpenses;

            // باقي الكود كما هو...
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator(config('app.name'));
            $pdf->SetAuthor($user->name);
            $pdf->SetTitle('التقرير الشهري للموظف - ' . $user->name);
            $pdf->AddPage();

            $html = view('reports.monthly_employee', [
                'user' => $user,
                'invoices' => $invoices,
                'visits' => $visits,
                'clientVisitsCount' => $clientVisitsCount,
                'payments' => $payments,
                'receipts' => $receipts,
                'expenses' => $expenses,
                'notes' => $notes,
                'totalSales' => $totalSales,
                'totalReturns' => $totalReturns,
                'netSales' => $netSales,
                'total_payments' => $totalPayments,
                'total_receipts' => $totalReceipts,
                'total_expenses' => $totalExpenses,
                'net_collection' => $netCollection,
                'startDate' => Carbon::parse($startDate), // تأكد من تحويله إلى كائن Carbon
                'endDate' => Carbon::parse($endDate), // تأكد من تحويله إلى كائن Carbon
            ])->render();

            $pdf->writeHTML($html, true, false, true, false, 'R');

            $pdfPath = storage_path('app/public/monthly_report_' . $user->id . '_' . $startDate->format('Y-m') . '.pdf');
            $pdf->Output($pdfPath, 'F');

            // إرسال التقرير عبر Telegram
            $botToken = '7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU';
            $chatId = '@Salesfatrasmart';

            $response = Http::attach('document', file_get_contents($pdfPath), 'monthly_report_' . $user->name . '.pdf')->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => '📊 التقرير الشهري - ' . $user->name . "\n" . '📅 شهر ' . $startDate->format('Y-m') . "\n" . '💰 صافي المبيعات: ' . number_format($netSales, 2) . " ر.س\n" . '💸 التحصيل : ' . number_format($netCollection, 2) . " ر.س\n" . '🔄 المرتجعات: ' . number_format($totalReturns, 2) . ' ر.س',
            ]);

            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }
    }
}
