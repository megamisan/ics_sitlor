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
 *
 *
 *   50: class tx_icssitlorquery_Category implements tx_icssitquery_IToString
 *   60:     private function __construct()
 *   70:     public static function FromXML(XMLReader $reader, tx_icssitlorquery_TypeList $types)
 *  108:     public function __get($name)
 *  128:     public function __toString()
 *  138:     public function toString()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitlorquery_Category' for the 'ics_sitlor_query' extension.
 * Represents a Category.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_Category implements tx_icssitquery_IToString {
	private $id;
	private $name;
	private $count;

	/**
	 * Initializes the object. Not callable from external code.
	 *
	 * @return	void
	 */
	private function __construct() {
	}

	/**
	 * Reads a Category from its XML representation.
	 *
	 * @param	XMLReader		$reader: The XML reader on the root element of the Category.
	 * @param	tx_icssitlorquery_TypeList		$types: The type list where to put the inner parsed types.
	 * @return	tx_icssitlorquery_Category		The parsed category instance.
	 */
	public static function FromXML(XMLReader $reader, tx_icssitlorquery_TypeList $types) {
		$category = new tx_icssitlorquery_Category();
		$reader->read();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				switch ($reader->name) {
					case 'CATEGORIE':
						$category->id = intval($reader->readString());
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'NomCat':
						$category->name = $reader->readString();
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'CpteCcat':
						$category->count = intval($reader->readString());
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'Types':
						if ($type = tx_icssitlorquery_Type::FromXML($reader)) {
							$types->Add($type);
						}
						break;
					default :
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
		return $category;
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
			case 'Count':
				return $this->count;
			case 'Types':
				return tx_icssitlorquery_NomenclatureFactory::getCategoryTypes($this);
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
	 * Use the name of this category.
	 *
	 * @return	string		Representation of the object.
	 */
	public function toString() {
		return $this->name;
	}

}