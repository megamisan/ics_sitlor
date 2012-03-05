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
 *   49: class tx_icssitlorquery_TimeEntriesRenderer
 *   65:     private function renderHead()
 *   77:     private function renderRows()
 *  127:     private function renderRows_addEntry(array &$entries, & $currentDay, & $currentPM, tx_icssitlorquery_TimeEntry $entry = null)
 *  146:     private function getSortedEntries()
 *  158:     public static function timeEntryComparer(tx_icssitlorquery_TimeEntry $te0, tx_icssitlorquery_TimeEntry $te1)
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitlorquery_TimeEntriesRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_TimeEntriesRenderer {
	/**
	 * Render time entries
	 *
	 * @return	string		Time entries HTML content
	 */
	public function renderTimeEntries($content, $conf) {
		$this->conf = $conf;
		$content = $this->renderRows();
		if (isset($this->conf['body.']))
			$content = $this->cObj->stdWrap($content, $this->conf['body.']);
		if (isset($this->conf['head.']))
			$content = $this->renderHead() . $content;
		return $content;
	}

	/**
	 * Render head
	 *
	 * @return	string		Head content
	 */
	private function renderHead() {
		$content = '';
		$content .= $this->cObj->stdWrap($this->conf['headAM'], $this->conf['headAM.']);
		$content .= $this->cObj->stdWrap($this->conf['headPM'], $this->conf['headPM.']);
		return $this->cObj->stdWrap($content, $this->conf['head.']);
	}

	/**
	 * Render rows
	 *
	 * @return	string		Rows content
	 */
	private function renderRows() {
		$entries = $this->getSortedEntries();
		if (!empty($this->conf['addMissingEntries'])) {
			$currentDay = 0;
			$currentPM = 1;
			$allEntries = array();
			foreach ($entries as $entry) {
				$entryDay = $entry->DayOfWeek;
				$entryPM = $entry->IsPM ? 1 : 0;
				while (($currentDay * 2 + $currentPM + 1) < ($entryDay * 2 + $entryPM)) {
					$this->renderRows_addEntry($allEntries, $currentDay, $currentPM);
				}
				$this->renderRows_addEntry($allEntries, $currentDay, $currentPM, $entry);
			}
			while (($currentDay * 2 + $currentPM) < 15) {
				$this->renderRows_addEntry($allEntries, $currentDay, $currentPM);
			}
		}
		else {
			$allEntries = $entries;
		}
		$content = '';
		$rowContent = '';
		$currentDay = 0;
		$asRows = isset($this->conf['row.']);
		foreach ($allEntries as $entry) {
			if ($asRows && ($entry->DayOfWeek != $currentDay)) {
				if ($currentDay) {
					$content .= $this->cObj->stdWrap($rowContent, $this->conf['row.']);
					$rowContent = '';
				}
				$currentDay = $entry->DayOfWeek;
			}
			$entryContent = $entry->toStringObjConf($this->cObj, $this->conf[$entry->IsPM ? 'rowPM.' : 'rowAM.']);
			$asRows ? ($rowContent .= $entryContent) : ($content .= $entryContent);
		}
		if ($asRows && $rowContent) {
			$content .= $this->cObj->stdWrap($rowContent, $this->conf['row.']);
		}
		return $content;
	}

	/**
	 * Adds renderRows entry
	 *
	 * @param	&array		$entries: Entries
	 * @param	int			$currentDay: Current day
	 * @param	int			$currentPM: Current PM
	 * @param	tx_icssitlorquery_TimeEntry $entry: TimeEntry
	 * @return	void
	 */
	private function renderRows_addEntry(array &$entries, & $currentDay, & $currentPM, tx_icssitlorquery_TimeEntry $entry = null) {
		if ($currentPM) {
			$currentDay++;
			$currentPM = 0;
		}
		else {
			$currentPM++;
		}
		if ($entry == null) {
			$entry = t3lib_div::makeInstance('tx_icssitlorquery_TimeEntry', $currentDay, 0, 0, $currentPM);
		}
		$entries[] = $entry;
	}

	/**
	 * Retrieves sorted entries
	 *
	 * @return	mixed
	 */
	private function getSortedEntries() {
		$entries = $this->cObj->data['timeEntries'];
		usort($entries, array(__CLASS__, 'timeEntryComparer'));
		return $entries;
	}

	/**
	 * Compares TimeEntry
	 *
	 * @param	tx_icssitlorquery_TimeEntry		$te0: TimeEntry 0
	 * @param	tx_icssitlorquery_TimeEntry		$te1: TimeEntry 1
	 * @return	int		Compare result
	 */
	public static function timeEntryComparer(tx_icssitlorquery_TimeEntry $te0, tx_icssitlorquery_TimeEntry $te1) {
		if ($te0->DayOfWeek != $te1->DayOfWeek) {
			return $te0->DayOfWeek - $te1->DayOfWeek;
		}
		if ($te0->IsPM) {
			if ($te1->IsPM) {
				return 0;
			}
			else {
				return 1;
			}
		}
		else {
			if ($te1->IsPM) {
				return -1;
			}
			else {
				return 0;
			}
		}
	}
}