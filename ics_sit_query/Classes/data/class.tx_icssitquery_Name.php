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
 *   52: class tx_icssitquery_Name implements tx_icssitquery_IToStringObjConf
 *   67:     public function __construct($title, $firstname, $lastname)
 *   79:     public function __get($name)
 *   98:     public static function SetDefaultConf(array $conf)
 *  107:     public function __toString()
 *  118:     public function toString()
 *  143:     public function toStringConf(array $conf)
 *  156:     public function toStringObj(tslib_cObj $cObj)
 *  170:     public function toStringObjConf(tslib_cObj $cObj, array $conf)
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitquery_Name' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitquery
 */
class tx_icssitquery_Name implements tx_icssitquery_IToStringObjConf {
	private $title = '';
	private $firstname = '';
	private $lastname = '';

	private static $lConf = array();

	/**
	 * Initializes this name definition.
	 *
	 * @param	string		$title: Title of the person.
	 * @param	string		$firstname: First name.
	 * @param	string		$lastname: Last name.
	 * @return	void
	 */
	public function __construct($title, $firstname, $lastname) {
		$this->title = $title;
		$this->firstname = $firstname;
		$this->lastname = $lastname;
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) 	{
			case 'Title':
				return $this->title;
			case 'Firstname':
				return $this->firstname;
			case 'Lastname':
				return $this->lastname;
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
	 * * url: string.
	 *
	 * @param	tslib_cObj		$cobj: Content object used as parent.
	 * @param	array		$conf: TypoScript configuration to use to render this object.
	 * @return	string		Representation of the object.
	 */
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$data = array(
			'title' => $this->title,
			'lastname' => $this->lastname,
			'firstname' => $this->firstname,
		);
		$local_cObj->start($data, 'Name');
		$local_cObj->setParent($cObj->data, $cObj->currentRecord);
		return $local_cObj->stdWrap('', $conf);
	}

}