<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFMemImage;

	use FPDF\Scripts\PDFMemImage\VariableStream;

	trait PDFMemImageTrait {
		function __construct($orientation= 'P', $unit = 'mm', $size = 'A4') {
			parent::__construct($orientation, $unit, $size);

			// Register var stream protocol
			stream_wrapper_register('var', VariableStream::class);
		}

		/**
		 * Puts an image contained in $data
		 *
		 * @param string $data
		 * @param null|float $x
		 * @param null|float $y
		 * @param float|int $w
		 * @param float|int $h
		 * @param string $link
		 * @return void
		 */
		function MemImage (string $data, ?float $x = null, ?float $y = null, float $w = 0, float $h = 0, $link = '') {
			$a = getimagesizefromstring($data);
			if (!$a) {
				$this->Error('Invalid image data');
			}

			$v = 'img' . md5($data);
			$GLOBALS[$v] = $data;

			$type = substr(strstr($a['mime'], '/'), 1);
			$this->Image('var://' . $v, $x, $y, $w, $h, $type, $link);

			unset($GLOBALS[$v]);
		}

		/**
		 * Puts an image contained in GDImage $im
		 *
		 * @param mixed $im
		 * @param null|float $x
		 * @param null|float $y
		 * @param float|int $w
		 * @param float|int $h
		 * @param string $link
		 * @return void
		 */
		function GDImage ($im, ?float $x = null, ?float $y = null, float $w = 0, float $h = 0, $link = '') {
			ob_start();
			imagepng($im);
			$data = ob_get_clean();
			$this->MemImage($data, $x, $y, $w, $h, $link);
		}
	}
