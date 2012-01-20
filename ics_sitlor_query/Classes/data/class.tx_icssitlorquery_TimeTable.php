<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cite Solution <technique@in-cite.net>
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
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Class 'tx_icssitlorquery_TimeTable' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_TimeTable implements tx_icssitquery_IToStringObjConf {
	private $start;	// timestamp
	private $end;	// timestamp
	private $comment;
	
	/**
	 * Private constructor
	 */
	private function __construct() {};
	
	/**
	 * Retrieves TimeTable
	 *
	 * @param	XMLReader $reader : Reader to the parsed document
	 */
	public static function FromXML(XMLReader $reader) {
	}
	
	/**
	 * Set default conf
	 *
	 * @param	array $conf
	 */
	public static function setDefaultConf(array $conf) {
	}
	
	/**
	 * Retrieves properties
	 *
	 * @param	string $name : Property's name
	 *
	 * @return name 's value
	 */
	public function __get($name) { // TODO: Entries
		switch ($name) 	{
			case 'Start':
				return $this->start;
			case 'End':
				return $this->end;
			case 'Comment':
				return $this->comment;
			default :
				tx_icssitquery_debug::notice('Undefined property of TimeTable via __get(): ' . $name);
		}
	}
	
	/**
	 * Convert object to display as string
	 * @return string
	 */
	public function __toString() {
		$confDefault = array();
		$numargs = func_num_args();		
		if ($numargs==0) {
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
		
		tx_icssitquery_debug::warning('Can not convert TimeTable to string, args :' . $args);
	}
	
	private function toStringConf(array $conf) {
		return 'Timetable';
	}
	
	private function toStringCObj(tslib_cObj $cObj, array $conf) {
		return 'Timetable';
	}
}