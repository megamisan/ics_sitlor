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
 * Class 'tx_icssitlorquery_Term' for the 'ics_sitlor_query' extension.
 * Represents a Term.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_Term implements tx_icssitquery_IToString {
	private $id;
	private $name;
	private $count;
	private $order;

	/**
	 * Initializes the object. Not callable from external code.
	 *
	 * @return	void
	 */
	private function __construct() {
	}

	/**
	 * Reads a Term from its XML representation.
	 *
	 * @param	XMLReader		$reader: The XML reader on the root element of the Term.
	 * @return	tx_icssitlorquery_Term		The parsed term instance.
	 */
	public static function FromXML(XMLReader $reader) {
		$term = new tx_icssitlorquery_Term();
		$reader->read();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				switch ($reader->name) {
					case 'MODALITE':
						$term->id = intval($reader->readString());
						break;
					case 'MODALITE_NOM':
						$term->name = $reader->readString();
						break;
					case 'MODALITE_ORDRE':
						$term->order = intval($reader->readString());
						break;
					case 'MODALITE_CPTE':
						$term->count = intval($reader->readString());
						break;
				}
				tx_icssitlorquery_XMLTools::skipChildren($reader);
			}
			$reader->read();
		}
		return $term;
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) 	{
			case 'ID':
				return $this->id;
			case 'Name':
				return $this->name;
			case 'Order':
				return $this->order;
			case 'Count':
				return $this->count;
			default :
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Converts this object to its string representation. PHP magic function.
	 *
	 * @return	string		Representation of the object.
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Converts this object to its string representation.
	 * Use the name of this term.
	 *
	 * @return	string		Representation of the object.
	 */
	public function toString() {
		return $this->name;
	}
}