<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Traits;

	use Fawno\Barcode\EAN13;

	trait EAN13Trait {
		private float $ean13_margin;
		private float $ean13_height;
		private float $ean13_textheight;
		private float $ean13_barwidth;
		private float $ean13_barheight;
		private float $ean13_senheight;
		private float $ean13_ypos;
		private float $ean13_xpos;

		/**
		 * Seting vars for draw EAN13 barcode
		 *
		 * @param bool $box Draw border
		 * @return void
		 */
		private function _ean13_initialize_draw ($box = false) {
			$barwidth = 0.7;
			$height = $this->mm2Pt(12.5);

			$this->ean13_margin = $box ? $this->fromPt(2) : 0;
			$this->ean13_height = $this->fromPt($height);
			$this->ean13_textheight = 8;
			$this->ean13_barwidth = $this->fromPt($barwidth);
			$this->ean13_barheight = $this->fromPt($height - 8.733071);
			$this->ean13_senheight = $this->fromPt($height - 5.133071);
			$this->ean13_ypos = $this->fromPt(8);
			$this->ean13_xpos = 8;

			$this->SetFont('Helvetica', '', $this->ean13_textheight);
		}

		/**
		 * Set EAN13 barcode
		 *
		 * @param float $x Abscisse of uppper left corner of the barcode
		 * @param float $y Ordinate of uppper left corner of the barcode
		 * @param string $message Message of the barcode
		 * @param null|string $supplemental Optional supplemental code
		 * @param bool $box Draw border
		 * @param float $width Width of barcode. Default is 40mm
		 * @param float $height Height of barcode. Default is 12.5mm
		 * @param null|float $angle Rotation of barcode
		 * @return void
		 */
		public function BarcodeEAN13 (float $x, float $y, string $message, ?string $supplemental = null, bool $box = false, float $width = 0, float $height = 0, ?float $angle = null) : void {
			$ean13 = EAN13::create();
			$ean13->setMessage($message);
			$message = $ean13->getMessage();
			$ean13->setSupplemental($supplemental);
			$supplemental = $ean13->getSupplemental();

			if (!$message) {
				return;
			}

			$this->_ean13_initialize_draw($box);

			$barcount = $supplemental ? ((strlen($supplemental) == 2) ? 135 : 162) : 105;
			$ean13_width = $this->ean13_barwidth * $barcount + 2 * $this->ean13_margin;
			$width = $width ?: $this->ean13_barwidth * $barcount;
			$scalex = ($width > 0) ? 100 * $width / $ean13_width : 100;

			$ean13_height = $this->ean13_height + 2 * $this->ean13_margin;
			$height = $height ?: $this->ean13_height;
			$scaley = ($height > 0) ? 100 * $height / $ean13_height : 100;

			$this->StartTransform();
			$this->Scale($scalex, $scaley, (float) $x, (float) $y);
			$this->Rotate((float) $angle, (float) $x, (float) $y);
			$this->Translate((float) $x, (float) $y);

			if ($box) {
				$this->SetLineWidth($this->fromPt(0.5));
				$this->Rect(0, 0, $ean13_width, $ean13_height);
			}

			$this->SetLineWidth($this->ean13_barwidth);

			$this->_ean13_sentinel(0, '101');
			$this->_ean13_bars(3, $ean13->getBarcodeLeftHand());
			$this->_ean13_sentinel(45, '01010');
			$this->_ean13_bars(50, $ean13->getBarcodeRightHand());
			$this->_ean13_sentinel(92, '101');

			$this->_ean13_put_text(-1, $this->fromPt(7), 0, $message[0]);
			$this->_ean13_put_text(3, $this->fromPt(7), 42, substr($message, 1, 6));
			$this->_ean13_put_text(49, $this->fromPt(7), 42, substr($message, 7, 6));

			if (!$supplemental) {
				$this->StopTransform();
				return;
			}

			$this->_ean13_bars_supp(105, $ean13->getBarcodeSupplemental());
			$this->_ean13_put_text(105, $this->fromPt(6) - $this->ean13_barheight, strlen($ean13->getBarcodeSupplemental()), $supplemental);

			$this->StopTransform();
		}

		/**
		 * Draw sentinel bars
		 *
		 * @param int $pos_init Initial position in bars
		 * @param string $data Data to draw
		 * @return void
		 */
		private function _ean13_sentinel (int $pos_init, string $data) {
			$data = str_split($data);
			foreach ($data as $rpos => $val) {
				if ($val) {
					$x = ($this->ean13_xpos + $pos_init + $rpos) * $this->ean13_barwidth + $this->ean13_barwidth / 2 + $this->ean13_margin;
					$y0 = $this->ean13_height + $this->ean13_margin - $this->ean13_ypos - $this->ean13_barheight + $this->ean13_barwidth / 2;
					$y1 = $this->ean13_height + $this->ean13_margin - $this->ean13_ypos - $this->ean13_barheight - $this->ean13_barwidth / 2 + $this->ean13_senheight;
					$this->Line($x, $y0, $x, $y1);
				}
			}
		}

		/**
		 * Draw code bars
		 *
		 * @param int $pos_init Initial position in bars
		 * @param string $data Data to draw
		 * @return void
		 */
		private function _ean13_bars (int $pos_init, string $data) {
			$data = str_split($data);
			foreach ($data as $rpos => $val) {
				if ($val) {
					$x = ($this->ean13_xpos + $pos_init + $rpos) * $this->ean13_barwidth + $this->ean13_barwidth / 2 + $this->ean13_margin;
					$y0 = $this->ean13_height + $this->ean13_margin - $this->ean13_ypos - $this->ean13_barheight + $this->ean13_barwidth / 2;
					$y1 = $this->ean13_height + $this->ean13_margin - $this->ean13_ypos - $this->ean13_barwidth / 2;
					$this->Line($x, $y0, $x, $y1);
				}
			}
		}

		/**
		 * Draw supplemental code bars
		 *
		 * @param int $pos_init Initial position in bars
		 * @param string $data Data to draw
		 * @return void
		 */
		private function _ean13_bars_supp (int $pos_init, string $data) {
			$data = str_split($data);
			foreach ($data as $rpos => $val) {
				if ($val) {
					$x = ($this->ean13_xpos + $pos_init + $rpos) * $this->ean13_barwidth + $this->ean13_barwidth / 2 + $this->ean13_margin;
					$y0 = $this->ean13_height + $this->ean13_margin - $this->ean13_ypos - $this->ean13_barheight + $this->ean13_barwidth / 2 + $this->fromPt(7);
					$y1 = $this->ean13_height + $this->ean13_margin - $this->ean13_ypos - $this->ean13_barheight - $this->ean13_barwidth / 2 + $this->ean13_senheight;
					$this->Line($x, $y0, $x, $y1);
				}
			}
		}

		/**
		 * Set text in barcode
		 *
		 * @param int $pos_init Initial position in bars
		 * @param float $y Vertical position of text
		 * @param float $width Width of text box in bars
		 * @param string $string Text to write
		 * @return void
		 */
		private function _ean13_put_text (int $pos_init, float $y, float $width, string $string) {
			$offset = ($pos_init < 0) ? $this->GetStringWidth($string[0]) : 0;
			$len = strlen($string);
			$width *= $this->ean13_barwidth;
			$inc = ($width * ($len + 1) / $len - $this->GetStringWidth('0')) / ($this->ean13_barwidth * $len);
			$y += $this->ean13_height + $this->ean13_margin - $this->ean13_ypos;
			foreach (str_split($string) as $rpos => $char) {
				$x = ($this->ean13_xpos + $pos_init + $rpos * $inc + 1) * $this->ean13_barwidth + $this->ean13_margin - $offset;
				$this->Text($x, $y, $char);
			}
		}
	}
