<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFTransform;
	//https://www.fpdf.org/en/script/script79.php

	trait PDFTransformTrait {
		/**
		 * Save the current graphic state
		 *
		 * Use this before calling any tranformation.
		 *
		 * @return void
		 */
		public function StartTransform () : void {
			$this->_out('q');
		}

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
		public function ScaleX (float $s_x, ?float $x = null, ?float $y = null) : void {
			$this->Scale($s_x, 100, $x, $y);
		}

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
		public function ScaleY (float $s_y, ?float $x = null, ?float $y = null) : void {
			$this->Scale(100, $s_y, $x, $y);
		}

		/**
		 * Scaling is obtained by [ s 0 0 s tx ty ].
		 *
		 * @param float $s Scaling factor for width and height as percent. 0 is not allowed.
		 * @param null|float $x Abscissa of the scaling center. Default is current x position
		 * @param null|float $y Ordinate of the scaling center. Default is current y position
		 * @return void
		 */
		public function ScaleXY(float $s, ?float $x = null, ?float $y = null) : void {
			$this->Scale($s, $s, $x, $y);
		}

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
		public function Scale (float $s_x, float $s_y, ?float $x = null, ?float $y = null) : void {
			$x = $x ?? $this->x;
			$y = $y ?? $this->y;

			if ($s_x == 0 || $s_y == 0) {
				$this->Error('Please use values unequal to zero for Scaling');
			}

			$y = ($this->h - $y) * $this->k;
			$x *= $this->k;
			$s_x /= 100;
			$s_y /= 100;

			//calculate elements of transformation matrix
			//[ sx 0 0 sy tx ty ]
			$a = $s_x;
			$b = 0;
			$c = 0;
			$d = $s_y;
			$e = $x * (1 - $s_x);
			$f = $y * (1 - $s_y);

			//scale the coordinate system
			$this->Transform([$a, $b, $c, $d, $e, $f]);
		}

		/**
		 * Alias for scaling -100% in x-direction
		 *
		 * @param null|float $x Abscissa of the axis of reflection
		 * @return void
		 */
		public function MirrorH (?float $x = null) : void {
			$this->Scale(-100, 100, $x);
		}

		/**
		 * Alias for scaling -100% in y-direction
		 *
		 * @param null|float $y Ordinate of the axis of reflection
		 * @return void
		 */
		public function MirrorV (?float $y = null) : void {
			$this->Scale(100, -100, null, $y);
		}

		/**
		 * Point reflection on point (x, y). (alias for scaling -100 in x- and y-direction)
		 *
		 * @param null|float $x Abscissa of the point. Default is current x position
		 * @param null|float $y Ordinate of the point. Default is current y position
		 * @return void
		 */
		public function MirrorP (?float $x = null, ?float $y = null) : void {
			$this->Scale(-100, -100, $x, $y);
		}

		/**
		 * Reflection against a straight line through point (x, y) with the gradient angle (angle).
		 *
		 * @param float $angle Gradient angle of the straight line. Default is 0 (horizontal line).
		 * @param null|float $x Abscissa of the point. Default is current x position
		 * @param null|float $y Ordinate of the point. Default is current y position
		 * @return void
		 */
		public function MirrorL (float $angle = 0, ?float $x = null, ?float $y = null) : void {
			$this->Scale(-100, 100, $x, $y);
			$this->Rotate(-2 * ($angle - 90), $x, $y);
		}

		/**
		 * Translate to the right
		 *
		 * @param float $t_x Movement to the right
		 * @return void
		 */
		public function TranslateX (float $t_x) : void {
			$this->Translate($t_x, 0);
		}

		/**
		 * Translate to the bottom
		 *
		 * @param float $t_y Movement to the bottom
		 * @return void
		 */
		public function TranslateY (float $t_y) : void {
			$this->Translate(0, $t_y);
		}

		/**
		 * Translate to the right and to the bottom
		 *
		 * @param float $t_x Movement to the right
		 * @param float $t_y Movement to the bottom
		 * @return void
		 */
		public function Translate (float $t_x, float $t_y) : void {
			//calculate elements of transformation matrix
			$a = 1;
			$b = 0;
			$c = 0;
			$d = 1;
			$e = $t_x * $this->k;
			$f = -$t_y * $this->k;

			//translate the coordinate system
			$this->Transform([$a, $b, $c, $d, $e, $f]);
		}

		/**
		 * Rotate counter-clockwise
		 *
		 * @param float $angle Angle in degrees for counter-clockwise rotation
		 * @param null|float $x Abscissa of the rotation center. Default is current x position
		 * @param null|float $y Ordinate of the rotation center. Default is current y position
		 * @return void
		 */
		public function Rotate (float $angle, ?float $x = null, ?float $y = null) : void {
			$x = $x ?? $this->x;
			$y = $y ?? $this->y;

			$y = ($this->h - $y) * $this->k;
			$x *= $this->k;

			//calculate elements of transformation matrix
			$a = cos(deg2rad($angle));
			$b = sin(deg2rad($angle));
			$c = -$b;
			$d = $a;
			$e = $x + $b * $y - $a * $x;
			$f = $y - $a * $y - $b * $x;

			//rotate the coordinate system around ($x,$y)
			$this->Transform([$a, $b, $c, $d, $e, $f]);
		}

		/**
		 *
		 *
		 * @param float $angle_x Angle in degrees between -90 (skew to the left) and 90 (skew to the right)
		 * @param null|float $x Abscissa of the skewing center. default is current x position
		 * @param null|float $y Ordinate of the skewing center. default is current y position
		 * @return void
		 */
		public function SkewX (float $angle_x, ?float $x = null, ?float $y = null) : void {
			$this->Skew($angle_x, 0, $x, $y);
		}

		/**
		 * @param float $angle_y Angle in degrees between -90 (skew to the bottom) and 90 (skew to the top)
		 * @param null|float $x Abscissa of the skewing center. default is current x position
		 * @param null|float $y Ordinate of the skewing center. default is current y position
		 * @return void
		 */
		public function SkewY (float $angle_y, ?float $x = null, ?float $y = null) : void {
			$this->Skew(0, $angle_y, $x, $y);
		}

		/**
		 * @param float $angle_x Angle in degrees between -90 (skew to the left) and 90 (skew to the right)
		 * @param float $angle_y Angle in degrees between -90 (skew to the bottom) and 90 (skew to the top)
		 * @param null|float $x Abscissa of the skewing center. default is current x position
		 * @param null|float $y Ordinate of the skewing center. default is current y position
		 * @return void
		 */
		public function Skew (float $angle_x, float $angle_y, ?float $x = null, ?float $y = null) : void {
			$x = $x ?? $this->x;
			$y = $y ?? $this->y;

			if ($angle_x <= -90 || $angle_x >= 90 || $angle_y <= -90 || $angle_y >= 90) {
				$this->Error('Please use values between -90° and 90° for skewing');
			}

			$x *= $this->k;
			$y = ($this->h - $y) * $this->k;

			//calculate elements of transformation matrix
			$a = 1;
			$b = tan(deg2rad($angle_y));
			$c = tan(deg2rad($angle_x));
			$d = 1;
			$e = -$c * $y;
			$f = -$b * $x;
			//skew the coordinate system
			$this->Transform([$a, $b, $c, $d, $e, $f]);
		}

		/**
		 * Concatenate matrix to current transformation matrix
		 *
		 * @param float[] $tm Transformation matrix [ a, b, c, d, e, f ]
		 * @return void
		 */
		public function Transform (array $tm) : void {
			$this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', ...$tm));
		}

		/**
		 * Restores the normal painting and placing behavior as it was before calling StartTransform().
		 *
		 * @return void
		 */
		public function StopTransform () : void {
			$this->_out('Q');
		}
	}
