<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 In Cite Solution <technique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the Coordinatess of the GNU General Public License as published by
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
 *   52: class tx_icssitquery_Coordinates implements tx_icssitquery_IToStringObjConf
 *   58:     public function __construct($latitude, $longitude)
 *   71:     public function __get($name)
 *   88:     public static function SetDefaultConf(array $conf)
 *   97:     public function __toString()
 *  108:     public function toString()
 *  133:     public function toStringConf(array $conf)
 *  146:     public function toStringObj(tslib_cObj $cObj)
 *  161:     public function toStringObjConf(tslib_cObj $cObj, array $conf)
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitquery_Coordinates' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitquery
 */
class tx_icssitquery_Coordinates implements tx_icssitquery_IToStringObjConf {
	private $latitude;
	private $longitude;

	private static $lConf = array();

	public function __construct($latitude, $longitude) {
		if (!is_float($latitude) || !is_float($longitude))
			throw new Exception('Coordinates latitude and longitude must be float.');
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) 	{
			case 'Latitude':
				return $this->latitude;
			case 'Longitude':
				return $this->longitude;
			default :
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
	 * * latitude: In decimal degrees.
	 * * longitude: In decimal degrees.
	 *
	 * @param	tslib_cObj		$cobj: Content object used as parent.
	 * @param	array		$conf: TypoScript configuration to use to render this object.
	 * @return	string		Representation of the object.
	 */
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$data = array(
			'latitude' => $this->latitude,
			'longitude' => $this->longitude,
		);
		$local_cObj->start($data, 'Coordinates');
		$local_cObj->setParent($cObj->data, $cObj->currentRecord);
		return $local_cObj->stdWrap('', $conf);
	}
}