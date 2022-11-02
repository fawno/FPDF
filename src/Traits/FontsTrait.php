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
		protected function _loadfont ($filename) {
			$fontpath = is_file($filename) ? dirname($filename) . DIRECTORY_SEPARATOR : '';
			$filename = is_file($filename) ? $filename : $this->fontpath . $filename;

			if (!is_file($filename)) {
				$this->Error('Could not include font definition file');
			}

			include($filename);

			if (isset($file)) {
				$file = is_file($fontpath . $file) ? $fontpath . $file : $file;
			}

			if (!isset($name)) {
				$this->Error('Could not include font definition file');
			}

			if (isset($enc)) {
				$enc = strtolower($enc);
			}

			$subsetted = $subsetted ?? false;

			return get_defined_vars();
		}

		protected function _putfonts() {
			foreach($this->FontFiles as $file => $info) {
				// Font file embedding
				$this->_newobj();
				$this->FontFiles[$file]['n'] = $this->n;
				$filepath = is_file($file) ? $file : $this->fontpath . $file;
				if (!is_file($filepath)) {
					$this->Error('Font file not found: '.$file);
				}
				$font = file_get_contents($filepath, true);
				if (!$font) {
					$this->Error('Font file cannot be loaded: '.$file);
				}
				$compressed = (substr($file, -2) == '.z');
				if(!$compressed && isset($info['length2']))
					$font = substr($font,6,$info['length1']).substr($font,6+$info['length1']+6,$info['length2']);
				$this->_put('<</Length '.strlen($font));
				if($compressed)
					$this->_put('/Filter /FlateDecode');
				$this->_put('/Length1 '.$info['length1']);
				if(isset($info['length2']))
					$this->_put('/Length2 '.$info['length2'].' /Length3 0');
				$this->_put('>>');
				$this->_putstream($font);
				$this->_put('endobj');
			}
			foreach($this->fonts as $k=>$font)
			{
				// Encoding
				if(isset($font['diff']))
				{
					if(!isset($this->encodings[$font['enc']]))
					{
						$this->_newobj();
						$this->_put('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$font['diff'].']>>');
						$this->_put('endobj');
						$this->encodings[$font['enc']] = $this->n;
					}
				}
				// ToUnicode CMap
				if(isset($font['uv']))
				{
					if(isset($font['enc']))
						$cmapkey = $font['enc'];
					else
						$cmapkey = $font['name'];
					if(!isset($this->cmaps[$cmapkey]))
					{
						$cmap = $this->_tounicodecmap($font['uv']);
						$this->_putstreamobject($cmap);
						$this->cmaps[$cmapkey] = $this->n;
					}
				}
				// Font object
				$this->fonts[$k]['n'] = $this->n+1;
				$type = $font['type'];
				$name = $font['name'];
				if($font['subsetted'])
					$name = 'AAAAAA+'.$name;
				if($type=='Core')
				{
					// Core font
					$this->_newobj();
					$this->_put('<</Type /Font');
					$this->_put('/BaseFont /'.$name);
					$this->_put('/Subtype /Type1');
					if($name!='Symbol' && $name!='ZapfDingbats')
						$this->_put('/Encoding /WinAnsiEncoding');
					if(isset($font['uv']))
						$this->_put('/ToUnicode '.$this->cmaps[$cmapkey].' 0 R');
					$this->_put('>>');
					$this->_put('endobj');
				} elseif ($type == 'Type1' || $type == 'TrueType') {
					// Additional Type1 or TrueType/OpenType font
					$this->_newobj();
					$this->_put('<</Type /Font');
					$this->_put('/BaseFont /'.$name);
					$this->_put('/Subtype /' . $type);
					$this->_put('/FirstChar 32 /LastChar 255');
					$this->_put('/Widths ' . ($this->n + 1) . ' 0 R');
					$this->_put('/FontDescriptor ' . ($this->n+2) . ' 0 R');
					if (isset($font['diff'])) {
						$this->_put('/Encoding ' . $this->encodings[$font['enc']] . ' 0 R');
					} else {
						$this->_put('/Encoding /WinAnsiEncoding');
					}

					if (isset($font['uv'])) {
						$this->_put('/ToUnicode ' . $this->cmaps[$cmapkey] . ' 0 R');
					}
					$this->_put('>>');
					$this->_put('endobj');

					// Widths
					$this->_newobj();
					$cw = &$font['cw'];
					$s = '[';

					for ($i = 32; $i <= 255; $i++) {
						$s .= $cw[chr($i)].' ';
					}

					$this->_put($s . ']');
					$this->_put('endobj');
					// Descriptor
					$this->_newobj();
					$s = '<</Type /FontDescriptor /FontName /' . $name;

					foreach ($font['desc'] as $k => $v) {
						$s .= ' /' . $k . ' ' . $v;
					}

					if (!empty($font['file'])) {
						$s .= ' /FontFile' . ($type == 'Type1' ? '' : '2') . ' ' . $this->FontFiles[$font['file']]['n'] . ' 0 R';
					}
					$this->_put($s . '>>');
					$this->_put('endobj');
				} else {
					// Allow for additional types
					$mtd = '_put' . strtolower($type);
					if (!method_exists($this, $mtd)) {
						$this->Error('Unsupported font type: ' . $type);
					}
					$this->$mtd($font);
				}
			}
		}
	}
