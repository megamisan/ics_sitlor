<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 In Cite Solution <technique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the ValuedTermTuples of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Class 'tx_icssitlorquery_ValuedTermTuple' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_ValuedTermTuple implements tx_icssitquery_IToStringObjConf {
	private $count;
	private $tag;
	private $items = array();

	private static $lConf = array();

	/**
	 * Constructor
	 *
	 * @param	int $count
	 * @param	string $tag
	 * @return	void
	 */
	public function __construct($count, $tag='') {
		$this->count = $count;
		$this->tag = $tag;
		for ($i=0; $i<$this->count; $i++) {
			$this->items[$i] = null;
		}
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$name: ...
	 * @return	[type]		...
	 */
	public function __get($name) {
		if ($name=='Count')
			return $this->count;

		if ((strlen($name)>=5) && (substr($name, 0, 4)=='Item')) {
			$numItem = substr($name, 4);
			if (is_numeric($numItem) && $numItem<=$this->count && $numItem>0)
				return $this->Get(intval($numItem) - 1);
			else
				tx_icssitquery_debug::notice('Undefined property of ValuedTermTuple via __get(): ' . $name);
		} else {
			tx_icssitquery_debug::notice('Undefined property of ValuedTermTuple via __get(): ' . $name);
		}
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$name: ...
	 * @param	[type]		$value: ...
	 * @return	[type]		...
	 */
	public function __set($name, $value) {
		if ((strlen($name)>=5) && (substr($name,0,4)=='Item')) {
			$numItem = substr($name, 4);
			if (is_numeric($numItem) && $numItem<=$this->count && $numItem>0) {
				$this->Set(intval($numItem) - 1, $value);
			} else {
				tx_icssitquery_debug::notice('Undefined property of ValuedTermTuple via __set(): ' . $name);
			}
		} else {
			tx_icssitquery_debug::notice('Undefined property of ValuedTermTuple via __set(): ' . $name);
		}
	}

	/**
	 * Retrieves ValuedTerm
	 *
	 * @param	int $number
	 * @return ValuedTerm
	 */
	public function Get($number) {
		if ($number<$this->count && $number>=0)
			return $this->items[$number];
		else
			tx_icssitquery_debug::warning('Index out of range for ValuedTermTuple. Only ' . $this->count . ' items against ' . $number . ' requested via __get().');
	}

	/**
	 * Set ValuedTerm
	 *
	 * @param	int $number
	 * @param	ValuedTerm $value
	 * @return void
	 */
	public function Set($number, tx_icssitlorquery_ValuedTerm $value = null) {
		if ($number<$this->count)
			$this->items[$number] = $value;
		else
			tx_icssitquery_debug::warning('Index out of range for ValuedTermTuple. Only ' . $this->count . ' items against ' . $number . ' requested via __set().');
	}

	/**
	 * Set default
	 *
	 * @param	array $conf
	 * @return void
	 */
	public function SetDefaultConf(array $conf) {
		self::$lConf = $conf;
	}

	/**
	 * Convert object to display as string
	 *
	 * @return string
	 */
	public function __toString() {
		$confDefault = array();
		$numargs = func_num_args();
		if ($numargs==0)
			return $this->toStringConf($confDefault);

		// $numargs >0
		$args = func_get_args();
		if (is_array($args[0]))
			return $this->toStringConf($args[0]);

		if ($args[0] instanceof tslib_cObj) {
			if ($numargs==1)
				return toStringCObj($args[0], $confDefault);
			return toStringCObj($args[0], $args[1]);
		}

		tx_icssitquery_debug::warning('Can not convert ValuedTermTuples to string, args :' . $args);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	public function toString() {
		return $this->toStringConf(self::$lConf);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$array $conf: ...
	 * @return	[type]		...
	 */
	public function toStringConf(array $conf) {
		$cObj = t3lib_div::makeIsntance('tslib_cObj');
		return $this->toStringObjConf($cObj, $conf);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$tslib_cObj $cObj: ...
	 * @return	[type]		...
	 */
	public function toStringObj(tslib_cObj $cObj) {
		return $this->toStringObjConf($cObj, self::$lConf);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$tslib_cObj $cObj, array $conf: ...
	 * @return	[type]		...
	 */
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
		return 'ValuedTermTuples';
	}
}