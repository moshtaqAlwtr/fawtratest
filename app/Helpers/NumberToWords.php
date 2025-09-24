<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
        15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
    ];

    private static $tens = [
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    ];

    public static function convert($number)
    {
        if ($number == 0) {
            return 'Zero';
        }

        $whole = floor($number);
        $fraction = round(($number - $whole) * 100);

        $words = self::convertWhole($whole);

        if ($fraction > 0) {
            $words .= ' and ' . $fraction . '/100';
        }

        return $words;
    }

    private static function convertWhole($number)
    {
        if ($number < 0) {
            return 'Negative ' . self::convertWhole(abs($number));
        }

        if ($number < 20) {
            return self::$ones[$number];
        }

        if ($number < 100) {
            return self::$tens[floor($number/10)] . ($number % 10 ? ' ' . self::$ones[$number % 10] : '');
        }

        if ($number < 1000) {
            return self::$ones[floor($number/100)] . ' Hundred' . ($number % 100 ? ' and ' . self::convertWhole($number % 100) : '');
        }

        if ($number < 1000000) {
            return self::convertWhole(floor($number/1000)) . ' Thousand' . ($number % 1000 ? ' ' . self::convertWhole($number % 1000) : '');
        }

        if ($number < 1000000000) {
            return self::convertWhole(floor($number/1000000)) . ' Million' . ($number % 1000000 ? ' ' . self::convertWhole($number % 1000000) : '');
        }

        return self::convertWhole(floor($number/1000000000)) . ' Billion' . ($number % 1000000000 ? ' ' . self::convertWhole($number % 1000000000) : '');
    }
}
