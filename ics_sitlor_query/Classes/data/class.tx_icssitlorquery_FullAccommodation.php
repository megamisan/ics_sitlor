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
 * Interface 'FullAccomodation' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_FullAccomodation extends tx_icssitlorquery_Accomodation {
	private $fax;
	private $email;
	private $webSite;

	private $providerName = null;
	private $tmpProviderName = array(
		'title' => '',
		'firstname' => '',
		'lastname' => ''
	);
	private $providerAddress = null;
	private $tmpProviderAddress = array(
		'number' => '',
		'street' => '',
		'extra' => '',
		'zip' => '',
		'city' => ''
	);
	private $providerPhones = null;
	private $providerFax;
	private $providerEmail;
	private $providerWebSite = null;

	private $timeTable = null;

	private $receptionLanguage;		// tx_icssitlorquery_ValuedTermList
	private $reservationLanguage;	// tx_icssitlorquery_ValuedTermList
	private $pets;
	private $allowedPets;
	private $allowedGroup;
	private $receptionGroup;	// tx_icssitlorquery_ValuedTermList
	private $motorCoachPark;
	private $opening24_24;

	private $comfortRoom;			// tx_icssitlorquery_ValuedTermList
	private $hotelEquipement;		// tx_icssitlorquery_ValuedTermList
	private $hotelService;			// tx_icssitlorquery_ValuedTermList

	private $documentationLanguage;	// tx_icssitlorquery_ValuedTermList
	private $campingCapacity;		// tx_icssitlorquery_ValuedTermList
	private $campingArea;
	private $campingEquipment;		// tx_icssitlorquery_ValuedTermList
	private $campingService;		// tx_icssitlorquery_ValuedTermList
	private $campingCar_equipment_service;	// tx_icssitlorquery_ValuedTermList
	
	private $guesthouseDescription;	// tx_icssitlorquery_ValuedTermList
	private $guesthouseComfort;		// tx_icssitlorquery_ValuedTermList
	private $guesthouseService;		// tx_icssitlorquery_ValuedTermList
	private $outsideEquipment;		// tx_icssitlorquery_ValuedTermList
	
	private $furnishedType;
	private $furnishedDescription;	// tx_icssitlorquery_ValuedTermList
	private $furnishedComfort;		// tx_icssitlorquery_ValuedTermList
	private $furnishedService;		// tx_icssitlorquery_ValuedTermList
	private $furnishedHeating;		// tx_icssitlorquery_ValuedTermList
	private $furnishedSmoker;
	
	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct() {
		parent::__construct();

		$this->providerPhones = array();
		$this->timeTable = t3lib_div::makeInstance('tx_icssitlorquery_TimeTableList');
		$this->receptionLanguage = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->reservationLanguage = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->receptionGroup = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->comfortRoom = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->hotelEquipement = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->hotelService = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->documentationLanguage = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->campingCapacity = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->campingEquipment = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->campingService = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->campingCar_equipment_service = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->guesthouseDescription = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->guesthouseComfort = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->guesthouseService = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->outsideEquipment = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->furnishedDescription = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->furnishedComfort = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->furnishedService = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->furnishedHeating = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) {
			//-- IDENTITY
			case 'Fax':
				return $this->fax;
			case 'Email':
				return $this->email;
			case 'WebSite':
				return $this->webSite;

			//-- PROVIDER
			case 'ProviderName':
				return $this->providerName;
			case 'ProviderAddress':
				return $this->providerAddress;
			case 'ProviderPhones':
				return $this->providerPhones;
			case 'ProviderFax':
				return $this->providerFax;
			case 'ProviderEmail':
				return $this->providerEmail;
			case 'ProviderWebSite':
				return $this->providerWebSite;

			//-- TIMETABLE
			case 'TimeTable':
				return $this->timeTable;

			//-- RECEPTION
			case 'ReceptionLanguage':
				return $this->receptionLanguage;
			case 'ReservationLanguage':
				return $this->reservationLanguage;
			case 'Pets':
				return $this->pets;
			case 'AllowedPets':
				return $this->allowedPets;
			case 'AllowedGroup':
				return $this->allowedGroup;
			case 'ReceptionGroup':
				return $this->receptionGroup;
			case 'MotorCoachPark':
				return $this->motorCoachPark;
			case 'Opening24_24':
				return $this->opening24_24;

			case 'ComfortRoom':
				return $this->comfortRoom;
			case 'HotelEquipement':
				return $this->hotelEquipement;
			case 'HotelService':
				return $this->hotelService;

			case 'DocumentationLanguage':
				return $this->documentationLanguage;
			case 'CampingCapacity':
				return $this->campingCapacity;
			case 'CampingEquipment':
				return $this->campingEquipment;
			case 'CampingService':
				return $this->campingService;
			case 'CampingCar_equipment_service':
				return $this->campingCar_equipment_service;
			case 'CampingArea':
				return $this->campingArea;
				
			case 'GuesthouseDescription':
				return $this->guesthouseDescription;
			case 'GuesthouseComfort':
				return $this->guesthouseComfort;
			case 'GuesthouseService':
				return $this->guesthouseService;
			case 'OutsideEquipment':
				return $this->outsideEquipment;

			case 'FurnishedType':
				return $this->furnishedType;
			case 'FurnishedDescription':
				return $this->furnishedDescription;
			case 'FurnishedComfort':
				return $this->furnishedComfort;
			case 'FurnishedService':
				return $this->furnishedService;
			case 'FurnishedHeating':
				return $this->furnishedHeating;
			case 'FurnishedSmoker':
				return $this->furnishedSmoker;
				
			default:
				return parent::__get($name);
		}

	}
	
	/**
	 * Obtains the property list.
	 *
	 * @return	array		The list of exisiting properties.
	 */
	public function getProperties() {
		return parent::getProperties() + array('Fax', 'Email', 'WebSite', 
			'ProviderName', 'ProviderAddress', 'ProviderPhones', 
			'ProviderFax', 'ProviderEmail', 'ProviderWebSite', 'TimeTable', 
			'ReceptionLanguage', 'ReservationLanguage', 'Pets', 
			'AllowedPets', 'AllowedGroup', 'ReceptionGroup', 'MotorCoachPark', 
			'Opening24_24', 'ComfortRoom', 'HotelEquipement', 'HotelService',
			'DocumentationLanguage', 'CampingCapacity', 'CampingEquipment',
			'CampingService', 'CampingCar_equipment_service', 'CampingArea',
			'GuesthouseDescription', 'GuesthouseComfort', 'GuesthouseService', 'OutsideEquipment',
			'FurnishedType','FurnishedDescription','FurnishedComfort','FurnishedService','FurnishedHeating','FurnishedSmoker');
	}

	/**
	 * Read the current XML element in the XMLReader
	 *
	 * @param	XMLReader		$reader : Reader to the parsed document
	 * @return	void
	 */
	protected function readElement(XMLReader $reader) {
		switch ($reader->name) {
			//-- IDENTITY
			case 'ADRPROD_FAX':
				$this->fax = t3lib_div::makeInstance(
					'tx_icssitquery_Phone',
					$reader->readString()
				);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_EMAIL':
				$email = $reader->readString();
				$this->email = t3lib_div::makeInstance('tx_icssitquery_Link', $email);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_URL':
				$url = $reader->readString();
				// TODO : Check whether url is valid url
				$this->webSite =  t3lib_div::makeInstance('tx_icssitquery_Link', $url);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			//-- PROVIDER
			case 'PREST_CIVILITE':
				$this->tmpProviderName['title'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'PREST_NOM_RESP':
				$this->tmpProviderName['lastname'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'PREST_PRENOM_RESP':
				$this->tmpProviderName['firstname'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_NUM_VOIE' :
				$this->tmpProviderAddress['number'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_LIB_VOIE' :
				$this->tmpProviderAddress['street'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_COMPL_ADRESSE':
				$this->tmpProviderAddress['extra'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_CP' :
				$this->tmpProviderAddress['zip'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_LIBELLE_COMMUNE' :
				$this->tmpProviderAddress['city'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_TEL':
				array_unshift($this->providerPhones, t3lib_div::makeInstance(
					'tx_icssitquery_Phone',
					$reader->readString()
				));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_TEL2':
				array_push($this->providerPhones, t3lib_div::makeInstance(
					'tx_icssitquery_Phone',
					$reader->readString()
				));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_FAX':
				$this->providerFax = t3lib_div::makeInstance(
					'tx_icssitquery_Phone',
					$reader->readString()
				);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_EMAIL':
				$email = $reader->readString();
				$this->providerEmail = t3lib_div::makeInstance('tx_icssitquery_Link', $email);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_URL':
				$url = $reader->readString();
				// TODO : Check whether url is valid url
				$this->providerWebSite =  t3lib_div::makeInstance('tx_icssitquery_Link', $url);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			//-- TIMETABLE
			case 'HORAIRES':
				$this->parseTimeTable($reader);
				break;

			default:	//-- CRITERION and other
				parent::readElement($reader);
		}
	}

	/**
	 * Parse the current XML node in the XMLReader
	 * Parse TimeTable
	 *
	 * @param	XMLReader		$reader : Reader to the parsed document
	 * @return	void
	 */
	private function parseTimeTable(XMLReader $reader) {
		$reader->read();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if($reader->nodeType == XMLReader::ELEMENT){
				switch ($reader->name) {
					case 'Horaire':
						if ($timeTable = tx_icssitlorquery_TimeTable::FromXML($reader))
							$this->timeTable->Add($timeTable);
						break;

					default:
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
	}

	/**
	 * Set criterion
	 *
	 * @param	tx_icssitlorquery_ValuedTerm		$valuedTerm
	 * @return	void
	 */
	protected function setCriterion(tx_icssitlorquery_ValuedTerm $valuedTerm) {
		parent::setCriterion($valuedTerm);

		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::RECEPTION_LANGUAGE)
			$this->receptionLanguage->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::RESERVATION_LANGUAGE)
			$this->reservationLanguage->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::PETS)
			$this->pets = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::ALLOWED_PETS)
			$this->allowedPets = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::ALLOWED_GROUP)
			$this->allowedGroup = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::RECEPTION_GROUP)
			$this->receptionGroup->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK)
			$this->motorCoachPark = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::OPENING_24_24)
			$this->opening24_24 = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::COMFORT_ROOM)
			$this->comfortRoom->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::HOTEL_EQUIPMENT)
			$this->hotelEquipement->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::HOTEL_SERVICE)
			$this->hotelService->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::DOCUMENTATION_LANGUAGE)
			$this->documentationLanguage->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CAMPING_CAPACITY)
			$this->campingCapacity->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CAMPING_EQUIPMENT)
			$this->campingEquipment->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CAMPING_SERVICE)
			$this->campingService->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CAMPINGCAR_EQUIPMENT_SERVICE)
			$this->campingCar_equipment_service->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CAMPING_AREA)
			$this->campingArea = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::GUESTHOUSE_DESCRIPTION)
			$this->guesthouseDescription->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::GUESTHOUSE_COMFORT)
			$this->guesthouseComfort->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::GUESTHOUSE_SERVICE)
			$this->guesthouseService->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::OUTSIDE_EQUIPMENT)
			$this->outsideEquipment->Add($valuedTerm);

		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::FURNISHED_TYPE)
			$this->furnishedType = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::FURNISHED_DESCRIPTION)
			$this->furnishedDescription->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::FURNISHED_COMFORT)
			$this->furnishedComfort->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::FURNISHED_SERVICE)
			$this->furnishedService->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::FURNISHED_HEATING)
			$this->furnishedHeating->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::FURNISHED_SMOKER)
			$this->furnishedSmoker = $valuedTerm;
				
	}

	/**
	 * Process after parsing the current XML node in the XMLReader
	 *
	 * @return	void
	 */
	protected function afterParseXML() {
		parent::afterParseXML();
		$this->providerName = t3lib_div::makeInstance(
			'tx_icssitquery_Name',
			$this->tmpProviderName['title'],
			$this->tmpProviderName['firstname'],
			$this->tmpProviderName['lastname']
		);
		$this->providerAddress = t3lib_div::makeInstance(
			'tx_icssitquery_Address',
			$this->tmpProviderAddress['number'],
			$this->tmpProviderAddress['street'],
			$this->tmpProviderAddress['extra'],
			$this->tmpProviderAddress['zip'],
			$this->tmpProviderAddress['city']
		);
	}

	/**
	 * Retrieves required criteria
	 *
	 * @return	mixed		Array of required criteria IDs
	 */
	public static function getRequiredCriteria() {
		$criteria = array(
			tx_icssitlorquery_CriterionUtils::RECEPTION_LANGUAGE,
			tx_icssitlorquery_CriterionUtils::RESERVATION_LANGUAGE,
			tx_icssitlorquery_CriterionUtils::PETS,
			tx_icssitlorquery_CriterionUtils::ALLOWED_PETS,
			tx_icssitlorquery_CriterionUtils::ALLOWED_GROUP,
			tx_icssitlorquery_CriterionUtils::RECEPTION_GROUP,
			tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK,
			tx_icssitlorquery_CriterionUtils::OPENING_24_24,
			tx_icssitlorquery_CriterionUtils::COMFORT_ROOM,
			tx_icssitlorquery_CriterionUtils::HOTEL_EQUIPMENT,
			tx_icssitlorquery_CriterionUtils::HOTEL_SERVICE,
			tx_icssitlorquery_CriterionUtils::DOCUMENTATION_LANGUAGE,
			tx_icssitlorquery_CriterionUtils::CAMPING_CAPACITY,
			tx_icssitlorquery_CriterionUtils::CAMPING_AREA,
			tx_icssitlorquery_CriterionUtils::CAMPING_EQUIPMENT,
			tx_icssitlorquery_CriterionUtils::CAMPING_SERVICE,
			tx_icssitlorquery_CriterionUtils::CAMPINGCAR_EQUIPMENT_SERVICE,
			tx_icssitlorquery_CriterionUtils::GUESTHOUSE_DESCRIPTION,
			tx_icssitlorquery_CriterionUtils::GUESTHOUSE_COMFORT,
			tx_icssitlorquery_CriterionUtils::GUESTHOUSE_SERVICE,
			tx_icssitlorquery_CriterionUtils::OUTSIDE_EQUIPMENT,
			tx_icssitlorquery_CriterionUtils::FURNISHED_TYPE,
			tx_icssitlorquery_CriterionUtils::FURNISHED_DESCRIPTION,
			tx_icssitlorquery_CriterionUtils::FURNISHED_COMFORT,
			tx_icssitlorquery_CriterionUtils::FURNISHED_SERVICE,
			tx_icssitlorquery_CriterionUtils::FURNISHED_HEATING,
			tx_icssitlorquery_CriterionUtils::FURNISHED_SMOKER,
		);
		return array_merge(parent::getRequiredCriteria(), $criteria);
	}


}