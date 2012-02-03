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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Class 'tx_icssitlorquery_ValuedTerm' for the 'ics_sitlor_query' extension.
 * Represents a ValuedTerm.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_ValuedTerm implements tx_icssitquery_IToString {
	private $criterion;	// tx_icssitlorquery_Criterion
	private $term;		// tx_icssitlorquery_Term
	private $value;

	private static $lConf = array();

	/**
	 * Initializes the object. Not callable from external code.
	 *
	 * @return	void
	 */
	private function __construct() {
		$this->criterion = null;
		$this->term = null;
		$this->value = null;
	}

	/**
	 * Reads a ValuedTerm from its XML representation.
	 *
	 * @param	XMLReader		$reader: The XML reader on the root element of the ValuedTerm.
	 * @return	tx_icssitlorquery_ValuedTerm		The parsed valued term instance.
	 */
	public static function FromXML(XMLReader $reader) {
		$valuedTerm = new tx_icssitlorquery_ValuedTerm();

		$valuedTerm->criterion = tx_icssitlorquery_CriterionFactory::GetCriterion(intval($reader->getAttribute('CLEF_CRITERE')));
		$valuedTerm->term = tx_icssitlorquery_CriterionFactory::GetCriterionTerm($valuedTerm->Criterion, intval($reader->getAttribute('CLEF_MODA')));
		$valuedTerm->value = $reader->readString();
		tx_icssitlorquery_XMLTools::skipChildren($reader);

		return $valuedTerm;
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) 	{
			case 'Criterion':
				return $this->criterion;
			case 'Term':
				return $this->term;
			case 'Value':
				return $this->value;
			default :
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Set name
	 *
	 * @param	string $name : Property's name
	 * @param	mixed : Property's value
	 * @return void
	 */
	public function __set($name, $value) {
		switch ($name) 	{
			case 'Criterion':
			case 'Term':
				tx_icssitquery_debug::notice('Read-only property of ValuedTerm via __set(): ' . $name);
				break;
			case 'Value':
				$this->value = $value;
				break;
			default :
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}


	/**
	 * Set default conf
	 *
	 * @param	array $conf
	 * @return void
	 */
	public static function SetDefaultConf(array $conf) {
		self::$lConf = $conf;
	}

	/**
	 * Converts this object to its string representation. PHP magic function.
	 *
	 * @return	string		Representation of the object.
	 */
	public function __toString() {
		// TODO : cObj local
		// Test number of args and call appropriate function

		return $this->toString();
	}

	/**
	 * Converts this object to its string representation.
	 *
	 * @return	string		Representation of the object.
	 */
	public function toString() {
		if (isset($this->value))
			return $this->value;
		if (isset($this->term))
			return $this->term;
		return $this->criterion;
	}
}