<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Traits;
	//http://www.fpdf.org/en/script/script44.php

	trait CMYKTrait {
		public function SetDrawColor($r = null, $g = null, $b = null) {
			$args = func_get_args();
			if (count($args) and is_array($args[0])) {
				$args = $args[0];
			}

			switch (count($args)) {
				case 1:
					$this->DrawColor = sprintf('%.3f G', $args[0] / 100);
					break;
				case 3:
					$this->DrawColor = sprintf('%.3f %.3f %.3f RG', $args[0] / 255, $args[1] / 255, $args[2] / 255);
					break;
				case 4:
					$this->DrawColor = sprintf('%.3f %.3f %.3f %.3f K', $args[0] / 100, $args[1] / 100, $args[2] / 100, $args[3] / 100);
					break;
				default:
					$this->DrawColor = '0 G';
			}

			if ($this->page > 0) {
				$this->_out($this->DrawColor);
			}
		}

		public function SetFillColor ($r = null, $g = null, $b = null) {
			$args = func_get_args();
			if (count($args) and is_array($args[0])) {
				$args = $args[0];
			}

			switch (count($args)) {
				case 1:
					$this->FillColor = sprintf('%.3f g', $args[0] / 100);
					break;
				case 3:
					$this->FillColor = sprintf('%.3f %.3f %.3f rg', $args[0] / 255, $args[1] / 255, $args[2] / 255);
					break;
				case 4:
					$this->FillColor = sprintf('%.3f %.3f %.3f %.3f k', $args[0] / 100, $args[1] / 100, $args[2] / 100, $args[3] / 100);
					break;
				default:
					$this->FillColor = '0 g';
			}

			$this->ColorFlag = ($this->FillColor != $this->TextColor);
			if ($this->page > 0) {
				$this->_out($this->FillColor);
			}
		}

		public function SetTextColor ($r = null, $g = null, $b = null) {
			//Set color for text
			$args = func_get_args();
			if (count($args) and is_array($args[0])) {
				$args = $args[0];
			}

			switch (count($args)) {
				case 1:
					$this->TextColor = sprintf('%.3f g', $args[0] / 100);
					break;
				case 3:
					$this->TextColor = sprintf('%.3f %.3f %.3f rg', $args[0] / 255, $args[1] / 255, $args[2] / 255);
					break;
				case 4:
					$this->TextColor = sprintf('%.3f %.3f %.3f %.3f k', $args[0] / 100, $args[1] / 100, $args[2] / 100, $args[3] / 100);
					break;
				default:
					$this->TextColor = '0 g';
			}

			$this->ColorFlag = ($this->FillColor != $this->TextColor);
		}
	}
