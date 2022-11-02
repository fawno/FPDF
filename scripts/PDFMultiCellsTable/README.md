# PDFMultiCellsTableTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Olivier-blue)](mailto:oliver@fpdf.org?subject=Table%20with%20MultiCells)

This trait is to show how to build a table from MultiCells.

As MultiCells go to the next line after being output, the base idea consists in saving the current position, printing the MultiCell and resetting the position to its right.

There is a difficulty, however, if the table is too long: page breaks. Before outputting a row, it is necessary to know whether it will cause a break or not. If it does overflow, a manual page break must be done first.

To do so, the height of the row must be known in advance; it is the maximum of the heights of the MultiCells it is made up of.

To know the height of a MultiCell, the NbLines() method is used: it returns the number of lines a MultiCell will occupy.

## Usage
Public methods:

```php
/**
 * Set the array of column widths
 *
 * @param array $w
 * @return void
 */
PDFMultiCellsTableTrait::SetWidths(array $w);

/**
 * Set the array of column alignments
 *
 * @param array $a
 * @return void
 */
PDFMultiCellsTableTrait::SetAligns(array $a);

/**
 * Draw the cells of the row
 *
 * @param array $data
 * @return void
 */
PDFMultiCellsTableTrait::Row (array $data);
```

## Example

```php
<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/PDFMultiCellsTableTrait.php';

use FPDF\Scripts\PDFMultiCellsTable\PDFMultiCellsTableTrait;

$pdf = new class extends FPDF {
  use PDFMultiCellsTableTrait;
};

$pdf->AddPage();
$pdf->SetFont('Arial', '', 14);

//Table with 20 rows and 4 columns
$filename = __DIR__ . '/table.json';
$table = json_decode(file_get_contents($filename), true);

$pdf->SetWidths([30, 50, 30, 40]);
foreach ($table as $row) {
  $pdf->Row($row);
}

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
