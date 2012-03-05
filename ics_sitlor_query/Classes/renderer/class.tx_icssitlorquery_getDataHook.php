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
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Class 'tx_icssitlorquery_getDataHook' for the 'ics_sitlor_query' extension.
 *
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_getDataHook implements tslib_content_getDataHook {
	public function getDataExtension($getDataString, array $fields, $sectionValue, $returnValue, tslib_cObj &$parentObject) {
		$retVal = $returnValue;
		$parts = explode(':', $sectionValue, 2);
		$key = trim($parts[1]);
		if ((string)$key != '') {
			$type = strtolower(trim($parts[0]));
			switch ($type) {
				case 'context':
					$keyP = explode(',', $key, 2);
					if (count($keyP) < 2) {
						array_unshift($keyP, 0);
					}
					if (!empty($keyP[1]) && isset(self::$context[intval($keyP[0])])) {
						$contextData = self::$context[intval($keyP[0])];
						$retVal = (string) $contextData[$keyP[1]];
					}
					break;
			}
		}
		return $retVal;
	}
	
	private static $context = array(); /**< The context. */
	
	private static function init() {
		static $initialized = false;
		if (!$initialized) {
			$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['getData'][] = __CLASS__;
			$initialized = true;
		}
	}
	
	/**
	 * Pushes a data array into the context.
	 *
	 * @param	array		$data: The data array to put into the context.
	 * @return	void
	 */
	public function pushContext(array $data) {
		self::init();
		array_unshift(self::$context, $data);
	}
	
	/**
	 * Pops the last added data array from the context.
	 *
	 * @return	void
	 */
	public function popContext() {
		self::init();
		array_shift(self::$context);
	}
}
