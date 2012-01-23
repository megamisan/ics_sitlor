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
	private $phone;
	private $fax;
	private $email;
	private $webSite;

	private $coordinates = null;
	private $latitude = 0;
	private $longitude = 0;
	
	public function __construct() {
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
			case 'Phone':
				return $this->phone;
			case 'Fax':
				return $this->fax;
			case 'Email':
				return $this->email;
			case 'WebSite':
				return $this->webSite;
			case 'Coordinates':
				return $this->coordinates;
				
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
			case 'Phone':
				$this->phone = $value;
				break;
			case 'Fax':
				$this->fax = $value;;
			case 'Email':
				$this->email = $value;
			case 'WebSite':
				$this->webSite = $value;
			case 'Coordinates':
				$this->coordinates = $value;
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
			case 'ADRPROD_TEL':
				$this->Phone =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPROD_FAX':
				$this->Fax =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPROD_EMAIL':
				$this->Email =  $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPROD_URL':
				$url = $reader->readString();
				// TODO : Check whether url is valid url
				$this->WebSite =  t3lib_div::makeInstance('tx_icssitlorquery_Link', $url);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'LATITUDE':
				$this->latitude =  floatval(str_replace(',', '.', $reader->readString()));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'LONGITUDE':
				$this->longitude =  floatval(str_replace(',', '.', $reader->readString()));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			default:
				parent::readElement($reader);
		}
	}
	
	protected function afterParseXML() {
		$this->Coordinates = t3lib_div::makeInstance('tx_icssitlorquery_Coordinates', $this->latitude, $this->longitude);
		parent::afterParseXML();
	}
}