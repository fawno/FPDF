# FPDFATrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Olivier-blue)](mailto:oliver@fpdf.org?subject=PDF%2FA%20support)

This trait adds support for the [PDF/A-3b](https://en.wikipedia.org/wiki/PDF/A) standard.

There are some rules to follow. The most important one is that all fonts must be embedded (which means that you can't use the standard fonts).

You can check the compliance of your files with a validator such as [verapdf.org](https://demo.verapdf.org/).

## Usage

```php
```

## Example

```php
<?php
declare(strict_types=1);

define('FPDF_FONTPATH', __DIR__);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/FPDFATrait.php';

use FPDF\Scripts\FPDFA\FPDFATrait;

$pdf = new class extends FPDF {
  use FPDFATrait;
};

$pdf->AddFont('DejaVuSansCondensed', '', 'DejaVuSansCondensed.php');
$pdf->SetFont('DejaVuSansCondensed', '', 16);
$pdf->AddPage();
$pdf->Write(10, 'This PDF is PDF/A-3b compliant.');

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
