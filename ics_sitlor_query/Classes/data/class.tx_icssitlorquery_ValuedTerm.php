<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 In Cite Solution <technique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the ValuedTerms of the GNU General Public License as published by
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
	 * Private constructor ?
	 *
	 * @return	void
	 */
	private function __construct() {
	}

	/**
	 * Retrieves ValuedTerm from XML
	 *
	 * @param	tx_icssitquery_ValuedTermList $ValuedTerms
	 * @return ValuedTerm
	 */
	public static function FromXML(XMLReader $reader) {
		$valuedTerm = new tx_icssitlorquery_ValuedTerm();

		$valuedTerm->criterion = tx_icssitlorquery_CriterionFactory::GetCriterion(intval($reader->getAttribute('CLEF_CRITERE')));
		$terms = tx_icssitlorquery_CriterionFactory::GetCriterionTerms($valuedTerm->Criterion);
		for ($i=0; $i<$terms->Count(); $i++) {
			$term = $terms->Get($i);
			if ($term->ID == intval($reader->getAttribute('CLEF_MODA'))) {
				$valuedTerm->term = $term;
				break;
			}
		}
		$valuedTerm->value = $reader->readString();
		tx_icssitlorquery_XMLTools::skipChildren($reader);

		return $valuedTerm;
	}

	/**
	 * Retrieves properties
	 *
	 * @param	string $name : Property's name
	 * @return name 's value
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
				tx_icssitquery_debug::notice('Undefined property of ValuedTerm via __get(): ' . $name);
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
				tx_icssitquery_debug::notice('Undefined property of ValuedTerm via __set(): ' . $name);
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
	 * Convert object to display as string
	 *
	 * @return string
	 */
	public function __toString() {
		// TODO : cObj local
		// Test number of args and call appropriate function

		return $this->toString();
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	public function toString() {
	}

}