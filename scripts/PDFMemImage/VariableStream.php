<?php
  declare(strict_types=1);

	namespace FPDF\Scripts\PDFMemImage;

	// Stream handler to read from global variables
	class VariableStream {
		public $context;
		private $varname;
		private $position;

		public function stream_open ($path, $mode, $options, &$opened_path) {
			$url = parse_url($path);
			$this->varname = $url['host'];
			if (!isset($GLOBALS[$this->varname])) {
				trigger_error('Global variable ' . $this->varname . ' does not exist', E_USER_WARNING);
				return false;
			}
			$this->position = 0;
			return true;
		}

		public function stream_read ($count) {
			$ret = substr($GLOBALS[$this->varname], $this->position, $count);
			$this->position += strlen($ret);
			return $ret;
		}

		public function stream_eof () {
			return $this->position >= strlen($GLOBALS[$this->varname]);
		}

		public function stream_tell () {
			return $this->position;
		}

		public function stream_seek ($offset, $whence) {
			if ($whence != SEEK_SET) {
				return false;
			}

			$this->position = $offset;
			return true;
		}

		public function stream_stat () {
			return [];
		}
	}
