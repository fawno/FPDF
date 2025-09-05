<?php
	declare(strict_types=1);

	namespace Fawno\Barcode;

	use Fawno\Barcode\BarcodeException;

	class EAN13 {
		// EAN Parity Encodig Table => EAN_pet
		protected const EAN_pet = [
			0 => ['O', 'O', 'O', 'O', 'O', 'O'],
			1 => ['O', 'O', 'E', 'O', 'E', 'E'],
			2 => ['O', 'O', 'E', 'E', 'O', 'E'],
			3 => ['O', 'O', 'E', 'E', 'E', 'O'],
			4 => ['O', 'E', 'O', 'O', 'E', 'E'],
			5 => ['O', 'E', 'E', 'O', 'O', 'E'],
			6 => ['O', 'E', 'E', 'E', 'O', 'O'],
			7 => ['O', 'E', 'E', 'E', 'O', 'E'],
			8 => ['O', 'E', 'O', 'E', 'E', 'O'],
			9 => ['O', 'E', 'E', 'O', 'E', 'O'],
		];

		// EAN Character Set Encoding Table => EAN_cet
		protected const EAN_cet = [
			0 => ['O' => '0001101', 'E' => '0100111', 'R' => '1110010'],
			1 => ['O' => '0011001', 'E' => '0110011', 'R' => '1100110'],
			2 => ['O' => '0010011', 'E' => '0011011', 'R' => '1101100'],
			3 => ['O' => '0111101', 'E' => '0100001', 'R' => '1000010'],
			4 => ['O' => '0100011', 'E' => '0011101', 'R' => '1011100'],
			5 => ['O' => '0110001', 'E' => '0111001', 'R' => '1001110'],
			6 => ['O' => '0101111', 'E' => '0000101', 'R' => '1010000'],
			7 => ['O' => '0111011', 'E' => '0010001', 'R' => '1000100'],
			8 => ['O' => '0110111', 'E' => '0001001', 'R' => '1001000'],
			9 => ['O' => '0001011', 'E' => '0010111', 'R' => '1110100'],
		];

		// UPC 2-Digit Parity Pattern => UPC_2dpp
		protected const UPC_2dpp = [
			0 => ['O', 'O'],
			1 => ['O', 'E'],
			2 => ['E', 'O'],
			3 => ['E', 'E'],
		];

		// UPC 5-Digit Parity Pattern => UPC_5dpp
		protected const UPC_5dpp = [
			0 => ['E', 'E', 'O', 'O', 'O'],
			1 => ['E', 'O', 'E', 'O', 'O'],
			2 => ['E', 'O', 'O', 'E', 'O'],
			3 => ['E', 'O', 'O', 'O', 'E'],
			4 => ['O', 'E', 'E', 'O', 'O'],
			5 => ['O', 'O', 'E', 'E', 'O'],
			6 => ['O', 'O', 'O', 'E', 'E'],
			7 => ['O', 'E', 'O', 'E', 'O'],
			8 => ['O', 'E', 'O', 'O', 'E'],
			9 => ['O', 'O', 'E', 'O', 'E'],
		];

		protected string $message;
		protected string $supplemental;

		/**
		 * Construct EAN13 with message and supplemental.
		 *
		 * @param null|string $message Message of EAN13 code
		 * @param null|string $supplemental Supplemental code for EAN13
		 * @return void
		 * @throws BarcodeException If message or supplemental has invalid length BarcodeException is thrown.
		 */
		private function __construct (?string $message = null, ?string $supplemental = null) {
			if (is_null($this->setMessage($message))) {
				throw new BarcodeException('Message invalid', 1);
			}

			if (is_null($this->setSupplemental($supplemental))) {
				throw new BarcodeException('Supplemental invalid', 1);
			}
		}

		/**
		 * Create EAN13 code with message and optional supplemental code.
		 *
		 * @param null|string $message Message of EAN13
		 * @param null|string $supplemental Supplemental code
		 * @return EAN13
		 * @throws BarcodeException If message or supplemental has invalid length BarcodeException is thrown.
		 */
		public static function create (?string $message = null, ?string $supplemental = null) : EAN13 {
			return new EAN13($message, $supplemental);
		}

		/**
		 * Get message of EAN13 with checksum digit
		 *
		 * @return null|string EAN13 message
		 */
		public function getMessage () : ?string {
			return $this->message ?: null;
		}

		/**
		 * Set message of EAN13 code
		 *
		 * @param null|string $message Message of EAN13
		 * @return null|int Checksum of EAN13. NULL is returned if message has invalid length. If message is cleared with void string or NULL, -1 is returned.
		 */
		public function setMessage (?string $message = null) : ?int {
			$this->message = substr(preg_replace('/\D/', '', (string) $message), 0, 12);

			$checksum = self::getBarcodeChecksum();

			$this->message = is_int($checksum) ? $this->message . $checksum : '';

			if ($message and is_null($checksum)) {
				$this->message = '';
				return null;
			}

			return $this->message ? $checksum : -1;
		}

		/**
		 * Get supplemental code
		 *
		 * @return null|string Supplemental
		 */
		public function getSupplemental () : ?string {
			return $this->supplemental ?: null;
		}

		/**
		 * Set the supplemental code for EAN13 code
		 *
		 * @param null|string $supplemental Supplemental code
		 * @return null|int Checksum of supplemental. NULL is returned if supplemental has invalid length. If supplemental is cleared with void string or NULL, -1 is returned.
		 */
		public function setSupplemental (?string $supplemental = null) : ?int {
			$this->supplemental = preg_replace('/\D/', '', (string) $supplemental);

			$checksum = self::getSupplementalChecksum();

			if ($supplemental and is_null($checksum)) {
				$this->supplemental = '';
				return null;
			}

			return $this->supplemental ? $checksum : -1;
		}

		/**
		 * Computes the EAN13 checksum
		 *
		 * @return null|int NULL is returned if mesage has invalid length
		 */
		public function getBarcodeChecksum () : ?int {
			$message = substr($this->message, 0, 12);

			if (strlen($message) != 12) {
				return null;
			}

			$checksum = 0;
			foreach (str_split(strrev($message)) as $pos => $val) {
				$checksum += $val * (3 - 2 * ($pos % 2));
			}

			return ((10 - ($checksum % 10)) % 10);
		}

		/**
		 * Computes the UPC checksum for supplemental code of EAN13
		 *
		 * @return null|int NULL is returned if supplemental has invalid length
		 */
		public function getSupplementalChecksum () : ?int {
			if (strlen($this->supplemental) == 2) {
				return ($this->supplemental % 4);
			}

			if (strlen($this->supplemental) == 5) {
				$supp_checksum = 0;
				foreach (str_split(strrev($this->supplemental)) as $pos => $val ) {
					$supp_checksum += $val * (3 + 6 * ($pos % 2));
				}
				return ($supp_checksum % 10);
			}

			return null;
		}

		/**
		 * Get EAN13 left hand bars coded
		 *
		 * @return null|string Left hand bars of EAN13 code
		 */
		public function getBarcodeLeftHand () : ?string {
			$lh_coded = '';

			foreach (str_split(substr($this->message, 1, 6)) as $pos => $val) {
				$lh_coded .= self::EAN_cet[$val][self::EAN_pet[$this->message[0]][$pos]];
			}

			return $lh_coded ?: null;
		}

		/**
		 * Get EAN13 right hand bars coded
		 *
		 * @return null|string Right hand bars of EAN13 code
		 */
		public function getBarcodeRightHand () : ?string {
			$rh_coded = '';

			foreach (str_split(substr($this->message, 7, 6)) as $pos => $val) {
				$rh_coded .= self::EAN_cet[$val]['R'];
			}

			return $rh_coded ?: null;
		}

		/**
		 * Get EAN13 supplemental bars coded
		 *
		 * @return null|string EAN13 supplemental bars
		 */
		public function getBarcodeSupplemental () : ?string {
			$supp_coded = '';

			$supp_checksum = $this->getSupplementalChecksum();

			$table = (strlen($this->supplemental) == 2) ? self::UPC_2dpp : self::UPC_5dpp;
			foreach(str_split($this->supplemental) as $pos => $val) {
				$supp_coded .= self::EAN_cet[$val][$table[$supp_checksum][$pos]] . '01';
			}

			return $supp_coded ? '1011' . substr($supp_coded, 0, -2) : null;
		}
	}
