<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFRotate;
	//http://www.fpdf.org/en/script/script2.php

	trait PDFRotateTrait {
		/**
		 * Prints a character string with rotation
		 *
		 * @param float $x Abscissa of the origin.
		 * @param float $y Ordinate of the origin.
		 * @param string $txt String to print.
		 * @param float $angle Angle in degrees.
		 * @return void
		 */
		public function RotatedText (float $x, float $y, string $txt, float $angle) {
			//Text rotated around its origin
			$this->StartTransform();
			$this->Rotate($angle, $x, $y);
			$this->Text($x, $y, $txt);
			$this->StopTransform();
		}

		/**
		 * Puts an image with rotation
		 *
		 * @param string $file Path or URL of the image.
		 * @param float $x Abscissa of the upper-left corner.
		 * @param float $y Ordinate of the upper-left corner.
		 * @param float $w Width of the image in the page.
		 * @param float $h Height of the image in the page.
		 * @param float $angle Angle in degrees.
		 * @return void
		 */
		public function RotatedImage (string $file, float $x, float $y, float $w, float $h, float $angle) {
			//Image rotated around its upper-left corner
			$this->StartTransform();
			$this->Rotate($angle, $x, $y);
			$this->Image($file, $x, $y, $w, $h);
			$this->StopTransform();
		}
	}
