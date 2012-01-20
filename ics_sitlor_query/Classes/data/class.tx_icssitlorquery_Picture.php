<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cite Solution <technique@in-cite.net>
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
	public $Uri;
	
	/**
	 * Constructor
	 *
	 * @param	string $uri
	 */
	public function __construct(string $uri) {
	}

	/**
	 * Convert object to display as string
	 * @return string
	 */
	public function __toString() {
	}

	/**
	 * Convert tslib_cObj to display as string
	 *
	 * @param	tslib_cObj $cObj
	 *
	 * @return string
	 */
	public function toString(tslib_cObj $cObj) {
	}
	
	/**
	 * Convert conf to display as string
	 *
	 * @param	array $conf
	 *
	 * @return string
	 */
	public function toString(array $conf) {
	}

	/**
	 * Convert tslib_cObj and conf to display as string
	 *
	 * @param	tslib_cObj $cObj
	 * @param	array $conf
	 *
	 * @return string
	 */
	public function toString(tslib_cObj $cObj, array $conf) {
	}
	
	/**
	 * Set default
	 *
	 * @param	array $conf
	 * @return void
	 */
	public function SetDefault(array $conf) {
	}
	
}