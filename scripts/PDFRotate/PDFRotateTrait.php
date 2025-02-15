<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFRotate;
	//http://www.fpdf.org/en/script/script2.php

	trait PDFRotateTrait {
		protected $angle = 0;

		/**
		 * Perform a rotation around a given center
		 *
		 * @param float $angle Angle in degrees.
		 * @param float|int $x Abscissa of the rotation center. Default value: current position.
		 * @param float|int $y Ordinate of the rotation center. Default value: current position.
		 * @return void
		 */
		public function Rotate (float $angle, ?float $x = null, ?float $y = null) {
			$x = $x ?? $this->x;
			$y = $y ?? $this->x;

			if ($this->angle != 0) {
				$this->_out('Q');
			}

			$this->angle = $angle;

			if ($angle != 0) {
				$angle *= M_PI / 180;
				$c = cos($angle);
				$s = sin($angle);
				$cx = $x * $this->k;
				$cy = ($this->h - $y) * $this->k;
				$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
			}
		}

		protected function _endpage () {
			if ($this->angle != 0) {
				$this->angle = 0;
				$this->_out('Q');
			}

			parent::_endpage();
		}

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
			$this->Rotate($angle, $x, $y);
			$this->Text($x, $y, $txt);
			$this->Rotate(0);
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
			$this->Rotate($angle, $x, $y);
			$this->Image($file, $x, $y, $w, $h);
			$this->Rotate(0);
		}
	}
