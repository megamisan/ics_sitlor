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
class tx_icssitlorquery_TimeEntry implements IToStringObjConf {
	private $dayOfWeek;	// int : ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)
	private $start;	// int
	private $end;	// int
	private $isPM;	// bool

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

}