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
 * Class 'tx_icssitquery_debug' for the 'ics_sit_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitquery
 */

class tx_icssitquery_Debug {
	public static function error($message, $backlevel = 0) {
		$trace = debug_backtrace();
		trigger_error(
			$message .
			' in ' . $trace[1 + $backlevel]['file'] .
			' on line ' . $trace[1 + $backlevel]['line'],
			E_USER_ERROR);
	}

	public static function warning($message, $backlevel = 0) {
		$trace = debug_backtrace();
		trigger_error(
			$message .
			' in ' . $trace[1 + $backlevel]['file'] .
			' on line ' . $trace[1 + $backlevel]['line'],
			E_USER_WARNING);
	}

	public static function notice($message, $backlevel = 0) {
		$trace = debug_backtrace();
		trigger_error(
			$message .
			' in ' . $trace[1 + $backlevel]['file'] .
			' on line ' . $trace[1 + $backlevel]['line'],
			E_USER_NOTICE);
	}
}