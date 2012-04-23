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
 *   51: class tx_icssitlorquery_ListRenderer
 *   61:     function __construct($pi, $cObj, $lConf)
 *   75:     function render($elements=null)
 *   86:     private function renderListEmpty()
 *  100:     private function renderList(array $elements)
 *  129:     private function renderListItemGeneric($element, &$markers)
 *  147:     private function renderListItemSpecific($element, &$markers)
 *  228:     protected function getListGetPageBrowser($numberOfPages)
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the extension "extdeveval")
 *
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
	 * @param	tslib_cObj		$cObj: tx_icssitlorquery_pi1 cObj
	 * @param	array		$lConf: Local conf
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
		$markers = array(
			'MESSAGE' => $this->pi->pi_getLL('empty_list', 'Empty list', true),
		);
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
			'PAGE_BROWSER' => $this->getListGetPageBrowser(intval(ceil($this->pi->queryService->getLastTotalCount()/$this->conf['view.']['size']))),
			'SORTING' => $this->pi->renderSortings(),
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
			'ILLUSTRATION' => $element->Illustration->Get(0),
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

		// Render Accomodations
		if ($element instanceof tx_icssitlorquery_Accomodation) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_ITEM_ACCOMODATION###');
			$price = $this->pi->pi_getLL('noPrice', 'No price', true);
			$valudeTerm = $element->CurrentSingleClientsRate->Get(0);
			if ($valudeTerm->Term->ID == tx_icssitlorquery_CriterionUtils::CURRENT_SINGLE_CLIENTS_RATE_DOUBLEROOM_MIN)
				$price = $this->pi->renderData('price', $valudeTerm);
			$locMarkers = array(
				'TYPE' => $element->Type,
				'TITLE' => $this->pi->renderSingleLink('title', $element),
				'DESCRIPTION' => $this->pi->renderData('description', $element->Description),
				'PRICE_LABEL' => $this->pi->pi_getLL('price', 'Price', true),
				'PRICE' => $price,
				'RATINGSTAR' => $this->pi->renderData('ratingStar', $element->RatingStar),
				'MOBILITYIMPAIRED' => $this->pi->renderData('mobilityImpaired', $element->MobilityImpaired),
				'LINK_MORE' => $this->pi->renderSingleLink('more', $element),
			);
		}
		// Render Restaurants
		elseif ($element instanceof tx_icssitlorquery_Restaurant) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_ITEM_RESTAURANT###');
			$price = $this->pi->pi_getLL('noPrice', 'No price', true);
			for ($i=0; $i<$element->CurrentMenuPrice->Count(); $i++) {
				$valudeTerm = $element->CurrentMenuPrice->Get($i);
				if ($valudeTerm->Term->ID == tx_icssitlorquery_CriterionUtils::CURRENT_MENU_PRICE_ADULT)
					$price = $this->pi->renderData('price', $valudeTerm);
			}
			for ($i=0; $i<$element->ServiceOpen->Count(); $i++) {
				$valudeTerm = $element->ServiceOpen->Get($i);
				if ($valudeTerm->Term->ID == tx_icssitlorquery_CriterionUtils::SERVICEOPEN_CLOSEDAY)
					$day = $valudeTerm;
			}
			$locMarkers = array(
				'TYPE' => $element->Type,
				'TITLE' => $this->pi->renderSingleLink('title', $element),
				'DESCRIPTION' => $this->pi->renderData('description', $element->Description),
				'PRICE_LABEL' => $this->pi->pi_getLL('price', 'Price', true),
				'PRICE' => $price,
				'LABELCHAIN' => $element->ChainAndLabel,
				'SERVICE_OPEN' => isset($day)? $this->pi->renderData('openCloseDay', $day): '',
				'LINK_MORE' => $this->pi->renderSingleLink('more', $element),
			);
		}
		// Render Events
		elseif ($element instanceof tx_icssitlorquery_Event) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_ITEM_EVENT###');
			$locMarkers = array(
				'TYPE_EVENT' => $element->TypeEvent,
				'TITLE' => $this->pi->renderSingleLink('title', $element),
				'DESCRIPTION' => $this->pi->renderData('description', $element->Description),
				'DATE' => ($element->TimeTable->Count()>0? $this->pi->renderData('date', $element->TimeTable->Get(0)): $this->pi->pi_getLL('noDate', 'No date', true)),
				'LINK_MORE' => $this->pi->renderSingleLink('more', $element),
			);
		}
		// Render records
		else {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_ITEM_RECORD###');
			$locMarkers = array(
				'TYPE' => $element->Type,
				'TITLE' => $this->pi->renderSingleLink('title', $element),
				'DESCRIPTION' => $this->pi->renderData('description', $element->Description),
				'LINK_MORE' => $this->pi->renderSingleLink('more', $element),
			);
		}
		
		$locMarkers['ADDRESS'] = $element->Address;
		$locMarkers['PHONE'] = $this->pi->renderData('phones', $element->Phones);

		$markers = array_merge($markers, $locMarkers);
		return $template;
	}

	/**
	 * Page browser
	 *
	 * @param	int		$numberOfPages
	 * @return	string		page browser content
	 */
	protected function getListGetPageBrowser($numberOfPages) {
		// Get default configuration
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_pagebrowse_pi1.'];
		// Modify this configuration
		$conf += array(
			'pageParameterName' => $this->prefixId . '|page',
			'numberOfPages' => $numberOfPages,
			'extraQueryString' => $this->pi->queryService->getLastRandomSession()? '&' . $this->prefixId . '[sortExtra]=' . $this->pi->queryService->getLastRandomSession(): ''
		);
		// Get page browser
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		/* @var $cObj tslib_cObj */
		$cObj->start(array(), '');
		return $cObj->cObjGetSingle('USER', $conf);
	}

 }