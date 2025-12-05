# ESignaturePlaceholderTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-ErikN-blue)](https://github.com/erikn69)

This trait handles e-Signature Placeholders

## Usage
The method to add empty placeholder for e-signature:

```php
/**
 * Add a e-signature placeholder
 *
 * @param float $x Abscissa of upper-left corner
 * @param float $y Ordinate of upper-left corner
 * @param float $w Width
 * @param float $h Height
 * @param string $name Name of pdf object
 */
function AddSignatureField(float $x, float $y, float $w, float $h, string $name = '');;
```

## Example

```php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/ESignaturePlaceholderTrait.php';

use FPDF\Scripts\ESignaturePlaceholder\ESignaturePlaceholderTrait;

$pdf = new class extends FPDF {
    use ESignaturePlaceholderTrait;
};

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->Text(20, 10, 'First Sign Here:');
// add placeholder with name `SignPlaceholder1` on page 1
$pdf->AddSignatureField(20, 15, 120, 40, 'SignPlaceholder1');

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->Text(20, 10, 'Second Sign Here:');
// add placeholder with default name on page 2
$pdf->AddSignatureField(20, 15, 120, 40);

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
