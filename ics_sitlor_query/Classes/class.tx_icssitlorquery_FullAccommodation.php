<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cite Solution <technique@in-cite.net>
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
	private $phone = null;
	private $tmpPhone = array(
		'phone1' => '', 
		'phone2' => ''
	);
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
	private $providerPhone = null;
	private $tmpProviderPhone = array(
		'phone1' => '', 
		'phone2' => ''
	);
	private $providerFax;
	private $providerEmail;
	private $providerWebSite = null;
	
	private $timeTable = null;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->TimeTable = t3lib_div::makeInstance('tx_icssitlorquery_TimeTableList');
		parent::__construct();
	}

	/**
	 * Retrieves required criteria
	 *
	 * @return mixed : Array of criteria IDs
	 */
	public static function getRequiredCriteria() {
		$criteria = parent::getRequiredCriteria();
		return $criteria;
	}

	/**
	 * Retrieves properties
	 *
	 * @param	string $name : Property's name
	 *
	 * @return mixed : name 's value
	 */
	public function __get($name) {
		switch ($name) {
			//-- IDENTITITY
			case 'Phone':
				return $this->phone;
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
			case 'ProviderPhone':
				return $this->providerPhone;
			case 'ProviderFax':
				return $this->providerFax;
			case 'ProviderEmail':
				return $this->providerEmail;
			case 'ProviderWebSite':
				return $this->providerWebSite;

			//-- TIMETABLE
			case 'TimeTable':
				return $this->timeTable;
			
			default : 
				return parent::__get($name);
		}
		
	}	

	/**
	 * Set name
	 *
	 * @param	string $name : Property's name
	 * @param	mixed : Property's value
	 *
	 * @return void
	 */
	public function __set($name, $value) {
		switch ($name) {
			//-- IDENTITY
			case 'Phone':
				if (!$value instanceof tx_icssitlorquery_Phone)
					throw new Exception('Phone value must be an instance of tx_icssitlorquery_Phone.');
				$this->phone = $value;
				break;
			case 'Fax':
				$this->fax = $value;
				break;
			case 'Email':
				$this->email = $value;
				break;
			case 'WebSite':
				if (!$value instanceof tx_icssitlorquery_Link)
					throw new Exception('WebSite value must be an instance of tx_icssitlorquery_Link.');
				$this->webSite = $value;
				break;
				
			//-- COORDINATES
			case 'Coordinates':
				$this->coordinates = $value;
				break;
				
			//-- PROVIDER
			case 'ProviderName':
				if (!$value instanceof tx_icssitlorquery_Name)
					throw new Exception('ProviderName value must be an instance of tx_icssitlorquery_Name.');
				$this->providerName = $value;
				break;
			case 'ProviderAddress':
				if (!$value instanceof tx_icssitlorquery_Address)
					throw new Exception('ProviderAddress value must be an instance of tx_icssitlorquery_Address.');
				$this->providerAddress = $value;
				break;
			case 'ProviderPhone':
				if (!$value instanceof tx_icssitlorquery_Phone)
					throw new Exception('ProviderPhone value must be an instance of tx_icssitlorquery_Phone.');
				$this->providerPhone = $value;
				break;
			case 'ProviderFax':
				$this->providerFax = $value;
				break;
			case 'ProviderEmail':
				$this->providerEmail = $value;
				break;
			case 'ProviderWebSite':
				if (!$value instanceof tx_icssitlorquery_Link)
					throw new Exception('ProviderWebSite value must be an instance of tx_icssitlorquery_Link.');
				$this->providerWebSite = $value;
				break;
			
			//-- TIMETABLE
			case 'TimeTable':
				if (!$value instanceof tx_icssitlorquery_TimeTableList)
					throw new Exception('Timetable value must be an instance of tx_icssitlorquery_TimeTableList.');
				$this->timeTable = $value;
				break;
			
			default : 
				parent::__set($name, $value);
		}		
	}	

	/**
	 * Read the current XML element in the XMLReader
	 *
	 * @param	XMLReader $reader : Reader to the parsed document
	 */
	protected function readElement(XMLReader $reader) {
		switch ($reader->name) {
			//-- IDENTITY
			case 'ADRPROD_TEL':
				$this->tmpPhone['phone1'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPROD_TEL2':
				$this->tmpPhone['phone2'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPROD_FAX':
				$this->Fax =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPROD_EMAIL':
				$email =  $reader->readString();
				$this->Email = t3lib_div::makeInstance('tx_icssitlorquery_Link', $email);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPROD_URL':
				$url = $reader->readString();
				// TODO : Check whether url is valid url
				$this->WebSite =  t3lib_div::makeInstance('tx_icssitlorquery_Link', $url);
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
				$this->tmpProviderName['firstname'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
			
			case 'PREST_PRENOM_RESP':
				$this->tmpProviderName['lastname'] =  $reader->readString();
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
				$this->tmpProviderPhone['phone1'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPREST_TEL2':
				$this->tmpProviderPhone['phone2'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPREST_FAX':
				$this->ProviderFax =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPREST_EMAIL':
				$email =  $reader->readString();
				$this->ProviderEmail = t3lib_div::makeInstance('tx_icssitlorquery_Link', $email);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPREST_URL':
				$url = $reader->readString();
				// TODO : Check whether url is valid url
				$this->ProviderWebSite =  t3lib_div::makeInstance('tx_icssitlorquery_Link', $url);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
			
			//-- TIMETABLE
			case 'HORAIRES':
				$this->parseTimeTable($reader);
				break;
			
			default:
				parent::readElement($reader);
		}
	}
	
	/**
	 * Parse the current XML node in the XMLReader
	 * Parse TimeTable
	 *
	 * @param	XMLReader $reader : Reader to the parsed document
	 */
	private function parseTimeTable(XMLReader $reader) {
		$reader->read();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if($reader->nodeType == XMLReader::ELEMENT){
				switch ($reader->name) {
					case 'Horaire':
						if ($timeTable = tx_icssitlorquery_TimeTable::FromXML($reader))
							$this->TimeTable->Add($timeTable);
						break;

					default:
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
	}
	
	/**
	 * Process after parsing the current XML node in the XMLReader
	 *
	 */
	protected function afterParseXML() {
		$this->Phone = t3lib_div::makeInstance(
			'tx_icssitlorquery_Phone',
			$this->tmpPhone['phone1'], 
			$this->tmpPhone['phone2']
		);
		$this->Coordinates = t3lib_div::makeInstance('tx_icssitlorquery_Coordinates', $this->latitude, $this->longitude);
		$this->ProviderName = t3lib_div::makeInstance(
			'tx_icssitlorquery_Name', 
			$this->tmpProviderName['title'], 
			$this->tmpProviderName['firstname'],
			$this->tmpProviderName['lastname']
		);
		$this->ProviderAddress = t3lib_div::makeInstance(
			'tx_icssitlorquery_Address', 
			$this->tmpProviderAddress['number'], 
			$this->tmpProviderAddress['street'], 
			$this->tmpProviderAddress['extra'],
			$this->tmpProviderAddress['zip'], 
			$this->tmpProviderAddress['city']
		);
		$this->ProviderPhone = t3lib_div::makeInstance(
			'tx_icssitlorquery_Phone', 
			$this->tmpProviderPhone['phone1'], 
			$this->tmpProviderPhone['phone2']
		);
		
		parent::afterParseXML();
	}
}