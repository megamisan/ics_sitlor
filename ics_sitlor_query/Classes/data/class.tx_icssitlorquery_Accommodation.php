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
 * Class 'Accomodation' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_Accomodation extends tx_icssitquery_AbstractAccomodation {
	protected $tmpAddress = array(
		'number' => '',
		'street' => '',
		'extra' => '',
		'zip' => '',
		'city' => '',
	);
	private $phones = null;

	private $currentSingleClientsRate;	// tx_icssitlorquery_ValuedTermList
	private $currentWeekRate;	// tx_icssitlorquery_ValuedTermList
	private $currentBedAndLunchRate;
	private $mobilityImpaired;

	private $coordinates = null;
	private $latitude = 0;
	private $longitude = 0;
	
	private $onlineBooking;
	private $codeBooking;
	private $photos = array();
	private $creditPhotos = array();
	
	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct() {
		$this->phones = array();
		$this->Illustration = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermTupleList');
		$this->currentSingleClientsRate = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->currentWeekRate = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
		$this->currentBedAndLunchRate = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermList');
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) {
			case 'Phones':
				return $this->phones;
			case 'CurrentSingleClientsRate':
				return $this->currentSingleClientsRate;
			case 'CurrentWeekRate':
				return $this->currentWeekRate;
			case 'CurrentBedAndLunchRate':
				return $this->currentBedAndLunchRate;
			case 'MobilityImpaired':
				return $this->mobilityImpaired;

			//-- COORDINATES
			case 'Coordinates':
				return $this->coordinates;
				
			case 'OnlineBooking':
				return $this->onlineBooking;
			
			case 'CodeBooking':
				return $this->codeBooking;
			
			default:
				return parent::__get($name);
		}

	}

	/**
	 * Defines a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @param	mixed		$value: Property's value.
	 * @return	void
	 */
	public function __set($name, $value) {
		switch ($name) {
			default:
				parent::__set($name, $value);
		}
	}
	
	/**
	 * Obtains the property list.
	 *
	 * @return	array		The list of exisiting properties.
	 */
	public function getProperties() {
		return parent::getProperties() + array('Coordinates', 'Phones', 'CurrentSingleClientsRate', 'MobilityImpaired', 'CurrentWeekRate', 'CurrentBedAndLunchRate', 'OnlineBooking', 'CodeBooking');
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
				$types = tx_icssitlorquery_NomenclatureFactory::GetTypes(array(intval($reader->readString())));
				$this->Type = $types->Get(0);
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
			$this->photos[$index] = $valuedTerm;
		}
		if (($index = array_search($valuedTerm->Criterion->ID, tx_icssitlorquery_CriterionUtils::$creditPhotos)) !== false) {
			$this->creditPhotos[$index] = $valuedTerm;
		}
		
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::RATINGSTAR || $valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::RATINGSTAR_RESIDENCE ) {	// Logiquement 1 seul de ces critères est fourni, on ne peut avoir les 2 critères renseignés pour 1 enregistrement
			$this->RatingStar = $valuedTerm;
		}
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CURRENT_SINGLE_CLIENTS_RATE)
			$this->currentSingleClientsRate->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CURRENT_WEEKRATE)
			$this->currentWeekRate->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CURRENT_BEDANDLUNCH)
			$this->currentBedAndLunchRate->Add($valuedTerm);
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::MOBILITY_IMPAIRED)
			$this->mobilityImpaired = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::ONLINE_BOOKING)
			$this->onlineBooking = $valuedTerm;
		if ($valuedTerm->Criterion->ID == tx_icssitlorquery_CriterionUtils::CODE_BOOKING)
			$this->codeBooking = $valuedTerm;
	}

	/**
	 * Process after parsing the current XML node in the XMLReader
	 *
	 * @return	void
	 */
	protected function afterParseXML() {
		$this->Address = t3lib_div::makeInstance(
			'tx_icssitquery_Address',
			$this->tmpAddress['number'],
			$this->tmpAddress['street'],
			$this->tmpAddress['extra'],
			$this->tmpAddress['zip'],
			$this->tmpAddress['city']
		);
		$this->coordinates = t3lib_div::makeInstance('tx_icssitquery_Coordinates', $this->latitude, $this->longitude);
		ksort($this->photos);

		foreach ($this->photos as $index=>$valuedTerm) {
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
		foreach ($this->creditPhotos as $index=>$valuedTerm) {
			tx_icssitlorquery_CriterionUtils::addToTupleList(
				$this->Illustration,
				$valuedTerm,
				1,
				0,
				tx_icssitlorquery_CriterionUtils::$photos[$index],
				'illustration'
			);
		}
	}

	/**
	 * Retrieves required criteria
	 *
	 * @return	array		Required criterion identifiers for object construction.
	 */
	public static function getRequiredCriteria() {
		$criteriaPhotos = array_merge(tx_icssitlorquery_CriterionUtils::$photos, tx_icssitlorquery_CriterionUtils::$creditPhotos);
		$criteria = array(
			tx_icssitlorquery_CriterionUtils::RATINGSTAR,
			tx_icssitlorquery_CriterionUtils::RATINGSTAR_RESIDENCE,
			tx_icssitlorquery_CriterionUtils::CURRENT_SINGLE_CLIENTS_RATE,
			tx_icssitlorquery_CriterionUtils::CURRENT_WEEKRATE,
			tx_icssitlorquery_CriterionUtils::CURRENT_BEDANDLUNCH,
			tx_icssitlorquery_CriterionUtils::MOBILITY_IMPAIRED,
			tx_icssitlorquery_CriterionUtils::ONLINE_BOOKING,
			tx_icssitlorquery_CriterionUtils::CODE_BOOKING,
		);
		return array_merge($criteriaPhotos, $criteria);
	}

}