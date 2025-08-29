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
		 * @param null|float $angle Rotate matrix for the first letter to center the text. Default (null) is half of total degrees.
		 * @return void
		 */
		public function CircularText (float $x, float $y, float $r, string $text, string $align = 'top', float $kerning = 120, float $fontwidth = 100, ?float $angle = null) : void {
			$align = ($align == 'top') ? -1 : 1;
			$kerning /= 100;
			$fontwidth /= 100;

			if ($kerning == 0) {
				$this->Error('Please use values unequal to zero for kerning');
			}

			if ($fontwidth == 0) {
				$this->Error('Please use values unequal to zero for font width');
			}

			// (angle / width) / 2
			$angle_width = ($align * 90) / ($r * M_PI);

			$total_width = $this->GetStringWidth($text) * $kerning * $fontwidth;

			// Angle for text beginning, default half of total degrees
			$angle = $angle ?? (-$total_width * $angle_width);

			$yr = ($align < 0) ? ($y - $r) : ($y + $r - $this->FontSize);

			$this->StartTransform();

			// Rotate matrix for the first letter to center the text
			$this->Rotate($angle, $x, $y);

			$prev_width = 0;

			// Run through the string
			foreach (str_split($text) as $char) {
				// Current letter width
				$char_width = $this->GetStringWidth($char) * $kerning * $fontwidth;

				// Rotate matrix half of the width of current letter + half of the width of preceding letter
				$this->Rotate($angle_width * ($char_width + $prev_width), $x, $y);

				if ($fontwidth != 1) {
					$this->StartTransform();
					$this->ScaleX($fontwidth * 100, $x, $y);
				}

				$this->SetXY($x - ($char_width / 2), $yr);
				$this->Cell($char_width, $this->FontSize, $char, 0, 0, 'C');

				if ($fontwidth != 1) {
					$this->StopTransform();
				}

				// Store current letter width for next letter
				$prev_width = $char_width;
			}

			$this->StopTransform();
		}
	}
