# PDFBookmarkTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Olivier-blue)](mailto:oliver@fpdf.org?subject=Bookmarks)

This trait adds bookmark support.

## Usage
The method to add a bookmark is:

```php
/**
 * Add a bookmark
 *
 * @param string $txt The bookmark title.
 * @param bool $isUTF8 Indicates if the title is encoded in ISO-8859-1 (false) or UTF-8 (true). Default value: false.
 * @param int $level The bookmark level (0 is top level, 1 is just below, and so on). Default value: 0.
 * @param float $y The y position of the bookmark destination in the current page. -1 means the current position. Default value: 0.
 * @return void
 */
PDFBookmarkTrait::Bookmark(string $txt [, bool $isUTF8 [, int $level [, float $y]]])
```

## Example

```php
<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/PDFBookmarkTrait.php';

use FPDF\Scripts\PDFBookmark\PDFBookmarkTrait;

$pdf = new class extends FPDF {
  use PDFBookmarkTrait;
};

$pdf->SetFont('Arial', '', 15);
// Page 1
$pdf->AddPage();
$pdf->Bookmark('Page 1', false);
$pdf->Bookmark('Paragraph 1', false, 1, -1);
$pdf->Cell(0, 6, 'Paragraph 1');
$pdf->Ln(50);
$pdf->Bookmark('Paragraph 2', false, 1, -1);
$pdf->Cell(0, 6, 'Paragraph 2');
// Page 2
$pdf->AddPage();
$pdf->Bookmark('Page 2', false);
$pdf->Bookmark('Paragraph 3', false, 1, -1);
$pdf->Cell(0, 6, 'Paragraph 3');

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
