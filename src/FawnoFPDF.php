<?php
	declare(strict_types=1);

	namespace Fawno\FPDF;

	use Fawno\FPDF\Traits\CMYKTrait;
	use Fawno\FPDF\Traits\PDFMacroableTrait;
	use Fawno\FPDF\PDFWrapper;
	use FPDF\Scripts\FPDFA\FPDFATrait;
	use FPDF\Scripts\PDFBookmark\PDFBookmarkTrait;
	use FPDF\Scripts\PDFCode128\PDFCode128Trait;
	use FPDF\Scripts\PDFDraw\PDFDrawTrait;
	use FPDF\Scripts\PDFMemImage\PDFMemImageTrait;
	use FPDF\Scripts\PDFMultiCellsTable\PDFMultiCellsTableTrait;
	use FPDF\Scripts\PDFProtection\PDFProtectionTrait;
	use FPDF\Scripts\PDFRotate\PDFRotateTrait;
	use FPDF\Scripts\RPDF\RPDFTrait;

	class FawnoFPDF extends PDFWrapper {
		use PDFMacroableTrait;
		use PDFBookmarkTrait { PDFBookmarkTrait::_putresources as PDFBookmark_putresources; }
		use PDFProtectionTrait { PDFProtectionTrait::_putresources as PDFProtection_putresources; }
		use PDFRotateTrait;
		use CMYKTrait;
		use PDFDrawTrait;
		use RPDFTrait;
		use PDFMemImageTrait;
		use PDFCode128Trait;
		use PDFMultiCellsTableTrait;
		use FPDFATrait;

		protected function _putresources () {
			parent::_putresources();
			$this->_putbookmarks();
			$this->_encrypresources();
		}
	}
