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
	private function renderGeneric($element, &$markers) {
		if (!($element instanceof tx_icssitquery_AbstractData))
			return '';
		$locMarkers = array(
				// Identity
			'IDENTITY_LABEL' => $this->pi->pi_getLL('identity', 'Identity', true),
			'TITLE' => $element->Name,
			'TYPE' => $element->Type,
			'ADDRESS' => $element->Address,
			'PHONE' => $this->pi->renderPhones($element->Phones),
			'FAX' => $this->pi->renderFax($element->Fax),
			'MAIL' => $element->Email,
			'WEBSITE' => $element->WebSite,
			'ILLUSTRATION' => $element->Illustration->__toString(' '),
				//Description
			'DESCRIPTION_LABEL' => $this->pi->pi_getLL('description', 'Description', true),
			'DESCRIPTION' => nl2br(htmlspecialchars($element->Description)),
				// Coordinates
			'COORDINATES_LABEL' => $this->pi->pi_getLL('coordinates', 'Coordinates', true),
			'COORDINATES' => $element->Coordinates,
		);
		$markers = array_merge($markers, $locMarkers);
		return $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_GENERIC###');
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

		$locMarkers = array();
		if ($element instanceof tx_icssitlorquery_Accomodation) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_ACCOMODATION###');
			$locMarkers = $this->getMarkers_Accomodation($element);
		}
		if ($element instanceof tx_icssitlorquery_Restaurant) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_RESTAURANT###');
			$locMarkers = $this->getMarkers_Restaurant($element);
		}
		if ($element instanceof tx_icssitlorquery_Event) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_EVENT###');
			$locMarkers = $this->getMarkers_Event($element);
		}
		$markers = array_merge($markers, $locMarkers);
		return $template;
	}

	/**
	 * Retrieves Accomodation markers
	 *
	 * @param	tx_icssitlorquery_Accomodation	$element: Accomodation
	 * @return	mixed	Markers array
	 */
	private function getMarkers_Accomodation($element) {
		return array(
				// Provider
			'PROVIDER_LABEL' => $this->pi->pi_getLL('provider', 'Provider', true),
			'PROVIDER_NAME' => $element->ProviderName,
			'PROVIDER_ADDRESS' => $element->ProviderAddress,
			'PROVIDER_PHONE' => $this->pi->renderPhones($element->ProviderPhones),
			'PROVIDER_FAX' => $this->pi->renderFax($element->ProviderFax),
			'PROVIDER_MAIL' => $element->ProviderEmail,
			'PROVIDER_WEBSITE' => $element->ProviderWebSite,
				// Timetable
			'TIMETABLE_LABEL' => $this->pi->pi_getLL('timetable', 'Timetable', true),
			'TIMETABLE' => $element->TimeTable,
				// Rating
			'HOTEL_RATING_LABEL' => $this->pi->pi_getLL('rating_star', 'Rating', true),
			'HOTEL_RATING' => $element->RatingStar,
				// Reception
			'RECEPTION_LABEL' => $this->pi->pi_getLL('reception', 'Reception', true),
			'RECEPTION_LANGUAGE' => $element->ReceptionLanguage,
			'RESERVATION_LANGUAGE' => $element->ReservationLanguage,
			'MOBILITY_IMPAIRED' => $element->MobilityImpaired,
			'PETS' => $element->Pets,
			'ALOWED_PETS' => $element->AllowedPets,
			'ALLOWED_GROUP' => $element->AllowedGroup,
			'RECEPTION_GROUP' => $element->ReceptionGroup,
			'MOTORCOACH_PARK' => $element->MotorCoachPark,
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
	}

	/**
	 * Retrieves Restaurant markers
	 *
	 * @param	tx_icssitlorquery_Restaurantn	$element: Restaurant
	 * @return	mixed	Markers array
	 */	
	private function getMarkers_Restaurant($element) {
		return array(
				// Provider
			'PROVIDER_LABEL' => $this->pi->pi_getLL('provider', 'Provider', true),
			'PROVIDER_NAME' => $element->ProviderName,
			'PROVIDER_ADDRESS' => $element->ProviderAddress,
			'PROVIDER_PHONE' => $element->ProviderPhones,
			'PROVIDER_FAX' => $element->ProviderFax,
			'PROVIDER_MAIL' => $element->ProviderEmail,
			'PROVIDER_WEBSITE' => $element->ProviderWebSite,
				// Class
			'RESTAURANT_CLASS_LABEL' => $this->pi->pi_getLL('restaurant_class', 'Class of restaurant', true),
			'RESTAURANT_CLASS' => $element->Class,
				// Reception
			'RECEPTION_LABEL' => $this->pi->pi_getLL('reception', 'Reception', true),
			'RECEPTION_LANGUAGE' => $element->ReceptionLanguage,
			'MENU_LANGUAGE' => $element->MenuLanguage,
			'PETS' => $element->Pets,
			'ALOWED_PETS' => $element->AllowedPets,
			'ALLOWED_GROUP' => $element->AllowedGroup,
			'ALLOWED_GROUP_NUMBER' => $element->AllowedGroupNumber,
			'MOTORCOACH_PARK' => $element->MotorCoachPark,
			'SERVICE_OPEN' => $element->ServiceOpen,
				// Capacity
			'CAPACITY_LABEL' => $this->pi->pi_getLL('capacity', 'Capacity', true),
			'CAPACITY' => $element->Capacity,
				// Price
			'PRICE_LABEL' => $this->pi->pi_getLL('price', 'Price', true),
			'SALE_FORMULA' => $element->CurrentSaleFormula,
			'CARTE_PRICE' => $element->CurrentCartePrice,
			'MENU_PRICE' => $element->CurrentMenuPrice,
		);
	}
	
	/**
	 * Retrieves Event markers
	 *
	 * @param	tx_icssitlorquery_Event	$element: Event
	 * @return	mixed	Markers array
	 */
	private function getMarkers_Event($element) {
		return array(
			'EVENT_INFORMATION' => $this->pi->pi_getLL('event_infos', 'Event informations', true),
			'KIND_EVENT' => $element->KindOfEvent,
			'TYPE_EVENT' => $element->TypeEvent,
			'INFORMATION' => $element->Information,
			'FESTIVAL' => $element->Festival,
				// Price
			'PRICE_LABEL' => $this->pi->pi_getLL('price', 'Price', true),
			'FREE' => $element->CurrentFree,
			'PRICE' => $element->CurrentBasePrice,
		);
	}
}