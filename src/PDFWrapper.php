<?php
	declare(strict_types=1);

	namespace Fawno\FPDF;

	if (class_exists('setasign\Fpdi\Fpdi')) {
		class PDFWrapper extends \setasign\Fpdi\Fpdi {};
	} else {
		class PDFWrapper extends \FPDF {};
	}
