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
	
	private static $default_startDate = '01/01/2000';

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
		if (!$this->conf['sitlor.']['login']) {
			tx_icssitquery_Debug::error('Login is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['sitlor.']['password']) {
			tx_icssitquery_Debug::error('Password is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['sitlor.']['url']) {
			tx_icssitquery_Debug::error('Url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['sitlor.']['nomenclatureUrl']) {
			tx_icssitquery_Debug::error('Nomenclature url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['sitlor.']['criterionUrl']) {
			tx_icssitquery_Debug::error('Criterion url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}

		// Initialize query, connection
		$this->setConnection();
		// Set typoscript defaultConf
		$this->setDefaultConf();
		$this->setDefaultSeparator();
		// Set search params
		if (isset($this->piVars['search']))
			$this->setPIVars_searchParams();

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
		$this->conf['url'] = $url? $url: $this->conf['sitlor.']['url'];

		$login = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'login', 'main');
		$this->conf['login'] = $login? $login: $this->conf['sitlor.']['login'];

		$password = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'password', 'main');
		$this->conf['password'] = $password? $password: $this->conf['sitlor.']['password'];

		// Get mode
		$codes = array();
		$modes = array();
		if (isset($this->piVars['showUid'])) {
			$this->sitlor_uid = $this->piVars['showUid'];
			$codes = array('SINGLE');
		}
		if (isset($this->piVars['mode']))
			$modes = array($this->piVars['mode']);
		if (empty($modes))
			$modes = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'what_to_display', 'main'), true);
		if (empty($modes))
			$modes = t3lib_div::trimExplode(',', $this->conf['view.']['modes'], true);
		if (!empty($modes))
			$codes = array_merge($codes, $modes);
		$this->codes = array_unique($codes);

		$PIDitemDisplay = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'PIDitemDisplay', 'main');
		if ($PIDitemDisplay)
			$this->conf['PIDitemDisplay'] = $PIDitemDisplay;
		if (!$this->conf['PIDitemDisplay']) 
			$this->conf['PIDitemDisplay'] = $GLOBALS['TSFE']->id;

		// Get OTNancySubscriber
		$OTNancySubscriber = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'OTNancySubscriber', 'main');
		$this->conf['sitlor.']['OTNancy'] = $OTNancySubscriber? $OTNancySubscriber: $this->conf['sitlor.']['OTNancy'];

		// Get page size
		$size = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'size', 'main');
		$this->conf['view.']['size'] = $size? $size: $this->conf['view.']['size'];
		$this->conf['view.']['size'] = $this->conf['view.']['size']? $this->conf['view.']['size']: $this->defaultSize;

		if (isset($this->piVars['page']))
			$this->conf['page'] = $this->piVars['page'] +1;
		if (!$this->conf['page'])
			$this->conf['page'] = 1;

		// Get param select
		if (isset($this->piVars['data']))
			$dataGroup = $this->piVars['data'];
		if (!$dataGroup)
			$dataGroup = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'dataGroup', 'paramSelect');
		$this->conf['view.']['dataGroup'] = $dataGroup? $dataGroup: $this->conf['view.']['dataGroup'];

		if (isset($this->piVars['subDataGroups']))
			$subDataGroups = $this->piVars['subDataGroups'];
		if (!$subDataGroups) {
			$subDataGroup = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'subDataGroup', 'paramSelect');
			switch ($subDataGroup) {
				case 'HOTEL':
					$subDataGroups = 'HOTEL';
					break;
				case 'CAMPING_YOUTHHOSTEL':
					$subDataGroups = 'CAMPING,YOUTHHOSTEL';
					break;
				case 'STRANGE':
					$subDataGroups = 'STRANGE';
					break;
				case 'HOLLIDAY_COTTAGE_GUESTHOUSE':
					$subDataGroups = 'HOLLIDAY_COTTAGE,GUESTHOUSE';
					break;
				default:
					$subDataGroups = '';
			}
		}
		$this->conf['view.']['subDataGroups'] = $subDataGroups? $subDataGroups: $this->conf['view.']['subDataGroups'];

		if (isset($this->piVars['select']['OTNancy']))
			$filterOTNancy = $this->piVars['select']['OTNancy'];
		$filterOTNancy = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'OTNancy', 'paramSelect');
		$this->conf['filter.']['OTNancy'] = $filterOTNancy? $filterOTNancy : $this->conf['filter.']['OTNancy'];
		
		// Filter on entity (not sure)
		if (isset($this->piVars['select']['entity_737']))
			$filterEntity737 = $this->piVars['select']['entity_737'];
		$filterEntity737 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'entity_737', 'paramSelect');
		$this->conf['filter.']['entity_737'] = $filterEntity737? $filterEntity737 : $this->conf['filter.']['entity_737'];
		
			// Select params Hotel
		$hotelTypes =  $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'hotelTypes', 'paramSelect');
		$this->conf['filter.']['hotelTypes'] = $hotelTypes? $hotelTypes : $this->conf['filter.']['hotelType'];
		$hotelEquipments =  $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'hotelEquipments', 'paramSelect');
		$this->conf['filter.']['hotelEquipments'] = $hotelEquipments? $hotelEquipments : $this->conf['filter.']['hotelEquipment'];
		
			// Select params Restaurant
		$restaurantCategories =  $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'restaurantCategories', 'paramSelect');
		$this->conf['filter.']['restaurantCategories'] = $restaurantCategories? $restaurantCategories : $this->conf['filter.']['restaurantCategories'];
		$foreignFood =  $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'foreignFoods', 'paramSelect');
		$this->conf['filter.']['foreignFoods'] = $foreignFood? $foreignFood : $this->conf['filter.']['foreignFoods'];

			// Select params Event
		$period =  $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'period', 'paramSelect');
		if ($period) {
			$this->conf['filter.']['startDate'] = date('d/m/Y');
			$this->conf['filter.']['endDate'] = date('d/m/Y', strtotime('+8 days'));
		} else {
			$startDate =  $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'startDate', 'paramSelect');
			$this->conf['filter.']['startDate'] = $startDate? date('d/m/Y', $startDate) : $this->conf['filter.']['startDate'];
			$endDate =  $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'endDate', 'paramSelect');
			$this->conf['filter.']['endDate'] = $endDate? date('d/m/Y', $endDate) : $this->conf['filter.']['endDate'];
		}
		
		$noFeeEvent =  $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'noFeeEvent', 'paramSelect');
		if ($noFeeEvent) {
			$this->conf['filter.']['noFeeEvent'] = tx_icssitlorquery_CriterionUtils::CURRENT_FREE.':'.tx_icssitlorquery_CriterionUtils::CURRENT_FREE_YES;
		}
		
		if (!$this->conf['filter.']['startDate'])
			$this->conf['filter.']['startDate'] = self::$default_startDate;
			
		// Get param sorting
		$sortName = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sortName', 'paramSorting');
		$this->conf['sortName'] = $sortName? $sortName: $this->conf['sortName'];
		$sortOrder = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sortOrder', 'paramSorting');
		$this->conf['sortOrder'] = $sortOrder? $sortOrder: $this->conf['sortOrder'];
	}
	
	/**
	 * Sets search params from piVars
	 *
	 * @return	void
	 */
	function setPIVars_searchParams() {
		$params = $this->piVars['search'];

		// if (isset($this->piVars['btn_sword']) && $params['sword'])
			$this->sword = $params['sword'];
			
		// if (isset($this->piVars['btn_hotelType']) && count($params['hotelType'])>0)
			$this->conf['filter.']['hotelTypes'] = implode(',', $params['hotelType']);
				
		// if (isset($this->piVars['btn_hotelEquipment']) && count($params['hotelEquipment'])>0)
			$this->conf['filter.']['hotelEquipments'] = implode(',', $params['hotelEquipment']);
				
		// if (isset($this->piVars['btn_restaurantCategory']) && count($params['restaurantCategory'])>0)
			$this->conf['filter.']['restaurantCategories'] = implode(',', $params['restaurantCategory']);
			
		// if (isset($this->piVars['btn_restaurantSpeciality']) && count($params['culinarySpeciality'])>0)
			$this->conf['filter.']['foreignFoods'] = implode(',', $params['culinarySpeciality']);
			
		// if (isset($this->piVars['btn_eventDate'])) {
			if ($params['startDate'])
				$this->conf['filter.']['startDate']= $params['startDate'];
			if ($params['endDate'])
				$this->conf['filter.']['endDate']= $params['endDate'];
		// }
		
		// if (isset($this->piVars['btn_noFee']) && $params['noFee'])
			$this->conf['filter.']['noFeeEvent'] = $params['noFee'];

		$this->navParams = array('search' => $params);
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
		$this->queryService->setPager(intval($this->conf['page']), intval($this->conf['view.']['size']));
		tx_icssitlorquery_NomenclatureFactory::SetConnectionParameters($this->conf['login'], $this->conf['password'], $this->conf['nomenclatureUrl']);
		tx_icssitlorquery_CriterionFactory::SetConnectionParameters($this->conf['login'], $this->conf['password'], $this->conf['criterionUrl']);
	}

	/**
	 * Sets default TypoScript configuration
	 *
	 * @return	void
	 */
	function setDefaultConf() {
		foreach ($this->conf['defaultConf.'] as $type => $conf) {
			if ($type{strlen($type) - 1} != '.')
				continue;
			$type = substr($type, 0, -1);
			$class = 'tx_icssitlorquery_' . $type;
			if ($type == 'ValuedTermTuple') {
				foreach ($conf as $tag => $subconf) {
					if ($tag{strlen($tag) - 1} != '.')
						continue;
					$tag = substr($tag, 0, -1);
					call_user_func(array($class, 'SetDefaultConf'), $tag, $subconf);
				}
			}
			else {
				try {
					call_user_func(array($class, 'SetDefaultConf'), $conf);
				}
				catch (Exception $e) {
					t3lib_div::devLog($class . ' default conf', 'ics_sitlor_query', 0, $conf);
				}
			}
		}
	}
	
	/**
	 * Sets default separator
	 *
	 * @return	void
	 */
	function setDefaultSeparator() {
		foreach ($this->conf['defaultSeparator.'] as $type => $conf) {
			$class = 'tx_icssitlorquery_' . $type . 'List';
			tx_icssitlorquery_AbstractList::setDefaultSeparator($conf, strtolower($class));
		}
	}

	
	/**
	 * Render phones
	 *
	 * @param	array		$phones : Array of Phone
	 * @return	string		The phones content
	 */
	function renderPhones($phones) {
		return $this->cObj->stdWrap(implode($this->conf['renderConf.']['phones.']['separator'], $phones), $this->conf['renderConf.']['phones.']);
	}
	
	/**
	 * Render fax
	 *
	 * @param	string		$fax : Fax
	 * @return	string		The phones content
	 */
	function renderFax($fax) {
		return $this->cObj->stdWrap($fax, $this->conf['renderConf.']['fax.']);
	}
	
	/**
	 * Render price
	 *
	 * @param	tx_icssitlorquery_ValuedTerm		$price :Price
	 * @return	string		The price content
	 */
	function renderPrice(tx_icssitlorquery_ValuedTerm $price) {
		return $this->cObj->stdWrap($price, $this->conf['renderConf.']['price.']);
	}
	
	/**
	 * Render open close day
	 *
	 * @param	tx_icssitlorquery_ValuedTerm		$price :Price
	 * @return	string		The price content
	 */
	function renderOpenCloseDay(tx_icssitlorquery_ValuedTerm $day) {
		return $day->toStringConf($this->conf['renderConf.']['openCloseDay.']);
	}
	
	/**
	 * Render date
	 *
	 * @param	tx_icssitlorquery_TimeTable		$timeTable : TimeTable
	 * @return	string		The timeTable content
	 */
	function renderDate(tx_icssitlorquery_TimeTable $timeTable) {
		return $timeTable->toStringConf($this->conf['renderConf.']['date.']);
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
		// Set filter on date to get date data
		list($day, $month, $year) = explode('/', $this->conf['filter.']['startDate']);
		$startDate = mktime(0,0,0,$month,$day,$year);
		$StartDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_StartDateFilter', $startDate);
		$this->queryService->addFilter($StartDateFilter);
		$noDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_NoDateFilter', true);
		$this->queryService->addFilter($noDateFilter);
		
		$idFilter = t3lib_div::makeInstance('tx_icssitlorquery_idFilter', intval($this->sitlor_uid));
		$this->queryService->addFilter($idFilter);
		$dataGroup = (string)strtoupper(trim($this->conf['view.']['dataGroup']));
		switch($dataGroup) {
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
				tx_icssitquery_Debug::warning('Single view datagroup ' . $this->conf['view.']['dataGroup'] . ' is not defined.');
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
		// Set filter on OT Nancy
		if ($this->conf['sitlor.']['OTNancy'] && $this->conf['filter.']['OTNancy']) {
			list($code, $value) = t3lib_div::trimExplode(':', $this->conf['sitlor.']['OTNancy']);
			$this->addCriterionFilter(intval($code), ($value? array($value): null));
		}
		// Set filter on entity 737
		if ($this->conf['filter.']['entity_737'])
			$this->queryService->addFilter(t3lib_div::makeInstance('tx_icssitlorquery_EntityFilter', 737));

			// Set filter on date to get date data
		list($day, $month, $year) = explode('/', $this->conf['filter.']['startDate']);
		$startDate = mktime(0,0,0,$month,$day,$year);
		$StartDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_StartDateFilter', $startDate);
		$this->queryService->addFilter($StartDateFilter);
		if ($this->conf['filter.']['endDate']) {
			list($day, $month, $year) = explode('/', $this->conf['filter.']['endDate']);
			$endDate = mktime(23,59,59,$month,$day,$year);
			$EndDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_EndDateFilter', $endDate);
			$this->queryService->addFilter($EndDateFilter);
		}
		// Always retrieves elements without date
		$noDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_NoDateFilter', true);
		$this->queryService->addFilter($noDateFilter);

		// Set filter on keyword
		if ($this->sword)
			$this->queryService->addFilter(t3lib_div::makeInstance('tx_icssitlorquery_KeywordFilter', $this->sword));

		$dataGroup = (string)strtoupper(trim($this->conf['view.']['dataGroup']));
		switch($dataGroup) {
			case 'ACCOMODATION':
				if (!$this->conf['view.']['subDataGroups']) {
					$this->queryService->addFilter(t3lib_div::makeInstance('tx_icssitlorquery_GenderFilter', tx_icssitlorquery_NomenclatureUtils::ACCOMODATION));
				} else {
					$subDataGroups = (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
					$subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);
					$types = array();
					$categories = array();
					foreach($subDataGroups as $subDataGroup) {
						switch($subDataGroup) {
							case 'HOTEL':
								if ($this->conf['filter.']['hotelTypes']) {
									$hotelTypes = t3lib_div::trimExplode(',', $this->conf['filter.']['hotelTypes'], true);
									$types = array_merge($types, $hotelTypes);
								} else {
									$types = array_merge($types, tx_icssitlorquery_NomenclatureUtils::$hotel);
								}
								if ($this->conf['filter.']['hotelEquipments']) {
									$criterionTerms = $this->process_criterionTermArray(t3lib_div::trimExplode(',', $this->conf['filter.']['hotelEquipments'], true));
									foreach ($criterionTerms as $criterionID=>$termIDs) {
										$this->addCriterionFilter($criterionID, $termIDs);
									}
								}
								break;
							case 'CAMPING':
								$types = array_merge($types, tx_icssitlorquery_NomenclatureUtils::$camping);
								break;
							case 'YOUTHHOSTEL':
								$types[] = tx_icssitlorquery_NomenclatureUtils::YOUTH_HOSTEL;
								break;
							case 'STRANGE':
								$this->addCriterionFilter(tx_icssitlorquery_CriterionUtils::STRANGE_ACCOMODATION, array(tx_icssitlorquery_CriterionUtils::STRANGE_ACCOMODATION_YES));
								break;
							case 'HOLLIDAY_COTTAGE':
								$categories[] = tx_icssitlorquery_NomenclatureUtils::HOLLIDAY_COTTAGE;
								break;
							case 'GUESTHOUSE':
								$categories[] = tx_icssitlorquery_NomenclatureUtils::GUESTHOUSE;
								break;
							default:
								tx_icssitquery_Debug::warning('Sub-Datagroup ' . $subDataGroup . ' is not defined.');
						}
					}
					if (!empty($types)) {
						$filter = t3lib_div::makeInstance(
							'tx_icssitlorquery_TypeFilter', 
							tx_icssitlorquery_NomenclatureFactory::GetTypes($types)
						);
						$this->queryService->addFilter($filter);
					}
					if (!empty($categories)) {
						$filter = t3lib_div::makeInstance(
							'tx_icssitlorquery_CategoryFilter', 
							tx_icssitlorquery_NomenclatureFactory::GetCategories($categories)
						);
						$this->queryService->addFilter($filter);
					}
				}
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_AccomodationSortingProvider');
				$elements = $this->queryService->getAccomodations($sorting);
				break;

			case 'RESTAURANT':
				$category = tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESTAURANT);
				$categoryList = t3lib_div::makeInstance('tx_icssitlorquery_CategoryList');
				$categoryList->Add($category);
				$filter = t3lib_div::makeInstance('tx_icssitlorquery_CategoryFilter', $categoryList);
				$this->queryService->addFilter($filter);
				if ($this->conf['filter.']['restaurantCategories']) {
					$criterionTerms = $this->process_criterionTermArray(t3lib_div::trimExplode(',', $this->conf['filter.']['restaurantCategories'], true));
					foreach ($criterionTerms as $criterionID=>$termIDs) {
						$this->addCriterionFilter($criterionID, $termIDs);
					}
				}
				if ($this->conf['filter.']['foreignFoods']) {
					$criterionTerms = $this->process_criterionTermArray(t3lib_div::trimExplode(',', $this->conf['filter.']['foreignFoods'], true));
					foreach ($criterionTerms as $criterionID=>$termIDs) {
						$this->addCriterionFilter($criterionID, $termIDs);
					}
				}
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_RestaurantSortingProvider');
				$elements = $this->queryService->getRestaurants($sorting);
				break;

			case 'EVENT':
				$filter = t3lib_div::makeInstance('tx_icssitlorquery_GenderFilter', tx_icssitlorquery_NomenclatureUtils::EVENT);
				$this->queryService->addFilter($filter);
				$this->addCriterionFilter(tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT);
				if ($this->conf['filter.']['noFeeEvent']) {
					list($crit, $term) = t3lib_div::trimExplode(':', $this->conf['filter.']['noFeeEvent']);
					$this->addCriterionFilter($crit, array($term));
				}
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_EventSortingProvider');
				$elements = $this->queryService->getEvents($sorting);
				break;

			default:
				tx_icssitquery_Debug::warning('List view datagroup ' . $dataGroup . ' is not defined.');
		}
		return $elements;
	}

	/**
	 * Add criterion filter
	 *
	 * @param	int			$criterionID: Criterion ID
	 * @param	array int	$terms: Array of Criterion's terms
	 * @return void
	 */
	private function addCriterionFilter($criterionID, $terms=null) {
		if (!is_int($criterionID))
			$criterionID = intval($criterionID);
			
		$criterion = tx_icssitlorquery_CriterionFactory::GetCriterion($criterionID);
		if (!$terms || empty($terms)) {
			$list = null;
		} else {
			$list = t3lib_div::makeInstance('tx_icssitlorquery_TermList');
			foreach ($terms as $term) {
				$list->Add(tx_icssitlorquery_CriterionFactory::GetCriterionTerm($criterion, intval($term)));
			}
		}
		$filter = t3lib_div::makeInstance('tx_icssitlorquery_CriterionFilter', $criterion, $list);
		$this->queryService->addFilter($filter);
	}
	
	/**
	 * Process criterionTermArray 
	 *
	 * @param	array	$criterionTermArray: Array of criterion and term
	 * @return mixed	An array of criterionTerms
	 */
	private function process_criterionTermArray(array $criterionTermArray){
		$criterionTerms = array();
		foreach ($criterionTermArray as $criterionTerm) {
			list($criterionID, $termID) = t3lib_div::trimExplode(':', $criterionTerm, true);
			if (!isset($criterionTerms[$criterionID]))
				$criterionTerms[$criterionID] = array();
			if ($termID)
				$criterionTerms[$criterionID][] = $termID;
		}
		return $criterionTerms;
	}
	

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php']);
}

?>