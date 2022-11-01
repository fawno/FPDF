<?php
	declare(strict_types=1);

	namespace Fawno\FPDF;

	use Fawno\FPDF\Traits\CMYKTrait;
	use Fawno\FPDF\Traits\PDFMacroableTrait;
	use Fawno\FPDF\PDFWrapper;
	use FPDF\Scripts\PDFBookmark\PDFBookmarkTrait;
	use FPDF\Scripts\PDFMemoryImage\MemoryImageTrait;
	use FPDF\Scripts\PDFDraw\PDFDrawTrait;
	use FPDF\Scripts\PDFProtection\PDFProtectionTrait;
	use FPDF\Scripts\PDFRotate\PDFRotateTrait;
	use FPDF\Scripts\RPDF\RPDFTrait;

	class FawnoFPDF extends PDFWrapper {
		use PDFMacroableTrait;
		use MemoryImageTrait;
		use PDFBookmarkTrait { PDFBookmarkTrait::_putresources as PDFBookmark_putresources; }
		use PDFProtectionTrait { PDFProtectionTrait::_putresources as PDFProtection_putresources; }
		use PDFRotateTrait;
		use CMYKTrait;
		use PDFDrawTrait;
		use RPDFTrait;

		function __construct($orientation='P', $unit='mm', $size='A4') {
			parent::__construct($orientation, $unit, $size);
			// Register var stream protocol
			stream_wrapper_register('var', '\\FPDF\\Scripts\\PDFMemoryImage\\VariableStream');
		}

		protected function _putresources () {
			parent::_putresources();
			$this->_putbookmarks();
			$this->_encrypresources();
		}
	}
