<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In Cite Solution <technique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
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
 *
 *
 *   46: class tx_icssitlorquery_TimeEntry
 *   52:     public function __construct($dow, $start=0, $end=0, $isPM=false)
 *   68:     public function __get($name)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitlorquery_TimeEntry' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_TimeEntry implements tx_icssitquery_IToStringObjConf {
	private $dayOfWeek;	// int : ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)
	private $start;	// int
	private $end;	// int
	private $isPM;	// bool

	private static $lConf = array();

	public function __construct($dow, $start=0, $end=0, $isPM=false) {
		if (!is_int($dow))
			throw new Exception('Time entry Day of week must be integer.');

		$this->dayOfWeek = $dow;
		$this->start = $start;
		$this->end = $end;
		$this->isPM = $isPM;
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) {
			case 'DayOfWeek':
				return $this->dayOfWeek;
			case 'Start':
				return $this->start;
			case 'End':
				return $this->end;
			case 'IsPM':
				return $this->isPM;
			default:
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Sets default TypoScript configuration.
	 *
	 * @param	array		$conf: The new default configuration.
	 * @return	void
	 */
	public static function SetDefaultConf(array $conf) {
		self::$lConf = $conf;
	}

	/**
	 * Converts this object to its string representation. PHP magic function.
	 *
	 * @return	string		Representation of the object.
	 */
	public function __toString() {
		$args = func_get_args();
		return (string)call_user_func_array(array($this, 'toString'), $args);
	}

	/**
	 * Converts this object to its string representation.
	 * Using default output settings.
	 *
	 * @return	string		Representation of the object.
	 */
	public function toString() {
		switch (func_num_args()) {
			case 0:
				return $this->toStringConf(self::$lConf);
			case 1:
				$a1 = func_get_arg(0);
				if (is_array($a1)) {
					return $this->toStringConf($a1);
				}
				else if ($a1 instanceof tslib_cObj) {
					return $this->toStringObj($a1);
				}
			default:
				$args = func_get_args();
				return call_user_func_array(array($this, 'toStringObjConf'), $args);
		}
	}

	/**
	 * Converts this object to its string representation.
	 * Uses the specified TypoScript configuration.
	 *
	 * @param	array		$conf: TypoScript configuration to use to render this object.
	 * @return	string		Representation of the object.
	 */
	public function toStringConf(array $conf) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start(array(), '');
		return $this->toStringObjConf($cObj, $conf);
	}

	/**
	 * Converts this object to its string representation.
	 * Uses the specified content object.
	 *
	 * @param	tslib_cObj		$cobj: Content object used as parent.
	 * @return	string		Representation of the object.
	 */
	public function toStringObj(tslib_cObj $cObj) {
		return toStringObjConf($cObj, self::$lConf);
	}

	/**
	 * Converts this object to its string representation.
	 * Uses the specified TypoScript configuration and content object.
	 * Data fields:
	 * * empty: Boolean. Indicates if this TimeEntry has no defined times. 0 if defined, 1 if not defined.
	 * * open: Opening time as a timestamp. The date part use an arbitrary date with the entry's day of week.
	 * * close: Closing time as a timestamp. The date part use an arbitrary date with the entry's day of week.
	 * * isPM: Boolean, 0 for AM, 1 for PM.
	 *
	 * @param	tslib_cObj		$cobj: Content object used as parent.
	 * @param	array		$conf: TypoScript configuration to use to render this object.
	 * @return	string		Representation of the object.
	 */
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
		static $today = null;
		if ($today == null) $today = getdate();
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$start = getdate($this->start);
		$end = getdate($this->end);
		$dayDiff = ($this->dayOfWeek % 7) - $today['wday'];
		$data = array(
			'empty' => (!$this->start || !$this->end) ? 1 : 0,
			'open' => mktime($start['hours'], $start['minutes'], $start['seconds'], $today['mon'], $today['mday'] + $dayDiff, $today['year']),
			'close' => mktime($end['hours'], $end['minutes'], $end['seconds'], $today['mon'], $today['mday'] + $dayDiff, $today['year']),
			'isPM' => $this->isPM ? 1 : 0,
		);
		$local_cObj->start($data, 'TimeEntry');
		$local_cObj->setParent($cObj->data, $cObj->currentRecord);
		return $local_cObj->stdWrap('', $conf);
	}
}