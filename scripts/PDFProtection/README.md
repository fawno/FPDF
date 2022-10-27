# PDFProtectionTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Klemen_Vodopivec-blue)](mailto:klemen@vodopivec.org?subject=Protection)
![Version](https://img.shields.io/badge/version-1.05-blue)
![Date](https://img.shields.io/badge/date-2018--03--19-blue)

This trait allows to protect the PDF, that is to say prevent people from copying its content, print it or modify it.

The protection against modification is for people who have the full Acrobat product.

If you don't set any password, the document will open as usual. If you set a user password, the PDF viewer will ask for it before displaying the document. The owner password, if different from the user one, can be used to get full access.

Note: protecting a document requires to encrypt it. If an encryption extension is available (OpenSSL or Mcrypt), it is used. Otherwise encryption is done in PHP, which can increase the processing time significantly (especially if the document contains images or fonts).

**Important:** some PDF readers like Firefox ignore the protection settings, which strongly reduces the usefulness of this script.

**Thanks:** [Cpdf](http://www.ros.co.nz/pdf) was my working sample of how to implement protection in pdf.

## Usage
The method to set protection is:

```php
/**
 * Set permissions as well as user and owner passwords
 *
 * @param array $permissions Array with values taken from the following list (if a value is present it means that the permission is granted):
 * - copy
 * - print
 * - modify
 * - annot-forms
 * @param string $user_pass If a user password is set, user will be prompted before document is opened
 * @param null|string $owner_pass If an owner password is set, document can be opened in privilege mode with no restriction if that password is entered
 * @return void
 */
PDFProtectionTrait::SetProtection([array $permissions [, ?string $user_pass [, ?string $owner_pass]]]);
```

The permission array contains values taken from the following list:
- ```copy```: copy text and images to the clipboard
- ```print```: print the document
- ```modify```: modify it (except for annotations and forms)
- ```annot-forms```: add annotations and forms

## Example

```php
<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/PDFProtectionTrait.php';

use FPDF\Scripts\PDFProtection\PDFProtectionTrait;

$pdf = new class extends FPDF {
  use PDFProtectionTrait;
};

$pdf->SetProtection(['print']);
$pdf->AddPage();
$pdf->SetFont('Arial');
$pdf->Write(10, 'You can print me but not copy my text.');

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
