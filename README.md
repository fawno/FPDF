[![GitHub Workflow](https://github.com/fawno/FPDF/actions/workflows/php.yml/badge.svg)](https://github.com/fawno/FPDF/actions/workflows/php.yml)
[![GitHub license](https://img.shields.io/github/license/fawno/FPDF)](https://github.com/fawno/FPDF/blob/master/LICENSE)
[![GitHub tag (latest SemVer)](https://img.shields.io/github/v/tag/fawno/FPDF)](https://github.com/fawno/FPDF/tags)
[![GitHub release](https://img.shields.io/github/release/fawno/FPDF)](https://github.com/fawno/FPDF/releases)
[![Packagist](https://img.shields.io/packagist/v/fawno/fpdf)](https://packagist.org/packages/fawno/fpdf)
[![Packagist Downloads](https://img.shields.io/packagist/dt/fawno/fpdf)](https://packagist.org/packages/fawno/fpdf/stats)
[![GitHub issues](https://img.shields.io/github/issues/fawno/FPDF)](https://github.com/fawno/FPDF/issues)
[![GitHub forks](https://img.shields.io/github/forks/fawno/FPDF)](https://github.com/fawno/FPDF/network)
[![GitHub stars](https://img.shields.io/github/stars/fawno/FPDF)](https://github.com/fawno/FPDF/stargazers)

# FPDF
## What is FPDF?
FPDF is a PHP class which allows to generate PDF files with pure PHP, that is to say without using the PDFlib library. F from FPDF stands for Free: you may use it for any kind of usage and modify it to suit your needs.

FPDF has other benefits: high level functions. Here is a list of its main features:

- Choice of measure unit, page format and margins
- Page header and footer management
- Automatic page break
- Automatic line break and text justification
- Image support (JPEG, PNG and GIF)
- Colors
- Links
- TrueType, Type1 and encoding support
- Page compression

FPDF requires no extension (except Zlib to enable compression and GD for GIF support). The latest version requires at least PHP 5.1 and is compatible with PHP 7 and PHP 8.

The [tutorials](http://fpdf.org/en/tutorial/index.php) will give you a quick start. The complete online documentation is [here](http://fpdf.org/en/doc/index.php) and download area is [there](http://fpdf.org/en/download.php). It is advised to read the [FAQ](http://fpdf.org/en/FAQ.php) which lists the most common questions and issues.

A [script](http://fpdf.org/en/script/index.php) section is available and provides some useful extensions (such as bookmarks, rotations, tables, barcodes...). Also, some of these scripts are delivered as traits with this package, you can check the list of available scripts [here](scripts).

## Installation

You can install the package via composer:

```sh
composer require fawno/fpdf
```

## What languages can I use?
The class can produce documents in many languages other than the Western European ones: Central European, Cyrillic, Greek, Baltic and [Thai](http://fpdf.org/en/script/script87.php), provided you own TrueType or Type1 fonts with the desired character set. [UTF-8 support](http://fpdf.org/en/script/script92.php) is also available.

## What about performance?
Of course, the generation speed of the document is less than with PDFlib. However, the performance penalty keeps very reasonable and suits in most cases, unless your documents are particularly complex or heavy.

For any remark, question or problem, you can leave a message on the [forum](http://fpdf.org/phorum/) (you don't need to register).

You can write to me [here](mailto:oliver@fpdf.org) (but please use the forum for basic questions).

## About this repository
The `/fpdf` directory contains a clone of the official FPDF releases, available at http://www.fpdf.org. No modifications will be made to that directory, which contains the history of changes between versions.

# FawnoFPDF Class
## What is FawnoFPDF?
FawnoFPDF is a wrapper FPDF class, FawnoFPDF already includes all the available scripts in the [scripts section](scripts). Also, it includes support for [Setasign/FPDI](https://github.com/Setasign/FPDI).

## Usage

In your php file that you want to use the class add a use statement.

```php
use Fawno\FPDF\FawnoFPDF;
```

Then use as per the [FPDF documentation](http://fpdf.org/en/tutorial/index.php).

``` php
$pdf = new FawnoFPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Hello World!');
$pdf->Output();
```

Alternatively you can extend as a typical php class and add your own custom scripts.

```php
class CustomPdf extends FawnoFPDF
{
    public function __construct(
        $orientation = 'P',
        $unit = 'mm',
        $size = 'letter'
    ) {
        parent::__construct( $orientation, $unit, $size );
        // ...
    }
}
```
