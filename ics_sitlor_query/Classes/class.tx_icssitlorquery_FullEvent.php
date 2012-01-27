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

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
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
				$this->tmpPhone['phone1'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_TEL2':
				$this->tmpPhone['phone2'] =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_FAX':
				$this->fax =  $reader->readString();
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
	}
	
	/**
	 * Process after parsing the current XML node in the XMLReader
	 *
	 */
	protected function afterParseXML() {
		parent::afterParseXML();
		$this->phone = t3lib_div::makeInstance(
			'tx_icssitlorquery_Phone',
			$this->tmpPhone['phone1'], 
			$this->tmpPhone['phone2']
		);
		$this->coordinates = t3lib_div::makeInstance('tx_icssitlorquery_Coordinates', $this->latitude, $this->longitude);
	}

	/**
	 * Retrieves required criteria
	 *
	 * @return mixed : Array of criteria IDs
	 */
	public static function getRequiredCriteria() {
		$criteria = array(
		);
		return array_merge(parent::getRequiredCriteria(), $criteria);
	}	
}