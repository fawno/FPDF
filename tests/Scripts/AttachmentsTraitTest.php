<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\Attachments\AttachmentsTrait;
	use Fawno\FPDF\Tests\TestCase;

	class AttachmentsTraitTest extends TestCase {
		public function testAttachmentsTrait () {
			$pdf = new class extends FPDF {
				use AttachmentsTrait;
			};

			$pdf->Attach(__DIR__ . '/../../scripts/Attachments/attached.txt');
			$pdf->OpenAttachmentPane();
			$pdf->AddPage();
			$pdf->SetFont('Arial','',14);
			$pdf->Write(5,'This PDF contains an attached file.');

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
