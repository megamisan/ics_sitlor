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
 * Interface 'FullRestaurant' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_FullRestaurant extends tx_icssitlorquery_Restaurant {
	private $phones = null;
	private $fax;
	private $email;
	private $webSite;

	private $coordinates = null;
	private $latitude = 0;
	private $longitude = 0;

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

	private $class;					// tx_icssitlorquery_ValuedTermList

	private $receptionLanguage;		// tx_icssitlorquery_ValuedTermList
	private $menuLanguage;			// tx_icssitlorquery_ValuedTermList
	private $pets;
	private $allowedPets;
	private $allowedGroup;
	private $allowedGroupNumber;	// tx_icssitlorquery_ValuedTermList
	private $motorCoachPark;
	// private $serviceOpen;			// tx_icssitlorquery_ValuedTermList

	private $capacity;				// tx_icssitlorquery_ValuedTermList

	private $currentSaleFormula;			// tx_icssitlorquery_ValuedTermList
	private $currentCartePrice;				// tx_icssitlorquery_ValuedTermList


	/**
	 * Initializes the FullRestaurant.
	 *
	 * @return	void
	 */
	public function __construct() {
		parent::__construct();
		$this->phones = array();
		$this->providerPhones = array();
		$this->class = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->receptionLanguage = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->menuLanguage = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->allowedGroupNumber = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		// $this->serviceOpen = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->capacity = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->currentSaleFormula = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->currentCartePrice = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
	}

	/**
	 * Retrieves properties
	 *
	 * @param	string		$name : Property's name
	 * @return	mixed		: name 's value
	 */
	public function __get($name) {
		switch ($name) {
			//-- IDENTITITY
			case 'Phones':
				return $this->phones;
			case 'Fax':
				return $this->fax;
			case 'Email':
				return $this->email;
			case 'WebSite':
				return $this->webSite;

			//-- COORDINATES
			case 'Coordinates':
				return $this->coordinates;

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

			//-- CLASS
			case 'Class':
				return $this->class;

			//-- RECEPTION
			case 'ReceptionLanguage':
				return $this->receptionLanguage;
			case 'MenuLanguage':
				return $this->menuLanguage;
			case 'Pets':
				return $this->pets;
			case 'AllowedPets':
				return $this->allowedPets;
			case 'AllowedGroup':
				return $this->allowedGroup;
			case 'AllowedGroupNumber':
				return $this->allowedGroupNumber;
			case 'MotorCoachPark':
				return $this->motorCoachPark;
			// case 'ServiceOpen':
				// return $this->serviceOpen;

			//-- CAPACITY
			case 'Capacity':
				return $this->capacity;

			//-- PRICE
			case 'CurrentSaleFormula':
				return $this->currentSaleFormula;
			case 'CurrentCartePrice':
				return $this->currentCartePrice;
			// case 'CurrentMenuPrice':
				// return $this->currentMenuPrice;

			default :
				return parent::__get($name);
		}
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
			case 'ADRPROD_TEL':
				array_unshift($this->phones, t3lib_div::makeInstance(
					'tx_icssitlorquery_Phone',
					$reader->readString()
				));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_TEL2':
				array_push($this->phones, t3lib_div::makeInstance(
					'tx_icssitlorquery_Phone',
					$reader->readString()
				));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_FAX':
				$this->fax = t3lib_div::makeInstance(
					'tx_icssitlorquery_Phone',
					$reader->readString()
				);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_EMAIL':
				$email = $reader->readString();
				$this->email = t3lib_div::makeInstance('tx_icssitlorquery_Link', $email);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_URL':
				$url = $reader->readString();
				// TODO : Check whether url is valid url
				$this->webSite =  t3lib_div::makeInstance('tx_icssitlorquery_Link', $url);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			//-- COORDINATES
			case 'LATITUDE':
				$this->latitude =  floatval(str_replace(',', '.', $reader->readString()));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'LONGITUDE':
				$this->longitude =  floatval(str_replace(',', '.', $reader->readString()));
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
					'tx_icssitlorquery_Phone',
					$reader->readString()
				));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_TEL2':
				array_push($this->providerPhones, t3lib_div::makeInstance(
					'tx_icssitlorquery_Phone',
					$reader->readString()
				));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_FAX':
				$this->providerFax = t3lib_div::makeInstance(
					'tx_icssitlorquery_Phone',
					$reader->readString()
				);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_EMAIL':
				$email = $reader->readString();
				$this->providerEmail = t3lib_div::makeInstance('tx_icssitlorquery_Link', $email);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPREST_URL':
				$url = $reader->readString();
				// TODO : Check whether url is valid url
				$this->providerWebSite =  t3lib_div::makeInstance('tx_icssitlorquery_Link', $url);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			default:	//-- CRITERION and other
				parent::readElement($reader);
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
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::RESTAURANT_CLASS)
			$this->class->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::RECEPTION_LANGUAGE)
			$this->receptionLanguage->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::MENU_LANGUAGE)
			$this->menuLanguage->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::PETS)
			$this->pets = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::ALLOWED_PETS)
			$this->allowedPets = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::ALLOWED_GROUP)
			$this->allowedGroup = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::ALLOWED_GROUP_NUMBER)
			$this->allowedGroupNumber->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK)
			$this->motorCoachPark = $valuedTerm;
		// if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::SERVICEOPEN)
			// $this->serviceOpen->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::RESTAURANT_CAPACITY)
			$this->capacity->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CURRENT_SALE_FORMULA)
			$this->currentSaleFormula->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CURRENT_CARTE_PRICE)
			$this->currentCartePrice->Add($valuedTerm);
		// if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CURRENT_MENU_PRICE)
			// $this->currentMenuPrice->Add($valuedTerm);
	}

	/**
	 * Process after parsing the current XML node in the XMLReader
	 *
	 * @return	[type]		...
	 */
	protected function afterParseXML() {
		parent::afterParseXML();
		$this->coordinates = t3lib_div::makeInstance('tx_icssitlorquery_Coordinates', $this->latitude, $this->longitude);
		$this->providerName = t3lib_div::makeInstance(
			'tx_icssitlorquery_Name',
			$this->tmpProviderName['title'],
			$this->tmpProviderName['firstname'],
			$this->tmpProviderName['lastname']
		);
		$this->providerAddress = t3lib_div::makeInstance(
			'tx_icssitlorquery_Address',
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
			tx_icssitlorquery_CriterionUtils::RESTAURANT_CLASS,
			tx_icssitlorquery_CriterionUtils::RECEPTION_LANGUAGE,
			tx_icssitlorquery_CriterionUtils::MENU_LANGUAGE,
			tx_icssitlorquery_CriterionUtils::PETS,
			tx_icssitlorquery_CriterionUtils::ALLOWED_PETS,
			tx_icssitlorquery_CriterionUtils::ALLOWED_GROUP,
			tx_icssitlorquery_CriterionUtils::ALLOWED_GROUP_NUMBER,
			tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK,
			// tx_icssitlorquery_CriterionUtils::SERVICEOPEN,
			tx_icssitlorquery_CriterionUtils::RESTAURANT_CAPACITY,
			tx_icssitlorquery_CriterionUtils::CURRENT_SALE_FORMULA,
			tx_icssitlorquery_CriterionUtils::CURRENT_CARTE_PRICE,
			// tx_icssitlorquery_CriterionUtils::CURRENT_MENU_PRICE,
		);
		return array_merge(parent::getRequiredCriteria(), $criteria);
	}

}