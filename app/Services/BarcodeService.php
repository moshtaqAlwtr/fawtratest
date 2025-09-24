<?php

namespace App\Services;

class BarcodeService
{
    public static function generateBarcode($code)
    {
        // Create image
        $width = 300;
        $height = 100;
        $image = imagecreate($width, $height);

        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        // Fill background
        imagefill($image, 0, 0, $white);

        // Barcode generation (simple Code 128 simulation)
        $thick = 3;
        $thin = 1;
        $posX = 10;

        // Start character
        $pattern = [
            '11011001100', // Start Code B
            '1', '0', '1', '1', '0', '0', '1', '1', '0', '0', '1', '1'
        ];

        foreach ($pattern as $bar) {
            $color = ($bar == '1') ? $black : $white;
            imagefilledrectangle($image, $posX, 10, $posX + ($bar == '1' ? $thick : $thin), $height - 20, $color);
            $posX += ($bar == '1' ? $thick : $thin);
        }

        // Add text below barcode
        $font = public_path('fonts/arial.ttf'); // Ensure you have this font
        imagettftext($image, 10, 0, $width/2 - 50, $height - 5, $black, $font, $code);

        // Output
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();

        // Free up memory
        imagedestroy($image);

        return 'data:image/png;base64,'.base64_encode($imageData);
    }
}
