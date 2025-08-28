<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFCircularText;
	//https://www.fpdf.org/en/script/script82.php

	trait PDFCircularTextTrait {
		/**
		 * Prints a circular text inside a given circle.
		 *
		 * @param float $x abscissa of center
		 * @param float $y ordinate of center
		 * @param float $r radius of circle
		 * @param string $text text to be printed
		 * @param string $align text alignment: top or bottom. Default value: top
		 * @param float $kerning spacing between letters in percentage.Default value: 120. Zero is not allowed.
		 * @param float $fontwidth width of letters in percentage. Default value: 100. Zero is not allowed.
		 * @return void
		 */
		public function CircularText (float $x, float $y, float $r, string $text, string $align = 'top', float $kerning = 120, float $fontwidth = 100) : void {
			$align = ($align == 'top') ? -1 : 1;
			$kerning /= 100;
			$fontwidth /= 100;

			if ($kerning == 0) {
				$this->Error('Please use values unequal to zero for kerning');
			}

			if ($fontwidth == 0) {
				$this->Error('Please use values unequal to zero for font width');
			}

			//get width of every letter
			$w = [-1 => 0];
			$t = 0;
			for ($i = 0; $i < strlen($text); $i++) {
				$w[$i] = $this->GetStringWidth($text[$i]);
				$w[$i] *= $kerning * $fontwidth;
				//total width of string
				$t += $w[$i];
			}

			//circumference
			$u = ($r * 2) * M_PI;

			//total width of string in degrees
			$d = ($t / $u) * 360;

			$this->StartTransform();

			// rotate matrix for the first letter to center the text
			// (half of total degrees)
			$this->Rotate($align * (-$d / 2), $x, $y);

			//run through the string
			for($i = 0; $i < strlen($text); $i++) {
				//rotate matrix half of the width of current letter + half of the width of preceding letter
				$this->Rotate($align * (($w[$i] / 2 + $w[$i - 1] / 2) / $u) * 360, $x, $y);

				if ($fontwidth != 1) {
					$this->StartTransform();
					$this->ScaleX($fontwidth * 100, $x, $y);
				}

				$this->SetXY($x - $w[$i] / 2, $y + $align * ($r - ($this->FontSize * ((1 + $align) / 2))));

				$this->Cell($w[$i], $this->FontSize, $text[$i], 0, 0, 'C');

				if ($fontwidth != 1) {
					$this->StopTransform();
				}
			}

			$this->StopTransform();
		}
	}
