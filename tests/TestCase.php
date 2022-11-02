<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests;

	use FPDF;
	use PHPUnit\Framework\TestCase as PHPUnitTestCase;
	use ReflectionClass;

	class TestCase extends PHPUnitTestCase {
		private function getExampleFileName() {
			$reflect = new ReflectionClass(get_class($this));

			return dirname($reflect->getFileName()) . DIRECTORY_SEPARATOR . 'example' . $reflect->getShortName() . '.pdf';
		}

		public function assertFileCanBeCreated (FPDF $pdf) {
			$filename = $this->getExampleFileName();

			$pdf->Output('F', $filename);

			$this->assertFileWasCreated($filename);
		}

		public function assertFileWasCreated (string $filename) {
			$this->assertFileExists($filename);

			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
