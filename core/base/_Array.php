<?php

class _Array {
	var $ar;

	function set($ar) {
		$this->ar = $ar;
		return $this;
	}

	function get() {
		return $this->ar;
	}

	public function sort($flags = SORT_REGULAR) {
		sort($this->ar, $flags);
		return $this;
	}

	public function count() {
		return count($this->ar);
	}

	function __toString() {
		return var_export($this->ar, true);
	}

	/**
	 * Sort  multi-dimensional arrays
	 * @link https://infospector.ru
	 * @param ar array <p>* An array being sorted. * </p>
	 * @param $sort string[optional] <p>
	 * Optionally string sort options for the
	 * next argument via ":" [ASC | DESC]
	 * example: array_multikey_sort($ar,'key[[:DESC | :ASC], key[:DESC | :ASC]...]');
	 * @return nothing!
	 */
	static function multikey_sort(&$ar, $sort) {
		usort($ar, function ($a, $b) use($sort) {
			$ar = explode(',', $sort);
			foreach ( $ar as $v => $k ) {
				$ark = explode(':', trim($k));
				$key = trim($ark[0]);
				$order = (isset($ark[1])) ? strtoupper(trim($ark[1])) : 'ASC';
				
				if (isset($a[$key])) {
					$res = strnatcmp($a[$key], @$b[$key]);
					if ($res != 0)
						return ($order == 'ASC') ? $res : $res * -1;
				} else
					return ($order == 'ASC') ? -1 : 1;
			}
			return 0;
		});
	}
}