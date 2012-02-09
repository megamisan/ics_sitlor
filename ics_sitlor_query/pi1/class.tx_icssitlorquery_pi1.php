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
 *   51: class tx_icssitlorquery_pi1 extends tslib_pibase
 *   73:     function main($content, $conf)
 *  143:     function init ()
 *  232:     function setConnection()
 *  244:     function displayList()
 *  274:     function displaySingle()
 *  299:     private function getElements()
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Class 'tx_icssitlorquery_pi1' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_pi1 extends tslib_pibase {
    var $prefixId      = 'tx_icssitlorquery_pi1';        // Same as class tx_icssitlorquery_name
    var $scriptRelPath = 'pi1/class.tx_icssitlorquery_pi1.php';    // Path to this script relative to the extension dir.
    var $extKey        = 'ics_sitlor_query';    // The extension key.

	var $templateFiles = array(
		'search' => 'typo3conf/ext/ics_sitlor_query/res/template_search.html',
		'results' => 'typo3conf/ext/ics_sitlor_query/res/template_results.html',
		'map' => 'typo3conf/ext/ics_sitlor_query/res/template_map.html',
		'detail' => 'typo3conf/ext/ics_sitlor_query/res/template_detail.html',
	);

	var $defaultPage = 1;
	var $defaultSize = 20;

    /**
 * The main method of the PlugIn
 *
 * @param	string		$content: The PlugIn content
 * @param	array		$conf: The PlugIn configuration
 * @return	string		The content that is displayed on the website
 */
    function main($content, $conf) {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_USER_INT_obj = 1;    // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_initPIflexForm();

		// Initialize the plugin
		$this->init();

		// Check login, password, urls
		if (!$this->conf['login']) {
			tx_icssitquery_Debug::error('Login is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['password']) {
			tx_icssitquery_Debug::error('Password is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['url']) {
			tx_icssitquery_Debug::error('Url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['nomenclatureUrl']) {
			tx_icssitquery_Debug::error('Nomenclature url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['criterionUrl']) {
			tx_icssitquery_Debug::error('Criterion url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}

		// Initialize query, connection
		$this->setConnection();

		// Display mode
		foreach ($this->codes as $theCode) {
			$theCode = (string)strtoupper(trim($theCode));
			$this->theCode = $theCode;
			switch($theCode) {
				case 'SEARCH':
				case 'LIST':
					try {
						$content .= $this->displayList();
					} catch (Exception $e) {
						tx_icssitquery_Debug::error('Retrieves data list failed: ' . $e);
					}
					break;
				case 'SINGLE':
					if ($this->sitlor_uid) {
						try {
							$content .= $this->displaySingle();
						} catch (Exception $e) {
							tx_icssitquery_Debug::error('Retrieves data set failed: ' . $e);
						}
					}
					break;
				default:
					tx_icssitquery_Debug::warning('The code ' . $theCode . ' is not defined.');
			}
		}

		return $this->pi_wrapInBaseClass($content);
    }

	/**
	 * Initialize the plugin
	 *
	 * @return	void
	 */
	function init () {
		// Get template code
		$templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template', 'main');
		$templateFile = $templateFile? $templateFile: $this->conf['template'];
		if ($templateFile) {
			$this->templateCode = $this->cObj->fileResource($templateFile);
		} else {
			$this->templateCode = $this->cObj->fileResource($this->templateFiles['search'])
				. $this->cObj->fileResource($this->templateFiles['results'])
				. $this->cObj->fileResource($this->templateFiles['map'])
				. $this->cObj->fileResource($this->templateFiles['detail']);
		}

		// Get login
		$url = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'url', 'main');
		$this->conf['url'] = $url? $url: $this->conf['url'];

		$login = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'login', 'main');
		$this->conf['login'] = $login? $login: $this->conf['login'];

		$password = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'password', 'main');
		$this->conf['password'] = $password? $password: $this->conf['password'];

		// Get mode
		$codes = array();
		if (isset($this->piVars['mode']))
			$codes[] = $this->piVars['mode'];
		if (isset($this->piVars['showUid'])) {
			$this->sitlor_uid = $this->piVars['showUid'];
			$codes = array_merge($codes, array('SINGLE'));
		}
		$modes = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'what_to_display', 'main'), true);
		$codes = array_merge($codes, $modes);
		$this->codes = array_unique($codes);

		// Get page size
		$size = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'size', 'main');
		$this->conf['size'] = $size? $size: $this->conf['size'];
		$this->conf['size'] = $this->conf['size']? $this->conf['size']: $this->defaultSize;

		if (isset($this->piVars['page']))
			$this->conf['page'] = $this->piVars['page'];
		if (!$this->conf['page'])
			$this->conf['page'] = 1;

		// Get param select
		if (isset($this->piVars['data']))
			$dataGroup = $this->piVars['data'];
		if (!$dataGroup) {
			$dataGroup = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'dataGroup', 'paramSelect');
		}
		$this->conf['dataGroup'] = $dataGroup? $dataGroup: $this->conf['dataGroup'];
		$this->conf['dataGroup'] = (string)strtoupper(trim($this->conf['dataGroup']));

		$subDataGroup = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'subDataGroup', 'paramSelect');
		$this->conf['subDataGroup'] = $subDataGroup? $subDataGroup: $this->conf['subDataGroup'];
		$this->conf['subDataGroup'] = (string)strtoupper(trim($this->conf['subDataGroup']));
		if (!$this->conf['subDataGroup'] && $this->conf['dataGroup']=='ACCOMODATION'){
			$this->conf['subDataGroup'] = 'HOTEL';
		}
		$this->conf['subDataGroup'] = (string)strtoupper(trim($this->conf['subDataGroup']));

		$OTNancySubscriber = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'OTNancySubscriber', 'paramSelect');
		$this->conf['filter.']['OTNancy'] = $OTNancySubscriber? $OTNancySubscriber: $this->conf['filter.']['OTNancy'];

		if (!$this->conf['filter.']['startDate'])
			$this->conf['filter.']['startDate'] = '01/01/2000';

		$noDate = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'noDate', 'paramSelect');
		if ($noDate != '')
			$this->conf['filter.']['noDate'] = $noDate;
		if ($this->conf['filter.']['noDate'] === '')
			$this->conf['filter.']['noDate'] = false;

		// Get param sorting
		$sortName = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sortName', 'paramSorting');
		$this->conf['sortName'] = $sortName? $sortName: $this->conf['sortName'];
		$sortOrder = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sortOrder', 'paramSorting');
		$this->conf['sortOrder'] = $sortOrder? $sortOrder: $this->conf['sortOrder'];
	}

	/**
	 * Set the queryService
	 *
	 * @param	string		$login The login
	 * @param	string		password The password
	 * @param	string		$url The url
	 * @return	void
	 */
	function setConnection() {
		$this->queryService = t3lib_div::makeInstance('tx_icssitlorquery_SitlorQueryService', $this->conf['login'], $this->conf['password'], $this->conf['url']);
		$this->queryService->setPager(intval($this->conf['page']), intval($this->conf['size']));
		tx_icssitlorquery_NomenclatureFactory::SetConnectionParameters($this->conf['login'], $this->conf['password'], $this->conf['nomenclatureUrl']);
		tx_icssitlorquery_CriterionFactory::SetConnectionParameters($this->conf['login'], $this->conf['password'], $this->conf['criterionUrl']);
	}

	/**
	 * Display the list view of elements
	 *
	 * @return	string		HTML content for list view
	 */
	function displayList() {
		$theCode = $this->theCode;
		switch ($theCode) {
			case 'SEARCH':
				$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SEARCH###');
				$renderForm = t3lib_div::makeInstance('tx_icssitlorquery_FormRenderer', $this, $this->cObj, $this->conf);
				$locMarkers['SEARCH_FORM'] = $renderForm->render();
				break;
			case 'LIST':
				$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_NOSEARCH###');
				break;
			default:
		}
		$renderList = t3lib_div::makeInstance('tx_icssitlorquery_ListRenderer', $this, $this->cObj, $this->conf);
		$locMarkers['RESULT_LIST'] = $renderList->render($this->getElements());
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
		);
		$template = $this->cObj->substituteMarkerArray($template, $markers, '###|###');
		return $template;
	}


	/**
	 * Display the single view of an element
	 *
	 * @return	string		HTML content for single view
	 */
	function displaySingle() {
		$idFilter = t3lib_div::makeInstance('tx_icssitlorquery_idFilter', intval($this->sitlor_uid));
		$this->queryService->addFilter($idFilter);
		switch($this->conf['dataGroup']) {
			case 'ACCOMODATION':
				$elements = $this->queryService->getAccomodations();
				break;
			case 'RESTAURANT':
				$elements = $this->queryService->getRestaurants();
				break;
			case 'EVENT':
				$elements = $this->queryService->getEvents();
				break;
			default:
				tx_icssitquery_Debug::warning('Single view datagroup ' . $this->conf['dataGroup'] . ' is not defined.');
		}
		$renderSingle = t3lib_div::makeInstance('tx_icssitlorquery_SingleRenderer', $this, $this->cObj, $this->conf);
		return $renderSingle->render($elements[0]);
	}

	/**
	 * Retrieves data
	 *
	 * @return	array		The array of elements
	 */
	private function getElements() {
		// Set filter on date to get date data
		list($day, $month, $year) = explode('/', $this->conf['filter.']['startDate']);
		$startDate = mktime(0,0,0,$month,$day,$year);
		$StartDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_StartDateFilter', $startDate);
		$this->queryService->addFilter($StartDateFilter);
		$noDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_NoDateFilter', $this->conf['filter.']['noDate']);
		$this->queryService->addFilter($noDateFilter);

		// Set filter on OT Nancy
		if ($this->conf['filter.']['OTNancy']) {
			$criterion = tx_icssitlorquery_CriterionFactory::GetCriterion(intval($this->conf['filter.']['OTNancy']));
			$this->queryService->addFilter(t3lib_div::makeInstance('tx_icssitlorquery_CriterionFilter', array($criterion)));
		}

		switch($this->conf['dataGroup']) {
			case 'ACCOMODATION':
				switch($this->conf['subDataGroup']) {
					case 'HOTEL':
						$types = tx_icssitlorquery_NomenclatureFactory::GetTypes(tx_icssitlorquery_NomenclatureUtils::$hotel);
						$filter = t3lib_div::makeInstance('tx_icssitlorquery_TypeFilter', $types);
						$this->queryService->addFilter($filter);
						break;
					case 'CAMPING_AND_YOUTHHOSTEL':
						$types = tx_icssitlorquery_NomenclatureFactory::GetTypes(tx_icssitlorquery_NomenclatureUtils::$campingAndYouthHostel);
						$filter = t3lib_div::makeInstance('tx_icssitlorquery_TypeFilter', $types);
						$this->queryService->addFilter($filter);
						break;
					case 'STRANGE':
						$criteria = tx_icssitlorquery_NomenclatureFactory::GetTypes(array(tx_icssitlorquery_CriterionUtils::STRANGE_ACCOMODATION));
						$filter = t3lib_div::makeInstance('tx_icssitlorquery_CriterionFilter', $criteria);
						$this->queryService->addFilter($filter);
						break;
					case 'HOLLIDAY_COTTAGE_AND_GUESTHOUSE':
						$categories = tx_icssitlorquery_NomenclatureFactory::GetTypes(array(
							tx_icssitlorquery_NomenclatureUtils::GUESTHOUSE,
							tx_icssitlorquery_NomenclatureUtils::HOLLIDAY_COTTAGE,
						));
						$filter = t3lib_div::makeInstance('tx_icssitlorquery_CategoryFilter', $categories);
						$this->queryService->addFilter($filter);
						break;
					default:
						tx_icssitquery_Debug::warning('Sub-Datagroup ' . $this->conf['subDataGroup'] . ' is not defined.');
				}
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_AccomodationSortingProvider');
				$elements = $this->queryService->getAccomodations($sorting);
				break;

			case 'RESTAURANT':
				$category = tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESTAURANT);
				$filter = t3lib_div::makeInstance('tx_icssitlorquery_CategoryFilter', array($category));
				$this->queryService->addFilter($filter);
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_RestaurantSortingProvider');
				$elements = $this->queryService->getRestaurants($sorting);
				break;

			case 'EVENT':
				$filter = t3lib_div::makeInstance('tx_icssitlorquery_GenderFilter', tx_icssitlorquery_NomenclatureUtils::EVENT);
				$this->queryService->addFilter($filter);
				$criterion = tx_icssitlorquery_CriterionFactory::GetCriterion(tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT);
				$filter = t3lib_div::makeInstance('tx_icssitlorquery_CriterionFilter', array($criterion));
				$this->queryService->addFilter($filter);
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_EventSortingProvider');
				$elements = $this->queryService->getEvents($sorting);
				break;

			default:
				tx_icssitquery_Debug::warning('List view datagroup ' . $dataGroup . ' is not defined.');
		}
		return $elements;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php']);
}

?>