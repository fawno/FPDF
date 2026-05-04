<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFProtection;
	//http://www.fpdf.org/en/script/script37.php
	//https://github.com/klemenv/FPDF_Protection

	trait PDFProtectionTrait {
		protected $encrypted = false;  //whether document is protected
		protected $Uvalue;             //U entry in pdf document
		protected $Ovalue;             //O entry in pdf document
		protected $Pvalue;             //P entry in pdf document
		protected $enc_obj_id;         //encryption PDF object id
		protected $enc_algorithm;      //Encryption algorithm, RC4 or AES
		protected $enc_security_handler;//Security handler version, 2 for basic, 3 for AES and 40+ bits
		protected $enc_key;            //Key used for encryption
		protected $enc_key_len;        //Number of bytes used for encryption key
		protected $id;                 //Document ID

		/**
		 * Set permissions as well as user and owner passwords
		 *
		 * @param array $permissions Array with values taken from the following list (if a value is present it means that the permission is granted):
		 * - copy
		 * - print
		 * - modify
		 * - annot-forms
		 * @param string $user_pass If a user password is set, user will be prompted before document is opened
		 * @param null|string $owner_pass If an owner password is set, document can be opened in privilege mode with no restriction if that password is entered
         * @param string $algorithm must be one of 'ARCFOUR' or 'AES'
         * @param int $bits is number of bits used as encryption algorithm, must be a multiple of 8 in range between 40 and 128
         *
         * Output PDF is version 1.3 when using RC4 40bit or 1.4 when using RC4 40+bit or AES encryption.
		 *
		 * @return void
		 */
		public function SetProtection (array $permissions = [], ?string $user_pass = null, ?string $owner_pass = null, string $algorithm = "RC4", int $bits = 40) : void {
			$this->id = uniqid().__FILE__.rand();
			$options = ['print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32];
			$protection = 192;

			foreach ($permissions as $permission) {
				if (!isset($options[$permission])) {
					$this->Error('Incorrect permission: ' . $permission);
				} else {
					$protection += $options[$permission];
				}
			}

			if (strncmp($algorithm, "RC4", 7) !== 0 && strncasecmp($algorithm, "AES", 3) !== 0) {
				$this->Error('Invalid encryption algorithm '.$algorithm.', supported RC4 and AES');

				return;
			}

			$bits = intval($bits);
			if ($bits < 40 || $bits > 128) {
				$this->Error('Number of bits limited between 40 and 128');

				return;
			}

			if (($bits % 8 ) != 0) {
				$this->Error('Number of bits not a multiple of 8');

				return;
			}

			$this->enc_algorithm = strtoupper(substr($algorithm,0,3));
			if ($this->enc_algorithm === 'AES') {
				if (!function_exists('openssl_encrypt')) { // fallback
					$this->enc_algorithm = 'RC4';
				} else {
					$bits = 128;
					if ($this->PDFVersion<'1.5') {
						$this->PDFVersion = '1.5';
					}
				}
			}

			$this->enc_key_len = $bits / 8;
			if ($bits == 40 && strcmp($this->enc_algorithm, "RC4") == 0) {
				$this->enc_security_handler = 2;
			} else {
				$this->enc_security_handler = 3;
				if ($this->PDFVersion<'1.4') {
					$this->PDFVersion = '1.4';
				}
			}

			if ($owner_pass === null) {
				$owner_pass = uniqid((string) rand());
			}

			$this->encrypted = true;
			$this->_setOvalue($owner_pass, $user_pass);
			$this->_setEncryptionKey($user_pass, $protection);
			$this->_setUvalue();
			$this->_setPvalue($protection);
		}

		protected function _encryptData($key, $data) {
			if ($this->enc_algorithm === 'AES') {
				return $this->AES($key, $data);
			}

			return $this->ARCFOUR($key, $data);
		}
		protected function AES (string $key, string $data) {
			$iv = random_bytes(16);
			$cipher = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

			return $iv . $cipher;
		}

		protected function ARCFOUR (string $key, string $data) {
			static $last_key;
			static $last_state;

			if (function_exists('openssl_encrypt')) {
				return openssl_encrypt($data, 'RC4-40', $key, OPENSSL_RAW_DATA);
			}

			if ($key != $last_key) {
				$k = str_repeat($key, (int) (256 / strlen($key) + 1));
				$state = range(0, 255);
				$j = 0;

				for ($i = 0; $i < 256; $i++) {
					$t = $state[$i];
					$j = ($j + $t + ord($k[$i])) % 256;
					$state[$i] = $state[$j];
					$state[$j] = $t;
				}

				$last_key = $key;
				$last_state = $state;
			} else {
				$state = $last_state;
			}

			$len = strlen($data);
			$a = 0;
			$b = 0;
			$out = '';

			for ($i = 0; $i < $len; $i++) {
				$a = ($a + 1) % 256;
				$t = $state[$a];
				$b = ($b + $t) % 256;
				$state[$a] = $state[$b];
				$state[$b] = $t;
				$k = $state[($state[$a] + $state[$b]) % 256];
				$out .= chr(ord($data[$i]) ^ $k);
			}

			return $out;
		}

		protected function _putstream ($s) {
			if ($this->encrypted) {
				$s = $this->_encryptData($this->_objectkey($this->n), $s);
			}
			parent::_putstream($s);
		}

		protected function _textstring($s) {
			if (!$this->_isascii($s)) {
				$s = $this->_UTF8toUTF16($s);
			}

			if ($this->encrypted) {
				$s = $this->_encryptData($this->_objectkey($this->n), $s);
			}

			return '(' . $this->_escape($s) . ')';
		}

		/**
		* Compute key depending on object number where the encrypted data is stored
		*/
		protected function _objectkey ($n) {
			$key = $this->enc_key.pack('VXxx', $n);
			if ($this->enc_algorithm === 'AES') {
				$key .= "sAlT";
			}
			$len = $this->enc_key_len + 5;

			return substr($this->_md5_16($key), 0, $len);
		}

		protected function _putresources () {
			parent::_putresources();
			$this->_encrypresources();
		}

		protected function _encrypresources () {
			if ($this->encrypted) {
				$this->_newobj();
				$this->enc_obj_id = $this->n;
				$this->_put('<<');
				$this->_putencryption();
				$this->_put('>>');
				$this->_put('endobj');
			}
		}

		protected function _putencryption () {
			$this->_put('/Filter');
			$this->_put('/Standard');
			if ($this->enc_algorithm === 'AES') {
				$this->_put('/V 4');
				$this->_put('/R 4');
				$this->_put('/Length 128');

				$this->_put('/CF << /StdCF << /CFM /AESV2 /Length 16 >> >>');
				$this->_put('/StmF /StdCF');
				$this->_put('/StrF /StdCF');
			} else {
				if ($this->enc_security_handler == 2) {
					$this->_put('/V 1');
					$this->_put('/R 2');
				} else {// ($this->enc_security_handler == 3)
					$this->_put('/V 2');
					$this->_put('/Length '.$this->enc_key_len*8);
					$this->_put('/R 3');
				}
			}
			$this->_put('/O (' . $this->_escape($this->Ovalue) . ')');
			$this->_put('/U (' . $this->_escape($this->Uvalue) . ')');
			$this->_put('/P ' . $this->Pvalue);
		}

		protected function _puttrailer () {
			parent::_puttrailer();
			if ($this->encrypted) {
				$id = md5($this->id);
				$this->_put('/Encrypt '.$this->enc_obj_id.' 0 R');
				$this->_put('/ID [ <'.$id.'> <'.$id.'> ]');
			}
		}

		/**
		* Get MD5 as 16 byte binary string
		*/
		protected function _md5_16 ($string) {
			return md5($string, true);
		}

		private function _pad ($string, $len = 0) {
			$padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08".
				"\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
			if ($len == 0) {
				$len = strlen($padding);
			}

			return substr($string.$padding, 0, $len);
		}

		/**
		* Compute O (owner password) value
		*
		* Depends on following member variables:
		* - enc_security_handler
		* - enc_key_len
		*/
		protected function _setOvalue ($owner_pass, $user_pass) {
			$key = $this->_md5_16($this->_pad($owner_pass, 32));
			if ($this->enc_security_handler >= 3) {
				for ($i=0; $i<50; $i++) {
					$key = $this->_md5_16($key);
				}
			}
			$key = substr($key, 0, $this->enc_key_len);
			$encrypted = $this->ARCFOUR($key, $this->_pad($user_pass, 32));
			if ($this->enc_security_handler >= 3) {
				for ($i=1; $i<=19; $i++) {
					$loop_key = '';
					for ($j=0; $j<$this->enc_key_len; $j++) {
						$loop_key .= chr(ord($key[$j]) ^ $i);
					}
					$encrypted = $this->ARCFOUR($loop_key, $encrypted ?: '');
				}
			}
			$this->Ovalue = $encrypted;
		}

		/**
		* Compute U (user password) value
		*
		* Depends on following member variables:
		* - enc_security_handler
		* - enc_key
		* - enc_key_len
		*/
		protected function _setUvalue () {
			$padding = $this->_pad('', 32);
			if ($this->enc_security_handler == 2) {
				$encrypted = $this->ARCFOUR($this->enc_key, $padding);
			} else {
				$id = $this->_md5_16($this->id);
				$hash = $this->_md5_16($padding.$id);
				$encrypted = $this->ARCFOUR($this->enc_key, $hash);
				for ($i=1; $i<=19; $i++) {
					$key = '';
					for ($j=0; $j<$this->enc_key_len; $j++) {
						$key .= chr( ord($this->enc_key[$j]) ^ $i );
					}
					$encrypted = $this->ARCFOUR($key, $encrypted ?: '');
				}
				$encrypted = $this->_pad($encrypted, 32);
			}
			$this->Uvalue = $encrypted;
		}

		/**
		* Set Pvalue
		*/
		protected function _setPvalue ($protection) {
			$this->Pvalue = -(($protection^255)+1);
		}

		/**
		* Compute encryption key
		*
		* Depends on following member variables:
		* - Ovalue
		* - enc_security_handler
		* - enc_key_len
		*/
		protected function _setEncryptionKey ($user_pass, $protection) {
			$user_pass = $this->_pad($user_pass, 32);
			$id = $this->_md5_16($this->id);
			$hash = $this->_md5_16($user_pass.$this->Ovalue.pack("V", $protection | 0xFFFFFF00).$id);

			if ($this->enc_security_handler >= 3) {
				for ($i=0; $i<50; $i++) {
					$hash = $this->_md5_16(substr($hash, 0, $this->enc_key_len));
				}
			} else {
				$key_len = 5;
			}

			$this->enc_key = substr($hash, 0, $this->enc_key_len);
		}
	}
