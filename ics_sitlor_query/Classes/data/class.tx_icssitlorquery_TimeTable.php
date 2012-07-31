<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 In Cite Solution <technique@in-cite.net>
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
 *   53: class tx_icssitlorquery_TimeTable implements tx_icssitquery_IToStringObjConf
 *   67:     private function __construct()
 *   76:     public function __get($name)
 *   97:     public static function FromXML(XMLReader $reader)
 *  168:     public static function SetDefaultConf(array $conf)
 *  177:     public function __toString()
 *  188:     public function toString()
 *  213:     public function toStringConf(array $conf)
 *  226:     public function toStringObj(tslib_cObj $cObj)
 *  239:     public function toStringObjConf(tslib_cObj $cObj, array $conf)
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
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

	private $timeEntries = array();

	private static $lConf = array();

	/**
	 * Private constructor
	 *
	 * @return	void
	 */
	private function __construct() {
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) {
			case 'Start':
				return $this->start;
			case 'End':
				return $this->end;
			case 'Comment':
				return $this->comment;
			case 'TimeEntries':
				return $this->timeEntries;
			default:
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Retrieves TimeTable
	 *
	 * @param	XMLReader		$reader : Reader to the parsed document
	 * @return	void
	 */
	public static function FromXML(XMLReader $reader) {
		static $dayOfWeekByXMLName = null;
		static $amPm = null;
		static $openClose = null;
		if ($dayOfWeekByXMLName == null) $dayOfWeekByXMLName = array_flip(array(1 => 'LUNDI', 'MARDI', 'MERCREDI', 'JEUDI', 'VENDREDI', 'SAMEDI', 'DIMANCHE'));
		if ($amPm == null) $amPm = array_flip(array('AM', 'PM'));
		if ($openClose == null) $openClose = array_flip(array('DE', 'A'));
		$timeTable = new tx_icssitlorquery_TimeTable();
		$timeEntryCache = array();
		$reader->read();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if($reader->nodeType == XMLReader::ELEMENT){
				switch ($reader->name) {
					case 'DATE_DEBUT':
						if ($start = $reader->readString()) {
							list($day, $month, $year) = explode('/', $start);
							$timeTable->start = mktime(0,0,0,$month,$day,$year);
						}
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					case 'DATE_FIN':
						if ($end = $reader->readString()) {
							list($day, $month, $year) = explode('/', $end);
							$timeTable->end = mktime(0,0,0,$month,$day,$year);
						}
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					case 'COMMENTAIRE':
						$timeTable->comment = $reader->readString();
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					default:
						$parts = explode('_', $reader->name, 3);
						if (isset($dayOfWeekByXMLName[$parts[0]]) &&
							isset($amPm[$parts[1]]) &&
							isset($openClose[$parts[2]])) {
							$day = $dayOfWeekByXMLName[$parts[0]];
							$isPM = $amPm[$parts[1]];
							$which = $openClose[$parts[2]];
							if ($value = $reader->readString()) {
								$value = strtotime($value);
								$id = implode('_', array($day, $isPM, $which));
								$otherId = implode('_', array($day, $isPM, 1 - $which));
								if (isset($timeEntryCache[$otherId])) {
									$start = $which ? $timeEntryCache[$otherId] : $value;
									$end = $which ? $value : $timeEntryCache[$otherId];
									$timeTable->timeEntries[] = t3lib_div::makeInstance('tx_icssitlorquery_TimeEntry', $day, $start, $end, (bool)$isPM);
									unset($timeEntryCache[$otherId]);
								}
								else {
									$timeEntryCache[$id] = $value;
								}
							}
						}
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
		foreach ($timeEntryCache as $entryId => $entryValue) {
			$idParts = explode('_', $entryId);
			$start = ($idParts[2] == 1) ? -1 : $entryValue;
			$end = ($idParts[2] == 1) ? $entryValue : -1;
			$timeTable->timeEntries[] = t3lib_div::makeInstance('tx_icssitlorquery_TimeEntry', intval($idParts[0]), $start, $end, (bool)intval($idParts[1]));
		}
		return $timeTable;
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
	 *
	 * @param	tslib_cObj		$cobj: Content object used as parent.
	 * @param	array		$conf: TypoScript configuration to use to render this object.
	 * @return	string		Representation of the object.
	 */
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$data = array(
			'start' => $this->start,
			'end' => $this->end,
			'comment' => $this->comment,
			'timeEntries' => $this->timeEntries,
		);
		$local_cObj->start($data, 'TimeTable');
		$local_cObj->setParent($cObj->data, $cObj->currentRecord);
		tx_icssitlorquery_getDataHook::pushContext($data);
		$result = $local_cObj->stdWrap('', $conf);
		tx_icssitlorquery_getDataHook::popContext();
		return $result;
	}

}