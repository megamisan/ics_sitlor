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
 * Interface 'FullEvent' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_FullEvent extends tx_icssitlorquery_Event {
	private $phones = null;
	private $fax;
	private $email;
	private $webSite;

	private $coordinates = null;
	private $latitude = 0;
	private $longitude = 0;

	private $kindOfEvent;
	private $typeEvent;
	private $information;	// tx_icssitlorquery_ValuedTermList
	private $festival;
	
	private $currentFree;
	private $currentBasePrice;	// tx_icssitlorquery_ValuedTermList
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->phones = array();
		$this->information = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->currentBasePrice = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
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

			case 'KindOfEvent':
				return $this->kindOfEvent;
			case 'TypeEvent':
				return $this->typeEvent;
			case 'Information':
				return $this->information;
			case 'Festival':
				return $this->festival;
				
			case 'CurrentFree':
				return $this->currentFree;
			case 'CurrentBasePrice':
				return $this->currentBasePrice;
				
			default : 
				return parent::__get($name);
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


			default:	//-- CRITERION and other
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
	 * @param	tx_icssitlorquery_ValuedTerm $valuedTerm
	 */
	protected function setCriterion(tx_icssitlorquery_ValuedTerm $valuedTerm) {
		parent::setCriterion($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT)
			$this->kindOfEvent = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::TYPE_EVENT)
			$this->typeEvent = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::COMPLEMENTARY_INFORMATION)
			$this->information->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::LORRAINE_FESTIVAL)
			$this->festival = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CURRENT_FREE)
			$this->currentFree = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CURRENT_BASE_PRICE)
			$this->currentBasePrice->Add($valuedTerm);
	}
	
	/**
	 * Process after parsing the current XML node in the XMLReader
	 *
	 */
	protected function afterParseXML() {
		parent::afterParseXML();
		$this->coordinates = t3lib_div::makeInstance('tx_icssitlorquery_Coordinates', $this->latitude, $this->longitude);
	}

	/**
	 * Retrieves required criteria
	 *
	 * @return mixed : Array of criteria IDs
	 */
	public static function getRequiredCriteria() {
		$criteria = array(
			tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT,
			tx_icssitlorquery_CriterionUtils::TYPE_EVENT,
			tx_icssitlorquery_CriterionUtils::COMPLEMENTARY_INFORMATION,
			tx_icssitlorquery_CriterionUtils::LORRAINE_FESTIVAL,
			tx_icssitlorquery_CriterionUtils::CURRENT_FREE,
			tx_icssitlorquery_CriterionUtils::CURRENT_BASE_PRICE,
		);
		return array_merge(parent::getRequiredCriteria(), $criteria);
	}	
}