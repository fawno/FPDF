<?php
	declare(strict_types=1);

	namespace Fawno\FPDF;

	use Fawno\FPDF\PDFWrapper;
	use Fawno\FPDF\Traits\CMYKTrait;
	use Fawno\FPDF\Traits\FontsTrait;
	use Fawno\FPDF\Traits\PDFMacroableTrait;
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
		use FontsTrait;
		use PDFMacroableTrait;
		use FontsTrait;
		use CMYKTrait;
		use PDFBookmarkTrait { PDFBookmarkTrait::_putcatalog as PDFBookmark_putcatalog; PDFBookmarkTrait::_putresources as PDFBookmark_putresources; }
		use PDFProtectionTrait { PDFProtectionTrait::_putresources as PDFProtection_putresources; }
		use PDFRotateTrait;
		use PDFDrawTrait;
		use RPDFTrait;
		use PDFMemImageTrait;
		use PDFCode128Trait;
		use PDFMultiCellsTableTrait;
		//use FPDFATrait { FPDFATrait::_putcatalog as FPDFA_putcatalog; FPDFATrait::_putresources as FPDFA_putresources; }

		protected function _putresources () {
			parent::_putresources();
			$this->_putbookmarks();
			/*
			$this->_putcolorprofile();
			$this->_putmetadata();
			*/
			$this->_encrypresources();
		}

		/*
		protected function _putcatalog () {
			parent::_putcatalog();
			$this->_putoutputintent();
			$this->_bookmarks_catalog();
		}
		*/
	}
