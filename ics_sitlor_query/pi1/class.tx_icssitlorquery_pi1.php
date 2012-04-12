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
 *  896:     private function process_criterionTermArray(array $criterionTermArray)
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
		$this->setDefaultConf();
		$this->setDefaultSeparator();
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
		elseif (count(array_intersect(array('SEARCH', 'LIST'), $this->codes)) > 0) {
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
	function init () {
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
	 * Sets search params from piVars
	 *
	 * @return	void
	 */
	function setPIVars_searchParams() {
		$params = $this->piVars['search'];

		$this->sword = '';
		if ($this->piVars['btn_sword'])
			$this->sword = $params['sword'];
		
		$this->conf['filter.']['hotelTypes'] = implode(',', $params['hotelType']);
		$this->conf['filter.']['hotelEquipments'] = implode(',', $params['hotelEquipment']);
		$this->conf['filter.']['restaurantCategories'] = implode(',', $params['restaurantCategory']);
		$this->conf['filter.']['foreignFoods'] = implode(',', $params['culinarySpeciality']);

		if ($params['startDate'])
			$this->conf['filter.']['startDate'] = $params['startDate'];
		if ($params['endDate'])
			$this->conf['filter.']['endDate'] = $params['endDate'];

		$this->conf['filter.']['noFeeEvent'] = $params['noFee'];

		if (is_array($params['subDataGroups']) && !empty($params['subDataGroups'])) {
			$this->conf['filter.']['subDataGroups'] = implode(',', $params['subDataGroups']);
		}

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
		tx_icssitlorquery_NomenclatureFactory::SetConnectionParameters($this->conf['login'], $this->conf['password'], $this->conf['sitlor.']['nomenclatureUrl']);
		tx_icssitlorquery_CriterionFactory::SetConnectionParameters($this->conf['login'], $this->conf['password'], $this->conf['sitlor.']['criterionUrl']);
		$this->queryService->setTypeGuessingConf(
			array(
				'tx_icssitlorquery_FullAccomodation' => array('id', tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::ACCOMODATION)),
				'tx_icssitlorquery_Accomodation' => array(tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::ACCOMODATION)),
				'tx_icssitlorquery_FullRestaurant' => array('id', tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESTAURANT)),
				'tx_icssitlorquery_Restaurant' => array(tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESTAURANT)),
				'tx_icssitlorquery_FullEvent' => array('id', tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::EVENT), $this->getCriterionFilter(tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT)),
				'tx_icssitlorquery_Event' => array(tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::EVENT), $this->getCriterionFilter(tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT)),
			)
		);
	}

	/**
	 * Sets default TypoScript configuration
	 *
	 * @return	void
	 */
	function setDefaultConf() {
		$common = array('Address', 'Coordinates', 'Link', 'Name', 'Phone', 'Picture');
		foreach ($this->conf['defaultConf.'] as $type => $conf) {
			if ($type{strlen($type) - 1} != '.')
				continue;
			$type = substr($type, 0, -1);
			if (in_array($type, $common)) {
				$class = 'tx_icssitquery_' . $type;
			}
			else {
				$class = 'tx_icssitlorquery_' . $type;
			}
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
			tx_icssitlorquery_AbstractList::setDefaultSeparator($conf, $class);
		}
	}

	/**
	 * Render data
	 *
	 * @param	string		$name : Name of element to render
	 * @param	mixed		$element : Element to render
	 * @return	string
	 */
	function renderData($name, $element) {
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
	function renderSingleLink($name, $element) {
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
	function renderSortings() {
		$dataGroup = (string)strtoupper(trim($this->conf['view.']['dataGroup']));
		$sortNames = array();
		switch($dataGroup) {
			case 'ACCOMODATION':
				$subDataGroups = (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
				$subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);
				if (in_array('HOTEL', $subDataGroups)) {
					$sortNames = array('ALPHA', 'HOTELRATING', 'PRICE');
				} elseif (in_array('HOLLIDAY_COTTAGE', $subDataGroups) && in_array('GUESTHOUSE', $subDataGroups)) {
					// $sortNames = array('ALPHA', 'RANDOM');
					$sortNames = array('ALPHA');
				}
				break;
			case 'RESTAURANT':
				// $sortNames = array('RANDOM', 'PRICE');
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

	/**
	 * Display the list view of elements
	 *
	 * @return	string		HTML content for list view
	 */
	function displayList() {
		if (in_array('SEARCH', $this->codes)) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SEARCH###');
			$renderForm = t3lib_div::makeInstance('tx_icssitlorquery_FormRenderer', $this, $this->cObj, $this->conf);
			$locMarkers['SEARCH_FORM'] = $renderForm->render();
		}
		else {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESULTS_NOSEARCH###');
		}
		if (in_array('LIST', $this->codes)) {
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
			if (!$extConf['no_cache'] && ($content = $this->renderCachedContent('SINGLE'))) {
				$locMarkers['RESULT_LIST'] = $content;
			} else {
				$renderList = t3lib_div::makeInstance('tx_icssitlorquery_ListRenderer', $this, $this->cObj, $this->conf);
				$content = $renderList->render($this->getElements());
				$locMarkers['RESULT_LIST'] = $content;
				if (!$extConf['no_cache'])
					$this->storeCachedContent($content);
			}
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
	 * Display the single view of an element
	 *
	 * @return	string		HTML content for single view
	 */
	function displaySingle() {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
		if (!$extConf['no_cache'] && ($content = $this->renderCachedContent('SINGLE')))
			return $content;

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
	 * Retrieves data
	 *
	 * @return	mixed		Array of elements
	 */
	private function getElements() {
		// Set filter on OT Nancy
		if ($this->conf['sitlor.']['OTNancy'] && $this->conf['filter.']['OTNancy']) {
			list($code, $value) = t3lib_div::trimExplode(':', $this->conf['sitlor.']['OTNancy']);
			$this->queryService->addFilter($this->getCriterionFilter(intval($code), ($value? array($value): null)));
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
				return $this->getAccomodations();
			case 'RESTAURANT':
				return $this->getRestaurants();
			case 'EVENT':
				return $this->getEvents();
			case 'FREETIME':
				return $this->getFreeTime();
			default:
				tx_icssitquery_Debug::warning('List view datagroup ' . $dataGroup . ' is not defined.');
		}
		return null;
	}

	/**
	 * Retrieves Accomodations
	 *
	 * @return	mixed		Array of elements
	 */
	private function getAccomodations() {
		$subDataGroups = (string)strtoupper(trim($this->conf['filter.']['subDataGroups']));
		$subDataGroups = $subDataGroups? $subDataGroups: (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
		$subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);

		// if (!$this->conf['view.']['subDataGroups']) {
		if (empty($subDataGroups)) {
			$kinds = t3lib_div::makeInstance('tx_icssitlorquery_KindList');
			$kinds->Add(tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::ACCOMODATION));
			$this->queryService->addFilter(
				t3lib_div::makeInstance(
					'tx_icssitlorquery_KindFilter',
					$kinds
				)
			);
		} else {
			// $subDataGroups = (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
			// $subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);
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
								$this->queryService->addFilter($this->getCriterionFilter($criterionID, $termIDs));
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
						$this->queryService->addFilter($this->getCriterionFilter(tx_icssitlorquery_CriterionUtils::STRANGE_ACCOMODATION, array(tx_icssitlorquery_CriterionUtils::STRANGE_ACCOMODATION_YES)));
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

		return $this->queryService->getAccomodations($sorting);
	}

	/**
	 * Retrieves Restaurants
	 *
	 * @return	mixed		Array of elements
	 */
	private function getRestaurants() {
		$category = tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESTAURANT);
		$categoryList = t3lib_div::makeInstance('tx_icssitlorquery_CategoryList');
		$categoryList->Add($category);
		$filter = t3lib_div::makeInstance('tx_icssitlorquery_CategoryFilter', $categoryList);
		$this->queryService->addFilter($filter);
		if ($this->conf['filter.']['restaurantCategories']) {
			$criterionTerms = $this->process_criterionTermArray(t3lib_div::trimExplode(',', $this->conf['filter.']['restaurantCategories'], true));
			foreach ($criterionTerms as $criterionID=>$termIDs) {
				$this->queryService->addFilter($this->getCriterionFilter($criterionID, $termIDs));
			}
		}
		if ($this->conf['filter.']['foreignFoods']) {
			$criterionTerms = $this->process_criterionTermArray(t3lib_div::trimExplode(',', $this->conf['filter.']['foreignFoods'], true));
			foreach ($criterionTerms as $criterionID=>$termIDs) {
				$this->queryService->addFilter($this->getCriterionFilter($criterionID, $termIDs));
			}
		}
		switch ($this->conf['sort.']['name']) {
			case 'RANDOM':
				if ($this->conf['sort.']['extra'])
					$sorting = t3lib_div::makeInstance('tx_icssitlorquery_RestaurantSortingProvider', 'random', $this->conf['sort.']['extra']);
				else
					$sorting = t3lib_div::makeInstance('tx_icssitlorquery_RestaurantSortingProvider', 'random', 'start');
				break;
			// case 'RATING':
				// $sorting = t3lib_div::makeInstance('tx_icssitlorquery_RestaurantSortingProvider', 'rating', strtoupper($this->conf['sort.']['extra']));
				// break;
			case 'PRICE':
				$sorting = t3lib_div::makeInstance('tx_icssitlorquery_RestaurantSortingProvider', 'price', strtoupper($this->conf['sort.']['extra']));
				break;
			default:
				$sorting = null;
		}
		return $this->queryService->getRestaurants($sorting);
	}

	/**
	 * Retrieves Events
	 *
	 * @return	mixed		Array of elements
	 */
	private function getEvents() {
		$kinds = t3lib_div::makeInstance('tx_icssitlorquery_KindList');
		$kinds->Add(tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::EVENT));
		$this->queryService->addFilter(
			t3lib_div::makeInstance(
				'tx_icssitlorquery_KindFilter',
				$kinds
			)
		);
		$this->queryService->addFilter($this->getCriterionFilter(tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT));
		if ($this->conf['filter.']['noFeeEvent']) {
			list($crit, $term) = t3lib_div::trimExplode(':', $this->conf['filter.']['noFeeEvent']);
			$this->queryService->addFilter($this->getCriterionFilter($crit, array($term)));
		}

		if ($this->conf['filter.']['illustration']) {
			$this->queryService->addFilter($this->getCriterionFilter(tx_icssitlorquery_CriterionUtils::PHOTO));
		}

		$sorting = null;
		if ($this->conf['sort.']['name']=='DATE') {
			$sorting = t3lib_div::makeInstance('tx_icssitlorquery_EventSortingProvider', 'endDate', strtoupper($this->conf['sort.']['extra']));
		}
		return $this->queryService->getEvents($sorting);
	}

	/**
	 * Retrieves FreeTime
	 *
	 * @return	mixed		Array of elements
	 */
	private function getFreeTime() {
		if ($this->conf['filter.']['freeTimeTheme']) {
			$criterionTerms = $this->process_criterionTermArray(t3lib_div::trimExplode(',', $this->conf['filter.']['freeTimeTheme'], true));
			foreach ($criterionTerms as $criterionID=>$termIDs) {
				$this->queryService->addFilter($this->getCriterionFilter($criterionID, $termIDs));
			}
		} else {
			$this->queryService->addFilter($this->getCriterionFilter(tx_icssitlorquery_CriterionUtils::FREETIME));
		}
		$sorting = null;
		if ($this->conf['sort.']['name']=='ALPHA') {
			$sorting = t3lib_div::makeInstance('tx_icssitlorquery_GenericSortingProvider', 'alpha', strtoupper($this->conf['sort.']['extra']));
		}
		return $this->queryService->getRecords($sorting);
	}


	/**
	 * Add criterion filter
	 *
	 * @param	int		$criterionID: Criterion ID
	 * @param	array		int	$terms: Array of Criterion's terms IDs
	 * @return	void
	 */
	private function getCriterionFilter($criterionID, $terms=null) {
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
		return t3lib_div::makeInstance('tx_icssitlorquery_CriterionFilter', $criterion, $list);
	}

	/**
	 * Process criterionTermArray
	 *
	 * @param	array		$criterionTermArray: Array of criterion and term
	 * @return	mixed		An array of criterionTerms
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

	/**
	 * Render cached content
	 *
	 * @param	string		$mode: 'SINGLE' or 'LIST' mode
	 * @return	mixed		The HTML content or false whether any cached
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
		$params['view'] = $this->conf['view.']['size'] . $this->conf['view.']['dataGroup'] . $this->conf['view.']['subDataGroups'];
		$params['page'] = $this->conf['page'];
		$params['code'] = $mode;
		$params['templateFile'] = $this->templateFile;

		$params['filter'] = $this->conf['filter.']['OTNancy'] . $this->conf['filter.']['entity_737'] . $this->conf['filter.']['startDate'] . $this->conf['filter.']['endDate'] . $this->conf['filter.']['hotelTypes'] . $this->conf['filter.']['hotelEquipments'] . $this->conf['filter.']['restaurantCategories'] . $this->conf['filter.']['foreignFoods'] . $this->conf['filter.']['noFeeEvent'] . $this->conf['filter.']['subDataGroups'] . $this->conf['filter.']['illustration'];

		$params['sorting'] = $this->conf['sort.']['name'] . $this->conf['sort.']['extra'];

		$this->hash = md5(implode(';', $params));

		if (!$this->sword && $this->cacheInstance->has($this->hash)) {

			$content = $this->cacheInstance->get($this->hash);
			return $content;
		}

		return false;
	}

	/**
	 * Store cached content
	 *
	 * @param	string		$content: The content to store in cache
	 * @return	void
	 */
	function storeCachedContent($content) {
		if ($this->sword)
			return;

		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
		$lifetime = intval($extConf['cacheTime']);

		$this->cacheInstance->set($this->hash, $content, array(), $lifetime);
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php']);
}

?>