# QRcodeTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Laurent_MINGUET-blue)](mailto:webmaster@spipu.net?subject=QRcode%20support)

This trait handles QR Codes

## Usage
The method to add a QR Code is:

```php
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
 * @return void
 */
QRcodeTrait::QRcode (float $x, float $y, float $w, string $value, string $level, array $background, array $color);
```

## Example

```php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/QRcodeTrait.php';
require __DIR__ . '/QRcode.php';

use FPDF\Scripts\QRcode\QRcodeTrait;

$pdf = new class extends FPDF {
    use QRcodeTrait;
};

$pdf->AddPage();

$pdf->QRcode(5, 5, 50, 'Generated QR Code Data');

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)

## Raw Usage
It can also be used to generate the QR code in HTML and PNG formats

**PNG**
```php
require __DIR__ . '/QRcode.php';

use FPDF\Scripts\QRcode\QRcode;

$qrcode = new QRcode('your message here', 'H');
$qrcode->displayPNG(300);
exit;
```

**HTML**
```php
require __DIR__ . '/QRcode.php';

use FPDF\Scripts\QRcode\QRcode;

$qrcode = new QRcode('your message here', 'H');
$qrcode->displayHTML();

/*
It needs css like:
table.qr td.on {
  background-color: black;
}
table.qr td {
  height: 10px;
  width: 10px;
}
*/
exit;
```