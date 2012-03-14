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
 * Class 'tx_icssitlorquery_SingleRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_SingleRenderer {
	private static $mapsIncluded = false;
	private static $jsIncluded = false;

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
	 * Render detail content
	 *
	 * @param	object		$elements : tx_icssitquery_AbstractData like tx_icssitlorquery_Accomodation, tx_icssitlorquery_Restaurant or tx_icssitlorquery_Event
	 * @return	string		HTML list content
	 */
	function render($element) {
		if (!($element instanceof tx_icssitquery_AbstractData))
			return '';
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL###');
		$markers = array();
		$locMarkers['GENERIC'] = $this->renderGeneric($element, $markers);
		$locMarkers['SPECIFIC'] = $this->renderSpecific($element, $markers);
		$template = $this->cObj->substituteMarkerArray($template, $locMarkers, '###|###');
		$template = $this->cObj->substituteMarkerArray($template, $markers, '###|###');

		$markers = array(
			'PREFIXID' => $this->prefixId,
		);
		return $this->cObj->substituteMarkerArray($template, $markers, '###|###');
	}

	/**
	 * Render detail generic
	 *
	 * @param	object		$element: tx_icssitquery_AbstractData like tx_icssitlorquery_Accomodation, tx_icssitlorquery_Restaurant or tx_icssitlorquery_Event
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	private function renderGeneric($element, &$markers) {// TODO: Case of Generic Data. (May some elements be transfered to Specific?)
		if (!($element instanceof tx_icssitquery_AbstractData))
			return '';
		$locMarkers = array(
				// Identity
			'IDENTITY_LABEL' => $this->pi->pi_getLL('identity', 'Identity', true),
			'TITLE' => $element->Name,
			'TYPE' => $element->Type,
			'ADDRESS' => $element->Address,
			'PHONE' => $this->pi->renderData('phones', $element->Phones),
			'FAX' => $this->pi->renderData('fax', $element->Fax),
			'MAIL' => $element->Email,
			'WEBSITE' => $element->WebSite,
			'ILLUSTRATION' => $element->Illustration->toString(' '),
				//Description
			'DESCRIPTION_LABEL' => $this->pi->pi_getLL('description', 'Description', true),
			'DESCRIPTION' => nl2br(htmlspecialchars($element->Description)),
				// Coordinates
			'COORDINATES_LABEL' => $this->pi->pi_getLL('coordinates', 'Coordinates', true),
			'COORDINATES' => $element->Coordinates,
		);
		$subparts = array();
		if (is_null($element->Coordinates)) {
			$subparts['###SUBPART_MAPCANVAS###'] = '';
		} else {
			$locMarkers['MAPCANVAS_SIZE'] = ' style="width: ' . $this->conf['geocode.']['canvas.']['width'] . '; height: ' .$this->conf['geocode.']['canvas.']['height']  . ';"';
			self::includeLibGMaps();
			self::includeLibJS(
				array_merge(
					$this->conf['geocode.'],
					array(
						'latitude' => $element->Coordinates->Latitude,
						'longitude' => $element->Coordinates->Longitude
					)
				),
				$this->prefixId
			);
		}
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_GENERIC###');
		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		$markers = array_merge($markers, $locMarkers);
		return $template;
	}

	/**
	 * Render detail specific
	 *
	 * @param	object		$element: tx_icssitquery_AbstractData like tx_icssitlorquery_Accomodation, tx_icssitlorquery_Restaurant or tx_icssitlorquery_Event
	 * @param	array&		$markers: Markers array
	 * @return	string		HTML detail content
	 */
	private function renderSpecific($element, &$markers) {
		if (!($element instanceof tx_icssitquery_AbstractData))
			return '';

		$template = '';
		$locMarkers = array();
		$subparts = array();
		if ($element instanceof tx_icssitlorquery_Accomodation) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_ACCOMODATION###');
			$this->renderAccomodation($element, $locMarkers, $subparts);
		}
		if ($element instanceof tx_icssitlorquery_Restaurant) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_RESTAURANT###');
			$this->renderRestaurant($element, $locMarkers, $subparts);
		}
		if ($element instanceof tx_icssitlorquery_Event) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_EVENT###');
			$this->renderEvent($element, $locMarkers, $subparts);
		}
		$template = $this->cObj->substituteSubpartArray($template, $subparts);
		$markers = array_merge($markers, $locMarkers);
		return $template;
	}

	/**
	 * Render Accomodation
	 *
	 * @param	tx_icssitlorquery_Accomodation		$element: Accomodation
	 * @param	&array		$markers: The marker array
	 * @param	&array		$subparts: The subpart array
	 * @return	void
	 */
	private function renderAccomodation(tx_icssitlorquery_Accomodation $element, array &$markers, array &$subparts) {
		$locMarkers = array(
				// Provider
			'PROVIDER_LABEL' => $this->pi->pi_getLL('provider', 'Provider', true),
			'PROVIDER_NAME' => $element->ProviderName,
			'PROVIDER_ADDRESS' => $element->ProviderAddress,
			'PROVIDER_PHONE' => $this->pi->renderData('phones', $element->Phones),
			'PROVIDER_FAX' => $this->pi->renderData('fax', $element->Fax),
			'PROVIDER_MAIL' => $element->ProviderEmail,
			'PROVIDER_WEBSITE' => $element->ProviderWebSite,
				// Timetable
			'TIMETABLE_LABEL' => $this->pi->pi_getLL('timetable', 'Timetable', true),
			'TIMETABLE' => $element->TimeTable,
				// Rating
			'HOTEL_RATING_LABEL' => $this->pi->pi_getLL('rating_star', 'Rating', true),
			'HOTEL_RATING' => $this->pi->renderData('ratingStar', $element->RatingStar),
				// Reception
			'RECEPTION_LABEL' => $this->pi->pi_getLL('reception', 'Reception', true),
			'RECEPTION_LANGUAGE_LABEL' => $this->pi->pi_getLL('reception_language', 'Reception language', true),
			'RECEPTION_LANGUAGE' => $element->ReceptionLanguage,
			'RESERVATION_LANGUAGE_LABEL' => $this->pi->pi_getLL('reservation_language', 'Reservation language', true),
			'RESERVATION_LANGUAGE' => $element->ReservationLanguage,
			'MOBILITY_IMPAIRED_LABEL' => $this->pi->pi_getLL('mobility_impaired', 'Mobility impaired', true),
			'MOBILITY_IMPAIRED' => $element->MobilityImpaired,
			'PETS_LABEL' => $this->pi->pi_getLL('pets', 'Pets', true),
			'PETS' => $element->Pets,
			'ALLOWED_PETS_LABEL' => $this->pi->pi_getLL('allowed_pets', 'Allowed pets', true),
			'ALLOWED_PETS' => $element->AllowedPets,
			'ALLOWED_GROUP_LABEL' => $this->pi->pi_getLL('allowed_group', 'Allowed group', true),
			'ALLOWED_GROUP' => $element->AllowedGroup,
			'RECEPTION_GROUP_LABEL' => $this->pi->pi_getLL('reception_group', 'Reception group', true),
			'RECEPTION_GROUP' => $element->ReceptionGroup,
			'MOTORCOACH_PARK_LABEL' => $this->pi->pi_getLL('motorcoach_park', 'Motorcoach park', true),
			'MOTORCOACH_PARK' => $element->MotorCoachPark,
			'OPENING24_24_LABEL' => $this->pi->pi_getLL('opening24_24', 'Opening 24/24', true),
			'OPENING24_24' => $element->Opening24_24,
				// Price
			'PRICE_LABEL' => $this->pi->pi_getLL('price', 'Price', true),
			'SINGLE_CLIENT_PRICE' => $element->CurrentSingleClientsRate,
				// Comfort room
			'COMFORTROOM_LABEL' =>  $this->pi->pi_getLL('comfort_room', 'Comfort room', true),
			'COMFORT_ROOM' => $element->ComfortRoom,
				// Hotel equipment
			'HOTEL_EQUIPMENT_LABEL' => $this->pi->pi_getLL('hotel_equipment', 'Hotel equipment', true),
			'HOTEL_EQUIPMENT' => $element->HotelEquipement,
				// Hotel service
			'HOTEL_SERVICE_LABEL' => $this->pi->pi_getLL('hotel_service', 'Hotel service', true),
			'HOTEL_SERVICE' => $element->HotelService,
		);

		$markers = array_merge($markers, $locMarkers);

		if ($element->ReceptionLanguage->Count()<=0)
			$subparts['###SUBPART_RECEPTION_LANGUAGE###'] = '';
		if ($element->ReservationLanguage->Count()<=0)
			$subparts['###SUBPART_RESERVATION_LANGUAGE###'] = '';
		if (!$element->MobilityImpaired)
			$subparts['###SUBPART_MOBILITY_IMPAIRED###'] = '';
		if (!$element->Pets)
			$subparts['###SUBPART_PETS###'] = '';
		if (!$element->AllowedPets)
			$subparts['###SUBPART_ALLOWED_PETS###'] = '';
		if (!$element->AllowedGroup)
			$subparts['###SUBPART_ALLOWED_GROUP###'] = '';
		if ($element->ReceptionGroup->Count()<=0)
			$subparts['###SUBPART_RECEPTION_GROUP###'] = '';
		if (!$element->MotorCoachPark)
			$subparts['###SUBPART_MOTORCOACH_PARK###'] = '';
		if (!$element->Opening24_24)
			$subparts['###SUBPART_OPENING24_24###'] = '';
	}

	/**
	 * Render Restaurant
	 *
	 * @param	tx_icssitlorquery_Restaurant		$element: Restaurant
	 * @param	&array		$markers: The marker array
	 * @param	&array		$subparts: The subpart array
	 * @return	void
	 */
	private function renderRestaurant(tx_icssitlorquery_Restaurant $element, array &$markers, array &$subparts) {
		$locMarkers = array(
				// Provider
			'PROVIDER_LABEL' => $this->pi->pi_getLL('provider', 'Provider', true),
			'PROVIDER_NAME' => $element->ProviderName,
			'PROVIDER_ADDRESS' => $element->ProviderAddress,
			'PROVIDER_PHONE' => $this->pi->renderData('phones', $element->Phones),
			'PROVIDER_FAX' => $this->pi->renderData('fax', $element->Fax),
			'PROVIDER_MAIL' => $element->ProviderEmail,
			'PROVIDER_WEBSITE' => $element->ProviderWebSite,
				// Class
			'RESTAURANT_CLASS_LABEL' => $this->pi->pi_getLL('restaurant_class', 'Class of restaurant', true),
			'RESTAURANT_CLASS' => $element->Class,
				// Reception
			'RECEPTION_LABEL' => $this->pi->pi_getLL('reception', 'Reception', true),
			'RECEPTION_LANGUAGE_LABEL' => $this->pi->pi_getLL('reception_language', 'Reception language', true),
			'RECEPTION_LANGUAGE' => $element->ReceptionLanguage,
			'MENU_LANGUAGE_LABEL' => $this->pi->pi_getLL('menu_language', 'Menu language', true),
			'MENU_LANGUAGE' => $element->MenuLanguage,
			'PETS_LABEL' => $this->pi->pi_getLL('pets', 'Pets', true),
			'PETS' => $element->Pets,
			'ALLOWED_PETS_LABEL' => $this->pi->pi_getLL('allowed_pets', 'Allowed pets', true),
			'ALLOWED_PETS' => $element->AllowedPets,
			'ALLOWED_GROUP_LABEL' => $this->pi->pi_getLL('allowed_group', 'Allowed group', true),
			'ALLOWED_GROUP' => $element->AllowedGroup,
			'ALLOWED_GROUP_NUMBER_LABEL' => $this->pi->pi_getLL('allowed_group_number', 'Allowed group number', true),
			'ALLOWED_GROUP_NUMBER' => $element->AllowedGroupNumber,
			'MOTORCOACH_PARK_LABEL' => $this->pi->pi_getLL('motorcoach_park', 'Motorcoach park', true),
			'MOTORCOACH_PARK' => $element->MotorCoachPark,
			'SERVICE_OPEN_LABEL' => $this->pi->pi_getLL('service_open', 'Service open', true),
			'SERVICE_OPEN' => $element->ServiceOpen,
				// Capacity
			'CAPACITY_LABEL' => $this->pi->pi_getLL('capacity', 'Capacity', true),
			'CAPACITY' => $element->Capacity,
				// Price
			'PRICE_LABEL' => $this->pi->pi_getLL('price', 'Price', true),
			'SALE_FORMULA_LABEL' => $this->pi->pi_getLL('sale_formula', 'Sale formula', true),
			'SALE_FORMULA' => $element->CurrentSaleFormula,
			'CARTE_PRICE_LABEL' => $this->pi->pi_getLL('carte_price', 'Carte price', true),
			'CARTE_PRICE' => $element->CurrentCartePrice,
			'MENU_PRICE_LABEL' => $this->pi->pi_getLL('menu_price', 'Menu price', true),
			'MENU_PRICE' => $element->CurrentMenuPrice,
		);

		$markers = array_merge($markers, $locMarkers);

		if ($element->ReceptionLanguage->Count()<=0)
			$subparts['###SUBPART_RECEPTION_LANGUAGE###'] = '';
		if ($element->MenuLanguage->Count()<=0)
			$subparts['###SUBPART_MENU_LANGUAGE###'] = '';
		if (!$element->Pets)
			$subparts['###SUBPART_PETS###'] = '';
		if (!$element->AllowedPets)
			$subparts['###SUBPART_ALLOWED_PETS###'] = '';
		if (!$element->AllowedGroup)
			$subparts['###SUBPART_ALLOWED_GROUP###'] = '';
		if ($element->AllowedGroupNumber->Count()<=0)
			$subparts['###SUBPART_ALLOWED_GROUP_NUMBER###'] = '';
		if (!$element->MotorCoachPark)
			$subparts['###SUBPART_MOTORCOACH_PARK###'] = '';
		if ($element->ServiceOpen->Count()<=0)
			$subparts['###SUBPART_SERVICE_OPEN###'] = '';

		if ($element->CurrentSaleFormula->Count()<=0)
			$subparts['###SUBPART_SALE_FORMULA###'] = '';
		if ($element->CurrentCartePrice->Count()<=0)
			$subparts['###SUBPART_CARTE_PRICE###'] = '';
		if ($element->CurrentMenuPrice->Count()<=0)
			$subparts['###SUBPART_MENU_PRICE###'] = '';
	}

	/**
	 * Render Event
	 *
	 * @param	tx_icssitlorquery_Event		$element: Event
	 * @param	&array		$markers: The marker array
	 * @param	&array		$subparts: The subpart array
	 * @return	void
	 */
	private function renderEvent(tx_icssitlorquery_Event $element, array &$markers, array &$subparts) {
		$locMarkers = array(
			'EVENT_INFORMATION' => $this->pi->pi_getLL('event_infos', 'Event informations', true),
			'KIND_EVENT_LABEL' => $this->pi->pi_getLL('kind_event', 'Kind of event', true),
			'KIND_EVENT' => $element->KindOfEvent,
			'TYPE_EVENT_LABEL' => $this->pi->pi_getLL('type_event', 'Type', true),
			'TYPE_EVENT' => $element->TypeEvent,
			'INFORMATION_LABEL' => $this->pi->pi_getLL('other_infos', 'Informations', true),
			'INFORMATION' => $element->Information,
			'FESTIVAL_LABEL' => $this->pi->pi_getLL('festival', 'Festival', true),
			'FESTIVAL' => $element->Festival,
				// Price
			'PRICE_LABEL' => $this->pi->pi_getLL('price', 'Price', true),
		);
		if ($element->CurrentFree && ($element->CurrentFree->Term->ID == tx_icssitlorquery_CriterionUtils::CURRENT_FREE_YES)) {
			$locMarkers['FREE'] = $this->pi->pi_getLL('noFee', 'No fee', true);
		} else {
			$subparts['###SUBPART_FREE###'] = '';
		}
		if ($element->CurrentBasePrice->Count()>0) {
			$locMarkers['PRICE'] = $element->CurrentBasePrice;
		} else {
			$subparts['###SUBPART_PRICE###'] = '';
		}

		if (!$element->KindOfEvent)
			$subparts['###SUBPART_KIND_EVENT###'] = '';
		if (!$element->TypeEvent)
			$subparts['###SUBPART_TYPE_EVENT###'] = '';
		if ($element->Information->Count()<=0)
			$subparts['###SUBPART_INFORMATION###'] = '';
		if (!$element->Festival)
			$subparts['###SUBPART_FESTIVAL###'] = '';

		$markers = array_merge($markers, $locMarkers);
	}

	/**
	 * Include lib GMaps
	 *
	 * @return	void
	 */
	private static function includeLibGMaps() {
		if (self::$mapsIncluded)
			return;
		$file = 'https://maps.googleapis.com/maps/api/js?sensor=false';
		$tag = '	<script src="' . htmlspecialchars($file) . '" type="text/javascript"></script>' . PHP_EOL;
		$GLOBALS['TSFE']->additionalHeaderData['geoloc_map'] = $tag;
		self::$mapsIncluded = true;
	}

	/**
	 * Include lib JS
	 *
	 * @param	array		$conf: ...
	 * @param	string		$prefixId: ...
	 * @return	void
	 */
	private static function includeLibJS($conf, $prefixId) {
		if (self::$jsIncluded)
			return;
		$file = t3lib_div::resolveBackPath($GLOBALS['TSFE']->tmpl->getFileName('EXT:ics_sitlor_query/res/geoloc.js'));
		$tag = '	<script src="' . htmlspecialchars($file) . '" type="text/javascript"></script>' . PHP_EOL;
		$mapOptions = array();
		$mapOptions[] = '\'lat\': ' . $conf['latitude'];
		$mapOptions[] = '\'lng\': ' . $conf['longitude'];
		$mapOptions[] = '\'zoom\': ' . $conf['zoom'];
		$mapOptions = '{ ' . implode(',', $mapOptions) . ' }';
		$tag .= '	<script type="text/javascript">google.maps.event.addDomListener(window, "load", function() {new ics.geoloc().initMap(' . $mapOptions . ');});</script>';
		$GLOBALS['TSFE']->additionalHeaderData['geoloc'] = $tag;
		self::$jsIncluded = true;
	}

}