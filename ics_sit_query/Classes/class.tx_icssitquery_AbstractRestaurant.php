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
 * Class 'AbstractRestaurant' for the 'ics_sit_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitquery
 */
 
abstract class tx_icssitquery_AbstractRestaurant extends tx_icssitquery_AbstractData{
	private $type;		// Type of restaurant
	private $category;	// Category of restaurant
	private $address;	// Address of restaurant
	private $zip;		// Location zip
	private $city;		// City
	private $illustration;	// Illustration of restaurant
	private $chainAndLabel;	// Restaurant chain or label
	
	/**
	 * Retrieves properties
	 *
	 * @param	string $name : Property's name
	 *
	 * @return mixed : name 's value
	 */
	public function __get($name) {
		switch ($name) {
			case 'Type':
				return $this->type;
			case 'Category':
				return $this->category;
			case 'Address':
				return $this->address;
			case 'Zip';
				return $this->zip;
			case 'City':
				return $this->city;
			case 'Illustration':
				return $this->illustration;
			case 'ChainAndLabel':
				return $this->chainAndLabel;
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
			case 'Type':
				$this->type = $value;
			break;
			case 'Category':
				$this->category = $value;
			break;
			case 'Address':
				$this->address = $value;
			break;
			case 'Zip':
				$this->zip = $value;
			break;
			case 'City':
				$this->city = $value;
			break;
			case 'Illustration':
				$this->illustration = $value;
			break;
			case 'ChainAndLabel':
				$this->chainAndLabel = $value;
			break;
			default :
				parent::__set($name, $value);
		}
	}	
}
