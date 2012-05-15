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
 *   51: class tx_icssitlorquery_RSSRenderer
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
 * Class 'tx_icssitlorquery_RSSRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
 class tx_icssitlorquery_RSSRenderer {

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
	function render($elements=null, $template, &$markers, &$subparts) {
		if (!isset($elements) || empty($elements))
			return '';
		
		$this->templateCode = $template;
		

		$subparts['###HEADER###'] = $this->renderHeader();
		$subparts['###CONTENT###'] = $this->renderContent($elements);
		
		$charset = ($GLOBALS['TSFE']->metaCharset?$GLOBALS['TSFE']->metaCharset:'iso-8859-1');
		if ($this->conf['displayXML.']['xmlDeclaration']) {
			$locMarkers['XML_DECLARATION'] = trim($this->conf['displayXML.']['xmlDeclaration']);
		} else {
			$locMarkers['XML_DECLARATION'] = '<?xml version="1.0" encoding="'.$charset.'"?>';
		}
			
		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');

		return $template;
	}

	/**
	 * Render header
	 *
	 * @return	string	The RSS header
	 */
	private function renderHeader() {
		$template = $this->cObj->getSubpart($this->templateCode, '###HEADER###');
		$locMarkers = array(
			'SITE_TITLE' => htmlspecialchars($this->conf['displayXML.']['xmlTitle']),
			'SITE_LINK' => t3lib_div::getIndpEnv('TYPO3_SITE_URL'),
			'SITE_DESCRIPTION' => htmlspecialchars($this->conf['displayXML.']['xmlDesc']),
			'SITLOR_COPYRIGHT' => '',
			'SITLOR_WEBMASTER' => $this->conf['displayXML.']['xmlWebMaster'],
			'SITLOR_MANAGINGEDITOR' => $this->conf['displayXML.']['xmlManagingEditor'],
		);
		
		$locMarkers['SITE_LANG'] = '<language>'.htmlspecialchars($this->conf['displayXML.']['xmlLang']).'</language>';
		if(empty($this->conf['displayXML.']['xmlLang'])) {
			$locMarkers['SITE_LANG'] = '';
		}
		
		$locMarkers['IMG'] = t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/' . $this->conf['displayXML.']['xmlIcon'];
		$imgFile = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . $this->conf['displayXML.']['xmlIcon'];
		$imgSize = is_file($imgFile)? getimagesize($imgFile): '';
		$locMarkers['IMG_W'] = $imgSize[0];
		$locMarkers['IMG_H'] = $imgSize[1];
		
		if ($this->conf['displayXML.']['xmlCopyright']) {
			$markerArray['SITLOR_COPYRIGHT'] = '<copyright>' . $this->conf['displayXML.']['xmlCopyright'] . '</copyright>';
		} else {
			$markerArray['SITLOR_COPYRIGHT'] = '';
		}
		
		return $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
	}
	
	/**
	 * Render content
	 *
	 * @param	array	$elements: SITLOR elements
	 * @return	string	The RSS feed
	 */
	private function renderContent($elements) {
		$template = $this->cObj->getSubpart($this->templateCode, '###CONTENT###');
		$subparts = array();
		$itemTemplate = $this->cObj->getSubpart($this->templateCode, '###SITLOR###');
		foreach ($elements as $element) {
			$locMarkers = array(
				'SITLOR_TITLE' => $element->Name,
				'SITLOR_LINK' => htmlspecialchars(t3lib_div::linkThisUrl(t3lib_div::getIndpEnv('TYPO3_SITE_URL'), array('id' => $this->conf['displayXML.']['linkItems.']['page'], 'tx_icssitlorquery_pi1[showUid]' => $element->ID))),
				'SITLOR_SUBHEADER' => htmlspecialchars($element->Description),
			);
			// TODO: if date $locMarkers['PUBDATE'] = '<pubdate>la date</pubdate>'
			$subparts['###SITLOR###'] .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
		}
		$markers = array();
		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

 }