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
***************************************************************/


/**
 * Class 'GenericRecord' for the 'ics_sitlor_query' extension.
 *
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_GenericRecord extends tx_icssitquery_AbstractData {
	protected $tmpAddress = array(
		'number' => '',
		'street' => '',
		'extra' => '',
		'zip' => '',
		'city' => '',
	);
	protected $criteriaValues;
	private static $additionalCriteria = array();

	private $phones = null;
	private $fax;
	private $email;
	private $webSite;

	private $coordinates = null;
	private $latitude = 0.0;
	private $longitude = 0.0;

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct() {
		$this->Illustration = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermTupleList');
		$this->criteriaValues = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->phones = array();
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
		return parent::getProperties() + array('Phones', 'Fax', 'Email', 'WebSite', 
			'Coordinates');
	}

	/**
	 * Parse the current XML node in the XMLReader
	 *
	 * @param	XMLReader		$reader : Reader to the parsed document
	 * @return	void
	 */
	public function parseXML(XMLReader $reader) {
		$reader->read();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				$this->readElement($reader);
			}
			$reader->read();
		}
		$this->afterParseXML();
	}

	/**
	 * Read the current XML element in the XMLReader
	 *
	 * @param	XMLReader		$reader : Reader to the parsed document
	 * @return	void
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
				$this->Type = tx_icssitlorquery_NomenclatureFactory::GetType(intval($reader->readString()));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_NUM_VOIE' :
				$this->tmpAddress['number'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_LIB_VOIE' :
				$this->tmpAddress['street'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_COMPL_ADRESSE':
				$this->tmpAddress['extra'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_CP' :
				$this->tmpAddress['zip'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_LIBELLE_COMMUNE' :
				$this->tmpAddress['city'] = $reader->readString();
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			//-- IDENTITY
			case 'ADRPROD_TEL':
				array_unshift($this->phones, t3lib_div::makeInstance(
					'tx_icssitquery_Phone',
					$reader->readString()
				));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'ADRPROD_TEL2':
				array_push($this->phones, t3lib_div::makeInstance(
					'tx_icssitquery_Phone',
					$reader->readString()
				));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

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

			//-- COORDINATES
			case 'LATITUDE':
				$this->latitude =  floatval(str_replace(',', '.', $reader->readString()));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'LONGITUDE':
				$this->longitude =  floatval(str_replace(',', '.', $reader->readString()));
				tx_icssitlorquery_XMLTools::skipChildren($reader);
				break;

			case 'CRITERES':
				if (!$reader->isEmptyElement)
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
	 * @param	XMLReader		$reader : Reader to the parsed document
	 * @return	void
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
	 * @param	tx_icssitlorquery_ValuedTerm		$valuedTerm
	 * @return	void
	 */
	protected function setCriterion(tx_icssitlorquery_ValuedTerm $valuedTerm) {
		if (($index = array_search($valuedTerm->Criterion->ID, tx_icssitlorquery_CriterionUtils::$photos)) !== false) {
			$valuedTerm->Value = t3lib_div::makeInstance('tx_icssitquery_Picture', $valuedTerm->Value);
			tx_icssitlorquery_CriterionUtils::addToTupleList(
				$this->Illustration,
				$valuedTerm,
				0,
				1,
				tx_icssitlorquery_CriterionUtils::$creditPhotos[$index],
				'illustration'
			);
		}
		if (($index = array_search($valuedTerm->Criterion->ID, tx_icssitlorquery_CriterionUtils::$creditPhotos)) !== false) {
			tx_icssitlorquery_CriterionUtils::addToTupleList(
				$this->Illustration,
				$valuedTerm,
				1,
				0,
				tx_icssitlorquery_CriterionUtils::$photos[$index],
				'illustration'
			);
		}
		$this->criteriaValues->Add($valuedTerm);
	}

	/**
	 * Process after parsing the current XML node in the XMLReader
	 *
	 * @return	void
	 */
	protected function afterParseXML() {
		$this->coordinates = t3lib_div::makeInstance('tx_icssitquery_Coordinates', $this->latitude, $this->longitude);
		$this->Address = t3lib_div::makeInstance(
			'tx_icssitquery_Address',
			$this->tmpAddress['number'],
			$this->tmpAddress['street'],
			$this->tmpAddress['extra'],
			$this->tmpAddress['zip'],
			$this->tmpAddress['city']
		);
	}

	/**
	 * Retrieves required criteria
	 *
	 * @return	array		Required criterion identifiers for object construction.
	 */
	public static function getRequiredCriteria() {
		return array_merge(tx_icssitlorquery_CriterionUtils::$photos, tx_icssitlorquery_CriterionUtils::$creditPhotos, self::$additionalCriteria);
	}
	
	public static function setAdditionalCriteria(array $criteria) {
		self::$additionalCriteria = $criteria;
	}

	public function hasCriterion(tx_icssitlorquery_Criterion $criterion) {
		for ($i = 0; $i < $this->criteriaValues->Count(); $i++) {
			if ($this->criteriaValues->Get($i)->Criterion->ID == $criterion->ID) {
				return true;
			}
		}
		return false;
	}
	
	public function getTerms(tx_icssitlorquery_Criterion $criterion) {
		$terms = t3lib_div::makeInstance('tx_icssitlorquery_TermList');
		for ($i = 0; $i < $this->criteriaValues->Count(); $i++) {
			if ($this->criteriaValues->Get($i)->Criterion->ID == $criterion->ID) {
				$terms->Add($this->criteriaValues->Get($i)->Term);
			}
		}
		return $terms;
	}
	
	public function getValuedTerms(tx_icssitlorquery_Criterion $criterion) {
		$valuedTerms = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		for ($i = 0; $i < $this->criteriaValues->Count(); $i++) {
			if ($this->criteriaValues->Get($i)->Criterion->ID == $criterion->ID) {
				$valuedTerms->Add($this->criteriaValues->Get($i));
			}
		}
		return $valuedTerms;
	}
}