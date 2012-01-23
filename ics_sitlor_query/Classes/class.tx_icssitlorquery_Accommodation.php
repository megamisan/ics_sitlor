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
 * Class 'Accomodation' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_Accomodation extends tx_icssitquery_AbstractAccomodation {
	private $roadNumber = '';
	private $roadName = '';

	public function __construct() {
		$this->Illustration = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermTupleList');
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
			default : 
				parent::__set($name, $value);
		}		
	}	
	
	/**
	 * Parse the current XML node in the XMLReader
	 *
	 * @param	XMLReader $reader : Reader to the parsed document
	 */
	public function parseXML(XMLReader $reader) {
		$reader->read();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				$this->readElement($reader);
			}
			$reader->read();
		}
	}
	
	/**
	 * Read the current XML element in the XMLReader
	 *
	 * @param	XMLReader $reader : Reader to the parsed document
	 */
	protected function readElement(XMLReader $reader) {
		switch ($reader->name) {
			case 'PRODUIT':
				$this->ID = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'NOM':	
				$this->Name = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'COMMENTAIRE':
				$this->Description = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'TYPE_DE_PRODUIT':
				$types = tx_icssitlorquery_NomenclatureFactory::GetTypes(array(intval($reader->readString())));
				$this->Type = $types->Get(0);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_NUM_VOIE' :
				$this->roadNumber = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				$this->Address = $this->roadNumber . ' ' . $this->roadName;
				break;
				
			case 'ADRPROD_LIB_VOIE' :
				$this->roadName = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				$this->Address = $this->roadNumber . ' ' . $this->roadName;
				break;
				
			case 'ADRPROD_CP' :
				$this->Zip = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;
				
			case 'ADRPROD_LIBELLE_COMMUNE' :
				$this->City = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'CRITERES':
				$this->parseCriteria($reader);
				break;

			default :
				tx_icssitlorquery_XMLTools::skipChildren($reader);
		}
	}
			
	/**
	 * Parse the current XML node in the XMLReader
	 * Parse criteria
	 *
	 * @param	XMLReader $reader : Reader to the parsed document
	 */
	protected function parseCriteria(XMLReader $reader) {
		$reader->read();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if($reader->nodeType == XMLReader::ELEMENT){
				switch ($reader->name) {
					case 'Crit':
						if (!$reader->isEmptyElement) {
							$valuedTerm = tx_icssitlorquery_ValuedTerm::FromXML($reader);
							$this->setCriterion($valuedTerm);
						}
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
		if (($indexPhoto = array_search($valuedTerm->Criterion->ID, tx_icssitlorquery_CriterionUtils::$photos)) !== false) {
			$valuedTerm->Value = t3lib_div::makeInstance('tx_icssitlorquery_Picture', $valuedTerm->Value);
			CriterionUtils::addToTupleList(
				$this->Illustration, 
				$valuedTerm, 
				0, 
				1, 
				tx_icssitlorquery_CriterionUtils::$creditPhotos[$indexPhoto]
			);
		}
		if (($indexCredit = array_search($valuedTerm->Criterion->ID, tx_icssitlorquery_CriterionUtils::$creditPhotos)) !== false) {
			CriterionUtils::addToTupleList(
				$this->Illustration, 
				$valuedTerm, 
				1, 
				0, 
				tx_icssitlorquery_CriterionUtils::$photos[$indexCredit]
			);
		}
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::RATINGSTAR) {
			$this->RatingStar = $valuedTerm;
		}
	}
	
	/**
	 * Retrieves required criteria
	 *
	 * @return mixed : Array of criteria IDs
	 */
	public static function getRequiredCriteria() {
		$criteria = array_merge(tx_icssitlorquery_CriterionUtils::$photos, tx_icssitlorquery_CriterionUtils::$creditPhotos);
		$criteria = array_merge($criteria, array(tx_icssitlorquery_CriterionUtils::RATINGSTAR));
		return $criteria;
	}
	
}