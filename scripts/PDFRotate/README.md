# PDFRotateTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Olivier-blue)](mailto:oliver@fpdf.org?subject=Rotations)

This script allows to perform a rotation around a given center.

The rotation affects all elements which are printed after the method call (with the exception of clickable areas)

**Remarks:**
- Only the display is altered. The GetX() and GetY() methods are not affected, nor the automatic page break mechanism.
- Rotation is not kept from page to page. Each page begins with a null rotation.

## Usage
The method to set a rotation is:

```php
/**
 * Perform a rotation around a given center
 *
 * @param float $angle Angle in degrees.
 * @param float|int $x Abscissa of the rotation center. Default value: current position.
 * @param float|int $y Ordinate of the rotation center. Default value: current position.
 * @return void
 */
PDFRotateTrait::Rotate(float $angle [, float $x [, float $y]]);

/**
 * Prints a character string with rotation
 *
 * @param float $x Abscissa of the origin.
 * @param float $y Ordinate of the origin.
 * @param string $txt String to print.
 * @param float $angle Angle in degrees.
 * @return void
 */
PDFRotateTrait::RotatedText(float $x, float $y, string $txt, float $angle);

/**
 * Puts an image with rotation
 *
 * @param string $file Path or URL of the image.
 * @param float $x Abscissa of the upper-left corner.
 * @param float $y Ordinate of the upper-left corner.
 * @param float $w Width of the image in the page.
 * @param float $h Height of the image in the page.
 * @param float $angle Angle in degrees.
 * @return void
 */
PDFRotateTrait::RotatedImage (string $file, float $x, float $y, float $w, float $h, float $angle)
```

## Example

```php
<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/PDFRotateTrait.php';

use FPDF\Scripts\PDFRotate\PDFRotateTrait;

$pdf = new class extends FPDF {
  use PDFRotateTrait;
};

$pdf->AddPage();
$pdf->SetFont('Arial', '', 20);
$pdf->RotatedImage('circle.png', 85, 60, 40, 16, 45);
$pdf->RotatedText(100, 60, 'Hello!', 45);

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
