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
 *   86:     private function renderMapEmpty()
 *  100:     private function renderMap(array $elements)
 *  129:     private function renderMapItemGeneric($element, &$markers)
 *  147:     private function renderMapItemSpecific($element, &$markers)
 *  228:     protected function getListGetPageBrowser($numberOfPages)
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitlorquery_MapRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
 class tx_icssitlorquery_MapRenderer {
	private $pi;
	private $cObj;
	private $conf;
	private $prefixId;
	private $templateCode;

	/**
	 * Initializes renderer.
	 *
	 * @param	tx_icssitlorquery_pi1		$pi: Plugin instance.
	 * @param	tslib_cObj		$cObj: Current frontend renderer.
	 * @param	array		$lConf: Rendering settings.
	 * @return	void
	 */
	public function __construct($pi, $cObj, $lConf) {
		$this->pi = $pi;
		$this->cObj = $cObj;
		$this->conf = $lConf;
		$this->prefixId = $pi->prefixId;
		$this->templateCode = $pi->templateCode;
	}

	/**
	 * Renders the view.
	 *
	 * @param	array		$elements: Elements to render.
	 * @return	string		HTML content to display the map.
	 */
	function render(array $elements = null) {
		if (!empty($elements)) {
			$elements = $this->filterElements($elements);
		}
		if (!empty($elements)) {
			return $this->renderMap($elements);
		}
		return $this->renderMapEmpty();
	}
	
	/**
	 * Filters element without corrdinates from the list.
	 *
	 * @param	array		$element: Array to filter.
	 * @return	array		The filtered array.
	 */
	private function filterElements(array $elements) {
		$newElements = array();
		foreach ($elements as $element) {
			if ($element->Coordinates != null) {
				$newElements[] = $element;
			}
		}
		return $newElements;
	}
	
	/**
	 * Render empty map content
	 *
	 * @return	string		HTML empty list content
	 */
	private function renderMapEmpty() {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_MAP_EMPTY###');
		$markers = array(
			'MESSAGE' => $this->pi->pi_getLL('empty_map', 'Empty map', true),
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###', false, true);
	}

	/**
	 * Render list content
	 *
	 * @param	array		$elements : tx_icssitquery_AbstractData array
	 * @return	string		HTML list content
	 */
	private function renderMap(array $elements) {
		if (!empty($this->conf['view.']['map_control.']['datakey'])) {
			$GLOBALS['SITLOR_RESULTS_MAP_ITEMS'][$this->conf['view.']['map_control.']['datakey']] = $elements;
			$GLOBALS['SITLOR_RESULTS_MAP_LINK'][$this->conf['view.']['map_control.']['datakey']] = $this->conf['PIDitemDisplay'];
			$content = $this->cObj->RECORDS(array(
				'source' => $this->conf['view.']['map_control'],
				'tables' => 'tt_content',
			));
			return $content;
		}
		return 'Datakey not set.';
	}
}