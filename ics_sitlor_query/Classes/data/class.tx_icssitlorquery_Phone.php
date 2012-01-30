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
 * Class 'tx_icssitlorquery_Phone' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_Phone implements tx_icssitquery_IToStringObjConf {
	private $phone = '';

	private static $lConf = array();

	/**
	 * Constructor
	 *
	 * @param	string		$phone1
	 * @param	string		$phone2
	 * @return	void
	 */
	public function __construct($phone) {
		$this->phone = $phone;
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) 	{
			case 'Phone':
				return $this->phone;
			default :
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Set default
	 *
	 * @param	array		$conf
	 * @return	void
	 */
	public function SetDefaultConf(array $conf) {
		self::$lConf = $conf;
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	public function __toString() {
		return $this->toString();
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
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		return $this->toStringObjConf($cObj, $conf);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$tslib_cObj $cObj: ...
	 * @return	[type]		...
	 */
	public function toStringObj(tslib_cObj $cObj) {
		return toStringObjConf($cObj, self::$lConf);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$tslib_cObj $cObj, array $conf: ...
	 * @return	[type]		...
	 */
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
		return 'Phone toString is not yet implemented';
	}

}