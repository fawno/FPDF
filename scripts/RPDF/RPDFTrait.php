<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\RPDF;
	//http://www.fpdf.org/en/script/script31.php

	trait RPDFTrait {
		/**
		 * Print text in orthogonal direction
		 *
		 * @param float $x Abscissa of the origin.
		 * @param float $y Ordinate of the origin.
		 * @param string $txt String to print.
		 * @param string $direction One of the following values (R by default):
		 * - R (Right): Left to Right
		 * - U (Up): Bottom to Top
		 * - D (Down): Top To Bottom
		 * - L (Left): Right to Left
		 * @return void
		 */
		public function TextWithDirection (float $x, float $y, string $txt, string $direction = 'R') {
			$directions = [
				'R' => [  1,  0,  0,  1],
				'L' => [ -1,  0,  0, -1],
				'U' => [  0,  1, -1,  0],
				'D' => [  0, -1,  1,  0],
			];

			$params = ($directions[$direction] ?? [1, 0, 0, 1]) + [
				'x' => $x * $this->k,
				'y' => ($this->h - $y) * $this->k,
				'txt' => $this->_escape($txt),
			];

			$s = vsprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', $params);

			if ($this->ColorFlag) {
				$s = sprintf('q %s %s Q', $this->TextColor, $s);
			}

			$this->_out($s);
		}

		/**
		 * Print rotated and sheared (i.e. distorted like in italic) text
		 *
		 * @param float $x Abscissa of the origin.
		 * @param float $y Ordinate of the origin.
		 * @param string $txt String to print.
		 * @param float $txt_angle Text angle in degrees.
		 * @param float|int $font_angle Font angle in degrees.
		 * @return void
		 */
		public function TextWithRotation (float $x, float $y, string $txt, float $txt_angle, float $font_angle = 0) {
			$font_angle += 90 + $txt_angle;
			$txt_angle *= M_PI / 180;
			$font_angle *= M_PI / 180;

			$txt_dx = cos($txt_angle);
			$txt_dy = sin($txt_angle);
			$font_dx = cos($font_angle);
			$font_dy = sin($font_angle);

			$s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', $txt_dx, $txt_dy, $font_dx, $font_dy, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));

			if ($this->ColorFlag) {
				$s = sprintf('q %s %s Q', $this->TextColor, $s);
			}

			$this->_out($s);
		}
	}
