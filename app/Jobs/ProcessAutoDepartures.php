<?php

namespace App\Jobs;

use App\Models\Visit;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessAutoDepartures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            DB::beginTransaction();

            $activeVisits = Visit::whereNull('departure_time')
                ->whereDate('visit_date', now())
                ->with('client.locations')
                ->get();

            foreach ($activeVisits as $visit) {
                $this->processVisit($visit);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing auto departures: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // إعادة رمي الخطأ لضمان تسجيله
        }
    }

    private function processVisit($visit)
    {
        try {
            // تسجيل البيانات الأولية
            Log::info('Processing visit for auto departure', [
                'visit_id' => $visit->id,
                'arrival_time' => $visit->arrival_time,
                'current_location' => [
                    'lat' => $visit->employee_latitude,
                    'lng' => $visit->employee_longitude
                ]
            ]);

            // حساب الوقت المنقضي
            $minutesSinceArrival = now()->diffInMinutes($visit->arrival_time);

            // حساب المسافة
            $distance = $this->calculateDistance(
                $visit->client->locations->last()->latitude,
                $visit->client->locations->last()->longitude,
                $visit->employee_latitude,
                $visit->employee_longitude
            );

            // تسجيل حالة الزيارة
            Log::info('Visit status', [
                'visit_id' => $visit->id,
                'minutes_since_arrival' => $minutesSinceArrival,
                'distance' => $distance
            ]);

            // شروط الانصراف
            if ($minutesSinceArrival >= 10 || $distance >= 100) {
                $reason = $minutesSinceArrival >= 10 ? 'بعد 10 دقائق' : 'بعد الابتعاد بمسافة 100 متر';

                Log::info('Departure conditions met', [
                    'visit_id' => $visit->id,
                    'reason' => $reason
                ]);

                $visit->update([
                    'departure_time' => now(),
                    'departure_latitude' => $visit->employee_latitude,
                    'departure_longitude' => $visit->employee_longitude,
                    'departure_notification_sent' => true,
                    'notes' => ($visit->notes ?? '') . "\nانصراف تلقائي: $reason"
                ]);

                Log::info('Departure recorded', [
                    'visit_id' => $visit->id,
                    'departure_time' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error processing visit: ' . $e->getMessage(), [
                'visit_id' => $visit->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // إعادة رمي الخطأ
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // نصف قطر الأرض بالمتر

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }
}
