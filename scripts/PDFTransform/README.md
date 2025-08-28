# PDFTransformTrait
![GitHub license](https://img.shields.io/badge/license-FPDF-green)
[![Author](https://img.shields.io/badge/author-Moritz_Wagner-blue)](mailto:wagner-moritz@gmx.de?subject=Transformations)
[![Author](https://img.shields.io/badge/author-Andreas_Würmser-blue)](mailto:fpdf@kreativschmiede.de?subject=Transformations)

Performs the following 2D transformations: scaling, mirroring, translation, rotation and skewing.

## Usage
Use StartTransform() before, and StopTransform() after the transformations to restore the normal behavior.

The public methods are:

```php
/**
 * Save the current graphic state
 *
 * Use this before calling any tranformation.
 *
 * @return void
 */
PDFTransformTrait::StartTransform();

/**
 * Restores the normal painting and placing behavior as it was before calling StartTransform().
 *
 * @return void
 */
PDFTransformTrait::StopTransform();

/**
 * Scaling is obtained by [ sx 0 0 1 tx ty ].
 *
 * Scaling factor for height is unchanged
 *
 * @param float $s_x Scaling factor for width as percent. 0 is not allowed.
 * @param null|float $x Abscissa of the scaling center. Default is current x position
 * @param null|float $y Ordinate of the scaling center. Default is current y position
 * @return void
 */
PDFTransformTrait::ScaleX(float $s_x [, ?float $x [, ?float $y]]);

/**
 * Scaling is obtained by [ sy 0 0 1 tx ty ].
 *
 * Scaling factor for width is unchanged
 *
 * @param float $s_y Scaling factor for height as percent. 0 is not allowed.
 * @param null|float $x Abscissa of the scaling center. Default is current x position
 * @param null|float $y Ordinate of the scaling center. Default is current y position
 * @return void
 */
PDFTransformTrait::ScaleY(float $s_y [, ?float $x [, ?float $y]]);

/**
 * Scaling is obtained by [ s 0 0 s tx ty ].
 *
 * @param float $s Scaling factor for width and height as percent. 0 is not allowed.
 * @param null|float $x Abscissa of the scaling center. Default is current x position
 * @param null|float $y Ordinate of the scaling center. Default is current y position
 * @return void
 */
PDFTransformTrait::ScaleXY(float $s [, ?float $x [, ?float $y]]);

/**
 * Scaling is obtained by [ sx 0 0 sy tx ty ].
 *
 * This scales the coordinates so that 1 unit in the horizontal and vertical dimensions of the new coordinate system is the same size as sx and sy units, respectively, in the previous coordinate system.
 *
 * @param float $s_x Scaling factor for width as percent. 0 is not allowed.
 * @param float $s_y Scaling factor for height as percent. 0 is not allowed.
 * @param null|float $x Abscissa of the scaling center. Default is current x position
 * @param null|float $y Ordinate of the scaling center. Default is current y position
 * @return void
 */
PDFTransformTrait::Scale(float $s_x, float $s_y [, ?float $x [, ?float $y]]);

/**
 * Alias for scaling -100% in x-direction
 *
 * @param null|float $x Abscissa of the axis of reflection
 * @return void
 */
PDFTransformTrait::MirrorH([?float $x]);

/**
 * Alias for scaling -100% in y-direction
 *
 * @param null|float $y Ordinate of the axis of reflection
 * @return void
 */
PDFTransformTrait::MirrorV([?float $y]);

/**
 * Point reflection on point (x, y). (alias for scaling -100 in x- and y-direction)
 *
 * @param null|float $x Abscissa of the point. Default is current x position
 * @param null|float $y Ordinate of the point. Default is current y position
 * @return void
 */
PDFTransformTrait::MirrorP([?float $x [, ?float $y]]);

/**
 * Reflection against a straight line through point (x, y) with the gradient angle (angle).
 *
 * @param float $angle Gradient angle of the straight line. Default is 0 (horizontal line).
 * @param null|float $x Abscissa of the point. Default is current x position
 * @param null|float $y Ordinate of the point. Default is current y position
 * @return void
 */
PDFTransformTrait::MirrorL(float $angle = 0 [, ?float $x [, ?float $y]]);

/**
 * Translate to the right
 *
 * @param float $t_x Movement to the right
 * @return void
 */
PDFTransformTrait::TranslateX(float $t_x);

/**
 * Translate to the bottom
 *
 * @param float $t_y Movement to the bottom
 * @return void
 */
PDFTransformTrait::TranslateY(float $t_y);

/**
 * Translate to the right and to the bottom
 *
 * @param float $t_x Movement to the right
 * @param float $t_y Movement to the bottom
 * @return void
 */
PDFTransformTrait::Translate(float $t_x, float $t_y);

/**
 * Rotate counter-clockwise
 *
 * @param float $angle Angle in degrees for counter-clockwise rotation
 * @param null|float $x Abscissa of the rotation center. Default is current x position
 * @param null|float $y Ordinate of the rotation center. Default is current y position
 * @return void
 */
PDFTransformTrait::Rotate(float $angle [, ?float $x [, ?float $y]]);

/**
 *
 *
 * @param float $angle_x Angle in degrees between -90 (skew to the left) and 90 (skew to the right)
 * @param null|float $x Abscissa of the skewing center. default is current x position
 * @param null|float $y Ordinate of the skewing center. default is current y position
 * @return void
 */
PDFTransformTrait::SkewX(float $angle_x [, ?float $x [, ?float $y]]);

/**
 * @param float $angle_y Angle in degrees between -90 (skew to the bottom) and 90 (skew to the top)
 * @param null|float $x Abscissa of the skewing center. default is current x position
 * @param null|float $y Ordinate of the skewing center. default is current y position
 * @return void
 */
PDFTransformTrait::SkewY(float $angle_y [, ?float $x [, ?float $y]]);

/**
 * @param float $angle_x Angle in degrees between -90 (skew to the left) and 90 (skew to the right)
 * @param float $angle_y Angle in degrees between -90 (skew to the bottom) and 90 (skew to the top)
 * @param null|float $x Abscissa of the skewing center. default is current x position
 * @param null|float $y Ordinate of the skewing center. default is current y position
 * @return void
 */
PDFTransformTrait::Skew(float $angle_x, float $angle_y [, ?float $x [, ?float $y]]);

/**
 * Concatenate matrix to current transformation matrix
 *
 * @param float[] $tm Transformation matrix [ a, b, c, d, e, f ]
 * @return void
 */
PDFTransformTrait::Transform(array $tm);

```

## Example

```php
<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
require __DIR__ . '/PDFTransformTrait.php';

use FPDF\Scripts\PDFTransform\PDFTransformTrait;

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

$pdf->Output('F', __DIR__ . '/example.pdf');
```
[Result](ex.pdf)
