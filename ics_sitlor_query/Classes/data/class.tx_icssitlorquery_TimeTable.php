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
 * Hint: use extdeveval to insert/update function index above.
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
	 * @param	XMLReader $reader : Reader to the parsed document
	 * @return	void
	 */
	public static function FromXML(XMLReader $reader) {
		$timeTable = new tx_icssitlorquery_TimeTable();
		$timeEntry = array();
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

					case 'LUNDI_AM_DE':
						if ($time = $reader->readString())
							$timeEntry[1]['am']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'LUNDI_AM_A':
						if ($time = $reader->readString())
							$timeEntry[1]['am']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'LUNDI_PM_DE':
						if ($time = $reader->readString())
							$timeEntry[1]['pm']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'LUNDI_PM_A':
						if ($time = $reader->readString())
							$timeEntry[1]['pm']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					case 'MARDI_AM_DE':
						if ($time = $reader->readString())
							$timeEntry[2]['am']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'MARDI_AM_A':
						if ($time = $reader->readString())
							$timeEntry[2]['am']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'MARDI_PM_DE':
						if ($time = $reader->readString())
							$timeEntry[2]['pm']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'MARDI_PM_A':
						if ($time = $reader->readString())
							$timeEntry[2]['pm']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					case 'MERCREDI_AM_DE':
						if ($time = $reader->readString())
							$timeEntry[3]['am']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'MERCREDI_AM_A':
						if ($time = $reader->readString())
							$timeEntry[3]['am']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'MERCREDI_PM_DE':
						if ($time = $reader->readString())
							$timeEntry[3]['pm']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'MERCREDI_PM_A':
						if ($time = $reader->readString())
							$timeEntry[3]['pm']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					case 'JEUDI_AM_DE':
						if ($time = $reader->readString())
							$timeEntry[4]['am']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'JEUDI_AM_A':
						if ($time = $reader->readString())
							$timeEntry[4]['am']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'JEUDI_PM_DE':
						if ($time = $reader->readString())
							$timeEntry[4]['pm']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'JEUDI_PM_A':
						if ($time = $reader->readString())
							$timeEntry[4]['pm']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					case 'VENDREDI_AM_DE':
						if ($time = $reader->readString())
							$timeEntry[5]['am']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'VENDREDI_AM_A':
						if ($time = $reader->readString())
							$timeEntry[5]['am']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'VENDREDI_PM_DE':
						if ($time = $reader->readString())
							$timeEntry[5]['pm']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'VENDREDI_PM_A':
						if ($time = $reader->readString())
							$timeEntry[5]['pm']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					case 'SAMEDI_AM_DE':
						if ($time = $reader->readString())
							$timeEntry[6]['am']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'SAMEDI_AM_A':
						if ($time = $reader->readString())
							$timeEntry[6]['am']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'SAMEDI_PM_DE':
						if ($time = $reader->readString())
							$timeEntry[6]['pm']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'SAMEDI_PM_A':
						if ($time = $reader->readString())
							$timeEntry[6]['pm']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					case 'DIMANCHE_AM_DE':
						if ($time = $reader->readString())
							$timeEntry[7]['am']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'DIMANCHE_AM_A':
						if ($time = $reader->readString())
							$timeEntry[7]['am']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'DIMANCHE_PM_DE':
						if ($time = $reader->readString())
							$timeEntry[7]['pm']['start'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'DIMANCHE_PM_A':
						if ($time = $reader->readString())
							$timeEntry[7]['pm']['end'] = strtotime($time);
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;

					default:
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}

		foreach ($timeEntry as $day=>$entry) {
			if (isset($entry['am']))
				$timeTable->timeEntries[] = t3lib_div::makeInstance('tx_icssitlorquery_TimeEntry', $day, $entry['am']['start'], $entry['am']['end'], false);
			if (isset($entry['pm']))
				$timeTable->timeEntries[] = t3lib_div::makeInstance('tx_icssitlorquery_TimeEntry', $day, $entry['pm']['start'], $entry['pm']['end'], true);
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
		return $this->toString();
	}

	/**
	 * Converts this object to its string representation.
	 * Using default output settings.
	 *
	 * @return	string		Representation of the object.
	 */
	public function toString() {
		return $this->toStringConf(self::$lConf);
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
	 *
	 * @param	tslib_cObj		$cobj: Content object used as parent.
	 * @param	array		$conf: TypoScript configuration to use to render this object.
	 * @return	string		Representation of the object.
	 */
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
		return 'TimeTable toString is not yet implemented';
	}
}