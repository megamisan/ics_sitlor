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
		$locMarkers['KEYWORD'] = $this->render_Keyword($markers);
		$locMarkers['HOTEL_TYPE'] = $this->render_HotelTypes($markers);
		$locMarkers['RESTAURANT_CATEGORY'] = $this->render_RestaurantCategories($markers);
		$locMarkers['CULINARY_SPECIALITY'] = $this->render_CulinarySpeciality($markers);
		$locMarkers['DATE'] = $this->render_Date($markers);

		$locMarkers['HOTEL_EQUIPMENT'] = $this->render_HotelEquipment($markers);
		$locMarkers['OPENDAY'] = $this->render_Openday($markers);
		$locMarkers['NOFEE'] = $this->render_NoFee($markers);

		$markers['PREFIXID'] = $this->prefixId;
		$markers['SUBMIT'] = $this->pi->pi_getLL('submit', 'Ok', true);

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SEARCH_FORM###');
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * [Describe function...]
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML search form content
	 */
	private function render_Keyword(&$markers) {
		$markers['KEYWORD_LABEL'] = $this->pi->pi_getLL('keyword', 'Keyword', true);
		$markers['KEYWORD_VALUE'] = $this->search['sword']? $this->search['sword']: $this->pi->pi_getLL('keyword', 'Search', true);
		return $this->cObj->getSubpart($this->templateCode, '###SEARCH_KEYWORD###');
	}

	/**
	 * [Describe function...]
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML search form content
	 */
	private function render_HotelTypes(&$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###SEARCH_HOTEL_TYPE###');
		$subparts = array();
		$itemTemplate = $this->cObj->getSubpart($template, '###ITEM###');
		foreach(self::$hotelTypes as $type) {
			$itemContent = '';
			if ($data = $this->getSelectData($type[0], $type[1])) {
				$locMarkers = array();
				$locMarkers['SELECTED_TYPE_ITEM'] = ($this->search['hotelType']==$data->ID)? 'selected="selected"': '';
				$locMarkers['TYPE_ITEM_VALUE'] = $data->ID;
				$locMarkers['TYPE_ITEM_LABEL'] = $data->Name;
				$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
			}
			$subparts['###ITEM###'] .= $itemContent;
		}
		$markers['HOTEL_TYPE_LABEL'] = $this->pi->pi_getLL('hotelType', 'Hotel type', true);
		return $template = $this->cObj->substituteSubpartArray($template, $subparts);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML search form content
	 */
	private function render_RestaurantCategories(&$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###SEARCH_RESTAURANT_CATEGORY###');
		$subparts = array();
		$itemTemplate = $this->cObj->getSubpart($template, '###ITEM###');
		foreach(self::$restaurantCategories as $cat) {
			$itemContent = '';
			if ($data = $this->getSelectData($cat[0], $cat[1])) {
				$locMarkers = array();
				$locMarkers['SELECTED_CATEGORY_ITEM'] = ($this->search['restaurantCategory']==$data->ID)? 'selected="selected"': '';
				$locMarkers['CATEGORY_ITEM_VALUE'] = $data->ID;
				$locMarkers['CATEGORY_ITEM_LABEL'] = $data->Name;
				$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
			}
			$subparts['###ITEM###'] .= $itemContent;
		}
		$markers['RESTAURANT_CATEGORY_LABEL'] = $this->pi->pi_getLL('restaurantCategory', 'Restaurant category', true);
		return $template = $this->cObj->substituteSubpartArray($template, $subparts);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML search form content
	 */
	private function render_CulinarySpeciality(&$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###SEARCH_CULINARY_SPECIALITY###');
		$subparts = array();
		$itemTemplate = $this->cObj->getSubpart($template, '###ITEM###');
		foreach(self::$foreignFood as $food) {
			$itemContent = '';
			if ($data = $this->getSelectData($food[0], $food[1])) {
				$locMarkers = array();
				$locMarkers['SELECTED_CULINARY_SPECIALITY'] = ($this->search['culinarySpeciality']==$data->ID)? 'selected="selected"': '';
				$locMarkers['CULINARY_SPECIALITY_ITEM_VALUE'] = $data->ID;
				$locMarkers['CULINARY_SPECIALITY_ITEM_LABEL'] = $data->Name;
				$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
			}
			$subparts['###ITEM###'] .= $itemContent;
		}
		$markers['CULINARY_SPECIALITY_LABEL'] = $this->pi->pi_getLL('culinarySpeciality', 'Culinary speciality', true);
		return $template = $this->cObj->substituteSubpartArray($template, $subparts);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML search form content
	 */
	private function render_Date(&$markers) {
		$markers['STARTDATE'] = $this->pi->pi_getLL('startDate', 'Start date', true);
		$markers['STARTDATE_VALUE'] = $this->search['startDate'];
		$markers['ENDDATE'] = $this->pi->pi_getLL('endDate', 'End date', true);
		$markers['ENDDATE_VALUE'] = $this->search['endDate'];
		return $this->cObj->getSubpart($this->templateCode, '###SEARCH_DATE###');
	}

	/**
	 * [Describe function...]
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML search form content
	 */
	private function render_HotelEquipment(&$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###SEARCH_HOTEL_EQUIPMENT###');
		$subparts = array();
		$itemTemplate = $this->cObj->getSubpart($template, '###ITEM###');
		foreach(self::$hotelEquipment as $equipment) {
			$itemContent = '';
			if ($data = $this->getSelectData($equipment[0], $equipment[1])) {
				$locMarkers = array();
				$locMarkers['SELECTED_EQUIPMENT_ITEM'] = ($this->search['hotelEquipment']==$data->ID)? 'selected="selected"': '';
				$locMarkers['EQUIPMENT_ITEM_VALUE'] = $data->ID;
				$locMarkers['EQUIPMENT_ITEM_LABEL'] = $data->Name;
				$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
			}
			$subparts['###ITEM###'] .= $itemContent;
		}
		$markers['HOTEL_EQUIPMENT_LABEL'] = $this->pi->pi_getLL('hotelEquipment', 'Hotel equipment', true);
		return $template = $this->cObj->substituteSubpartArray($template, $subparts);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML search form content
	 */
	private function render_Openday(&$markers) {
		$days = tx_icssitlorquery_CriterionFactory::GetCriterionTerms(
			tx_icssitlorquery_CriterionFactory::GetCriterion(tx_icssitlorquery_CriterionUtils::OPENDAY)
		);
		$template = $this->cObj->getSubpart($this->templateCode, '###SEARCH_OPENDAY###');
		$subparts = array();
		$itemTemplate = $this->cObj->getSubpart($template, '###ITEM###');
		for($i=0; $i<$days->Count(); $i++) {
			$day = $days->Get($i);
			$locMarkers = array();
			$locMarkers['SELECTED_OPENDAY_ITEM'] = ($this->search['openday']==$day->ID)? 'selected="selected"': '';
			$locMarkers['OPENDAY_ITEM_VALUE'] = $day->ID;
			$locMarkers['OPENDAY_ITEM_LABEL'] = $day->Name;
			$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
			$subparts['###ITEM###'] .= $itemContent;
		}
		$markers['OPENDAY_LABEL'] = $this->pi->pi_getLL('openday', 'Open days', true);
		return $template = $this->cObj->substituteSubpartArray($template, $subparts);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML search form content
	 */
	private function render_NoFee(&$markers) {
		$data = $this->getSelectData('TERM', array(tx_icssitlorquery_CriterionUtils::CURRENT_FREE, tx_icssitlorquery_CriterionUtils::CURRENT_FREE_YES));
		$markers['NOFEE_LABEL'] = $this->pi->pi_getLL('noFee', 'No fee', true);
		$markers['SELECTED_NOFEE'] = $this->search['noFee']? 'selected="selected"': '';
		$markers['NOFEE_VALUE'] = $data->ID;
		return $this->cObj->getSubpart($this->templateCode, '###SEARCH_NOFEE###');
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