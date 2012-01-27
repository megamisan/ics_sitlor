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
 * Hint: use extdeveval to insert/update function index above.
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
	
	var $templateFile = 'typo3conf/ext/ics_sitlor_query/res/template.html';
	
	var $defaultPage = 1;
	var $defaultSize = 20;
	
    
    /**
     * The main method of the PlugIn
     *
     * @param    string        $content: The PlugIn content
     * @param    array        $conf: The PlugIn configuration
     * @return    The content that is displayed on the website
     */
    function main($content, $conf) {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_USER_INT_obj = 1;    // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_initPIflexForm();
    
		//--
        $content='
            <strong>This is a few paragraphs:</strong><br />
            <p>This is line 1</p>
            <p>This is line 2</p>
    
            <h3>This is a form:</h3>
            <form action="'.$this->pi_getPageLink($GLOBALS['TSFE']->id).'" method="POST">
                <input type="text" name="'.$this->prefixId.'[input_field]" value="'.htmlspecialchars($this->piVars['input_field']).'">
                <input type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL('submit_button_label')).'">
            </form>
            <br />
            <p>You can click here to '.$this->pi_linkToPage('get to this page again',$GLOBALS['TSFE']->id).'</p>
        ';
		//--
		
		// Initialize the plugin
		$this->init();
		
		// Check login, password, urls
		if (!$this->conf['login']) {
			tx_icssitlorquery_Debug::error('Login is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['password']) {
			tx_icssitlorquery_Debug::error('Password is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['url']) {
			tx_icssitlorquery_Debug::error('Url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['nomenclatureUrl']) {
			tx_icssitlorquery_Debug::error('Nomenclature url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}
		if (!$this->conf['criterionUrl']) {
			tx_icssitlorquery_Debug::error('Criterion url is required.');
			return $this->pi_wrapInBaseClass($this->pi_getLL('data_not_available', 'Can not reach data', true));
		}

		// Initialize query, connection
		$this->setQueryService($this->conf['login'], $this->conf['password'], $this->conf['url']);
		$this->setSortingProvider();
		tx_icssitlorquery_NomenclatureFactory::SetConnectionParameters($this->conf['login'], $this->conf['password'], $this->conf['nomenclatureUrl']);
		tx_icssitlorquery_CriterionFactory::SetConnectionParameters($this->conf['login'], $this->conf['password'], $this->conf['criterionUrl']);
		
		// Render data
		// $modes = t3lib_div::trimExplode(',', $this->conf['mode'], true);
		// foreach ($modes as $mode) {
			// $mode = (string) strtoupper(trim($mode));
			// switch ($mode) {
				// case 'LIST' :
					// $content .= $this->renderDataList();
				// break;
				
				// case 'SINGLE':
					// $content .= $this->renderDataSingle();
				// break;
				
				// case 'SEARCH':
					// $content .= $this->renderDataSearch();
				// break;
				
				// default:
			// }
		// }

		// try {
			// $types = tx_icssitlorquery_NomenclatureFactory::GetTypes(array(4000002, 4000003, 4000012));
		// } catch (Exception $e) {
			// tx_icssitquery_Debug::error('Retrieves Type for HOTEL failed : ' . $e);
		// }
		// $typeFilter = t3lib_div::makeInstance('tx_icssitlorquery_TypeFilter', $types);
		// $this->queryService->addFilter($typeFilter);

		$StartDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_StartDateFilter', mktime(0,0,0,1,1,2000));
		$this->queryService->addFilter($StartDateFilter);

		$idFilter = t3lib_div::makeInstance('tx_icssitlorquery_idFilter', 737000521);
		// $idFilter = t3lib_div::makeInstance('tx_icssitlorquery_idFilter', 737000259);
		// $idFilter = t3lib_div::makeInstance('tx_icssitlorquery_idFilter', 737000115);
		$this->queryService->addFilter($idFilter);

		try {
			$accomodations = $this->queryService->getAccomodations($this->sortingProvider);
		} catch (Exception $e) {
			tx_icssitquery_Debug::error('Retrieves Accomodation proccess failed : ' . $e);
		}
		if (empty($accomodations))
			$content = $this->pi_getLL('no_data', 'There is any Accomodations', true);
		else 
			$content = 'There are ' . count($accomodations) . ' accomodation(s).';
		
		return $this->pi_wrapInBaseClass($content);
    }
	
	/**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	function init () {
	
		// Get template code
		$templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'templateFile', 'main');
		$this->conf['templateFile'] = $templateFile? $templateFile: $this->conf['templateFile'];
		$this->conf['templateFile'] = $this->conf['templateFile']? $this->conf['templateFile']: $this->templateFile;
		$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
		
		// Get mode
		if (isset($this->piVars['mode'])) {
			$mode = $this->piVars['mode'];
		}
		if (isset($this->piVars['showUid'])) {
			$this->conf['showUid'] = $this->piVars['showUid'];
			$mode = 'SINGLE';
		}
		if (isset($this->piVars['dataCategory'])) {
			$dataCategory = $this->piVars['dataCategory'];
			if ($dataCategory == 'ACCOMODATAION')
				$accomodationCategory = $this->piVars['accomodationCategory'];
		}
		if (!$mode || !$dataCategory || ($dataCategory == 'ACCOMODATAION' && !$accomodationCategory)) {
			$this->initMode();
		}
		
		// Get page and page size
		$page = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'page', 'main');
		$this->conf['page'] = $page? $page: $this->conf['page'];
		$this->conf['page'] = $this->conf['page']? $this->conf['page']: $this->defaultPage;
		
		$size = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'size', 'main');
		$this->conf['size'] = $size? $size: $this->conf['size'];
		$this->conf['size'] = $this->conf['size']? $this->conf['size']: $this->defaultSize;
		
		// Get login
		$login = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'login', 'main');
		$this->conf['login'] = $login? $login: $this->conf['login'];
		
		$password = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'password', 'main');
		$this->conf['password'] = $password? $password: $this->conf['password'];
		
		$url = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'url', 'main');
		$this->conf['url'] = $url? $url: $this->conf['url'];
							
	}
	
	/**
	 * Set the queryService
	 *
	 * @param	string $login : The login
	 * @param	string $password : The password
	 * @param	string $url : The url
	 *
	 * @return void
	 */
	function setQueryService($login, $password, $url) {
		$this->queryService = t3lib_div::makeInstance('tx_icssitlorquery_SitlorQueryService', $login, $password, $url);
		$this->queryService->setPager(intval($this->conf['page']), intval($this->conf['size']));
	}
	
	/**
	 * Set the sortingProvider
	 *
	 * @return void
	 */
	function setSortingProvider() {
		$this->sortingProvider = t3lib_div::makeInstance('tx_icssitlorquery_AccomodationSortingProvider');
	}
	
	/**
	 * Initialize the mode
	 *
	 * @return void
	 */
	function initMode() {
		$display = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'what_to_display', 'main');
		switch($display) {
			case 'HOTEL_LIST':
				$mode = $mode? $mode: 'LIST';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'HOTEL';
			break;
			
			case 'HOTEL_SINGLE':
				$mode = $mode? $mode: 'SINGLE';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'HOTEL';
			break;
			
			case 'HOTEL_SEARCH':
				$mode = $mode? $mode: 'SEARCH';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'HOTEL';
			break;
			
			case 'CAMPING_YOUTH_HOSTEL_LIST':
				$mode = $mode? $mode: 'LIST';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'CAMPING, YOUTH_HOSTEL';
			break;
			
			case 'CAMPING_YOUTH_HOSTEL_SINGLE':
				$mode = $mode? $mode: 'SINGLE';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'CAMPING, YOUTH_HOSTEL';
			break;
			
			case 'CAMPING_YOUTH_HOSTEL_SEARCH':
				$mode = $mode? $mode: 'SEARCH';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'CAMPING, YOUTH_HOSTEL';
			break;
			
			case 'STRANGE_ACCOMODATION_LIST':
				$mode = $mode? $mode: 'LIST';
				$dataCategory= $dataCategory? $dataCategory: 'ACCOMODATION';
				$accomodationCategory = 'STRANGE';
			break;
			
			case 'STRANGE_ACCOMODATION_SINGLE':
				$mode = $mode? $mode: 'SINGLE';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'STRANGE';
			break;
			
			case 'STRANGE_ACCOMODATION_SEARCH':
				$mode = $mode? $mode: 'SEARCH';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'STRANGE';
			break;
			
			case 'COTTAGE_GUESTHOUSE_LIST':
				$mode = $mode? $mode: 'LIST';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'HOLLIDAY_COTTAGE, GUESTHOUSE';
			break;
			
			case 'COTTAGE_GUESTHOUSE_SINGLE':
				$mode = $mode? $mode: 'SINGLE';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'HOLLIDAY_COTTAGE, GUESTHOUSE';
			break;
			
			case 'COTTAGE_GUESTHOUSE_SEARCH':
				$mode = $mode? $mode: 'SEARCH';
				$dataCategory= $dataCategory? $dataCategory:  'ACCOMODATION';
				$accomodationCategory = 'HOLLIDAY_COTTAGE, GUESTHOUSE';
			break;
			
			case 'RESTAURANT_LIST':
				$mode = $mode? $mode: 'LIST';
				$dataCategory= $dataCategory? $dataCategory:  'RESTAURANT';
			break;
			
			case 'RESTAURANT_SINGLE':
				$mode = $mode? $mode: 'SINGLE';
				$dataCategory= $dataCategory? $dataCategory:  'RESTAURANT';
			break;
			
			case 'RESTAURANT_SEARCH':
				$mode = $mode? $mode: 'SEARCH';
				$dataCategory= $dataCategory? $dataCategory:  'RESTAURANT';
			break;
			
			case 'EVENT_LIST':
				$mode = $mode? $mode: 'LIST';
				$dataCategory= $dataCategory? $dataCategory:  'EVENT';
			break;
			
			case 'EVENT_SINGLE':
				$mode = $mode? $mode: 'SINGLE';
				$dataCategory= $dataCategory? $dataCategory:  'EVENT';
			break;
			
			case 'EVENT_SEARCH':
				$mode = $mode? $mode: 'SEARCH';
				$dataCategory= $dataCategory? $dataCategory:  'EVENT';
			break;
			
			default:
		}
		$this->conf['mode'] = $mode? $mode: $this->conf['mode'];
		$this->conf['dataCategory'] = $dataCategory? $dataCategory: $this->conf['dataCategory'];
		$this->conf['accomodationCategory'] = $accomodationCategory? $accomodationCategory: $this->conf['accomodationCategory'];
	}
	
	/**
	 * Render data list
	 *
	 * @return string : The data content
	 */
	function renderDataList() {
		$categories = t3lib_div::trimExplode(',', $this->conf['dataCategory'], true);
		foreach ($categories as $category) {
			$category = (string) strtoupper(trim($category));
			switch ($category) {
				case 'ACCOMODATION' :
					$content .= $this->renderAccomodationList();
				break;
				
				case 'RESTAURANT' :
					$content .= $this->renderRestaurantList();
				break;
				
				case 'EVENT' :
					$content .= $this->renderEventList();
				break;
				
				default;
			}
		}
		return $content;
	}
	
	/**
	 * Render data single
	 *
	 * @return string : The data content
	 */
	function renderDataSingle() {
		$categories = t3lib_div::trimExplode(',', $this->conf['dataCategory'], true);
		foreach ($categories as $category) {
			$category = (string) strtoupper(trim($category));
			switch ($category) {
				case 'ACCOMODATION' :
					$content .= $this->renderAccomodationSingle();
				break;
				
				case 'RESTAURANT' :
					$content .= $this->renderRestaurantSingle();
				break;
				
				case 'EVENT' :
					$content .= $this->renderEventSingle();
				break;
				
				default;
			}
		}
		return $content;
	}
	
	/**
	 * Render data search
	 *
	 * @return string : The search form
	 */
	function renderDataSearch() {
		$categories = t3lib_div::trimExplode(',', $this->conf['dataCategory'], true);
		foreach ($categories as $category) {
			$category = (string) strtoupper(trim($category));
			switch ($category) {
				case 'ACCOMODATION' :
					$content .= $this->renderAccomodationList();
				break;
				
				case 'RESTAURANT' :
					$content .= $this->renderRestaurantSearch();
				break;
				
				case 'EVENT' :
					$content .= $this->renderEventSearch();
				break;
				
				default;
			}
		}
		return $content;
	}
	
	/**
	 * Render accomodation list
	 *
	 * @return string : The list of accomodations
	 */
	function renderAccomodationList() {
		$renderer = t3lib_div::makeInstance('tx_icssitlorquery_accomodationListRenderer');
		return $renderer->main($this);
	}
	
	/**
	 * Render accomodation detail
	 *
	 * @return string : The detail of an accomodation
	 */
	function renderAccomodationSingle() {
		$renderer = t3lib_div::makeInstance('tx_icssitlorquery_accomodationSingleRenderer');
		return $renderer->main($this);		
	}
	
	/**
	 * Render accomodation search
	 *
	 * @return string : The search form content
	 */
	function renderAccomodationSearch() {
		$renderer = t3lib_div::makeInstance('tx_icssitlorquery_accomodationSearchRenderer');
		return $renderer->main($this);		
	}
	
	/**
	 * Render restaurant list
	 *
	 * @return string : The list of restaurants
	 */
	function renderRestaurantList() {
		$renderer = t3lib_div::makeInstance('tx_icssitlorquery_restaurantListRenderer');
		return $renderer->main($this);		
	}
	
	/**
	 * Render restaurant detail
	 *
	 * @return string : The detail of a restaurant
	 */
	function renderRestaurantSingle() {
		$renderer = t3lib_div::makeInstance('tx_icssitlorquery_restaurantSingleRenderer');
		return $renderer->main($this);		
	}
	
	/**
	 * Render restaurant search
	 *
	 * @return string : The search form content
	 */
	function renderRestaurantSearch() {
		$renderer = t3lib_div::makeInstance('tx_icssitlorquery_restaurantSearchRenderer');
		return $renderer->main($this);		
	}
	
	/**
	 * Render vent list
	 *
	 * @return string : The list of events
	 */
	function renderEventList() {
		$renderer = t3lib_div::makeInstance('tx_icssitlorquery_eventListRenderer');
		return $renderer->main($this);		
	}
	
	/**
	 * Render event detail
	 *
	 * @return string : The detail of a event
	 */
	function renderEventSingle() {
		$renderer = t3lib_div::makeInstance('tx_icssitlorquery_eventSingleRenderer');
		return $renderer->main($this);		
	}
	
	/**
	 * Render event search
	 *
	 * @return string : The search form content
	 */
	function renderEventSearch() {
		$renderer = t3lib_div::makeInstance('tx_icssitlorquery_eventSearchRenderer');
		return $renderer->main($this);		
	}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/pi1/class.tx_icssitlorquery_pi1.php']);
}

?>