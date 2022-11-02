<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\FPDFA;
	//http://www.fpdf.org/en/script/script103.php

	trait FPDFATrait {
		protected $n_colorprofile;
		protected $n_metadata;
		protected $CreationDate;

		protected function _putcolorprofile () : void {
			$icc = file_get_contents(__DIR__ . '/sRGB2014.icc');
			if (!$icc) {
				$this->Error('Could not load the ICC profile');
			}
			$this->_newobj();
			$this->n_colorprofile = $this->n;
			$this->_put('<<');
			$this->_put('/Length '.strlen($icc));
			$this->_put('/N 3');
			$this->_put('>>');
			$this->_putstream($icc);
			$this->_put('endobj');
		}

		protected function _getxmpdescription (string $alias, string $ns, string $body) : string {
			return sprintf("\t<rdf:Description rdf:about=\"\" xmlns:%s=\"%s\">\n%s\t</rdf:Description>\n", $alias, $ns, $body);
		}

		protected function _getxmpsimple (string $tag, string $value) : string {
			$value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
			return sprintf("\t\t<%s>%s</%s>\n", $tag, $value, $tag);
		}

		protected function _getxmpseq (string $tag, string $value) : string {
			$value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
			return sprintf("\t\t<%s>\n\t\t\t<rdf:Seq>\n\t\t\t\t<rdf:li>%s</rdf:li>\n\t\t\t</rdf:Seq>\n\t\t</%s>\n", $tag, $value, $tag);
		}

		protected function _getxmpalt (string $tag, string $value) : string {
			$value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
			return sprintf("\t\t<%s>\n\t\t\t<rdf:Alt>\n\t\t\t\t<rdf:li xml:lang=\"x-default\">%s</rdf:li>\n\t\t\t</rdf:Alt>\n\t\t</%s>\n", $tag, $value, $tag);
		}

		protected function _putmetadata () {
			$pdf = $this->_getxmpsimple('pdf:Producer', $this->metadata['Producer']);
			if(isset($this->metadata['Keywords']))
				$pdf .= $this->_getxmpsimple('pdf:Keywords', $this->metadata['Keywords']);

			$date = @date('c', $this->CreationDate);
			$xmp = $this->_getxmpsimple('xmp:CreateDate', $date);
			if(isset($this->metadata['Creator']))
				$xmp .= $this->_getxmpsimple('xmp:CreatorTool', $this->metadata['Creator']);

			$dc = '';
			if(isset($this->metadata['Author']))
				$dc .= $this->_getxmpseq('dc:creator', $this->metadata['Author']);
			if(isset($this->metadata['Title']))
				$dc .= $this->_getxmpalt('dc:title', $this->metadata['Title']);
			if(isset($this->metadata['Subject']))
				$dc .= $this->_getxmpalt('dc:description', $this->metadata['Subject']);

			$pdfaid = $this->_getxmpsimple('pdfaid:part', '3');
			$pdfaid .= $this->_getxmpsimple('pdfaid:conformance', 'B');

			$s = '<?xpacket begin="" id="W5M0MpCehiHzreSzNTczkc9d"?>'."\n";
			$s .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'."\n";
			$s .= $this->_getxmpdescription('pdf', 'http://ns.adobe.com/pdf/1.3/', $pdf);
			$s .= $this->_getxmpdescription('xmp', 'http://ns.adobe.com/xap/1.0/', $xmp);
			if($dc)
				$s .= $this->_getxmpdescription('dc', 'http://purl.org/dc/elements/1.1/', $dc);
			$s .= $this->_getxmpdescription('pdfaid', 'http://www.aiim.org/pdfa/ns/id/', $pdfaid);
			$s .= '</rdf:RDF>'."\n";
			$s .= '<?xpacket end="r"?>';

			$this->_newobj();
			$this->n_metadata = $this->n;
			$this->_put('<<');
			$this->_put('/Type /Metadata');
			$this->_put('/Subtype /XML');
			$this->_put('/Length '.strlen($s));
			$this->_put('>>');
			$this->_putstream($s);
			$this->_put('endobj');
		}

		protected function _putinfo () {
			$date = @date('YmdHisO', $this->CreationDate);
			$this->metadata['CreationDate'] = 'D:'.substr($date,0,-2)."'".substr($date,-2)."'";
			foreach($this->metadata as $key=>$value)
				$this->_put('/'.$key.' '.$this->_textstring($value));
		}

		protected function _putresources () {
			parent::_putresources();
			$this->_putcolorprofile();
			$this->_putmetadata();
		}

		function _putoutputintent () {
			$oi = '<</Type /OutputIntent /S /GTS_PDFA1 ';
			$oi .= '/OutputConditionIdentifier (sRGB2014.icc) /Info (sRGB2014.icc) /RegistryName (http://www.color.org) ';
			$oi .= '/DestOutputProfile '.$this->n_colorprofile.' 0 R>>';
			$this->_put('/OutputIntents ['.$oi.']');
			$this->_put('/Metadata '.$this->n_metadata.' 0 R');
		}

		function _putcatalog () {
			parent::_putcatalog();
			$this->_putoutputintent();
		}

		protected function _putheader () {
			$this->_put('%PDF-1.4');
			$this->_put("%\xE2\xE3\xCF\xD3");
		}

		/*
		protected function _puttrailer () {
			parent::_puttrailer();
			$id = uniqid();
			$this->_put("/ID [($id)($id)]");
		}
		*/

		protected function _enddoc () {
			foreach($this->fonts as $font)
			{
				if($font['type']=='Core')
					$this->Error('All fonts must be embedded in PDF/A');
			}
			$this->CreationDate = time();
			if(!defined('FPDF_VERSION'))
				$this->Error('Unsupported FPDF version');
			$this->metadata['Producer'] = 'FPDF '.FPDF_VERSION;
			parent::_enddoc();
		}
	}
