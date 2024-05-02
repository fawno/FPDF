# AttachmentsTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Olivier-blue)](mailto:oliver@fpdf.org?subject=Bookmarks)

This script allows to attach files to the PDF.

## Usage
The method to attach a file is:

```php
/**
 * Add a attachment
 *
 * @param string $file path to the file to attach.
 * @param string $name the name under which the file will be attached. The default value is taken from file.
 * @param string $desc an optional description.
 * @return void
 */
AttachmentsTrait::Attach(string file [, string name [, string desc]]);
```

The `OpenAttachmentPane()` method is also provided to force the PDF viewer to open the attachment pane when the document is loaded.

## Example

```php
<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/AttachmentsTrait.php';

use FPDF\Scripts\Attachments\AttachmentsTrait;

$pdf = new class extends FPDF {
  use AttachmentsTrait;
};

$pdf->Attach('attached.txt');
$pdf->OpenAttachmentPane();
$pdf->AddPage();
$pdf->SetFont('Arial','',14);
$pdf->Write(5,'This PDF contains an attached file.');

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
