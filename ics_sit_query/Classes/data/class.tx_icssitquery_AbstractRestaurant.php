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
 * Class 'AbstractRestaurant' for the 'ics_sit_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitquery
 */
abstract class tx_icssitquery_AbstractRestaurant extends tx_icssitquery_AbstractData {
	private $chainAndLabel;	// Restaurant chain or label

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		switch ($name) {
			case 'ChainAndLabel':
				return $this->chainAndLabel;
			default :
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
			case 'ChainAndLabel':
				$this->chainAndLabel = $value;
			break;
			default :
				parent::__set($name, $value);
		}
	}
	
	/**
	 * Obtains the property list.
	 *
	 * @return	array		The list of exisiting properties.
	 */
	public function getProperties() {
		return parent::getProperties() + array('ChainAndLabel');
	}
}
