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
 *   68: class tx_icssitlorquery_pi1 extends tslib_pibase
 *   95:     function main($content, $conf)
 *  162:     function init ()
 *  218:     private function initMode()
 *  240:     private function initFilterParams()
 *  334:     private function initSortingParams()
 *  368:     function setPIVars_searchParams()
 *  398:     function setConnection()
 *  420:     function setDefaultConf()
 *  456:     function setDefaultSeparator()
 *  470:     function renderData($name, $element)
 *  494:     function renderSingleLink($name, $element)
 *  511:     function renderSortings()
 *  547:     function displayList()
 *  585:     function displaySingle()
 *  629:     private function getElements()
 *  679:     private function getAccomodations()
 *  777:     private function getRestaurants()
 *  819:     private function getEvents()
 *  850:     private function getFreeTime()
 *  874:     private function getCriterionFilter($criterionID, $terms=null)
 *  896:     private function parseCriterionsTermsDefinition(array $criterionTermArray)
 *  914:     private function renderCachedContent($mode)
 *  954:     function storeCachedContent($content)
 *
 * TOTAL FUNCTIONS: 23
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

	var $templateFile = null;
	var $templateFiles = array(
		'search' => 'typo3conf/ext/ics_sitlor_query/res/template_search.html',
		'results' => 'typo3conf/ext/ics_sitlor_query/res/template_results.html',
		'map' => 'typo3conf/ext/ics_sitlor_query/res/template_map.html',
		'detail' => 'typo3conf/ext/ics_sitlor_query/res/template_detail.html',
	);

	var $defaultPage = 1;
	var $defaultSize = 20;

	private static $default_startDate = '01/01/2000';
	private static $geoc_zoom = 12;
	private static $geoc_canvas = array('340px', '200px');

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	string		The content that is displayed on the website
	 */
	public function main($content, $conf) {
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
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Invalid service parameters', true));
		}
		if (!$this->conf['sitlor.']['password']) {
			tx_icssitquery_Debug::error('Password is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Invalid service parameters', true));
		}
		if (!$this->conf['sitlor.']['url']) {
			tx_icssitquery_Debug::error('Url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Invalid service parameters', true));
		}
		if (!$this->conf['sitlor.']['nomenclatureUrl']) {
			tx_icssitquery_Debug::error('Nomenclature url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Invalid service parameters', true));
		}
		if (!$this->conf['sitlor.']['criterionUrl']) {
			tx_icssitquery_Debug::error('Criterion url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Invalid service parameters', true));
		}

		// Initialize query, connection
		$this->setConnection();
		// Set typoscript defaultConf
		tx_icssitlorquery_Configurator::setDefaultConf($this->conf['defaultConf.']);
		tx_icssitlorquery_Configurator::setDefaultSeparator($this->conf['defaultSeparator.']);
		// Set search params
		if (isset($this->piVars['search']))
			$this->setPIVars_searchParams();

		if ($this->sitlor_uid && in_array('SINGLE', $this->codes)) {
			try {
				$content .= $this->displaySingle();
			} catch (Exception $e) {
				tx_icssitquery_Debug::error('Retrieves data set failed: ' . $e);
			}
		}
		elseif (count(array_intersect(array('SEARCH', 'LIST', 'MAP'), $this->codes)) > 0) {
			try {
				$content .= $this->displayList();
			} catch (Exception $e) {
				tx_icssitquery_Debug::error('Retrieves data list failed: ' . $e);
			}
		}
		else {
			return '';
		}

		return $this->pi_wrapInBaseClass($content);
    }

	/**
	 * Initialize the plugin
	 *
	 * @return	void
	 */
	private function init() {
		// Get template code
		$this->templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template', 'main');
		$this->templateFile = $this->templateFile ? $this->templateFile : $this->conf['template'];
		if ($this->templateFile) {
			$this->templateCode = $this->cObj->fileResource($this->templateFile);
		} else {
			$this->templateCode = $this->cObj->fileResource($this->templateFiles['search'])
				. $this->cObj->fileResource($this->templateFiles['results'])
				. $this->cObj->fileResource($this->templateFiles['map'])
				. $this->cObj->fileResource($this->templateFiles['detail']);
		}

		// Get login
		$url = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'url', 'main');
		$this->conf['url'] = $url ? $url : $this->conf['sitlor.']['url'];

		$login = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'login', 'main');
		$this->conf['login'] = $login ? $login : $this->conf['sitlor.']['login'];

		$password = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'password', 'main');
		$this->conf['password'] = $password ? $password : $this->conf['sitlor.']['password'];

		// Get mode
		$this->initMode();

		// Get OTNancySubscriber
		$OTNancySubscriber = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'OTNancySubscriber', 'main');
		$this->conf['sitlor.']['OTNancy'] = $OTNancySubscriber ? $OTNancySubscriber : $this->conf['sitlor.']['OTNancy'];

		// Get page size
		$size = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'size', 'main');
		$this->conf['view.']['size'] = $size ? $size : $this->conf['view.']['size'];
		$this->conf['view.']['size'] = $this->conf['view.']['size'] ? $this->conf['view.']['size'] : $this->defaultSize;

		if (isset($this->piVars['page']))
			$this->conf['page'] = $this->piVars['page'] + 1;
		if (!$this->conf['page'])
			$this->conf['page'] = 1;

		// Get param select
		$this->initFilterParams();

		// Get param sorting
		$this->initSortingParams();

		$this->conf['geocode.']['zoom'] = $this->conf['geocode.']['zoom'] ? $this->conf['geocode.']['zoom'] : self::$geoc_zoom;
		$this->conf['geocode.']['canvas.']['width'] = $this->conf['geocode.']['canvas.']['width'] ? $this->conf['geocode.']['canvas.']['width'] : self::$geoc_canvas[0] ;
		$this->conf['geocode.']['canvas.']['height'] = $this->conf['geocode.']['canvas.']['height'] ? $this->conf['geocode.']['canvas.']['height'] : self::$geoc_canvas[1] ;
	}

	/**
	 * Initialize mode
	 *
	 * @return	void
	 */
	private function initMode() {
		$codes = array();
		if (isset($this->piVars['showUid'])) {
			$this->sitlor_uid = $this->piVars['showUid'];
		}
		$codes = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'what_to_display', 'main'), true);
		if (empty($codes))
			$codes = t3lib_div::trimExplode(',', $this->conf['view.']['modes'], true);
		$this->codes = array_unique($codes);

		$PIDitemDisplay = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'PIDitemDisplay', 'main');
		if ($PIDitemDisplay)
			$this->conf['PIDitemDisplay'] = $PIDitemDisplay;
		if (!$this->conf['PIDitemDisplay'])
			$this->conf['PIDitemDisplay'] = $GLOBALS['TSFE']->id;

		$mapControl = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'map_control', 'main');
		if (!empty($mapControl))
			$this->conf['view.']['map_control'] = $mapControl;

		$dataKey = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'map_control_datakey', 'main');
		if (!empty($mapControl))
			$this->conf['view.']['map_control.']['datakey'] = $dataKey;
	}

	/**
	 * Initialize filter params
	 *
	 * @return	void
	 */
	private function initFilterParams() {
		// Get dataGroup and sub-dataGroup
		if (isset($this->piVars['data']))
			$dataGroup = $this->piVars['data'];
		if (!$dataGroup)
			$dataGroup = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'dataGroup', 'paramSelect');
		$this->conf['view.']['dataGroup'] = $dataGroup ? $dataGroup : $this->conf['view.']['dataGroup'];

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
			$this->conf['filter.']['endDate'] = date('d/m/Y', strtotime('+7 days'));
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

		// Init startDate
		if (!$this->conf['filter.']['startDate'])
			$this->conf['filter.']['startDate'] = self::$default_startDate;

		// Select params on illustration
		if (isset($this->piVars['select']['illustration']))
			$filterIllustration = $this->piVars['select']['illustration'];
		$filterIllustration = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'illustration', 'paramSelect');
		$this->conf['filter.']['illustration'] = $filterIllustration? $filterIllustration : $this->conf['filter.']['illustration'];

		// Select free time theme
		if (isset($this->piVars['select']['freeTimeTheme']))
			$freeTimeTheme = $this->piVars['select']['freeTimeTheme'];
		$freeTimeTheme = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'freeTimeThemes', 'paramSelect');
		$this->conf['filter.']['freeTimeTheme'] = $freeTimeTheme? $freeTimeTheme: $this->conf['filter.']['freeTimeTheme'];

		
		// Select subscriber arts and crafts
		$subscriberDataArray = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'subscriber_types', 'paramSelect'));
		
		$subscriber_type = $this->getSubscriberFilter($subscriberDataArray);
		$subscriber_typeCategory = $subscriber_type[0];
		$subscriber_typeValue = $subscriber_type[1];
		
		$this->conf['filter.']['subscriber_type.']['category'] = $subscriber_typeCategory? $subscriber_typeCategory: $this->conf['filter.']['subscriber_type.']['category'];
		$this->conf['filter.']['subscriber_type.']['value'] = $subscriber_typeValue? $subscriber_typeValue: $this->conf['filter.']['subscriber_type.']['value'];	

	}

	/**
	 * Initialize sorting params
	 *
	 * @return	void
	 */
	private function initSortingParams() {
		$sortName = $this->piVars['sortName']? $this->piVars['sortName']: '';
		if (!$sortName) {
			$dataGroup = (string)strtoupper(trim($this->conf['view.']['dataGroup']));
			$subDataGroups = (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
			$subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);
			if ($dataGroup == 'ACCOMODATION') {
				$sortName = 'ALPHA';
				if (in_array('HOTEL', $subDataGroups))
					$sortName = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'hotel_sortName', 'paramSorting');
				if (in_array('HOLLIDAY_COTTAGE', $subDataGroups) && in_array('GUESTHOUSE', $subDataGroups))
					$sortName = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'hCandGH_sortName', 'paramSorting');
			}
			if ($dataGroup == 'RESTAURANT') {
				$sortName = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'restaurant_sortName', 'paramSorting');
			}
			if ($dataGroup == 'EVENT') {
				$sortName = 'DATE';
			}
			if ($dataGroup == 'FREETIME') {
				$sortName = 'ALPHA';
			}
		}
		$this->conf['sort.']['name'] = $sortName ? $sortName : $this->conf['sort.']['name'];

		$sortExtra = $this->piVars['sortExtra'] ? $this->piVars['sortExtra'] : '';
		$this->conf['sort.']['extra'] = $sortExtra ? $sortExtra : $this->conf['sort.']['extra'];
	}

	/**
	 * Set the queryService
	 *
	 * @param	string		$login The login
	 * @param	string		password The password
	 * @param	string		$url The url
	 * @return	void
	 */
	private function setConnection() {
		$this->queryService = t3lib_div::makeInstance('tx_icssitlorquery_SitlorQueryService', $this->conf['login'], $this->conf['password'], $this->conf['url']);
		$this->queryService->setPager(intval($this->conf['page']), intval($this->conf['view.']['size']));
		tx_icssitlorquery_Configurator::setConnection($this->conf['login'], $this->conf['password'], $this->conf['sitlor.']['nomenclatureUrl'], $this->conf['sitlor.']['criterionUrl']);
		tx_icssitlorquery_Configurator::setDefaultTypeGuessing($this->queryService);
	}

	/**
	 * Sets search params from piVars
	 *
	 * @return	void
	 */
	private function setPIVars_searchParams() {
		$params = $this->piVars['search'];

		$this->sword = '';
		if ($this->piVars['btn_sword'])
			$this->sword = $params['sword'];
			
		$this->FormFilter = array();
		if ($this->piVars['btn_hotelType']) {
			$this->FormFilter['hotelType'] = $params['hotelType'];
			$this->conf['filter.']['hotelTypes'] = implode(',', $params['hotelType']);
		}
		if ($this->piVars['btn_hotelEquipment']) {
			$this->FormFilter['hotelEquipment'] = $params['hotelEquipment'];
			$this->conf['filter.']['hotelEquipments'] = implode(',', $params['hotelEquipment']);
		}
		if ($this->piVars['btn_restaurantCategory']) {
			$this->FormFilter['restaurantCategory'] = $params['restaurantCategory'];
			$this->conf['filter.']['restaurantCategories'] = implode(',', $params['restaurantCategory']);
		}
		if ($this->piVars['btn_restaurantSpeciality']) {
			$this->FormFilter['culinarySpeciality'] = $params['culinarySpeciality'];
			$this->conf['filter.']['foreignFoods'] = implode(',', $params['culinarySpeciality']);
		}
		if ($this->piVars['btn_eventDate']) {
			$this->FormFilter['startDate'] = $params['startDate'];
			$this->FormFilter['endDate'] = $params['endDate'];
			if ($params['startDate'])
				$this->conf['filter.']['startDate'] = $params['startDate'];
			if ($params['endDate'])
				$this->conf['filter.']['endDate'] = $params['endDate'];			
		}
		if ($this->piVars['btn_noFee']) {
			$this->FormFilter['noFee'] = $params['noFee'];
			$this->conf['filter.']['noFeeEvent'] = $params['noFee'];
		}

		if ($this->piVars['btn_subDataGroup']) {
			$this->FormFilter['subDataGroups'] = $params['subDataGroups'];
			if (is_array($params['subDataGroups']) && !empty($params['subDataGroups'])) {
				$this->conf['filter.']['subDataGroups'] = implode(',', $params['subDataGroups']);
			}
		}
		
		if ($this->piVars['btn_subscriber_type']) {
			$this->FormFilter['subscriber_type'] = $params['subscriber_type'];
			$subscriberDataArray = t3lib_div::trimExplode(',', $this->piVars['search']['subscriber_type'], true);
			$subscriber_type = $this->getSubscriberFilter($subscriberDataArray);
			$this->conf['filter.']['subscriber_type.']['category'] = $subscriber_type[0];
			$this->conf['filter.']['subscriber_type.']['value'] = $subscriber_type[1];	
		}

	}
	
	/**
	 * Retrieves subscriber filter
	 *
	 * @param	array	$subscriberDataArray
	 * @return	mixed	Subscriber type
	 */
	private function getSubscriberFilter(array $subscriberDataArray) {
		$subscriber_type = null;
		if ($subscriberDataArray[0]==='CRITERION' && $subscriberDataArray[1]==tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS) {
			$subscriber_typeCategory = 'ARTS_CRAFTS';
			$subscriber_typeValue = $subscriberDataArray[2];
			$subscriber_type = array($subscriber_typeCategory, $subscriber_typeValue);
		}
		elseif ($subscriberDataArray[0]==='CRITERION' && $subscriberDataArray[1]==tx_icssitlorquery_CriterionUtils::COMMERCE) {
			$subscriber_typeCategory = 'COMMERCE';
			$subscriber_typeValue = $subscriberDataArray[2];
			$subscriber_type = array($subscriber_typeCategory, $subscriber_typeValue);
		}
		elseif ($subscriberDataArray[0]==='NOMENCLATURE') {
			$subscriber_typeCategory = 'NOMENCLATURE_CATEGORY';
			$subscriber_typeValue = $subscriberDataArray[2];
			$subscriber_type = array($subscriber_typeCategory, $subscriber_typeValue);
		}
		return $subscriber_type;
	}

	/**
	 * Displays the single view of an element.
	 *
	 * @return	string		HTML content for single view.
	 */
	private function displaySingle() {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
		if (!$extConf['no_cache'] && ($content = $this->renderCachedContent('SINGLE')))
			return $content;
		$this->setQueryDateFilter(false);
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
				$elements = $this->queryService->getRecords();
				break;
		}
		$renderSingle = t3lib_div::makeInstance('tx_icssitlorquery_SingleRenderer', $this, $this->cObj, $this->conf);
		$content =  $renderSingle->render($elements[0]);

		if (!$extConf['no_cache'])
			$this->storeCachedContent($content);

		return $content;
	}
	
	/**
	 * Adds the filters on date on the query service.
	 *
	 * @param	bool		$addEndDate: Wether to add the end date filter. Optional, default to true.
	 * @return	void
	 */
	private function setQueryDateFilter($addEndDate = true) {
		list($day, $month, $year) = explode('/', $this->conf['filter.']['startDate']);
		$startDate = mktime(0,0,0,$month,$day,$year);
		$StartDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_StartDateFilter', $startDate);
		$this->queryService->addFilter($StartDateFilter);
		if ($addEndDate && $this->conf['filter.']['endDate']) {
			list($day, $month, $year) = explode('/', $this->conf['filter.']['endDate']);
			$endDate = mktime(23,59,59,$month,$day,$year);
			$EndDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_EndDateFilter', $endDate);
			$this->queryService->addFilter($EndDateFilter);
		}
		$noDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_NoDateFilter', true);
		$this->queryService->addFilter($noDateFilter);
	}

	/**
	 * Displays the list view of elements and the search form.
	 *
	 * @return	string		HTML content for list view and search form.
	 */
	private function displayList() {
		$resultModes = array('LIST', 'MAP');
		$resultTabs = array_map('strtolower', $resultModes);
		if (count(array_intersect($this->codes, $resultModes)) == 2) {
			$template = $this->cObj->getSubpart($this->templateCode, in_array('SEARCH', $this->codes) ? '###TEMPLATE_SEARCH_TABS###' : '###TEMPLATE_NOSEARCH_TABS###');
			$tabTemplate = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULT_TAB###');
			$tabs = '';
			foreach ($resultTabs as $tab) {
				$markers = array(
					'TAB_ADDCLASS' => 'tab-' . $tab . ((($this->piVars['viewTab'] == strtoupper($tab)) || (!isset($this->piVars['viewTab']) && ($tab == 'list'))) ? ' current' : ''),
					'TAB_LINK' => htmlspecialchars($this->cObj->typoLink_URL(array(
						'parameter.' => array('data' => 'tsfe:id'),
						'additionalParams' => t3lib_div::implodeArrayForUrl(
							$this->prefixId,
							array_merge(
								$this->piVars,
								array(
									'viewTab' => strtoupper($tab),
								)
							)
						),
					))),
					'TAB_LABEL' => $this->pi_getLL('results_' . $tab)
				);
				$tabs .= $this->cObj->substituteMarkerArray($tabTemplate, $markers, '###|###');
			}
			$locMarkers['RESULT_TABS'] = $tabs;
			if (isset($this->piVars['viewTab'])) {
				$this->codes = array_diff($this->codes, array_diff($resultModes, array($this->piVars['viewTab'])));
			}
		}
		else if (in_array('SEARCH', $this->codes)) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SEARCH###');
		}
		else {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_NOSEARCH###');
		}
		if (in_array('SEARCH', $this->codes)) {
			$renderForm = t3lib_div::makeInstance('tx_icssitlorquery_FormRenderer', $this, $this->cObj, $this->conf);
			$renderForm->addViewTab = isset($locMarkers['RESULT_TABS']);
			$locMarkers['SEARCH_FORM'] = $renderForm->render();
		}
		if (in_array('LIST', $this->codes)) {
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
			if (!$extConf['no_cache'] && ($content = $this->renderCachedContent('LIST'))) {
				$locMarkers['RESULT_LIST'] = $content;
			} else {
				$renderList = t3lib_div::makeInstance('tx_icssitlorquery_ListRenderer', $this, $this->cObj, $this->conf);
				$content = $renderList->render($this->getElements());
				$locMarkers['RESULT_LIST'] = $content;
				if (!$extConf['no_cache'])
					$this->storeCachedContent($content);
			}
		}
		else if (in_array('MAP', $this->codes)) {
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
			if (!$extConf['no_cache'] && ($content = $this->renderCachedContent('MAP'))) { // HTML could not be cached as the rendering is not really controlled.
				$elements = unserialize($content);
			} else {
				$elements = $this->getElements(true);
				if (!$extConf['no_cache'])
					$this->storeCachedContent(serialize($elements));
			}
			$renderMap = t3lib_div::makeInstance('tx_icssitlorquery_MapRenderer', $this, $this->cObj, $this->conf);
			$content = $renderMap->render($elements);
			$locMarkers['RESULT_LIST'] = $content;
		}
		else {
			$locMarkers['RESULT_LIST'] = '';
		}
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
		);
		$template = $this->cObj->substituteMarkerArray($template, $markers, '###|###');
		return $template;
	}

	/**
	 * Retrieves data
	 *
	 * @return	mixed		Array of elements
	 */
	private function getElements($queryAll = false) {
		// Set filter on OT Nancy
		if ($this->conf['sitlor.']['OTNancy'] && $this->conf['filter.']['OTNancy']) {
			list($code, $value) = t3lib_div::trimExplode(':', $this->conf['sitlor.']['OTNancy']);
			$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter(intval($code), ($value? array($value): null)));
		}
		// Set filter on entity 737
		if ($this->conf['filter.']['entity_737'])
			$this->queryService->addFilter(t3lib_div::makeInstance('tx_icssitlorquery_EntityFilter', 737));
		// Set filter on date to get date data
		$this->setQueryDateFilter();
		// Set filter on keyword
		if ($this->sword)
			$this->queryService->addFilter(t3lib_div::makeInstance('tx_icssitlorquery_KeywordFilter', $this->sword));

		$dataGroup = (string)strtoupper(trim($this->conf['view.']['dataGroup']));
		switch($dataGroup) {
			case 'ACCOMODATION':
				return $this->getAccomodations($queryAll);
			case 'RESTAURANT':
				return $this->getRestaurants($queryAll);
			case 'EVENT':
				return $this->getEvents($queryAll);
			case 'FREETIME':
				return $this->getFreeTime($queryAll);
			case 'SUBSCRIBER':
				return $this->getSubscriber($queryAll);
			default:
				tx_icssitquery_Debug::warning('List view datagroup ' . $dataGroup . ' is not defined.');
		}
		return null;
	}

	/**
	 * Retrieves Accomodations
	 *
	 * @param	boolean		$queryAll
	 * @return	mixed		Array of elements
	 */
	private function getAccomodations($queryAll = false) {
		$subDataGroups = (string)strtoupper(trim($this->conf['filter.']['subDataGroups']));
		$subDataGroups = $subDataGroups ? $subDataGroups : (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
		$subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);

		if (empty($subDataGroups)) {
			$kinds = t3lib_div::makeInstance('tx_icssitlorquery_KindList');
			$kinds->Add(tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::ACCOMODATION));
			$this->queryService->addFilter(t3lib_div::makeInstance('tx_icssitlorquery_KindFilter', $kinds));
		} else {
			$types = array();
			$categories = array();
			foreach ($subDataGroups as $subDataGroup) {
				switch ($subDataGroup) {
					case 'HOTEL':
						if ($this->conf['filter.']['hotelTypes']) {
							$hotelTypes = t3lib_div::trimExplode(',', $this->conf['filter.']['hotelTypes'], true);
							$types = array_merge($types, $hotelTypes);
						} else {
							$types = array_merge($types, tx_icssitlorquery_NomenclatureUtils::$hotel);
						}
						if ($this->conf['filter.']['hotelEquipments']) {
							$criterionTerms = $this->parseCriterionsTermsDefinition(t3lib_div::trimExplode(',', $this->conf['filter.']['hotelEquipments'], true));
							foreach ($criterionTerms as $criterionID=>$termIDs) {
								$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter($criterionID, $termIDs));
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
						$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter(tx_icssitlorquery_CriterionUtils::STRANGE_ACCOMODATION, array(tx_icssitlorquery_CriterionUtils::STRANGE_ACCOMODATION_YES)));
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
				$filter = t3lib_div::makeInstance('tx_icssitlorquery_TypeFilter', tx_icssitlorquery_NomenclatureFactory::GetTypes($types));
				$this->queryService->addFilter($filter);
			}
			if (!empty($categories)) {
				$filter = t3lib_div::makeInstance('tx_icssitlorquery_CategoryFilter', tx_icssitlorquery_NomenclatureFactory::GetCategories($categories));
				$this->queryService->addFilter($filter);
			}
		}
		switch ($this->conf['sort.']['name']) {
			case 'ALPHA':
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_AccomodationSortingProvider', 'alpha', strtoupper($this->conf['sort.']['extra']));
				break;
			case 'RANDOM':
				if ($this->conf['sort.']['extra'])
					$sorting = t3lib_div::makeInstance('tx_icssitlorquery_AccomodationSortingProvider', 'random', $this->conf['sort.']['extra']);
				else
					$sorting = t3lib_div::makeInstance('tx_icssitlorquery_AccomodationSortingProvider', 'random', 'start');
				break;
			case 'HOTELRATING':
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_AccomodationSortingProvider', 'rating', strtoupper($this->conf['sort.']['extra']));
				break;
			case 'PRICE':
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_AccomodationSortingProvider', 'price', strtoupper($this->conf['sort.']['extra']));
				break;
			default:
				$sorting = null;
		}

		if ($queryAll) {
			$result = $this->queryService->getAccomodations($sorting);
			if ($result) {
				$this->queryService->setPager(0, $this->queryService->getLastTotalCount());
			}
			else {
				return $result;
			}
		}
		return $this->queryService->getAccomodations($sorting);
	}

	/**
	 * Retrieves Restaurants
	 *
	 * @param	boolean		$queryAll
	 * @return	mixed		Array of elements
	 */
	private function getRestaurants($queryAll = false) {
		$category = tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESTAURANT);
		$categoryList = t3lib_div::makeInstance('tx_icssitlorquery_CategoryList');
		$categoryList->Add($category);
		$filter = t3lib_div::makeInstance('tx_icssitlorquery_CategoryFilter', $categoryList);
		$this->queryService->addFilter($filter);
		if ($this->conf['filter.']['restaurantCategories']) {
			$criterionTerms = $this->parseCriterionsTermsDefinition(t3lib_div::trimExplode(',', $this->conf['filter.']['restaurantCategories'], true));
			foreach ($criterionTerms as $criterionID=>$termIDs) {
				$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter($criterionID, $termIDs));
			}
		}
		if ($this->conf['filter.']['foreignFoods']) {
			$criterionTerms = $this->parseCriterionsTermsDefinition(t3lib_div::trimExplode(',', $this->conf['filter.']['foreignFoods'], true));
			foreach ($criterionTerms as $criterionID=>$termIDs) {
				$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter($criterionID, $termIDs));
			}
		}
		switch ($this->conf['sort.']['name']) {
			case 'RANDOM':
				if ($this->conf['sort.']['extra'])
					$sorting = t3lib_div::makeInstance('tx_icssitlorquery_RestaurantSortingProvider', 'random', $this->conf['sort.']['extra']);
				else
					$sorting = t3lib_div::makeInstance('tx_icssitlorquery_RestaurantSortingProvider', 'random', 'start');
				break;
			case 'PRICE':
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_RestaurantSortingProvider', 'price', strtoupper($this->conf['sort.']['extra']));
				break;
			default:
				$sorting = null;
		}

		if ($queryAll) {
			$result = $this->queryService->getRestaurants($sorting);
			if ($result) {
				$this->queryService->setPager(0, $this->queryService->getLastTotalCount());
			}
			else {
				return $result;
			}
		}
		return $this->queryService->getRestaurants($sorting);
	}

	/**
	 * Retrieves Events
	 *
	 * @param	boolean		$queryAll
	 * @return	mixed		Array of elements
	 */
	private function getEvents($queryAll = false) {
		$kinds = t3lib_div::makeInstance('tx_icssitlorquery_KindList');
		$kinds->Add(tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::EVENT));
		$this->queryService->addFilter(t3lib_div::makeInstance('tx_icssitlorquery_KindFilter', $kinds));
		$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter(tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT));
		if ($this->conf['filter.']['noFeeEvent']) {
			list($crit, $term) = t3lib_div::trimExplode(':', $this->conf['filter.']['noFeeEvent']);
			$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter($crit, array($term)));
		}

		if ($this->conf['filter.']['illustration']) {
			$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter(tx_icssitlorquery_CriterionUtils::PHOTO));
		}
		switch ($this->conf['sort.']['name']) {
			case 'DATE':
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_EventSortingProvider', 'endDate', strtoupper($this->conf['sort.']['extra']));
				break;
			default:
				$sorting = null;
		}

		if ($queryAll) {
			$result = $this->queryService->getEvents($sorting);
			if ($result) {
				$this->queryService->setPager(0, $this->queryService->getLastTotalCount());
			}
			else {
				return $result;
			}
		}
		return $this->queryService->getEvents($sorting);
	}

	/**
	 * Retrieves FreeTime
	 *
	 * @param	boolean		$queryAll
	 * @return	mixed		Array of elements
	 */
	private function getFreeTime($queryAll = false) {
		if ($this->conf['filter.']['freeTimeTheme']) {
			$criterionTerms = $this->parseCriterionsTermsDefinition(t3lib_div::trimExplode(',', $this->conf['filter.']['freeTimeTheme'], true));
			foreach ($criterionTerms as $criterionID=>$termIDs) {
				$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter($criterionID, $termIDs));
			}
		} else {
			$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter(tx_icssitlorquery_CriterionUtils::FREETIME));
		}
		switch ($this->conf['sort.']['name']) {
			case 'ALPHA':
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_GenericSortingProvider', 'alpha', strtoupper($this->conf['sort.']['extra']));
				break;
			default:
				$sorting = null;
		}

		if ($queryAll) {
			$result = $this->queryService->getRecords($sorting);
			if ($result) {
				$this->queryService->setPager(0, $this->queryService->getLastTotalCount());
			}
			else {
				return $result;
			}
		}
		return $this->queryService->getRecords($sorting);
	}
	
	/**
	 * Retrieves Subscriber "Adhérent OT Nancy"
	 *
	 * @param	boolean		$queryAll
	 * @return	mixed		Array of elements
	 */
	private function getSubscriber($queryAll = false) {
		$criterionFilter = null;
		if ($this->conf['filter.']['subscriber_type.']['category']=='ARTS_CRAFTS') {
 			$filter = t3lib_div::makeInstance(
				'tx_icssitlorquery_CategoryFilter', 
				tx_icssitlorquery_NomenclatureFactory::GetCategories(array(tx_icssitlorquery_NomenclatureUtils::ARTS_CRAFTS))
			);
			$this->queryService->addFilter($filter);
			$criterionFilter = $this->conf['filter.']['subscriber_type.']['value'];
		}
		elseif ($this->conf['filter.']['subscriber_type.']['category']=='COMMERCE') {
			$filter = t3lib_div::makeInstance(
				'tx_icssitlorquery_CategoryFilter', 
				tx_icssitlorquery_NomenclatureFactory::GetCategories(array(tx_icssitlorquery_NomenclatureUtils::COMMERCE))
			);
			$this->queryService->addFilter($filter);
			$criterionFilter = $this->conf['filter.']['subscriber_type.']['value'];
		}
		elseif ($this->conf['filter.']['subscriber_type.']['category']=='NOMENCLATURE_CATEGORY') {
			$filter = t3lib_div::makeInstance(
				'tx_icssitlorquery_CategoryFilter', 
				tx_icssitlorquery_NomenclatureFactory::GetCategories(array($this->conf['filter.']['subscriber_type.']['value']))
			);
			$this->queryService->addFilter($filter);
		}
		if ($criterionFilter) {
			$criterionTerms = $this->parseCriterionsTermsDefinition(t3lib_div::trimExplode(',', $criterionFilter, true));
			foreach ($criterionTerms as $criterionID=>$termIDs) {
				$this->queryService->addFilter(tx_icssitlorquery_CriterionUtils::getCriterionFilter($criterionID, $termIDs));
			}
		}
		switch ($this->conf['sort.']['name']) {
			case 'ALPHA':
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_GenericSortingProvider', 'alpha', strtoupper($this->conf['sort.']['extra']));
				break;
			default:
				$sorting = null;
		}

		if ($queryAll) {
			$result = $this->queryService->getRecords($sorting);
			if ($result) {
				$this->queryService->setPager(0, $this->queryService->getLastTotalCount());
			}
			else {
				return $result;
			}
		}
		return $this->queryService->getRecords($sorting);
	}

	/**
	 * Parses an array of criterion and term definition.
	 * Each element is of the form criterion:term. The term can be omitted.
	 *
	 * @param	array		$criterionTermArray: Array of criterion and term definition.
	 * @return	array		A map of terms attached to their criterion.
	 */
	private function parseCriterionsTermsDefinition(array $criterionTermArray){
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

	/**
	 * Retrieves content from cache.
	 * Search by keyword cannot be cached.
	 *
	 * @param	string		$mode: Current view mode. 'SINGLE' or 'LIST' mode.
	 * @return	mixed		The content if set in cache. False if not found.
	 */
	private function renderCachedContent($mode) {
		t3lib_cache::initializeCachingFramework();
        try {
            $this->cacheInstance = $GLOBALS['typo3CacheManager']->getCache('icssitlorquery_cache');
        } catch (t3lib_cache_exception_NoSuchCache $e) {
            $this->cacheInstance = $GLOBALS['typo3CacheFactory']->create(
                'icssitlorquery_cache',
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['frontend'],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['backend'],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['options']
            );
        }

		$params['connection'] = $this->conf['url'] . $this->conf['login'] . $this->conf['password'];
		$params['view'] = implode('', $this->conf['view.']);
		$params['page'] = $this->conf['page'];
		$params['code'] = $mode;
		$params['templateFile'] = $this->templateFile;
		$params['filter'] = implode('', $this->conf['filter.']);
		$params['sorting'] = implode('', $this->conf['sort.']);

		$this->hash = md5(implode(';', $params));

		if (!$this->sword && $this->cacheInstance->has($this->hash)) {

			$content = $this->cacheInstance->get($this->hash);
			return $content;
		}

		return false;
	}

	/**
	 * Stores content to cache.
	 * Uses the hash computed when querying the cache.
	 *
	 * @param	string		$content: The content to store in cache.
	 * @return	void
	 */
	private function storeCachedContent($content) {
		if ($this->sword)
			return;

		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
		$lifetime = intval($extConf['cacheTime']);

		$this->cacheInstance->set($this->hash, $content, array(), $lifetime);
	}

	/**
	 * Render data
	 *
	 * @param	string		$name : Name of element to render
	 * @param	mixed		$element : Element to render
	 * @return	string
	 */
	public function renderData($name, $element) {
		$content = '';
		$lConf = $this->conf['renderConf.'][$name . '.'];
		if (empty($lConf)) {
			$content .= $element;
		} else {
			if ($element instanceof tx_icssitlorquery_TimeTable || $element instanceof tx_icssitlorquery_ValuedTerm) {
				$content = $element->toStringConf($lConf);
			} elseif(is_array($element)) {
				$content = $this->cObj->stdWrap(implode($lConf['separator'], $element), $lConf);
			} else {
				$content = $this->cObj->stdWrap($element, $lConf);
			}
		}
		return $content;
	}

	/**
	 * Render single link
	 *
	 * @param	string		$name : Name of element to render
	 * @param	tx_icssitlorquery_AbstractData		$element : Element to render
	 * @return	string
	 */
	public function renderSingleLink($name, $element) {
		$data = array(
			'id' => $element->ID,
			'title' => $element->Name,
		);
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($data, 'Sitlor');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);

		return $cObj->stdWrap('', $this->conf['renderConf.'][$name . '.']);
	}

	/**
	 * Render sortings
	 *
	 * @return	string
	 */
	public function renderSortings() {
		$dataGroup = (string)strtoupper(trim($this->conf['view.']['dataGroup']));
		$sortNames = array();
		switch ($dataGroup) {
			case 'ACCOMODATION':
				$subDataGroups = (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
				$subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);
				if (in_array('HOTEL', $subDataGroups)) {
					$sortNames = array('ALPHA', 'HOTELRATING', 'PRICE');
				} elseif (in_array('HOLLIDAY_COTTAGE', $subDataGroups) && in_array('GUESTHOUSE', $subDataGroups)) {
					$sortNames = array('ALPHA');
				}
				break;
			case 'RESTAURANT':
				$sortNames = array('PRICE');
				break;
			case 'EVENT':
				break;
			default:
		}
		$data = array(
			'sortNames' => implode(',', $sortNames),
			'active' => $this->conf['sort.']['name'],
		);
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($data, 'Sorting');
		$cObj->setParent($this->cObj->data, $this->cObj->currentRecord);

		return $cObj->stdWrap('', $this->conf['renderConf.']['sortings.']);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php']);
}

?>