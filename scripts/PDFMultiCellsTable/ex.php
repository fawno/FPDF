<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require __DIR__ . '/PDFMultiCellsTableTrait.php';

	use FPDF\Scripts\PDFMultiCellsTable\PDFMultiCellsTableTrait;

	/**
	 * Get a random word
	 *
	 * @return string
	 */
	function GenerateWord () : string {
		$nb = rand(3,10);
		$word = '';

		while (strlen($word) <= $nb) {
			$word .= chr(rand(ord('a'), ord('z')));
		}

		return $word;
	}

	/**
	 * Get a random sentence
	 *
	 * @return string
	 */
	function GenerateSentence () : string {
		$nw = rand(1, 10);
		$words = [];

		while (count($words) < $nw) {
			$words[] = GenerateWord();
		}

		return implode(' ', $words);
	}

	/**
	 * Generate a table of random sentences
	 *
	 * @param int $cols
	 * @param int $rows
	 * @return array
	 */
	function GenerateTable (int $cols, int $rows) : array {
		$table = [];

		while (count($table) < $rows) {
			$row = [];

			while (count($row) < $cols) {
				$row[] = GenerateSentence();
			}

			$table[] = $row;
		}

		return $table;
	}

	$pdf = new class extends FPDF {
		use PDFMultiCellsTableTrait;
	};

	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 14);

	//Table with 20 rows and 4 columns
	$filename = __DIR__ . '/table.json';
	if (!is_file($filename)) {
		srand((int) (microtime(true) * 1000000));
		$table = GenerateTable(4, 20);
		file_put_contents($filename, json_encode($table));
	} else {
		$table = json_decode(file_get_contents($filename), true);
	}

	$pdf->SetWidths([30, 50, 30, 40]);
	foreach ($table as $row) {
		$pdf->Row($row);
	}

	$pdf->Output('F', __DIR__ . '/example.pdf');
