<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFMultiCellsTable\PDFMultiCellsTableTrait;
	use PHPUnit\Framework\TestCase;

	class PDFMultiCellsTableTraitTest extends TestCase {
		public function testPDFMultiCellsTableTrait () {
			$pdf = new class extends FPDF {
				use PDFMultiCellsTableTrait;
			};

			$pdf->AddPage();
			$pdf->SetFont('Arial', '', 14);

			//Table with 20 rows and 4 columns
			$filename = dirname(dirname(__DIR__)) . '/scripts/PDFMultiCellsTable/table.json';
			$table = json_decode(file_get_contents($filename), true);

			$pdf->SetWidths([30, 50, 30, 40]);
			foreach ($table as $row) {
				$pdf->Row($row);
			}

			$filename = __DIR__ . '/example' . basename(__CLASS__) . '.pdf';
			$pdf->Output('F', $filename);

			$this->assertFileExists($filename);

			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
