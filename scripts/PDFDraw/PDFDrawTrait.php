<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFDraw;
	//http://www.fpdf.org/en/script/script69.php

	trait PDFDrawTrait {
		/**
		 * Sets line style
		 *
		 * @param array $style Line style. Array with keys among the following:
		 * - width: Width of the line in user units
		 * - cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
		 * - join: miter, round or bevel
		 * - dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
		 *         For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
		 *                      (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
		 * - phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
		 * - color: Draw color. Array with components (red, green, blue)
		 * @return void
		 */
		public function SetLineStyle (array $style = []) : void {
			if (isset($style['width']) and !is_null($style['width'])) {
				$width_prev = $this->LineWidth;
				$this->SetLineWidth($style['width']);
				$this->LineWidth = $width_prev;
			}

			$cap = ['butt' => 0, 'round'=> 1, 'square' => 2];
			if (isset($style['cap']) and isset($cap[$style['cap']])) {
				$this->_out($cap[$style['cap']] . ' J');
			}

			$join = ['miter' => 0, 'round' => 1, 'bevel' => 2];
			if (isset($style['join']) and isset($join[$style['join']])) {
				$this->_out($join[$style['join']] . ' j');
			}

			if (isset($style['dash']) and !is_null($style['dash'])) {
				$dash = '';
				if ($style['dash']) {
					$dash = array_map(function($value) {
						return sprintf('%.2F', $value);
					}, explode(',', $style['dash']));
					$dash = implode(' ', $dash);
				}

				$phase = $style['dash'] ? ($style['phase'] ?? 0) : 0;

				$this->_out(sprintf('[%s] %.2F d', $dash, $phase));
			}

			if (isset($style['color']) and !is_null($style['color'])) {
				$this->SetDrawColor($style['color']);
			}
		}

		/**
		 * Draws a line between two points
		 *
		 * @param float $x1 Abscissa of first point.
		 * @param float $y1 Ordinate of first point.
		 * @param float $x2 Abscissa of second point.
		 * @param float $y2 Ordinate of second point.
		 * @param null|array $style Style of line as described in SetLineStyle
		 * @return void
		 */
		public function Line ($x1, $y1, $x2, $y2, ?array $style = []) : void {
			if ($style) {
				$this->SetLineStyle($style);
			}

			parent::Line($x1, $y1, $x2, $y2);
		}

		/**
		 * Outputs a rectangle. It can be drawn (border only), filled (with no border) or both.
		 *
		 * @param float $x Abscissa of upper-left corner.
		 * @param float $y Ordinate of upper-left corner.
		 * @param float $w Width.
		 * @param float $h Height.
		 * @param null|string $style Style of rendering. Possible values are:
		 * - D or empty string: draw. This is the default value.
		 * - F: fill
		 * - DF or FD: draw and fill
		 * @param null|array $border_style Border style of rectangle. Array with some of this index
		 * - all: Line style of all borders. Array like for SetLineStyle
		 * - L: Line style of left border. null (no border) or array like for SetLineStyle
		 * - T: Line style of top border. null (no border) or array like for SetLineStyle
		 * - R: Line style of right border. null (no border) or array like for SetLineStyle
		 * - B: Line style of bottom border. null (no border) or array like for SetLineStyle
		 * @param null|array $fill_color Fill color. Array like for SetFillColor
		 * @return void
		 */
		public function Rect ($x, $y, $w, $h, $style = 'D', ?array $border_style = [], ?array $fill_color = []) {
			$style = strtoupper($style ?? 'D');

			if (false !== strpos($style, 'F') and $fill_color) {
				$this->SetFillColor($fill_color);
			}

			switch ($style) {
				case 'F':
					$border_style = [];
					parent::Rect($x, $y, $w, $h, $style);
					break;
				case 'DF':
				case 'FD':
					if (isset($border_style['all'])) {
						$this->SetLineStyle($border_style['all']);
						$border_style = [];
					}

					if (!empty($border_style)) {
						$style = 'F';
					}

					parent::Rect($x, $y, $w, $h, $style);
					break;
				default:
					$style = 'D';
					if (isset($border_style['all'])) {
						$this->SetLineStyle($border_style['all']);
						$border_style = [];
					}
					parent::Rect($x, $y, $w, $h, $style);
					break;
			}

			if (!empty($border_style)) {
				if (isset($border_style['L']) and !empty($border_style['L'])) {
					$this->Line($x, $y, $x, $y + $h, $border_style['L']);
				}

				if (isset($border_style['T']) and !empty($border_style['T'])) {
					$this->Line($x, $y, $x + $w, $y, $border_style['T']);
				}

				if (isset($border_style['R']) and !empty($border_style['R'])) {
					$this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
				}

				if (isset($border_style['B']) and !empty($border_style['B'])) {
					$this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
				}
			}
		}

		/**
		 * Draws a Bézier curve (the Bézier curve is tangent to the line between the control points at either end of the curve)
		 *
		 * @param float $x0 Abscissa of start point
		 * @param float $y0 Ordinate of start point
		 * @param float $x1 Abscissa of control point 1
		 * @param float $y1 Ordinate of control point 1
		 * @param float $x2 Abscissa of control point 2
		 * @param float $y2 Ordinate of control point 2
		 * @param float $x3 Abscissa of end point
		 * @param float $y3 Ordinate of end point
		 * @param null|string $style Style of rendering. Possible values are:
		 * - D or empty string: draw. This is the default value.
		 * - F: fill
		 * - DF or FD: draw and fill
		 * @param null|array $line_style Style of line as described in SetLineStyle
		 * @param null|array $fill_color Fill color. Array like for SetFillColor
		 * @return void
		 */
		public function Curve (float $x0, float $y0, float $x1, float $y1, float $x2, float $y2, float $x3, float $y3, ?string $style = 'D', ?array $line_style = [], ?array $fill_color = []) {
			$style = strtoupper($style ?? 'D');

			if (false !== strpos($style, 'F') and $fill_color) {
				$this->SetFillColor($fill_color);
			}

			switch ($style) {
				case 'F':
					$op = 'f';
					$line_style = [];
					break;
				case 'FD':
				case 'DF':
					$op = 'B';
					break;
				default:
					$op = 'S';
					break;
			}

			if (!empty($line_style)) {
				$this->SetLineStyle($line_style);
			}

			$this->_Point($x0, $y0);
			$this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
			$this->_out($op);
		}

		/**
		 * Draws an ellipse
		 *
		 * @param float $x0 Abscissa of center point
		 * @param float $y0 Ordinate of center point
		 * @param float $rx Horizontal radius
		 * @param null|float $ry Vertical radius (if 0, draws a circle)
		 * @param float $angle Orientation angle (anti-clockwise)
		 * @param float $astart Start angle
		 * @param float $afinish Finish angle
		 * @param null|string $style Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
		 * @param null|array $line_style Line style for ellipse. Array like for SetLineStyle
		 * @param null|array $fill_color Fill color. Array like for SetFillColor
		 * @param int $nSeg Ellipse is made up of nSeg Bézier curves
		 * @return void
		 */
		public function Ellipse (float $x0, float $y0, float $rx, ?float $ry = 0, float $angle = 0, float $astart = 0, float $afinish = 360, ?string $style = 'D', ?array $line_style = [], ?array $fill_color = [], int $nSeg = 8) : void {
			if (!$rx) {
				return;
			}

			$style = strtoupper($style ?? 'D');

			if (false !== strpos($style, 'F') and $fill_color) {
				$this->SetFillColor($fill_color);
			}

			switch ($style) {
				case 'F':
					$op = 'f';
					$line_style = [];
					break;
				case 'FD': case 'DF':
					$op = 'B';
					break;
				case 'C':
					$op = 's'; // small 's' means closing the path as well
					break;
				default:
					$op = 'S';
					break;
			}

			if ($line_style) {
				$this->SetLineStyle($line_style);
			}

			$ry = $ry ?: $rx;

			$rx *= $this->k;
			$ry *= $this->k;

			$nSeg = ($nSeg < 2) ? 2 : $nSeg;

			$astart = deg2rad($astart);
			$afinish = deg2rad($afinish);
			$totalAngle = $afinish - $astart;

			$dt = $totalAngle / $nSeg;
			$dtm = $dt / 3;

			$x0 *= $this->k;
			$y0 = ($this->h - $y0) * $this->k;

			if ($angle != 0) {
				$a = -deg2rad($angle);
				$this->_out(sprintf('q %.2F %.2F %.2F %.2F %.2F %.2F cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
				$x0 = 0;
				$y0 = 0;
			}

			$t1 = $astart;
			$a0 = $x0 + ($rx * cos($t1));
			$b0 = $y0 + ($ry * sin($t1));
			$c0 = -$rx * sin($t1);
			$d0 = $ry * cos($t1);
			$this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
			for ($i = 1; $i <= $nSeg; $i++) {
				// Draw this bit of the total curve
				$t1 = ($i * $dt) + $astart;
				$a1 = $x0 + ($rx * cos($t1));
				$b1 = $y0 + ($ry * sin($t1));
				$c1 = -$rx * sin($t1);
				$d1 = $ry * cos($t1);

				$this->_Curve(
					($a0 + ($c0 * $dtm)) / $this->k,
					$this->h - (($b0 + ($d0 * $dtm)) / $this->k),
					($a1 - ($c1 * $dtm)) / $this->k,
					$this->h - (($b1 - ($d1 * $dtm)) / $this->k),
					$a1 / $this->k,
					$this->h - ($b1 / $this->k)
				);

				$a0 = $a1;
				$b0 = $b1;
				$c0 = $c1;
				$d0 = $d1;
			}

			$this->_out($op);

			if ($angle != 0) {
				$this->_out('Q');
			}
		}

		/**
		 * Draws a circle
		 *
		 * @param float $x0 Abscissa of center point
		 * @param float $y0 Ordinate of center point
		 * @param float $r Radius
		 * @param float $astart Start angle
		 * @param float $afinish Finish angle
		 * @param null|string $style Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
		 * @param null|array $line_style Line style for circle. Array like for SetLineStyle
		 * @param null|array $fill_color Fill color. Array like for SetFillColor
		 * @param int $nSeg
		 * @return void
		 */
		public function Circle (float $x0, float $y0, float $r, float $astart = 0, float $afinish = 360, ?string $style = 'D', ?array $line_style = [], ?array $fill_color = [], int $nSeg = 8) {
			$this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
		}

		// Draws a polygon
		// Parameters:
		// - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
		// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
		// - line_style: Line style. Array with one of this index
		//   . all: Line style of all lines. Array like for SetLineStyle
		//   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		public function Polygon (array $p, ?string $style = 'D', ?array $line_style = [], ?array $fill_color = []) {
			$style = strtoupper($style ?? 'D');

			$np = count($p) / 2;

			if (false !== strpos($style, 'F') and $fill_color) {
				$this->SetFillColor($fill_color);
			}

			switch ($style) {
				case 'F':
					$line_style = [];
					$op = 'f';
					break;
				case 'FD': case 'DF':
					$op = 'B';
					break;
				default:
					$op = 'S';
					break;
			}
			$draw = true;
			if ($line_style)
				if (isset($line_style['all']))
					$this->SetLineStyle($line_style['all']);
				else { // 0 .. (np - 1), op = {B, S}
					$draw = false;
					if ('B' == $op) {
						$op = 'f';
						$this->_Point($p[0], $p[1]);
						for ($i = 2; $i < ($np * 2); $i = $i + 2)
							$this->_Line($p[$i], $p[$i + 1]);
						$this->_Line($p[0], $p[1]);
						$this->_out($op);
					}
					$p[$np * 2] = $p[0];
					$p[($np * 2) + 1] = $p[1];
					for ($i = 0; $i < $np; $i++)
						if (!empty($line_style[$i]))
							$this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
				}

			if ($draw) {
				$this->_Point($p[0], $p[1]);
				for ($i = 2; $i < ($np * 2); $i = $i + 2)
					$this->_Line($p[$i], $p[$i + 1]);
				$this->_Line($p[0], $p[1]);
				$this->_out($op);
			}
		}

		// Draws a regular polygon
		// Parameters:
		// - x0, y0: Center point
		// - r: Radius of circumscribed circle
		// - ns: Number of sides
		// - angle: Orientation angle (anti-clockwise)
		// - circle: Draw circumscribed circle or not
		// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
		// - line_style: Line style. Array with one of this index
		//   . all: Line style of all lines. Array like for SetLineStyle
		//   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		// - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
		// - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
		// - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
		public function RegularPolygon (float $x0, float $y0, float $r, int $ns, float $angle = 0, $circle = false, ?string $style = 'D', ?array $line_style = [], ?array $fill_color = [], ?string $circle_style = 'D', ?array $circle_line_style = [], ?array $circle_fill_color = []) {
			if ($ns < 3)
				$ns = 3;
			if ((bool) $circle)
				$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
			$p = null;
			for ($i = 0; $i < $ns; $i++) {
				$a = $angle + ($i * 360 / $ns);
				$a_rad = deg2rad((float) $a);
				$p[] = $x0 + ($r * sin($a_rad));
				$p[] = $y0 + ($r * cos($a_rad));
			}
			$this->Polygon($p, $style, $line_style, $fill_color);
		}

		// Draws a star polygon
		// Parameters:
		// - x0, y0: Center point
		// - r: Radius of circumscribed circle
		// - nv: Number of vertices
		// - ng: Number of gaps (ng % nv = 1 => regular polygon)
		// - angle: Orientation angle (anti-clockwise)
		// - circle: Draw circumscribed circle or not
		// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
		// - line_style: Line style. Array with one of this index
		//   . all: Line style of all lines. Array like for SetLineStyle
		//   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		// - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
		// - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
		// - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
		public function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
			if ($nv < 2)
				$nv = 2;
			if ($circle)
				$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
			$p2 = null;
			$visited = null;
			for ($i = 0; $i < $nv; $i++) {
				$a = $angle + ($i * 360 / $nv);
				$a_rad = deg2rad((float) $a);
				$p2[] = $x0 + ($r * sin($a_rad));
				$p2[] = $y0 + ($r * cos($a_rad));
				$visited[] = false;
			}
			$p = null;
			$i = 0;
			do {
				$p[] = $p2[$i * 2];
				$p[] = $p2[($i * 2) + 1];
				$visited[$i] = true;
				$i += $ng;
				$i %= $nv;
			} while (!$visited[$i]);
			$this->Polygon($p, $style, $line_style, $fill_color);
		}

		// Draws a rounded rectangle
		// Parameters:
		// - x, y: Top left corner
		// - w, h: Width and height
		// - r: Radius of the rounded corners
		// - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
		// - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
		// - border_style: Border style of rectangle. Array like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		public function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
			if ('0000' == $round_corner) // Not rounded
				$this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
			else { // Rounded
				if (!(false === strpos((string) $style, 'F')) && $fill_color) {
					list($red, $g, $b) = $fill_color;
					$this->SetFillColor($red, $g, $b);
				}
				switch ($style) {
					case 'F':
						$border_style = null;
						$op = 'f';
						break;
					case 'FD': case 'DF':
						$op = 'B';
						break;
					default:
						$op = 'S';
						break;
				}
				if ($border_style)
					$this->SetLineStyle($border_style);

				$MyArc = 4 / 3 * (sqrt(2) - 1);

				$this->_Point($x + $r, $y);
				$xc = $x + $w - $r;
				$yc = $y + $r;
				$this->_Line($xc, $y);
				if ($round_corner[0])
					$this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
				else
					$this->_Line($x + $w, $y);

				$xc = $x + $w - $r ;
				$yc = $y + $h - $r;
				$this->_Line($x + $w, $yc);

				if ($round_corner[1])
					$this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
				else
					$this->_Line($x + $w, $y + $h);

				$xc = $x + $r;
				$yc = $y + $h - $r;
				$this->_Line($xc, $y + $h);
				if ($round_corner[2])
					$this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
				else
					$this->_Line($x, $y + $h);

				$xc = $x + $r;
				$yc = $y + $r;
				$this->_Line($x, $yc);
				if ($round_corner[3])
					$this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
				else {
					$this->_Line($x, $y);
					$this->_Line($x + $r, $y);
				}
				$this->_out($op);
			}
		}

		/**
		 * Sets a draw point
		 *
		 * @param float $x
		 * @param float $y
		 * @return void
		 */
		protected function _Point (float $x, float $y) {
			$this->_out(sprintf('%.2F %.2F m', $x * $this->k, ($this->h - $y) * $this->k));
		}

		/**
		 * Draws a line from last draw point
		 *
		 * @param float $x
		 * @param float $y
		 * @return void
		 */
		protected function _Line (float $x, float $y) {
			$this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $y) * $this->k));
		}

		/**
		 * Draws a Bézier curve from last draw point
		 *
		 * @param float $x1 Control point 1 abscissa
		 * @param float $y1 Control point 1 ordinate
		 * @param float $x2 Control point 2 abscissa
		 * @param float $y2 Control point 2 ordinate
		 * @param float $x3 End point abscissa
		 * @param float $y3 End point ordinate
		 * @return void
		 */
		protected function _Curve (float $x1, float $y1, float $x2, float $y2, float $x3, float $y3) {
			$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
		}
	}
