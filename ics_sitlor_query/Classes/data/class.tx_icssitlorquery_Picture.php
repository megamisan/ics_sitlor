<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 In Cite Solution <technique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the Pictures of the GNU General Public License as published by
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
 * Class 'tx_icssitlorquery_Picture' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_Picture implements tx_icssitquery_IToStringObjConf {
	private $uri;
	private static $lConf = array();

	/**
	 * Constructor
	 *
	 * @param	string $uri
	 * @return	void
	 */
	public function __construct($uri) {
		$this->uri = $uri;
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) 	{
			case 'Uri':
				return $this->uri;
			default :
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Set default
	 *
	 * @param	array $conf
	 * @return	void
	 */
	public function SetDefaultConf(array $conf) {
		self::$lConf = $conf;
	}

	/**
	 * Convert object to display as string
	 *
	 * @return	string
	 */
	public function __toString() {
		$numargs = func_num_args();
		if ($numargs==0)
			return $this->toStringConf(self::$lConf);

		// $numargs >0
		$args = func_get_args();
		if (is_array($args[0]))
			return $this->toStringConf(array_merge(self::$lConf, $args[0]));

		if ($args[0] instanceof tslib_cObj) {
			if ($numargs==1)
				return toStringObj($args[0], self::$lConf);
			return toStringObj($args[0], array_merge(self::$lConf, $args[1]));
		}

		tx_icssitquery_debug::warning('Can not convert ValuedTermTuples to string, args :' . $args);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	public function toString() {
		return $this->uri;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$array $conf: ...
	 * @return	[type]		...
	 */
	public function toStringConf(array $conf) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		return $this->toStringCObj($cObj, $conf);
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
		return $cObj->stdWrap_current($this->uri, $conf);
	}
}