# RPDFTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Pivkin_Vladimir-blue)](mailto:boba@khspu.ru?subject=Text%20rotations)

This extension allows to print rotated and sheared (i.e. distorted like in italic) text.

## Usage
The method to print text with direction is:

```php
/**
 * Print text in orthogonal direction
 *
 * @param float $x Abscissa of the origin.
 * @param float $y Ordinate of the origin.
 * @param string $txt String to print.
 * @param string $direction One of the following values (R by default):
 * - R (Right): Left to Right
 * - U (Up): Bottom to Top
 * - D (Down): Top To Bottom
 * - L (Left): Right to Left
 * @return void
 */
RPDFTrait::TextWithDirection(float $x, float $y, string $txt [, string $direction]);

/**
 * Print rotated and sheared (i.e. distorted like in italic) text
 *
 * @param float $x Abscissa of the origin.
 * @param float $y Ordinate of the origin.
 * @param string $txt String to print.
 * @param float $txt_angle Text angle in degrees.
 * @param float|int $font_angle Font angle in degrees.
 * @return void
 */
RPDFTrait::TextWithRotation(float $x, float $y, string $txt, float $txt_angle, float $font_angle = 0);
```

## Example

```php
<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/RPDFTrait.php';

use FPDF\Scripts\RPDF\RPDFTrait;

$pdf = new class extends FPDF {
  use RPDFTrait;
};

$pdf->AddPage();
$pdf->SetFont('Arial', '' , 40);
$pdf->TextWithRotation(50, 65, 'Hello', 45, -45);
$pdf->SetFontSize(30);
$pdf->TextWithDirection(110, 50, 'world!', 'L');
$pdf->TextWithDirection(110, 50, 'world!', 'U');
$pdf->TextWithDirection(110, 50, 'world!', 'R');
$pdf->TextWithDirection(110, 50, 'world!', 'D');

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
