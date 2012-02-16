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
 * Class 'tx_icssitlorquery_TimeEntriesRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_TimeEntriesRenderer {
	public function renderTimeEntries($content, $conf) {
		$this->conf = conf;
		$content = $this->renderRows();
		if (isset($this->conf['body.']))
			$content = $this->cObj->stdWrap($content, $this->conf['body.']);
		if (isset($this->conf['head.']))
			$content = $this->renderHead() . $content;
		return $content;
	}
	
	private function renderHead() {
		$content = '';
		$content .= $this->cObj->stdWrap($this->conf['headAM'], $this->conf['headAM.']);
		$content .= $this->cObj->stdWrap($this->conf['headPM'], $this->conf['headPM.']);
		return $this->cObj->stdWrap($content, $this->conf['head.']);
	}
	
	private function renderRows() {
		$entries = $this->getSortedEntries();
		if (!empty($this->conf['addMissingEntries'])) {
			$currentDay = 0;
			$currentPM = 1;
			$allEntries = array();
			foreach ($entries as $entry) {
				$entryDay = $entry->DayOfWeek;
				$entryPM = $entry->isPM ? 1 : 0;
				while (($currentPM == $entryPM) ||
					(($currentPM < $entryPM) && ($currentDay < $entryDay)) ||
					(($currentPM > $entryPM) && ($currentDay + 1 < $entryDay))) {
					$this->renderRows_addEntry($allEntries, $currentDay, $currentPM);
				}
				$this->renderRows_addEntry($allEntries, $currentDay, $currentPM, $entry);
			}
			while ($currentDay < 8) {
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
			$entryContent = $entry->toStringObjConf($this->cObj, $this->conf[$entry->isPM ? 'rowPM.' : 'rowAM']);
			$asRows ? ($rowContent .= $entryContent) : ($content .= $entryContent);
		}
		if ($asRows && $rowContent) {
			$content .= $this->cObj->stdWrap($rowContent, $this->conf['row.']);
		}
		return $content;
	}
	
	private function renderRows_addEntry(array &$entries, & $currentDay, & $currentPM, tx_icssitlorquery_TimeEntry $entry = null) {
		if ($entry == null) {
			if ($currentPM) {
				$entry = t3lib_div::makeInstance('tx_icssitlorquery_TimeEntry', $currentDay + 1, 0, 0, false);
			}
			else {
				$entry = t3lib_div::makeInstance('tx_icssitlorquery_TimeEntry', $currentDay, 0, 0, true);
			}
		}
		$entries[] = $entry;
		if ($currentPM) {
			$currentDay++;
			$currentPM = 0;
		}
		else {
			$currentPM++;
		}
	}
	
	private function getSortedEntries() {
		$entries = $this->cObj->data['timeEntries'];
		usort($entries, array(__CLASS__, 'timeEntryComparer'));
		return $entries;
	}
	
	public static function timeEntryComparer(tx_icssitlorquery_TimeEntry $te0, tx_icssitlorquery_TimeEntry $te1) {
		if ($te0->DayOfWeek != $te1->DayOfWeek) {
			return $te0->DayOfWeek - $te1->DayOfWeek;
		}
		if ($te0->isPM) {
			if ($te1->isPM) {
				return 0;
			}
			else {
				return 1;
			}
		}
		else {
			if ($te1->isPM) {
				return -1;
			}
			else {
				return 0;
			}
		}
	}
}