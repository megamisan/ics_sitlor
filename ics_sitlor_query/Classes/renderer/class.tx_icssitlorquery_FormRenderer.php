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
 * Class 'tx_icssitlorquery_FormRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
 class tx_icssitlorquery_FormRenderer {
	private static $hotelTypes = array(
		array('TYPE', tx_icssitlorquery_NomenclatureUtils::HOTEL_RESTAURANT),
		array('TYPE', tx_icssitlorquery_NomenclatureUtils::FURNISHED),
	);
	private static $restaurantCategories = array(
		array('TERM', array(tx_icssitlorquery_CriterionUtils::RCATEGORIE, tx_icssitlorquery_CriterionUtils::RCATEGORIE_FASTFOOD)),
		array('TERM', array(tx_icssitlorquery_CriterionUtils::RCATEGORIE, tx_icssitlorquery_CriterionUtils::RCATEGORIE_ICECREAM_THEAHOUSE)),
		array('TERM', array(tx_icssitlorquery_CriterionUtils::RCATEGORIE, tx_icssitlorquery_CriterionUtils::RCATEGORIE_CREPERIE)),
	);
	private static $foreignFood = array(
		array('TERM', array(tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD, tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD_ASIAN)),
		array('TERM', array(tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD, tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD_SA)),
		array('TERM', array(tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD, tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD_ORIENTAL)),
	);
	private static $hotelEquipment = array(
		array('TERM', array(tx_icssitlorquery_CriterionUtils::COMFORT_ROOM, tx_icssitlorquery_CriterionUtils::WIFI)),
		array('CRITERION', tx_icssitlorquery_CriterionUtils::ALLOWED_PETS),
		array('CRITERION',  tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK),
	);
	private static $dayOfWeek = array(1,2,3,4,5,6,7);	// int : ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)
	
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
		if (isset($pi->piVars['search']))
			$this->search = $pi->piVars['search'];
	}

	/**
	 * Render the view
	 *
	 * @return	string		HTML search form content
	 */
	function render() {
		$markers = array();
		$locMarkers['GENERIC'] = $this->renderGeneric($markers);
		$locMarkers['SPECIFIC'] = $this->renderSpecific($markers);

		$dataGroup = (string)strtoupper(trim($this->conf['view.']['dataGroup']));
		$subDataGroups = (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
		$subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);
		$locMarkers['MORE'] = '';
		if (($dataGroup == 'ACCOMODATION' && in_array('HOTEL', $subDataGroups)) ||
			// ($dataGroup == 'RESTAURANT') ||
			($dataGroup == 'EVENT')) {
			$locMarkers['MORE'] = $this->renderMore($markers);
		}
		
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SEARCH_FORM###');
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
		$template = $this->cObj->substituteMarkerArray($template, $markers, '###|###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ACTION_URL' =>  t3lib_div::getIndpEnv('TYPO3_REQUEST_URL') ,
		);
			// Get dataGroup main search title
		switch($dataGroup ) {
			case 'ACCOMODATION':
				$markers['MAIN_SEARCH_TITLE'] = $this->pi->pi_getLL('search_accomodation', 'Search accomodation', true);
				break;
			case 'RESTAURANT':
				$markers['MAIN_SEARCH_TITLE'] = $this->pi->pi_getLL('search_restaurant', 'Search restaurant', true);
				break;
			case 'EVENT':
				$markers['MAIN_SEARCH_TITLE'] = $this->pi->pi_getLL('search_event', 'Search event', true);
				break;
			default:
				$markers['MAIN_SEARCH_TITLE'] = '';
		}
		
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}
	
	/**
	 * Render search form generic
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	private function renderGeneric(&$markers) {
		$markers['KEYWORD_LABEL'] = $this->pi->pi_getLL('keyword', 'Keyword', true);
		$markers['KEYWORD_VALUE'] = $this->search['sword']? $this->search['sword']: $this->pi->pi_getLL('keyword', 'Search', true);
		$markers['SEARCH'] = $this->pi->pi_getLL('search', 'Search', true);
		return $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_GENERIC###');
	}
	
	/**
	 * Render search form  specific
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	private function renderSpecific(&$markers) {
		$dataGroup = (string)strtoupper(trim($this->conf['view.']['dataGroup']));
		$template = '';
		switch($dataGroup) {
			case 'ACCOMODATION':
				$template = $this->renderSpecific_Accomodation($markers);
				break;
			case 'RESTAURANT':
				$template = $this->renderSpecific_Restaurant($markers);
				break;
			case 'EVENT':
				$template = $this->renderSpecific_Event($markers);
				break;
			default:
				$template = '';
		}
		return $template;
	}

	/**
	 * Render accomodation search form specific
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	function renderSpecific_Accomodation(&$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_ACCOMODATION###');
		$subDataGroups = (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
		$subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);

		// Hotel
		$subparts['###SUBPART_HOTEL###'] = '';
		if (in_array('HOTEL', $subDataGroups)) {
			$markers['HOTEL_TYPE_LABEL'] = $this->pi->pi_getLL('hotelType', 'Hotel type', true);
			$itemSubparts = array();
			$hotelTemplate = $this->cObj->getSubpart($template, '###SUBPART_HOTEL###');
			$itemTemplate = $this->cObj->getSubpart($hotelTemplate, '###ITEM###');
			foreach(self::$hotelTypes as $type) {
				$itemContent = '';
				if ($data = $this->getSelectData($type[0], $type[1])) {
					$itemMarkers = array();
					$itemMarkers['SELECTED_TYPE_ITEM'] = ($this->search['hotelType']==$data->ID)? 'selected="selected"': '';
					$itemMarkers['TYPE_ITEM_VALUE'] = $data->ID;
					$itemMarkers['TYPE_ITEM_LABEL'] = $data->Name;
					$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
				}
				$itemSubparts['###ITEM###'] .= $itemContent;
			}
			$subparts['###SUBPART_HOTEL###'] = $this->cObj->substituteSubpartArray($hotelTemplate, $itemSubparts);
		}
		
		return $this->cObj->substituteSubpartArray($template, $subparts);
	}
	
	/**
	 * Render restaurant search form specific
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	function renderSpecific_Restaurant(&$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_RESTAURANT###');
		
		// Category
		$markers['RESTAURANT_CATEGORY_LABEL'] = $this->pi->pi_getLL('restaurantCategory', 'Restaurant category', true);
		$categoryTemplate = $this->cObj->getSubpart($template, '###SUBPART_CATEGORY###');
		$itemTemplate = $this->cObj->getSubpart($categoryTemplate, '###ITEM###');
		$itemSubparts['###ITEM###'] = '';
		foreach(self::$restaurantCategories as $cat) {
			$itemContent = '';
			if ($data = $this->getSelectData($cat[0], $cat[1])) {
				$itemMarkers = array();
				$itemMarkers['SELECTED_CATEGORY_ITEM'] = ($this->search['restaurantCategory']==$data->ID)? 'selected="selected"': '';
				$itemMarkers['CATEGORY_ITEM_VALUE'] = $data->ID;
				$itemMarkers['CATEGORY_ITEM_LABEL'] = $data->Name;
				$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
			}
			$itemSubparts['###ITEM###'] .= $itemContent;
		}
		$subparts['###SUBPART_CATEGORY###'] = $this->cObj->substituteSubpartArray($categoryTemplate, $itemSubparts);
		
		// Speciality
		$markers['CULINARY_SPECIALITY_LABEL'] = $this->pi->pi_getLL('culinarySpeciality', 'Culinary speciality', true);
		$specialityTemplate = $this->cObj->getSubpart($template, '###SUBPART_SPECIALITY###');
		$itemTemplate = $this->cObj->getSubpart($specialityTemplate, '###ITEM###');
		$itemSubparts['###ITEM###'] = '';
		foreach(self::$foreignFood as $food) {
			$itemContent = '';
			if ($data = $this->getSelectData($food[0], $food[1])) {
				$itemMarkers = array();
				$itemMarkers['SELECTED_CULINARY_SPECIALITY'] = ($this->search['culinarySpeciality']==$data->ID)? 'selected="selected"': '';
				$itemMarkers['CULINARY_SPECIALITY_ITEM_VALUE'] = $data->ID;
				$itemMarkers['CULINARY_SPECIALITY_ITEM_LABEL'] = $data->Name;
				$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
			}
			$itemSubparts['###ITEM###'] .= $itemContent;
		}
		$subparts['###SUBPART_SPECIALITY###'] = $this->cObj->substituteSubpartArray($specialityTemplate, $itemSubparts);
		
		return $this->cObj->substituteSubpartArray($template, $subparts);
	}


	/**
	 * Render event search form specific
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	function renderSpecific_Event(&$markers) {
		$markers['DATE_LABEL'] = $this->pi->pi_getLL('search_date', 'Search date', true);
		$markers['STARTDATE'] = $this->pi->pi_getLL('startDate', 'Start date', true);
		$markers['STARTDATE_VALUE'] = $this->search['startDate'];
		$markers['ENDDATE'] = $this->pi->pi_getLL('endDate', 'End date', true);
		$markers['ENDDATE_VALUE'] = $this->search['endDate'];
		return $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_EVENT###');
	} 
	
	
	/**
	 * Render search form more
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	private function renderMore(&$markers) {
		$dataGroup = (string)strtoupper(trim($this->conf['view.']['dataGroup']));
		$template = '';
		$locMarkers = array();
		switch($dataGroup) {
			case 'ACCOMODATION':
				$locMarkers['SPECIFIC'] = $this->renderMore_Accomodation($markers);
				break;
			case 'RESTAURANT':
				$locMarkers['SPECIFIC'] = $this->renderMore_Restaurant($markers);
				break;
			case 'EVENT':
				$locMarkers['SPECIFIC'] = $this->renderMore_Event($markers);
				break;
			default:
				$locMarkers['SPECIFIC'] = '';
		}
		
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_MORE###');
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
		
		$markers['SEARCH_MORE_TITLE'] = $this->pi->pi_getLL('search_more', 'Search more', true);
		return $template;
	}
	
	/**
	 * Render  accomodation search form more
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	function renderMore_Accomodation(&$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_MORE_ACCOMODATION###');
		$subDataGroups = (string)strtoupper(trim($this->conf['view.']['subDataGroups']));
		$subDataGroups = t3lib_div::trimExplode(',', $subDataGroups, true);

		// Hotel
		$subparts['###SUBPART_HOTEL###'] = '';
		if (in_array('HOTEL', $subDataGroups)) {
			$markers['HOTEL_EQUIPMENT_LABEL'] = $this->pi->pi_getLL('hotelEquipment', 'Hotel equipment', true);
			$itemSubparts = array();
			$hotelTemplate = $this->cObj->getSubpart($template, '###SUBPART_HOTEL###');
			$itemTemplate = $this->cObj->getSubpart($hotelTemplate, '###ITEM###');
			foreach(self::$hotelEquipment as $equipment) {
				$itemContent = '';
				if ($data = $this->getSelectData($equipment[0], $equipment[1])) {
					$itemMarkers = array();
					$itemMarkers['SELECTED_EQUIPMENT_ITEM'] = ($this->search['hotelEquipment']==$data->ID)? 'selected="selected"': '';
					$itemMarkers['EQUIPMENT_ITEM_VALUE'] = ($equipment[0]=='TERM')? $equipment[1][0].':'.$data->ID: $data->ID;
					$itemMarkers['EQUIPMENT_ITEM_LABEL'] = $data->Name;
					$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
				}
				$itemSubparts['###ITEM###'] .= $itemContent;
			}
			$subparts['###SUBPART_HOTEL###'] = $this->cObj->substituteSubpartArray($hotelTemplate, $itemSubparts);
		}
		
		return $this->cObj->substituteSubpartArray($template, $subparts);
	}
	
	/**
	 * Render  restaurant search form more
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	function renderMore_Restaurant(&$markers) {
		/*
			// NOTA : pb, il semblerait que ce soit sur la modalité "Jours de fermeture" du critère "Ouverture service"
			//		donc sélectionner sur du texte libre => sait pas faire
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_MORE_RESTAURANT###');
		$markers['OPENDAY_LABEL'] = $this->pi->pi_getLL('openday', 'Open days', true);
		$opendayTemplate = $this->cObj->getSubpart($template, '###SUBPART_OPENDAY###');
		$itemTemplate = $this->cObj->getSubpart($opendayTemplate, '###ITEM###');
		$opendayTerms = tx_icssitlorquery_CriterionFactory::GetCriterionTerms(
			tx_icssitlorquery_CriterionFactory::GetCriterion(tx_icssitlorquery_CriterionUtils::OPENDAY)
		);
		// for($i=0; $i<$opendayTerms->Count(); $i++) {
			// $day = $opendayTerms->Get($i);
			// $itemContent = '';
			// $itemMarkers = array();
			// $itemMarkers['SELECTED_OPENDAY_ITEM'] = ($this->search['openday']==$day->ID)? 'selected="selected"': '';
			// $itemMarkers['OPENDAY_ITEM_VALUE'] = $day->ID;
			// $itemMarkers['OPENDAY_ITEM_LABEL'] = $day->Name;
			// $itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
			// $itemSubparts['###ITEM###'] .= $itemContent;
		// }
		foreach(self::$dayOfWeek as $numDay) {
			$days = array($opendayTerms->Get($numDay*2 -2), $opendayTerms->Get($numDay*2 -1));
			$itemContent = '';
			$itemMarkers = array();
			$itemMarkers['SELECTED_OPENDAY_ITEM'] = ($this->search['openday']== ($days[0]->ID.','.$days[1]->ID))? 'selected="selected"': '';
			$itemMarkers['OPENDAY_ITEM_VALUE'] = $days[0]->ID.','.$days[1]->ID;
			$itemMarkers['OPENDAY_ITEM_LABEL'] = $this->pi->pi_getLL('openday_'.$numDay, 'Open day '.$numDay, true);
			$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
			$itemSubparts['###ITEM###'] .= $itemContent;
		}
		$subparts['###SUBPART_OPENDAY###'] = $this->cObj->substituteSubpartArray($opendayTemplate, $itemSubparts);
		
		return $this->cObj->substituteSubpartArray($template, $subparts);
		*/
		return '';
	}
	 
	/**
	 * Render  event search form more
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	function renderMore_Event(&$markers) {
		$data = $this->getSelectData('TERM', array(tx_icssitlorquery_CriterionUtils::CURRENT_FREE, tx_icssitlorquery_CriterionUtils::CURRENT_FREE_YES));
		$markers['NOFEE_LABEL'] = $this->pi->pi_getLL('noFee', 'No fee', true);
		$markers['SELECTED_NOFEE'] = $this->search['noFee']? 'selected="selected"': '';
		$markers['NOFEE_VALUE'] = $data->ID;
		return $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_MORE_EVENT###');
	}

	/**
	 * [Describe function...]
	 *
	 * @param	string		$type: Data type
	 * @param	mixed		$value: Data value
	 * @return	mixed		Category, Type, Criterion or Term
	 */
	private function getSelectData($type, $value) {
		switch ($type) {
			case 'CATEGORY':
				return tx_icssitlorquery_NomenclatureFactory::GetCategory($value);
			case 'TYPE':
				return tx_icssitlorquery_NomenclatureFactory::GetType($value);
			case 'CRITERION':
				return tx_icssitlorquery_CriterionFactory::GetCriterion($value);
			case 'TERM':
				return tx_icssitlorquery_CriterionFactory::GetCriterionTerm(
					tx_icssitlorquery_CriterionFactory::GetCriterion($value[0]),
					$value[1]
				);
			default:
				return false;
		}
	}
 }