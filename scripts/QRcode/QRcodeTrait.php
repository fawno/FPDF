<?php

declare(strict_types=1);

namespace FPDF\Scripts\QRcode;

trait QRcodeTrait {
    /**
     * QR Code Drawing
     *
     * @param float $x Abscissa of upper-left corner
     * @param float $y Ordinate of upper-left corner
     * @param float $w Width
     * @param string $value QR Code data
     * @param string $level Error level: L, M, Q, H
     * @param array $background background color (R,V,B)
     * @param array $color boxes and border color (R,V,B)
     */
    public function QRcode ($x, $y, $w, $value, $level = 'L', $background = array(255,255,255), $color = array(0,0,0)) : void {
        $qrcode = new QRcode($value, $level);
        $qrcode->displayFPDF($this, $x, $y, $w, $background, $color);
    }
}
