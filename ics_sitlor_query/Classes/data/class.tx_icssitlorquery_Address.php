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
 * Class 'tx_icssitlorquery_Address' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
 
class tx_icssitlorquery_Address implements tx_icssitquery_IToStringObjConf {
	private $number = '';
	private $street = '';
	private $extra = '';
	
	private static $lConf = array();
	
	/**
	 * Constructor
	 *
	 * @param	string $number : Street number
	 * @param	string $street : Street name
	 * @param	string $extra : Complement of address
	 *
	 */
	public function __construct($number, $street, $extra) {
		$this->number = $number;
		$this->street = $street;
		$this->extra = $extra;
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
	
	public function __toString() {
		return $this->toString();
	}
	
	public function toString() {
		return $this->toStringConf(self::$lConf);
	}
	
	public function toStringConf(array $conf) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		return $this->toStringObjConf($cObj, $conf);
	}
	
	public function toStringObj(tslib_cObj $cObj) {
		return toStringObjConf($cObj, self::$lConf);
	}
	
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
		return 'Address';
	}		

}