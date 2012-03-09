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
 * Class 'AbstractData' for the 'ics_sit_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitquery
 */

abstract class tx_icssitquery_AbstractData {
	private $id; // ID
	private $name; // Name/Title
	private $description; // Description
	private $type;		// Type of the element, a definition of its categorization
	private $address;	// Address: an Address object with street, zip, city, ...
	private $illustration;	// Illustrations

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) {
			case 'ID':
				return $this->id;
			case 'Name':
				return $this->name;
			case 'Description':
				return $this->description;
			case 'Type':
				return $this->type;
			case 'Address':
				return $this->address;
			case 'Illustration':
				return $this->illustration;
			default :
				tx_icssitquery_debug::notice('Undefined property via __get(): ' . $name);
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
			case 'ID':
				$this->id = $value;
			break;
			case 'Name':
				$this->name = $value;
			break;
			case 'Description':
				$this->description = $value;
			break;
			case 'Type':
				$this->type = $value;
			break;
			case 'Address':
				if (!$value instanceof tx_icssitquery_Address) {
				}
				$this->address = $value;
			break;
			case 'Illustration':
				$this->illustration = $value;
			break;
			default :
				tx_icssitquery_debug::notice('Undefined property via __set(): ' . $name);
		}
	}
	
	/**
	 * Obtains the property list.
	 *
	 * @return	array		The list of exisiting properties.
	 */
	public function getProperties() {
		return array(
			'ID',
			'Name',
			'Description',
			'Type',
			'Address',
			'Illustration',
		);
	}
}
