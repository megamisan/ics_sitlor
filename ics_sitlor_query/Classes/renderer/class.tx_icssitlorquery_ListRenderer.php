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
***************************************************************//**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Class 'tx_icssitlorquery_ListRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
 class tx_icssitlorquery_ListRenderer {

	/**
	 * Constructor
	 *
	 * @param	tx_icssitlorquery_pi1		$pi: Instance of tx_icssitlorquery_pi1
	 * @param	tslib_cObj					$cObj: tx_icssitlorquery_pi1 cObj
	 * @param	array						$lConf: Local conf
	 * @return	void
	 */
	function __construct($pi, $cObj, $lConf) {
		$this->pi = $pi;
		$this->cObj = $cObj;
		$this->conf = $lConf;
		$this->prefixId = $pi->prefixId;
		$this->templateCode = $pi->templateCode;
	}

	/**
	 * Render the view
	 *
	 * @param	array		$elements: The array of elements
	 * @return	string		HTML list content
	 */
	function render($elements=null) {
		if (isset($elements) && !empty($elements))
			return $this->renderList($elements);
		return $this->renderListEmpty();
	}

	/**
	 * Render empty list content
	 *
	 * @return	string		HTML empty list content
	 */
	private function renderListEmpty() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_LIST_EMPTY###');
		$markers = array();
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###', false, true);
	}

	/**
	 * Render list content
	 *
	 * @param	array		$elements : tx_icssitquery_AbstractData array
	 * @return	string		HTML list content
	 */
	private function renderList(array $elements) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_LIST###');
		$subparts = array();
		$itemTemplate = $this->cObj->getSubpart($template, '###ITEM###');
		foreach ($elements as $element) {
			$markers = array();
			$locMarkers = array();
			$locMarkers['GENERIC'] = $this->renderListItemGeneric($element, $markers);
			$locMarkers['SPECIFIC'] = $this->renderListItemSpecific($element, $markers);
			$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
			$subparts['###ITEM###'] .= $this->cObj->substituteMarkerArray($itemContent, $markers, '###|###');
		}
		$markers = array(
			'PREFIXID' => $this->prefixId,
		);
		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Render list item generic
	 *
	 * @param	object		$element: tx_icssitquery_AbstractData like tx_icssitlorquery_Accomodation, tx_icssitlorquery_Restaurant or tx_icssitlorquery_Event
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML item list content
	 */
	private function renderListItemGeneric($element, &$markers) {
		if (!($element instanceof tx_icssitquery_AbstractData))
			return '';

		$locMarkers = array(
			'ILLUSTRATION' => $element->Illustration,
		);
		$markers = array_merge($markers, $locMarkers);
		return $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_ITEM_GENERIC###');
	}

	/**
	 * Render list item specific
	 *
	 * @param	object		$element: tx_icssitquery_AbstractData like tx_icssitlorquery_Accomodation, tx_icssitlorquery_Restaurant or tx_icssitlorquery_Event
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML item list content
	 */
	private function renderListItemSpecific($element, &$markers) {
		if (!($element instanceof tx_icssitquery_AbstractData))
			return '';

		$locMarkers = array();
		if ($element instanceof tx_icssitlorquery_Accomodation) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_ITEM_ACCOMODATION###');
			$locMarkers = array(
				'TYPE' => $element->Type,
				'TITLE' => $element->Name,
				'DESCRIPTION' => $element->Description,
				'PRICE' => $element->CurrentSingleClientsRate,
				'RATINGSTAR' => $element->RatingStar,
			);
		}
		if ($element instanceof tx_icssitlorquery_Restaurant) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_ITEM_RESTAURANT###');
			$locMarkers = array(
				'TYPE' => $element->Type,
				'TITLE' => $element->Name,
				'DESCRIPTION' => $element->Description,
				'PRICE' => $element->CurrentMenuPrice,
				'LABELCHAIN' => $element->LabelChain,
			);
		}
		if ($element instanceof tx_icssitlorquery_Event) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_ITEM_EVENT###');
			$locMarkers = array(
				'TYPE' => $element->Type,
				'TITLE' => $element->Name,
				'DESCRIPTION' => $element->Description,
				'DATE' => $element->TimeTable,
			);
		}
		$markers = array_merge($markers, $locMarkers);
		return $template;
	}
 }