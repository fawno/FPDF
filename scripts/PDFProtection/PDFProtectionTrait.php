<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFProtection;
	//http://www.fpdf.org/en/script/script37.php

	trait PDFProtectionTrait {
		protected $encrypted = false;  //whether document is protected
		protected $padding;
		protected $encryption_key;
		protected $Uvalue;             //U entry in pdf document
		protected $Ovalue;             //O entry in pdf document
		protected $Pvalue;             //P entry in pdf document
		protected $enc_obj_id;         //encryption object id

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
		 * @return void
		 */
		public function SetProtection (array $permissions = [], ?string $user_pass = null, ?string $owner_pass = null) : void {
			$options = ['print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32];
			$protection = 192;

			foreach ($permissions as $permission) {
				if (!isset($options[$permission])) {
					$this->Error('Incorrect permission: ' . $permission);
				}
				$protection += $options[$permission];
			}

			if ($owner_pass === null) {
				$owner_pass = uniqid((string) rand());
			}

			$this->encrypted = true;
			$this->padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
			$this->_generateencryptionkey((string) $user_pass, $owner_pass, $protection);
		}

		protected function RC4 (string $key, string $data) {
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
				$s = $this->RC4($this->_objectkey($this->n), $s);
			}
			parent::_putstream($s);
		}

		protected function _textstring($s) {
			if (!$this->_isascii($s)) {
				$s = $this->_UTF8toUTF16($s);
			}

			if ($this->encrypted) {
				$s = $this->RC4($this->_objectkey($this->n), $s);
			}

			return '(' . $this->_escape($s) . ')';
		}

		/**
		* Compute key depending on object number where the encrypted data is stored
		*/
		protected function _objectkey ($n) {
			return substr($this->_md5_16($this->encryption_key.pack('VXxx',$n)), 0, 10);
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
			$this->_put('/Filter /Standard');
			$this->_put('/V 1');
			$this->_put('/R 2');
			$this->_put('/O (' . $this->_escape($this->Ovalue) . ')');
			$this->_put('/U (' . $this->_escape($this->Uvalue) . ')');
			$this->_put('/P ' . $this->Pvalue);
		}

		protected function _puttrailer () {
			parent::_puttrailer();
			if ($this->encrypted) {
				$this->_put('/Encrypt '.$this->enc_obj_id.' 0 R');
				$this->_put('/ID [()()]');
			}
		}

		/**
		* Get MD5 as binary string
		*/
		protected function _md5_16 ($string) {
			return md5($string, true);
		}

		/**
		* Compute O value
		*/
		protected function _Ovalue ($user_pass, $owner_pass) {
			$tmp = $this->_md5_16($owner_pass);
			$owner_RC4_key = substr($tmp,0,5);
			return $this->RC4($owner_RC4_key, $user_pass);
		}

		/**
		* Compute U value
		*/
		protected function _Uvalue () {
			return $this->RC4($this->encryption_key, $this->padding);
		}

		/**
		* Compute encryption key
		*/
		protected function _generateencryptionkey ($user_pass, $owner_pass, $protection) {
			// Pad passwords
			$user_pass = substr($user_pass.$this->padding,0,32);
			$owner_pass = substr($owner_pass.$this->padding,0,32);
			// Compute O value
			$this->Ovalue = $this->_Ovalue($user_pass,$owner_pass);
			// Compute encyption key
			$tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
			$this->encryption_key = substr($tmp,0,5);
			// Compute U value
			$this->Uvalue = $this->_Uvalue();
			// Compute P value
			$this->Pvalue = -(($protection^255)+1);
		}
	}
