<?php

  declare(strict_types=1);

	namespace Fawno\FPDF\Tests;

	use ddn\sapp\PDFDoc;
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

		public function assertPdfIsOk (FPDF $pdf) : void {
			$expected = file_get_contents($this->getExpectedFileName());
			$this->assertPdfAreEquals($expected, $pdf->Output('S'));
		}

		public function assertPdfAreEquals (string $expected, string $actual) : void {
			$doc_expected = PDFDoc::from_string($expected);
			$this->assertIsObject($doc_expected);

			$doc_actual = PDFDoc::from_string($actual);
			$this->assertIsObject($doc_actual);

			$differences = $doc_expected->compare($doc_actual);
			foreach ($differences as $oid => $obj) {
				foreach ($obj->get_keys() as $key) {
					$this->assertTrue(in_array($key, ['Producer', 'CreationDate']));
				}
			}
		}
	}
