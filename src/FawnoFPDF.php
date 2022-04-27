<?php
	declare(strict_types=1);

	namespace Fawno\FPDF;

	use Fawno\FPDF\Traits\CMYKTrait;
	use Fawno\FPDF\Traits\PDFDrawTrait;
	use Fawno\FPDF\Traits\RPDFTrait;
	use Fawno\FPDF\PDFWrapper;

	class FawnoFPDF extends PDFWrapper {
		use CMYKTrait;
		use PDFDrawTrait;
		use RPDFTrait;
	}
