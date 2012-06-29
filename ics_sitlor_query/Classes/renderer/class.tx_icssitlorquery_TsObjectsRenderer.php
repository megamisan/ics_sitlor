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
 *   51: class tx_icssitlorquery_TsObjectsRenderer
 *   61:     function __construct($pi, $cObj, $lConf)
 *   75:     function render($elements=null)
 *  100:     private function renderTsObject(array $elements)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitlorquery_TsObjectsRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Mickaël PAILLARD <mickael.paillard@plan-net.fr>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
 class tx_icssitlorquery_TsObjectsRenderer {

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
	function render($content) {
		$markersArray=array();
		preg_match_all('/###TSOBJECT([0-9]*)###/is', $content,$aMatches);
		foreach((array)$aMatches[0] as $sMarker) {
			$sObjectName=strtolower(str_replace('###', '', $sMarker));
			$markersArray[$sMarker]=($this->conf[$sObjectName]!='')?$this->cObj->cObjGetSingle($this->conf[$sObjectName],$this->conf[$sObjectName.'.']):'';
		}
		
		$content= $this->cObj->substituteMarkerArray($content, $markersArray);
		
		return $content;
	}

 }