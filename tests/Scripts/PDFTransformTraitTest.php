<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFTransform\PDFTransformTrait;
	use Fawno\FPDF\Tests\TestCase;

	class PDFTransformTraitTest extends TestCase {
		public function testPDFTransformTrait () {
			$pdf = new class extends FPDF {
				use PDFTransformTrait;
			};

			$pdf->AddPage();
			$pdf->SetFont('Arial', '', 12);

			//Scaling
			$pdf->SetDrawColor(200);
			$pdf->SetTextColor(200);
			$pdf->Rect(50, 20, 40, 10, 'D');
			$pdf->Text(50, 19, 'Scale');
			$pdf->SetDrawColor(0);
			$pdf->SetTextColor(0);
			//Start Transformation
			$pdf->StartTransform();
			//Scale by 150% centered by (50,30) which is the lower left corner of the rectangle
			$pdf->ScaleXY(150, 50, 30);
			$pdf->Rect(50, 20, 40, 10, 'D');
			$pdf->Text(50, 19, 'Scale');
			//Stop Transformation
			$pdf->StopTransform();

			//Translation
			$pdf->SetDrawColor(200);
			$pdf->SetTextColor(200);
			$pdf->Rect(125, 20, 40, 10, 'D');
			$pdf->Text(125, 19, 'Translate');
			$pdf->SetDrawColor(0);
			$pdf->SetTextColor(0);
			//Start Transformation
			$pdf->StartTransform();
			//Translate 7 to the right, 5 to the bottom
			$pdf->Translate(7, 5);
			$pdf->Rect(125, 20, 40, 10, 'D');
			$pdf->Text(125, 19, 'Translate');
			//Stop Transformation
			$pdf->StopTransform();

			//Rotation
			$pdf->SetDrawColor(200);
			$pdf->SetTextColor(200);
			$pdf->Rect(50, 50, 40, 10, 'D');
			$pdf->Text(50, 49, 'Rotate');
			$pdf->SetDrawColor(0);
			$pdf->SetTextColor(0);
			//Start Transformation
			$pdf->StartTransform();
			//Rotate 20 degrees counter-clockwise centered by (50,60) which is the lower left corner of the rectangle
			$pdf->Rotate(20, 50, 60);
			$pdf->Rect(50, 50, 40, 10, 'D');
			$pdf->Text(50, 49, 'Rotate');
			//Stop Transformation
			$pdf->StopTransform();

			//Skewing
			$pdf->SetDrawColor(200);
			$pdf->SetTextColor(200);
			$pdf->Rect(125, 50, 40, 10, 'D');
			$pdf->Text(125, 49, 'Skew');
			$pdf->SetDrawColor(0);
			$pdf->SetTextColor(0);
			//Start Transformation
			$pdf->StartTransform();
			//skew 30 degrees along the x-axis centered by (125,60) which is the lower left corner of the rectangle
			$pdf->SkewX(30, 125, 60);
			$pdf->Rect(125, 50, 40, 10, 'D');
			$pdf->Text(125, 49, 'Skew');
			//Stop Transformation
			$pdf->StopTransform();

			//Mirroring horizontally
			$pdf->SetDrawColor(200);
			$pdf->SetTextColor(200);
			$pdf->Rect(50, 80, 40, 10, 'D');
			$pdf->Text(50, 79, 'MirrorH');
			$pdf->SetDrawColor(0);
			$pdf->SetTextColor(0);
			//Start Transformation
			$pdf->StartTransform();
			//mirror horizontally with axis of reflection at x-position 50 (left side of the rectangle)
			$pdf->MirrorH(50);
			$pdf->Rect(50, 80, 40, 10, 'D');
			$pdf->Text(50, 79, 'MirrorH');
			//Stop Transformation
			$pdf->StopTransform();

			//Mirroring vertically
			$pdf->SetDrawColor(200);
			$pdf->SetTextColor(200);
			$pdf->Rect(125, 80, 40, 10, 'D');
			$pdf->Text(125, 79, 'MirrorV');
			$pdf->SetDrawColor(0);
			$pdf->SetTextColor(0);
			//Start Transformation
			$pdf->StartTransform();
			//mirror vertically with axis of reflection at y-position 90 (bottom side of the rectangle)
			$pdf->MirrorV(90);
			$pdf->Rect(125, 80, 40, 10, 'D');
			$pdf->Text(125, 79, 'MirrorV');
			//Stop Transformation
			$pdf->StopTransform();

			//Point reflection
			$pdf->SetDrawColor(200);
			$pdf->SetTextColor(200);
			$pdf->Rect(50, 110, 40, 10, 'D');
			$pdf->Text(50, 109, 'MirrorP');
			$pdf->SetDrawColor(0);
			$pdf->SetTextColor(0);
			//Start Transformation
			$pdf->StartTransform();
			//point reflection at the lower left point of rectangle
			$pdf->MirrorP(50,120);
			$pdf->Rect(50, 110, 40, 10, 'D');
			$pdf->Text(50, 109, 'MirrorP');
			//Stop Transformation
			$pdf->StopTransform();

			//Mirroring against a straigth line described by a point (120, 120) and an angle -20°
			$angle = -20;
			$px = 120;
			$py = 120;

			/* */ //just vor visualisation: the straight line to mirror against
			/* */ $pdf->SetDrawColor(200);
			/* */ $pdf->Line($px - 1, $py - 1, $px + 1, $py + 1);
			/* */ $pdf->Line($px - 1, $py + 1, $px + 1, $py - 1);
			/* */ $pdf->StartTransform();
			/* */ $pdf->Rotate($angle, $px, $py);
			/* */ $pdf->Line($px - 5, $py, $px + 60, $py);
			/* */ $pdf->StopTransform();

			$pdf->SetDrawColor(200);
			$pdf->SetTextColor(200);
			$pdf->Rect(125, 110, 40, 10, 'D');
			$pdf->Text(125, 109, 'MirrorL');
			$pdf->SetDrawColor(0);
			$pdf->SetTextColor(0);
			//Start Transformation
			$pdf->StartTransform();
			//mirror against the straight line
			$pdf->MirrorL($angle, $px, $py);
			$pdf->Rect(125, 110, 40, 10, 'D');
			$pdf->Text(125, 109, 'MirrorL');
			//Stop Transformation
			$pdf->StopTransform();

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
