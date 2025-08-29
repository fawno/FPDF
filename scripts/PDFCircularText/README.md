# PDFCircularTextTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Andreas_Würmser-blue)](<mailto:fpdf@kreativschmiede.de?subject=Circular text>)

This trait aprints a circular text inside a given circle. It makes use of the [PDFTransformTrait](../PDFTransform).

## Requires

Require use of [PDFTransformTrait](../PDFTransform)

## Usage
The public methods are:

```php
/**
 * Prints a circular text inside a given circle.
 *
 * @param float $x abscissa of center
 * @param float $y ordinate of center
 * @param float $r radius of circle
 * @param string $text text to be printed
 * @param string $align text alignment: top or bottom. Default value: top
 * @param float $kerning spacing between letters in percentage.Default value: 120. Zero is not allowed.
 * @param float $fontwidth width of letters in percentage. Default value: 100. Zero is not allowed.
 * @param null|float $angle Rotate matrix for the first letter to center the text. Default (null) is half of total degrees.
 * @return void
 */
PDFCircularTextTrait::CircularText (float $x, float $y, float $r, string $text [, string $align = 'top' [, float $kerning = 120 [, float $fontwidth = 100 [, ?float $angle = null]]]]);

```

## Example

```php
<?php
declare(strict_types=1);

declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require dirname(__DIR__) . '/PDFTransform/PDFTransformTrait.php';
require __DIR__ . '/PDFCircularTextTrait.php';

use FPDF\Scripts\PDFTransform\PDFTransformTrait;
use FPDF\Scripts\PDFCircularText\PDFCircularTextTrait;

$pdf = new class extends FPDF {
  use PDFTransformTrait;
  use PDFCircularTextTrait;
};

$pdf->AddPage();
$pdf->SetFont('Arial', '', 32);

$text='Circular Text';
$pdf->CircularText(105, 50, 30, $text, 'top');
$pdf->CircularText(105, 50, 30, $text, 'bottom');

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
