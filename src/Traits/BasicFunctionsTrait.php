<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Traits;

	trait BasicFunctionsTrait {
		/**
		 * Get the actual scale factor
		 *
		 * @return float Scale factor
		 */
		public function getScaleFactor () : float {
			return $this->k;
		}

		/**
		 * Converts a value from points to the document unit
		 *
		 * @param float $val Value in points
		 * @return float Value in document units
		 */
		public function fromPt (float $val) : float {
			return $val / $this->k;
		}

		/**
		 * Converts a value from the document unit to points
		 * @param float $val Value in document units
		 * @return float Value in points
		 */
		public function toPt (float $val) : float {
			return $val * $this->k;
		}

		/**
		 * Converts a value from millimeters to points
		 *
		 * @param float $val Value in millemeters
		 * @return float Value in points
		 */
		public function mm2Pt (float $val) : float {
			return $val * 720 / 254;
		}

		/**
		 * Converts a value from centimeters to points
		 *
		 * @param float $val Value in centimeters
		 * @return float Value in points
		 */
		public function cm2Pt (float $val) : float {
			return $val * 7200 / 254;
		}

		/**
		 * Converts a value from inches to points
		 *
		 * @param float $val Value in inches
		 * @return float Value in points
		 */
		public function in2Pt (float $val) : float {
			return $val * 72;
		}
	}
