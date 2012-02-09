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
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @param	mixed		$value: Property's value.
	 * @return	void
	 */
	public function __set($name, $value) {
		switch ($name) 	{
			case 'Criterion':
			case 'Term':
				tx_icssitquery_debug::notice('Read-only property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
				break;
			case 'Value':
				$this->value = $value;
				break;
			default :
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Sets default TypoScript configuration.
	 *
	 * @param	array		$conf: The new default configuration.
	 * @return	void
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
		switch (func_num_args()) {
			case 0:
				return $this->toString();
			default:
				return call_user_func_array(array($this, 'toStringConf'), func_get_args());
		}
	}

	/**
	 * Converts this object to its string representation.
	 * Using default output settings.
	 *
	 * @return	string		Representation of the object.
	 */
	public function toString() {
		return $this->toStringConf(self::$lConf);
	}

	/**
	 * Converts this object to its string representation.
	 * Uses the specified TypoScript configuration.
	 * Data fields:
	 * * criterion: String representation of the criterion.
	 * * criterionID: Id of the criterion.
	 * * term: String representation of the term.
	 * * termValue: Value of the term.
	 * * value: Local value of the element, if applicable.
	 * TypoScript special elements:
	 * * value_conf: The rendering configuration of an item if it is an object
	 *   implementing IToStringConf or IToStringObjConf.
	 *
	 * @remarks The rendering is done in two pass. First, the value is rendered
	 * using its specified configuration and not configured data, if supported.
	 * Finally, stdWrap is called on the updated data to give the final value.
	 *
	 * @param	array		$conf: TypoScript configuration to use to render this object.
	 * @return	string		Representation of the object.
	 */
	public function toStringConf(array $conf) {
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$data = array(
			'criterion' => $this->criterion,
			'criterionId' => $this->criterion->ID,
			'term' => $this->term,
			'termValue' => $this->term->Name,
			'value' => $this->value,
			'valueType' => is_object($this->value) ? get_class($this->value) : gettype($this->value),
		);
		$local_cObj->start($data, 'ValuedTerm');
		if (($this->value != null) && is_object($this->value) && isset($conf['value_conf.'])) {
			if ($this->value instanceof IToStringObjConf) {
				$data['value'] = $this->value->toStringObjConf($local_cObj, $conf);
			}
			else if ($this->value instanceof IToStringConf) {
				$data['value'] = $this->value->toStringConf($conf);
			}
		}
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$local_cObj->start($data, 'ValuedTerm');
		return $local_cObj->stdWrap('', $conf);
	}
}