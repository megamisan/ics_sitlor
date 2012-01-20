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
		return $accomodation;
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
				
			case 'TYPE_DE_PRODUIT':
				$types = tx_icssitlorquery_NomenclatureFactory::GetTypes(array(intval($reader->readString())));
				$this->Type = $types->Get(0);
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'COMMENTAIRE':
				$this->Description = $reader->readString();
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
			if( $xmlreader->nodeType == XMLReader::ELEMENT ){
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
	
	protected function setCriterion(tx_icssitlorquery_ValuedTerm $valuedTerm) {
		if (($indexPhoto = array_search($valuedTerm->Criterion->ID, tx_icssitlorquery_CriterionID::$photos)) !== false) {
			$cObj = t3lib_div::makeInstance('tslib_cObj');
			$valuedTerm->Value = $cObj->cImage($valuedTerm->Value, array()); // TODO: Picture object
			// CriterionUtils::addToTupleList($list, $element, $elIndex, $searchIndex, $searchedID)
			// $list=$this->Illustration; $element=$valuedTerm; $elIndex=0; $searchedIndex=1; $searchedId=tx_icssitlorquery_CriterionID::$creditPhotos[$indexPhoto]
			$tuple_exists = false;
			for ($i=0; $i<$this->Illustration->Count(); $i++) {
				$tuple = $this->Illustration->Get($i);
				if ($tuple->Item2->ID == tx_icssitlorquery_CriterionID::$creditPhotos[$indexPhoto]) {//TODO: Test existance Item2.
					$tuple_exists = true;
					$tuple->Set(0, $valuedTerm);
					$this->Illustration->Set($i, $tuple);
					break;
				}
			}
			if (!$tuple_exists) {
				$tuple = makeInstance('tx_icssitlorquery_ValuedTermTuple', 2);
				$tuple->Set(0, $valuedTerm);
				$this->Illustration->Add($tuple);
			}			
			
		}
		if (($indexCredit = array_search($valuedTerm->Criterion->ID, tx_icssitlorquery_CriterionID::$creditPhotos)) !== false) {
			// $list=$this->Illustration; $element=$valuedTerm; $elIndex=1; $searchedIndex=0; $searchedId=tx_icssitlorquery_CriterionID::$photos[$indexCredit]
			$tuple_exists = false;
			for ($i=0; $i<$this->Illustration->Count(); $i++) {
				$tuple = $this->Illustration->Get($i);
				if ($tuple->Item1->ID == tx_icssitlorquery_CriterionID::$photos[$indexCredit]) {//TODO: Test existance Item1.
					$tuple_exists = true;
					$tuple->Set(1, $valuedTerm);
					$this->Illustration->Set($i, $tuple);
					break;
				}
			}
			if (!$tuple_exists) {
				$tuple = makeInstance('tx_icssitlorquery_ValuedTermTuple', 2);
				$tuple->Set(1, $valuedTerm);
				$this->Illustration->Add($tuple);
			}			
		}
	}
		
	public static function getRequiredCriteria() {
		return array_merge(
			tx_icssitlorquery_CriterionID::$photos, 
			tx_icssitlorquery_CriterionID::$creditPhotos
		);
	}
	
}