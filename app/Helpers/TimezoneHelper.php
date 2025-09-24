<?php

namespace App\Helpers;

class TimezoneHelper
{
    public static function getAllTimezones()
    {
        $timezones = [];
        $regions = [
            'Africa' => \DateTimeZone::AFRICA,
            'America' => \DateTimeZone::AMERICA,
            'Antarctica' => \DateTimeZone::ANTARCTICA,
            'Asia' => \DateTimeZone::ASIA,
            'Atlantic' => \DateTimeZone::ATLANTIC,
            'Australia' => \DateTimeZone::AUSTRALIA,
            'Europe' => \DateTimeZone::EUROPE,
            'Indian' => \DateTimeZone::INDIAN,
            'Pacific' => \DateTimeZone::PACIFIC
        ];

        foreach ($regions as $name => $mask) {
            $zones = \DateTimeZone::listIdentifiers($mask);
            foreach ($zones as $timezone) {
                $time = new \DateTime(null, new \DateTimeZone($timezone));
                $offset = $time->format('P');
                $timezones[$timezone] = $timezone . ' (' . $offset . ')';
            }
        }

        // Sort timezones by offset
        asort($timezones);

        return $timezones;
    }
}
