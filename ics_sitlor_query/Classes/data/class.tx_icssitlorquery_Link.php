<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cite Solution <technique@in-cite.net>
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
 * Class 'tx_icssitlorquery_Link' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_Link implements tx_icssitquery_IToStringObjConf {
	private $url;
	
	/**
	 * Constructor
	 *
	 * @param	string $url
	 */
	public function __construct($url) {
	}

	/**
	 * Convert object to display as string
	 * @return string
	 */
	public function __toString() {
	}

	public function toString() {
	}
	/**
	 * Convert tslib_cObj to display as string
	 *
	 * @param	tslib_cObj $cObj
	 *
	 * @return string
	 */
	public function toStringObj(tslib_cObj $cObj) {
	}
	
	/**
	 * Convert conf to display as string
	 *
	 * @param	array $conf
	 *
	 * @return string
	 */
	public function toStringConf(array $conf) {
	}

	/**
	 * Convert tslib_cObj and conf to display as string
	 *
	 * @param	tslib_cObj $cObj
	 * @param	array $conf
	 *
	 * @return string
	 */
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
	}
	
	/**
	 * Set default
	 *
	 * @param	string $tag
	 * @param	array $conf
	 * @return void
	 */
	public function SetDefault($tag, array $conf) {
	}
	
	
}