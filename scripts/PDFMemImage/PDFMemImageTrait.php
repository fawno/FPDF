<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFMemImage;
	//http://www.fpdf.org/en/script/script45.php

	use FPDF\Scripts\PDFMemImage\VariableStream;

	trait PDFMemImageTrait {
		private static $memImageInitialized;

		/**
		 * Register var stream protocol
		 *
		 * @return void
		 */
		private function register_var_stream_wrapper () : void {
			if (!self::$memImageInitialized && !in_array('var', stream_get_wrappers())) {
				stream_wrapper_register('var', VariableStream::class);
			}

			self::$memImageInitialized = true;
		}

		/**
		 * Checks if $string is Base64 encoded string
		 *
		 * @param string $string
		 * @return bool
		 */
		private function is_base64 (string $string) : bool {
			// Check if there are valid base64 characters
			if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
				return false;
			}

			// Decode the string in strict mode and check the results
			$decoded = base64_decode($string, true);
			if (false === $decoded) {
				return false;
			}

			// Encode the string again
			if (base64_encode($decoded) != $string) {
				return false;
			}

			return true;
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
		public function MemImage (string $data, ?float $x = null, ?float $y = null, float $w = 0, float $h = 0, $link = '') {
			if ($this->is_base64($data)) {
			  $data = base64_decode($data);
			}

			$a = getimagesizefromstring($data);
			if (!$a) {
				$this->Error('Invalid image data');
			}

			$v = 'img' . md5($data);
			$GLOBALS[$v] = $data;

			$type = substr(strstr($a['mime'], '/'), 1);
			$this->register_var_stream_wrapper();
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
		public function GDImage ($im, ?float $x = null, ?float $y = null, float $w = 0, float $h = 0, $link = '') {
			ob_start();
			imagepng($im);
			$data = ob_get_clean();
			$this->MemImage($data, $x, $y, $w, $h, $link);
		}
	}
