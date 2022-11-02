# PDFBookmarkTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Roland_Gautier-blue)](mailto:dir@persolaser.com?subject=Code%20128%20barcodes)

This trait handles Code 128 barcodes (A, B and C).

All the 128 ASCII characters are available. A, B and C character sets are automatically selected according to the value to print.

## Usage
The method to add a Code 128 barcode is:

```php
/**
 * Encodage et dessin du code 128
 *
 * @param float $x Abscissa of upper-left corner
 * @param float $y Ordinate of upper-left corner
 * @param string $code Barcode value
 * @param float $w Width
 * @param float $h Height
 * @return void
 */
PDFCode128Trait::Code128 (float $x, float $y, string $code, float $w, float $h);
```

## Example

```php
<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/PDFCode128Trait.php';

use FPDF\Scripts\PDFCode128\PDFCode128Trait;


$pdf = new class extends FPDF {
  use PDFCode128Trait;
};


$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

//A set
$code = 'CODE 128';
$pdf->Code128(50, 20, $code, 80, 20);
$pdf->SetXY(50, 45);
$pdf->Write(5, 'A set: "' . $code . '"');

//B set
$code = 'Code 128';
$pdf->Code128(50, 70, $code, 80, 20);
$pdf->SetXY(50, 95);
$pdf->Write(5, 'B set: "' . $code . '"');

//C set
$code = '12345678901234567890';
$pdf->Code128(50, 120, $code, 110, 20);
$pdf->SetXY(50, 145);
$pdf->Write(5,'C set: "' . $code . '"');

//A,C,B sets
$code = 'ABCDEFG1234567890AbCdEf';
$pdf->Code128(50, 170, $code, 125, 20);
$pdf->SetXY(50, 195);
$pdf->Write(5, 'ABC sets combined: "' . $code . '"');

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
