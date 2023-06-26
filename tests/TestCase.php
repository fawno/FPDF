<?php

  declare(strict_types=1);

	namespace Fawno\FPDF\Tests;

	use ddn\sapp\PDFDoc;
	use ddn\sapp\pdfvalue\PDFValue;
	use FPDF;
	use PHPUnit\Framework\TestCase as PHPUnitTestCase;
	use ReflectionClass;

	class TestCase extends PHPUnitTestCase {
		private function getExampleFileName () : string {
			$reflect = new ReflectionClass(get_class($this));

			return dirname($reflect->getFileName()) . DIRECTORY_SEPARATOR . 'example' . $reflect->getShortName() . '.pdf';
		}

		private function getExpectedFileName () : string {
			$reflect = new ReflectionClass(get_class($this));

			return __DIR__ . DIRECTORY_SEPARATOR . 'examples' . DIRECTORY_SEPARATOR . 'example' . $reflect->getShortName() . '.pdf';
		}

		public function assertFileCanBeCreated (FPDF $pdf, string $message = '') : void {
			$filename = $this->getExampleFileName();

			$pdf->Output('F', $filename);

			$this->assertFileWasCreated($filename, $message);
		}

		public function assertFileWasCreated (string $filename, string $message = '') : void {
			$this->assertFileExists($filename, $message);

			if (is_file($filename)) {
				unlink($filename);
			}
		}

		private static function rebuildPdf(string $content){
			$real_content = \ctype_print($content) && @\file_exists($content)
				? \file_get_contents(realpath($content)) : $content;

			$pdf = PDFDoc::from_string($real_content);

			if ($pdf === false) {
				return null;
			}

			return PDFDoc::from_string($pdf->to_pdf_file_s(true));
		}

		public function assertPdfIsOk (FPDF $pdf) : void {
			$expected = file_get_contents($this->getExpectedFileName());
			$this->assertPdfAreEquals($expected, $pdf->Output('S'));
		}

		public function assertPdfAreEquals (string $expected, string $actual) : void {
			$doc_expected = self::rebuildPdf($expected);
			$this->assertIsObject($doc_expected, 'The expected file can\'t be parsed as PDF.');

			$doc_actual = self::rebuildPdf($actual);
			$this->assertIsObject($doc_actual, 'The actual file can\'t be parsed as PDF.');

			$differences = $doc_expected->compare($doc_actual);

			$diff = [];
			foreach ($differences as $oid => $obj) {
				$keys = (is_a($obj->get_value(), PDFValue::class) ? $obj->get_keys() : false) ?: ['OID_' . $obj->get_oid()];
				$diff = array_merge($diff, array_diff($keys, ['Producer', 'CreationDate', 'Title', 'O', 'U']));
			}

			$this->assertEquals([], $diff, 'The PDFs contents have differences.');
		}
	}
