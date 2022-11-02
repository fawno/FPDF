<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFMultiCellsTable;
	//http://www.fpdf.org/en/script/script3.php

	trait PDFMultiCellsTableTrait {
		protected $widths;
		protected $aligns;

		/**
		 * Set the array of column widths
		 *
		 * @param array $w
		 * @return void
		 */
		public function SetWidths (array $w) : void {
			$this->widths = $w;
		}

		/**
		 * Set the array of column alignments
		 *
		 * @param array $a
		 * @return void
		 */
		public function SetAligns (array $a) : void {
			$this->aligns = $a;
		}

		/**
		 * Draw the cells of the row
		 *
		 * @param array $data
		 * @return void
		 */
		public function Row (array $data) : void {
			//Calculate the height of the row
			$nb = 0;
			for ($i = 0; $i < count($data); $i++) {
				$nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
			}

			$h = 5 * $nb;

			//Issue a page break first if needed
			$this->CheckPageBreak($h);

			//Draw the cells of the row
			for ($i = 0; $i < count($data); $i++) {
				$w = $this->widths[$i];
				$a = $this->aligns[$i] ?? 'L';

				//Save the current position
				$x = $this->GetX();
				$y = $this->GetY();

				//Draw the border
				$this->Rect($x, $y, $w, $h);

				//Print the text
				$this->MultiCell($w, 5, $data[$i], 0, $a);

				//Put the position to the right of the cell
				$this->SetXY($x + $w, $y);
			}

			//Go to the next line
			$this->Ln($h);
		}

		/**
		 * If the height h would cause an overflow, add a new page immediately
		 *
		 * @param float $h
		 * @return void
		 */
		public function CheckPageBreak (float $h) : void {
			if ($this->GetY() + $h > $this->PageBreakTrigger) {
				$this->AddPage($this->CurOrientation);
			}
		}

		/**
		 * Computes the number of lines a MultiCell of width w will take
		 *
		 * @param float $w
		 * @param string $txt
		 * @return int
		 */
		public function NbLines (float $w, string $txt) : int {
			$cw = &$this->CurrentFont['cw'];
			if ($w==0) {
				$w = $this->w - $this->rMargin - $this->x;
			}

			$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
			$s = str_replace("\r", '', $txt);
			$nb = strlen($s);
			if ($nb > 0 and $s[$nb-1] == "\n") {
				$nb--;
			}
			$sep = -1;
			$i = 0;
			$j = 0;
			$l = 0;
			$nl = 1;
			while ($i < $nb) {
				$c = $s[$i];
				if ($c == "\n") {
					$i++;
					$sep = -1;
					$j = $i;
					$l = 0;
					$nl++;
					continue;
				}

				if ($c == ' ') {
					$sep = $i;
				}

				$l += $cw[$c];
				if ($l > $wmax) {
					if ($sep == -1) {
						if ($i == $j) {
							$i++;
						}
					} else {
						$i = $sep + 1;
					}

					$sep = -1;
					$j = $i;
					$l = 0;
					$nl++;
				} else {
					$i++;
				}
			}

			return $nl;
		}
	}
