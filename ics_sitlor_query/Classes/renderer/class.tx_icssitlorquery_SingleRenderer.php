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
		$locMarkers['GENERIC'] = $this->renderDetailGeneric($element, $markers);
		$locMarkers['SPECIFIC'] = $this->renderDetailSpecific($element, $markers);
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
	function renderDetailGeneric($element, &$markers) {
		if (!($element instanceof tx_icssitquery_AbstractData))
			return '';
		$locMarkers = array(
			'TITLE' => $element->Name,
			'TYPE' => $element->Type,
			'ADDRESS' => $element->Address,
			'PHONE' => $element->Phones,
			'FAX' => $element->Fax,
			'MAIL' => $element->Email,
			'WEBSITE' => $element->WebSite,
			'ILLUSTRATION' => $element->Illustration,
			'DESCRIPTION' => $element->Description,
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
	function renderDetailSpecific($element, &$markers) {
		if (!($element instanceof tx_icssitquery_AbstractData))
			return '';

		$locMarkers = array();
		if ($element instanceof tx_icssitlorquery_Accomodation) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_ACCOMODATION###');
			$locMarkers = array(
				'PROVIDER_NAME' => $element->ProviderName,
				'PROVIDER_ADDRESS' => $element->ProviderAddress,
				'PROVIDER_PHONE' => $element->ProviderPhones,
				'PROVIDER_FAX' => $element->ProviderFax,
				'PROVIDER_MAIL' => $element->ProviderEmail,
				'PROVIDER_WEBSITE' => $element->ProviderWebSite,
				'TIMETABLE' => $element->TimeTable,
				'RECEPTION_LANGUAGE' => $element->ReceptionLanguage,
				'RESERVATION_LANGUAGE' => $element->ReservationLanguage,
				'MOBILITY_IMPAIRED' => $element->MobilityImpaired,
				'PETS' => $element->Pets,
				'ALOWED_PETS' => $element->AllowedPets,
				'ALLOWED_GROUP' => $element->AllowedGroup,
				'RECEPTION_GROUP' => $element->ReceptionGroup,
				'MOTORCOACH_PARK' => $element->MotorCoachPark,
				'OPENING24_24' => $element->Opening24_24,
				'SINGLE_CLIENT_PRICE' => $element->CurrentSingleClientsRate,
				'COMFORT_ROOM' => $element->ComfortRoom,
				'HOTEL_EQUIPMENT' => $element->HotelEquipement,
				'HOTEL_SERVICE' => $element->HotelService,
			);
		}
		if ($element instanceof tx_icssitlorquery_Restaurant) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_RESTAURANT###');
			$locMarkers = array(
				'PROVIDER_NAME' => $element->ProviderName,
				'PROVIDER_ADDRESS' => $element->ProviderAddress,
				'PROVIDER_PHONE' => $element->ProviderPhones,
				'PROVIDER_FAX' => $element->ProviderFax,
				'PROVIDER_MAIL' => $element->ProviderEmail,
				'PROVIDER_WEBSITE' => $element->ProviderWebSite,
				'RESTAURANT_CLASS' => $element->Class,
				'RECEPTION_LANGUAGE' => $element->ReceptionLanguage,
				'MENU_LANGUAGE' => $element->MenuLanguage,
				'PETS' => $element->Pets,
				'ALOWED_PETS' => $element->AllowedPets,
				'ALLOWED_GROUP' => $element->AllowedGroup,
				'ALLOWED_GROUP_NUMBER' => $element->AllowedGroupNumber,
				'MOTORCOACH_PARK' => $element->MotorCoachPark,
				'SERVICE_OPEN' => $element->ServiceOpen,
				'CAPACITY' => $element->Capacity,
				'SALE_FORMULA' => $element->CurrentSaleFormula,
				'CARTE_PRICE' => $element->CurrentCartePrice,
				'MENU_PRICE' => $element->CurrentMenuPrice,
			);
		}
		if ($element instanceof tx_icssitlorquery_Event) {
			$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DETAIL_EVENT###');
			$locMarkers = array(
				'KIND_EVENT' => $element->KindOfEvent,
				'TYPE_EVENT' => $element->TypeEvent,
				'INFORMATION' => $element->Information,
				'FESTIVAL' => $element->Festival,
				'FREE' => $element->CurrentFree,
				'PRICE' => $element->CurrentBasePrice,
			);
		}
		$markers = array_merge($markers, $locMarkers);
		return $template;
	}

}