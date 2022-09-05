<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Traits;
	//https://github.com/fawno/FPDF/issues/1

	use BadMethodCallException;
	use Closure;

	trait PDFMacroableTrait {
		/**
		 * The registered string macros.
		 *
		 * @var array
		 */
		protected static $macros = [];

		/**
		 * Register a custom macro.
		 *
		 * @param  string  $name
		 * @param  callable  $macro
		 * @return void
		 */
		public static function macro ($name, $macro) {
			static::$macros[$name] = $macro;
		}

		/**
		 * Dynamically handle calls to the class.
		 *
		 * @param  string  $method
		 * @param  array  $parameters
		 * @return mixed
		 *
		 * @throws \BadMethodCallException
		 */
		public function __call ($method, $parameters) {
			if (!array_key_exists($method, static::$macros)) {
				throw new BadMethodCallException(sprintf(
					'Method %s::%s does not exist.', static::class, $method
				));
			}

			$macro = static::$macros[$method];

			if ($macro instanceof Closure) {
				$macro = $macro->bindTo($this, static::class);
			}

			return $macro(...$parameters);
		}
	}
