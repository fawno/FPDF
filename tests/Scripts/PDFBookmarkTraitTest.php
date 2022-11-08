<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFBookmark\PDFBookmarkTrait;
	use Fawno\FPDF\Tests\TestCase;

	class PDFBookmarkTraitTest extends TestCase {
		public function testPDFBookmarkTrait () {
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

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
