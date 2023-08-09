<?php
	declare(strict_types=1);

	namespace Fawno\FPDF\Traits;

	trait FontsTrait {
		/**
		 * Load a font definition file from the font directory
		 *
		 * @param mixed $filename
		 * @return array
		 */
		public function AddFont ($family, $style = '', $file = '', $dir = '') {
			if (is_file($file) and !$dir) {
				$dir = dirname($file);
				$file = basename($file);
			}

			parent::AddFont($family, $style, $file, $dir);
		}
	}
