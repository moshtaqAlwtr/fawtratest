<?php

namespace Modules\Api\Services;

use App\Models\Location;

class ClientDistanceService
{
    public static function append($clients, $user)
    {
        $userLocation = Location::where('employee_id', $user->id)->latest()->first();

        foreach ($clients as $client) {
            $clientLocation = Location::where('client_id', $client->id)->latest()->first();
            if ($userLocation && $clientLocation) {
                $distance = self::calculate($userLocation->latitude, $userLocation->longitude, $clientLocation->latitude, $clientLocation->longitude);
                $client->distance = $distance;
            } else {
                $client->distance = null;
            }
        }

        return $clients;
    }

    protected static function calculate($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 3);
    }
}
