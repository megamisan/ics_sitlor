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
 *   58: class tx_icssitlorquery_FormRenderer
 *   80:     function __construct($pi, $cObj, $lConf)
 *   97:     private function setDataForm()
 *  184:     function render()
 *  234:     private function renderGeneric(&$markers)
 *  247:     private function renderSpecific(&$markers)
 *  275:     function renderSpecific_Accomodation(&$markers)
 *  332:     function renderSpecific_Restaurant(&$markers)
 *  377:     function renderSpecific_Event(&$markers)
 *  392:     function renderSpecific_Subscriber(&$markers)
 *  411:     function renderSpecific_Subscriber_types(array $types)
 *  434:     private function renderMore(&$markers)
 *  465:     function renderMore_Accomodation(&$markers)
 *  499:     function renderMore_Event(&$markers)
 *
 * TOTAL FUNCTIONS: 13
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitlorquery_FormRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
 class tx_icssitlorquery_FormRenderer {
	private static $subAccomodations = array();
	private static $hotelTypes = array();
	private static $restaurantCategories = array();
	private static $foreignFood = array();
	private static $hotelEquipment = array();
	private static $dayOfWeek = array(1,2,3,4,5,6,7);	// int : ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)

	private static $subscriber_types_artsAndCrafts = array();
	private static $subscriber_types_commerce = array();
	private static $subscriber_types_other = array();

	public $addViewTab = false;

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
		$this->search = $pi->FormFilter;
		$this->sword = $pi->sword;

		$this->setDataForm();
	}

	/**
	 * Sets data form
	 *
	 * @return	void
	 */
	private function setDataForm() {
		self::$subAccomodations = array(
			'hotel' => 'HOTEL',
			'camping_youthHostel' => 'CAMPING, YOUTHHOSTEL',
			'strange' => 'STRANGE',
			'hollidayCottage_guesthouse' => 'HOLLIDAY_COTTAGE, GUESTHOUSE',
		);
		self::$hotelTypes = array(
			'hotel_restaurant' => array(
				'label' => $this->pi->pi_getLL('hotel_restaurant', 'Hotel-restaurant', true),
				'value' => tx_icssitlorquery_NomenclatureUtils::HOTEL_RESTAURANT
			),
			'furnished' => array(
				'label' =>  $this->pi->pi_getLL('furnished', 'Furnished', true),
				// 'value' => tx_icssitlorquery_NomenclatureUtils::FURNISHED
				'value' => tx_icssitlorquery_NomenclatureUtils::RESIDENCE
			),
		);
		self::$hotelEquipment = array(
			'wifi' => array(
				'label' => $this->pi->pi_getLL('wifi', 'Wifi', true),
				'value' => tx_icssitlorquery_CriterionUtils::COMFORT_ROOM . ':' . tx_icssitlorquery_CriterionUtils::WIFI
			),
			'pets' => array(
				'label' => $this->pi->pi_getLL('allowed_pets', 'Allowed pets', true),
				'value' => tx_icssitlorquery_CriterionUtils::ALLOWED_PETS . ':' . tx_icssitlorquery_CriterionUtils::ALLOWED_PETS_YES
			),
			'park' => array(
				'label' => $this->pi->pi_getLL('park', 'Park', true),
				'value' => tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK . ':' . tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK_YES
			),
		);
		self::$restaurantCategories = array(
			'fastfood' => array(
				'label' => $this->pi->pi_getLL('fastfood', 'Fast food', true),
				'value' => tx_icssitlorquery_CriterionUtils::RCATEGORIE . ':' . tx_icssitlorquery_CriterionUtils::RCATEGORIE_FASTFOOD
			),
			'icecream_theahouse' => array(
				'label' => $this->pi->pi_getLL('icecream_theahouse', 'Ice cream and thea house', true),
				'value' => tx_icssitlorquery_CriterionUtils::RCATEGORIE . ':' . tx_icssitlorquery_CriterionUtils::RCATEGORIE_ICECREAM_THEAHOUSE
			),
			'creperie' => array(
				'label' => $this->pi->pi_getLL('creperie', 'Creperie', true),
				'value' => tx_icssitlorquery_CriterionUtils::RCATEGORIE . ':' . tx_icssitlorquery_CriterionUtils::RCATEGORIE_CREPERIE
			),
		);
		self::$foreignFood = array(
			'asian' => array(
				'label' => $this->pi->pi_getLL('asian_food', 'Asian food', true),
				'value' => tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD . ':' . tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD_ASIAN
			),
			'sa' => array(
				'label' => $this->pi->pi_getLL('sa_food', 'South american food', true),
				'value' => tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD . ':' . tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD_SA
			),
			'oriental' => array(
				'label' => $this->pi->pi_getLL('oriental_food', 'Oriental food', true),
				'value' => tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD . ':' . tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD_ORIENTAL
			),
		);

		// Get subscribers type | artsAndCrafts 
		self::$subscriber_types_artsAndCrafts = array();
		$termList = tx_icssitlorquery_CriterionFactory::GetCriterionTerms(tx_icssitlorquery_CriterionFactory::GetCriterion(tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS));
		for ($i=0; $i<$termList->Count(); $i++) {
			$term = $termList->Get($i);
			$label = $this->pi->pi_getLL('criterionTerm_' . tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS . '_' . $term->ID,$term->Name, true);
			$value =  'CRITERION,' . tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS . ',' . tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS . ':' . $term->ID;
			self::$subscriber_types_artsAndCrafts[$label] = $value;
		}
		ksort(self::$subscriber_types_artsAndCrafts);

		// Get subscribers type | commerce 
		self::$subscriber_types_commerce = array();
		$termList = tx_icssitlorquery_CriterionFactory::GetCriterionTerms(tx_icssitlorquery_CriterionFactory::GetCriterion(tx_icssitlorquery_CriterionUtils::COMMERCE));
		for ($i=0; $i<$termList->Count(); $i++) {
			$term = $termList->Get($i);
			$label = $this->pi->pi_getLL('criterionTerm_' . tx_icssitlorquery_CriterionUtils::COMMERCE . '_' . $term->ID,$term->Name, true);
			$value = 'CRITERION,' . tx_icssitlorquery_CriterionUtils::COMMERCE . ',' . tx_icssitlorquery_CriterionUtils::COMMERCE . ':' . $term->ID;
			self::$subscriber_types_commerce[$label] = $value;
		}
		ksort(self::$subscriber_types_commerce);

		// Get subscribers type | other 
		$type_hotel = tx_icssitlorquery_NomenclatureFactory::GetType(tx_icssitlorquery_NomenclatureUtils::HOTEL);
		$cat_hollidayCottage = tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::HOLLIDAY_COTTAGE);
		$cat_residence = tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESIDENCE);
		$cat_guestHouse = tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::GUESTHOUSE);
		$cat_restaurant = tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESTAURANT);
		$cat_association = tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::ASSOCIATION);
		self::$subscriber_types_other = array(
			$this->pi->pi_getLL('hotel', $type_hotel->Name, true) => $type_hotel->ID,
			$this->pi->pi_getLL('hollidayCottage', $cat_hollidayCottage->Name, true) => $cat_hollidayCottage->ID,
			$this->pi->pi_getLL('residence', $cat_residence->Name, true) => $cat_residence->ID,
			$this->pi->pi_getLL('guesthouse', $cat_guestHouse->Name, true) => $cat_guestHouse->ID,
			$this->pi->pi_getLL('restaurant', $cat_restaurant->Name, true) => $cat_restaurant->ID,
			$this->pi->pi_getLL('association', $cat_association->Name, true) => $cat_association->ID,
		);
		ksort(self::$subscriber_types_other);

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
			($dataGroup == 'EVENT')) {
			$locMarkers['MORE'] = $this->renderMore($markers);
		}
		if ($this->addViewTab && isset($this->pi->piVars['viewTab'])) {
			$locMarkers['MORE'] .= '<input type="hidden" name="###PREFIXID###[viewTab]" value="###VIEWTAB###" />';
			$markers['VIEWTAB'] = $this->pi->piVars['viewTab'];
		}

		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SEARCH_FORM###');
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
		$template = $this->cObj->substituteMarkerArray($template, $markers, '###|###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
			'ACTION_URL' =>  $this->pi->pi_getPageLink ($GLOBALS['TSFE']->id, $target='', array()),//t3lib_div::getIndpEnv('TYPO3_REQUEST_URL') ,
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
		$markers['KEYWORD_VALUE'] = $this->sword? $this->sword: $this->pi->pi_getLL('keyword', 'Search', true);
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
			case 'SUBSCRIBER':
				$template = $this->renderSpecific_Subscriber($markers);
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

		// Any sub-data group
		$subparts['###SUBPART_ANY_SUBDATAGROUP###'] = '';
		if (empty($subDataGroups)) {
			$locMarkers = array(
				'HOTEL_LABEL' => $this->pi->pi_getLL('hotel', 'Hotel', true),
				'HOTEL_VALUE' => self::$subAccomodations['hotel'],
				'SELECTED_HOTEL' => (is_array($this->search['subDataGroups']) && in_array(self::$subAccomodations['hotel'], $this->search['subDataGroups']))? 'checked="checked"': '',

				'CAMPING_YOUTHHOSTEL_LABEL' => $this->pi->pi_getLL('camping_youthHostel', 'Camping and youth hostel', true),
				'CAMPING_YOUTHHOSTEL_VALUE' => self::$subAccomodations['camping_youthHostel'],
				'SELECTED_CAMPING_YOUTHHOSTEL' => (is_array($this->search['subDataGroups']) &&in_array(self::$subAccomodations['camping_youthHostel'], $this->search['subDataGroups']))? 'checked="checked"': '',

				'STRANGE_LABEL' => $this->pi->pi_getLL('strange', 'Strange', true),
				'STRANGE_VALUE' => self::$subAccomodations['strange'],
				'SELECTED_STRANGE' => (is_array($this->search['subDataGroups']) && in_array(self::$subAccomodations['strange'], $this->search['subDataGroups']))? 'checked="checked"': '',

				'HOLLIDAY_COTTAGE_GUESTHOUSE_LABEL' => $this->pi->pi_getLL('hollidayCottage_guesthouse', 'Holliday cottage and guesthouse', true),
				'HOLLIDAY_COTTAGE_GUESTHOUSE_VALUE' => self::$subAccomodations['hollidayCottage_guesthouse'],
				'SELECTED_HOLLIDAY_COTTAGE_GUESTHOUSE' => (is_array($this->search['subDataGroups']) && in_array(self::$subAccomodations['hollidayCottage_guesthouse'], $this->search['subDataGroups']))? 'checked="checked"': '',
			);
			$markers = array_merge($locMarkers, $markers);
			$subparts['###SUBPART_ANY_SUBDATAGROUP###'] = $this->cObj->getSubpart($template, '###SUBPART_ANY_SUBDATAGROUP###');
		}

		// Hotel
		$subparts['###SUBPART_HOTEL###'] = '';
		if (in_array('HOTEL', $subDataGroups)) {
			$markers['HOTEL_TYPE_LABEL'] = $this->pi->pi_getLL('hotelType', 'Hotel type', true);
			$itemSubparts = array();
			$hotelTemplate = $this->cObj->getSubpart($template, '###SUBPART_HOTEL###');
			$itemTemplate = $this->cObj->getSubpart($hotelTemplate, '###ITEM###');
			foreach(self::$hotelTypes as $title=>$data) {
				$itemContent = '';
				$itemMarkers = array();
				$itemMarkers['SELECTED_TYPE_ITEM'] = in_array($data['value'], $this->search['hotelType'])? 'checked="checked"': '';
				$itemMarkers['TYPE_ITEM_VALUE'] = $data['value'];
				$itemMarkers['TYPE_ITEM_LABEL'] = $data['label'];
				$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
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
		foreach(self::$restaurantCategories as $title=>$data) {
			$itemContent = '';
			$itemMarkers = array();
			$itemMarkers['SELECTED_CATEGORY_ITEM'] = in_array($data['value'], $this->search['restaurantCategory'])? 'checked="checked"': '';
			$itemMarkers['CATEGORY_ITEM_VALUE'] = $data['value'];
			$itemMarkers['CATEGORY_ITEM_LABEL'] = $data['label'];
			$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
			$itemSubparts['###ITEM###'] .= $itemContent;
		}
		$subparts['###SUBPART_CATEGORY###'] = $this->cObj->substituteSubpartArray($categoryTemplate, $itemSubparts);

		// Speciality
		$markers['CULINARY_SPECIALITY_LABEL'] = $this->pi->pi_getLL('culinarySpeciality', 'Culinary speciality', true);
		$specialityTemplate = $this->cObj->getSubpart($template, '###SUBPART_SPECIALITY###');
		$itemTemplate = $this->cObj->getSubpart($specialityTemplate, '###ITEM###');
		$itemSubparts['###ITEM###'] = '';
		foreach(self::$foreignFood as $title=>$data) {
			$itemContent = '';
			$itemMarkers = array();
			$itemMarkers['SELECTED_CULINARY_SPECIALITY'] = in_array($data['value'], $this->search['culinarySpeciality'])? 'checked="checked"': '';
			$itemMarkers['CULINARY_SPECIALITY_ITEM_VALUE'] = $data['value'];
			$itemMarkers['CULINARY_SPECIALITY_ITEM_LABEL'] = $data['label'];
			$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
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
	 * Render Subscribers search form specific
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML content
	 */
	function renderSpecific_Subscriber(&$markers) {
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_SUBSCRIBER###');
		$locMarkers = array(
			'ANY_TYPE' => $this->renderSpecific_Subscriber_types(array( $this->pi->pi_getLL('subscriber_types_anyType', 'subscriber_types_anyType', true) => '')),
			'ARTS_CRAFTS_TYPE' => $this->renderSpecific_Subscriber_types(self::$subscriber_types_artsAndCrafts),
			'COMMERCE_TYPE' => $this->renderSpecific_Subscriber_types(self::$subscriber_types_commerce),
			'OTHER_TYPE' => $this->renderSpecific_Subscriber_types(self::$subscriber_types_other),
			'CATEGORY_ANY' => $this->pi->pi_getLL('subscriber_category_any', 'Any type', true),
			'CATEGORY_ARTS_CRAFTS' => $this->pi->pi_getLL('subscriber_category_artsAndCrafts', 'Arts and cratfs', true),
			'CATEGORY_COMMERCE' => $this->pi->pi_getLL('subscriber_category_commerce', 'Commerce', true),
			'CATEGORY_OTHER' => $this->pi->pi_getLL('subscriber_category_other', 'Other category', true),
		);
		return $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
	}

	/**
	 * @param	array		$types: Array of subscribers types
	 * @return	string		HTML content
	 */
	function renderSpecific_Subscriber_types(array $types) {
		$itemTemplate = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_SUBSCRIBER_TYPE###');
		foreach ($types as $label=>$type) {
			$uniqId =  uniqid();
			$locMarkers = array(
				'UNIQID' => $uniqId,
				'SUBSCRIBER_TYPE_VALUE' => $type,
				'CHECKED' => ($this->search['subscriber_type']==$type)? 'checked="checked"': '',
				'SUBSCRIBER_TYPE_LABEL' => $label,
			);
			$itemContent .= $this->cObj->substituteMarkerArray($itemTemplate, $locMarkers, '###|###');
		}
		return $itemContent;
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
			foreach(self::$hotelEquipment as $title=>$data) {
				$itemContent = '';
				$itemMarkers = array();
				$itemMarkers['SELECTED_EQUIPMENT_ITEM'] = in_array($data['value'], $this->search['hotelEquipment'])? 'checked="checked"': '';
				$itemMarkers['EQUIPMENT_ITEM_VALUE'] = $data['value'];
				$itemMarkers['EQUIPMENT_ITEM_LABEL'] = $data['label'];
				$itemContent = $this->cObj->substituteMarkerArray($itemTemplate, $itemMarkers, '###|###');
				$itemSubparts['###ITEM###'] .= $itemContent;
			}
			$subparts['###SUBPART_HOTEL###'] = $this->cObj->substituteSubpartArray($hotelTemplate, $itemSubparts);
		}

		return $this->cObj->substituteSubpartArray($template, $subparts);
	}


	/**
	 * Render  event search form more
	 *
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	function renderMore_Event(&$markers) {
		$value = tx_icssitlorquery_CriterionUtils::CURRENT_FREE . ':' . tx_icssitlorquery_CriterionUtils::CURRENT_FREE_YES;
		$markers['NOFEE_LABEL'] = $this->pi->pi_getLL('noFee', 'No fee', true);
		$markers['SELECTED_NOFEE'] = $this->search['noFee']? 'checked="checked"': '';
		$markers['NOFEE_VALUE'] = $value;
		return $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_FORM_MORE_EVENT###');
	}

}
