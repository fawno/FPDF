<?php
	namespace Fawno\FPDF;

	if (class_exists('setasign\Fpdi\Fpdi')) {
		class FawnoPDF extends \setasign\Fpdi\Fpdi {};
	} else {
		class FawnoPDF extends \FPDF {};
	}

	class FawnoFPDF extends FawnoPDF {

		function SetDrawColor($r = null, $g = null, $b = null) {
			$args = func_get_args();
			if (count($args) and is_array($args[0])) {
				$args = $args[0];
			}

			switch (count($args)) {
				case 1:
					$this->DrawColor = sprintf('%.3f G', $args[0] / 100);
					break;
				case 3:
					$this->DrawColor = sprintf('%.3f %.3f %.3f RG', $args[0] / 255, $args[1] / 255, $args[2] / 255);
					break;
				case 4:
					$this->DrawColor = sprintf('%.3f %.3f %.3f %.3f K', $args[0] / 100, $args[1] / 100, $args[2] / 100, $args[3] / 100);
					break;
				default:
					$this->DrawColor = '0 G';
			}

			if ($this->page > 0) {
				$this->_out($this->DrawColor);
			}
		}

		public function SetFillColor ($r = null, $g = null, $b = null) {
			$args = func_get_args();
			if (count($args) and is_array($args[0])) {
				$args = $args[0];
			}

			switch (count($args)) {
				case 1:
					$this->FillColor = sprintf('%.3f g', $args[0] / 100);
					break;
				case 3:
					$this->FillColor = sprintf('%.3f %.3f %.3f rg', $args[0] / 255, $args[1] / 255, $args[2] / 255);
					break;
				case 4:
					$this->FillColor = sprintf('%.3f %.3f %.3f %.3f k', $args[0] / 100, $args[1] / 100, $args[2] / 100, $args[3] / 100);
					break;
				default:
					$this->FillColor = '0 g';
			}

			$this->ColorFlag = ($this->FillColor != $this->TextColor);
			if ($this->page > 0) {
				$this->_out($this->FillColor);
			}
		}

		public function SetTextColor ($r = null, $g = null, $b = null) {
			//Set color for text
			$args = func_get_args();
			if (count($args) and is_array($args[0])) {
				$args = $args[0];
			}

			switch (count($args)) {
				case 1:
					$this->TextColor = sprintf('%.3f g', $args[0] / 100);
					break;
				case 3:
					$this->TextColor = sprintf('%.3f %.3f %.3f rg', $args[0] / 255, $args[1] / 255, $args[2] / 255);
					break;
				case 4:
					$this->TextColor = sprintf('%.3f %.3f %.3f %.3f k', $args[0] / 100, $args[1] / 100, $args[2] / 100, $args[3] / 100);
					break;
				default:
					$this->TextColor = '0 g';
			}

			$this->ColorFlag = ($this->FillColor != $this->TextColor);
		}

		public function TextWithDirection ($x, $y, $txt, $direction = 'R', $font_angle = 0) {
			$line_angle = ['R' => 0, 'U' => 90, 'L' => 180, 'D' => 270];
			if (!empty($line_angle[$direction])) {
				$this->TextWithRotation($x, $y, $txt, $line_angle[$direction], $font_angle);
			} else {
				$this->TextWithRotation($x, $y, $txt, 0, $font_angle);
			}
		}

		public function TextWithRotation ($x, $y, $txt, $line_angle = 0, $font_angle = 0) {
			$x = $x * $this->k;
			$y = ($this->h - $y) * $this->k;

			if ($line_angle != 0 or $font_angle != 0) {
				$font_angle = 90 + $line_angle - $font_angle;
				$line_angle *= M_PI / 180;
				$font_angle *= M_PI / 180;

				$text_matrix = [cos($line_angle), sin($line_angle), cos($font_angle), sin($font_angle), $x, $y, $this->_escape($txt)];
				$s = vsprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET', $text_matrix);
			} else {
				$s = sprintf('BT %.2f %.2f Td (%s) Tj ET', $x, $y, $this->_escape($txt));
			}

			if ($this->ColorFlag) {
				$s = sprintf('q %s %s Q', $this->TextColor, $s);
			}
			$this->_out($s);
		}

	// Author: David Hernández Sanz
	// License: FPDF
	// Sets line style
	// Parameters:
	// - style: Line style. Array with keys among the following:
	//   . width: Width of the line in user units
	//   . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
	//   . join: miter, round or bevel
	//   . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
	//           For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
	//                        (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
	//   . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
	//   . color: Draw color. Array with components (red, green, blue)
	function SetLineStyle($style) {
		extract($style);
		if (isset($width)) {
			$width_prev = $this->LineWidth;
			$this->SetLineWidth($width);
			$this->LineWidth = $width_prev;
		}
		if (isset($cap)) {
			$ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
			if (isset($ca[$cap]))
				$this->_out($ca[$cap] . ' J');
		}
		if (isset($join)) {
			$ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
			if (isset($ja[$join]))
				$this->_out($ja[$join] . ' j');
		}
		if (isset($dash)) {
			$dash_string = '';
			if ($dash) {
				$tab = explode(',', $dash);
				$dash_string = '';
				foreach ($tab as $i => $v) {
					if ($i > 0)
						$dash_string .= ' ';
					$dash_string .= sprintf('%.2F', $v);
				}
			}
			if (!isset($phase) || !$dash)
				$phase = 0;
			$this->_out(sprintf('[%s] %.2F d', $dash_string, $phase));
		}
		if (isset($color)) {
			//list($r, $g, $b) = $color;
			$this->SetDrawColor($color);
		}
	}

	function Line($x1, $y1, $x2, $y2, $style = null) {
		if (!empty($style)) $this->SetLineStyle($style);
		parent::Line($x1, $y1, $x2, $y2);
	}

	// Draws a rectangle
	// Parameters:
	// - x, y: Top left corner
	// - w, h: Width and height
	// - style: Style of rectangle (draw and/or fill: D, F, DF, FD)
	// - border_style: Border style of rectangle. Array with some of this index
	//   . all: Line style of all borders. Array like for SetLineStyle
	//   . L: Line style of left border. null (no border) or array like for SetLineStyle
	//   . T: Line style of top border. null (no border) or array like for SetLineStyle
	//   . R: Line style of right border. null (no border) or array like for SetLineStyle
	//   . B: Line style of bottom border. null (no border) or array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
				$border_style = null;
				parent::Rect($x, $y, $w, $h, $style);
				break;
			case 'DF': case 'FD':
				if (!$border_style || isset($border_style['all'])) {
					if (isset($border_style['all'])) {
						$this->SetLineStyle($border_style['all']);
						$border_style = null;
					}
				} else
					$style = 'F';
				parent::Rect($x, $y, $w, $h, $style);
				break;
			default:
				if (!$border_style || isset($border_style['all'])) {
					if (isset($border_style['all']) && $border_style['all']) {
						$this->SetLineStyle($border_style['all']);
						$border_style = null;
					}
					parent::Rect($x, $y, $w, $h, $style);
				}
				break;
		}
		if ($border_style) {
			if (isset($border_style['L']) && $border_style['L'])
				$this->Line($x, $y, $x, $y + $h, $border_style['L']);
			if (isset($border_style['T']) && $border_style['T'])
				$this->Line($x, $y, $x + $w, $y, $border_style['T']);
			if (isset($border_style['R']) && $border_style['R'])
				$this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
			if (isset($border_style['B']) && $border_style['B'])
				$this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
		}
	}

	// Draws a B�zier curve (the B�zier curve is tangent to the line between the control points at either end of the curve)
	// Parameters:
	// - x0, y0: Start point
	// - x1, y1: Control point 1
	// - x2, y2: Control point 2
	// - x3, y3: End point
	// - style: Style of rectangule (draw and/or fill: D, F, DF, FD)
	// - line_style: Line style for curve. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
				$op = 'f';
				$line_style = null;
				break;
			case 'FD': case 'DF':
				$op = 'B';
				break;
			default:
				$op = 'S';
				break;
		}
		if ($line_style)
			$this->SetLineStyle($line_style);

		$this->_Point($x0, $y0);
		$this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
		$this->_out($op);
	}

	// Draws an ellipse
	// Parameters:
	// - x0, y0: Center point
	// - rx, ry: Horizontal and vertical radius (if ry = 0, draws a circle)
	// - angle: Orientation angle (anti-clockwise)
	// - astart: Start angle
	// - afinish: Finish angle
	// - style: Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
	// - line_style: Line style for ellipse. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - nSeg: Ellipse is made up of nSeg B�zier curves
	function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
		if ($rx) {
			if (!(false === strpos($style, 'F')) && $fill_color) {
				list($r, $g, $b) = $fill_color;
				$this->SetFillColor($r, $g, $b);
			}
			switch ($style) {
				case 'F':
					$op = 'f';
					$line_style = null;
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
			if ($line_style)
				$this->SetLineStyle($line_style);
			if (!$ry)
				$ry = $rx;
			$rx *= $this->k;
			$ry *= $this->k;
			if ($nSeg < 2)
				$nSeg = 2;

			$astart = deg2rad((float) $astart);
			$afinish = deg2rad((float) $afinish);
			$totalAngle = $afinish - $astart;

			$dt = $totalAngle/$nSeg;
			$dtm = $dt/3;

			$x0 *= $this->k;
			$y0 = ($this->h - $y0) * $this->k;
			if ($angle != 0) {
				$a = -deg2rad((float) $angle);
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
				$this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
							$this->h - (($b0 + ($d0 * $dtm)) / $this->k),
							($a1 - ($c1 * $dtm)) / $this->k,
							$this->h - (($b1 - ($d1 * $dtm)) / $this->k),
							$a1 / $this->k,
							$this->h - ($b1 / $this->k));
				$a0 = $a1;
				$b0 = $b1;
				$c0 = $c1;
				$d0 = $d1;
			}
			$this->_out($op);
			if ($angle !=0)
				$this->_out('Q');
		}
	}

	// Draws a circle
	// Parameters:
	// - x0, y0: Center point
	// - r: Radius
	// - astart: Start angle
	// - afinish: Finish angle
	// - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
	// - line_style: Line style for circle. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - nSeg: Ellipse is made up of nSeg B�zier curves
	function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
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
	function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
		$np = count($p) / 2;
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
				$line_style = null;
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
	function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
		if ($ns < 3)
			$ns = 3;
		if ($circle)
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
	function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
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
	function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
		if ('0000' == $round_corner) // Not rounded
			$this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
		else { // Rounded
			if (!(false === strpos($style, 'F')) && $fill_color) {
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

	/* PRIVATE METHODS */

	// Sets a draw point
	// Parameters:
	// - x, y: Point
	function _Point($x, $y) {
		$this->_out(sprintf('%.2F %.2F m', $x * $this->k, ($this->h - $y) * $this->k));
	}

	// Draws a line from last draw point
	// Parameters:
	// - x, y: End point
	function _Line($x, $y) {
		$this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $y) * $this->k));
	}

	// Draws a B�zier curve from last draw point
	// Parameters:
	// - x1, y1: Control point 1
	// - x2, y2: Control point 2
	// - x3, y3: End point
	function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
	}
}
