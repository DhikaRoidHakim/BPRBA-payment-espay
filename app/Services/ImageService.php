<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    public function generateVaImage($order_id, $va_number)
    {
        $manager = new ImageManager(new Driver());
        $width = 900;
        $height = 500;


        $img = $manager->create($width, $height)->fill('#f8f9fa');


        $img->drawRectangle(0, 0, function ($draw) use ($width) {
            $draw->size($width, 80);
            $draw->background('#1e3a8a');
        });


        $img->text('VIRTUAL ACCOUNT', $width / 2, 40, function ($font) {
            $font->size(32);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });


        $logoPath = public_path('images/logo_polos.png');
        if (file_exists($logoPath)) {
            $logo = $manager->read($logoPath)->scale(80, 80);
            $img->place($logo, 'top', 0, 100);
        }


        $cardX = 50;
        $cardY = 200;
        $cardWidth = $width - 100;
        $cardHeight = 300;


        $img->drawRectangle($cardX + 5, $cardY + 5, function ($draw) use ($cardWidth, $cardHeight) {
            $draw->size($cardWidth, $cardHeight);
            $draw->background('#e5e7eb');
        });

        $img->drawRectangle($cardX, $cardY, function ($draw) use ($cardWidth, $cardHeight) {
            $draw->size($cardWidth, $cardHeight);
            $draw->background('#ffffff');
        });


        $img->drawRectangle($cardX, $cardY, function ($draw) use ($cardWidth, $cardHeight) {
            $draw->size($cardWidth, $cardHeight);
            $draw->border('#cbd5e1', 2);
        });


        $img->drawRectangle($cardX, $cardY, function ($draw) use ($cardWidth) {
            $draw->size($cardWidth, 5);
            $draw->background('#3b82f6');
        });


        $img->text('Nomor rekening tabungan BPR Bangunarta', $cardX + 40, $cardY + 60, function ($font) {
            $font->size(18);
            $font->color('#64748b');
            $font->align('left');
        });


        $img->text('01' . $order_id, $cardX + 40, $cardY + 95, function ($font) {
            $font->size(26);
            $font->color('#1e293b');
            $font->align('left');
        });


        $img->drawRectangle($cardX + 40, $cardY + 130, function ($draw) use ($cardWidth) {
            $draw->size($cardWidth - 80, 1);
            $draw->background('#e2e8f0');
        });


        $img->text('Nomor Virtual Account', $cardX + 40, $cardY + 170, function ($font) {
            $font->size(18);
            $font->color('#64748b');
            $font->align('left');
        });


        $vaBoxX = $cardX + 40;
        $vaBoxY = $cardY + 195;
        $img->drawRectangle($vaBoxX, $vaBoxY, function ($draw) use ($cardWidth) {
            $draw->size($cardWidth - 80, 55);
            $draw->background('#eff6ff');
        });

        $img->drawRectangle($vaBoxX, $vaBoxY, function ($draw) use ($cardWidth) {
            $draw->size($cardWidth - 80, 55);
            $draw->border('#3b82f6', 2);
        });


        $formattedVA = chunk_split($va_number, 4, ' ');
        $img->text(trim($formattedVA), $vaBoxX + ($cardWidth - 80) / 2, $vaBoxY + 28, function ($font) {
            $font->size(32);
            $font->color('#1e40af');
            $font->align('center');
            $font->valign('middle');
        });


        $img->text('Gunakan nomor virtual account di atas untuk melakukan pembayaran', $width / 2, $height - 30, function ($font) {
            $font->size(14);
            $font->color('#64748b');
            $font->align('center');
        });


        $path = public_path('images/va/' . $va_number . '.png');
        $img->save($path, quality: 95);

        return $path;
    }
}
