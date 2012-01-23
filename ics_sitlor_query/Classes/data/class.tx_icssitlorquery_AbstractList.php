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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Abstract Class 'tx_icssitlorquery_AbstractList' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

abstract class tx_icssitlorquery_AbstractList implements tx_icssitquery_IToString {

	private $elements = array();	// Array of stdClass
	
	/**
	 * Constructor
	 *
	 */
	protected function __construct(tx_icssitlorquery_AbstractList $source = null) {
		if (isset($source)) {
			$this->elements = $source->elements;
			// for ($i=0; $i<$source->Count(); $i++) {
				// $this->_Add($source->Get($i));
			// }
		}
	}
	
	/**
	 * Add element in the list
	 *
	 * @param	stdClass $element : The element to add
	 * @return void
	 */
	protected function _Add($element) {
		$this->elements[] = $element;
	}
	
	/**
	 * Remove element in the list
	 *
	 * @param	stdClass $element : The element to remove
	 * @return void
	 */
	protected function _Remove($element) {
		$this->removeAt(array_search($element, $this->elements));
	}
	
	/**
	 * Remove element at position
	 *
	 * @param	int $position : The position for element to remove
	 * @return void
	 */
	public function RemoveAt($position) {
		if (!is_int($position) || $position<0 || $position >= count($this->elements)) {
			tx_icssitquery_Debug::error('Position not an integer or out of range, position ' . $position . ' given.');
			return;
		}
		array_splice($this->elements, $position, 1);
	}
	
	/**
	 * Retrieves
	 *
	 * @param	int $position: The position of element
	 *
	 * @return stdClass
	 */
	public function Get($position) {
		if (!is_int($position) || $position<0) {
			tx_icssitquery_Debug::error('Position must be positive int or 0, position ' . $position . ' given.');
			return;
		}
		if ($position >= count($this->elements))
			return null;
		return $this->elements[$position];
	}
	
	/**
	 * Set element at positon
	 *
	 * @param	int $position
	 * @param	stdClass $element
	 */
	protected function _Set($position, $element) {
		if (!is_int($position) || $position<0 || $position >= count($this->elements)) {
			tx_icssitquery_Debug::error('Position not an integer or out of range, position ' . $position . ' given.');
			return;
		}
		$this->elements[$position] = $element;
	}
	
	/**
	 * Retrieves size of list
	 *
	 * @return int The size of list
	 */
	public function Count() {
		return count($this->elements);
	}
	
	/**
	 * Convert object to display as string
	 * @return string
	 */
	public function __toString() {
		$separator = ',';
		
		$args = func_get_args();
		if (!empty($args) && is_string($args[0])) {
			$separator = $args[0];
		}
		return implode($separator, $this->elements);
	}
	
	public function toString() {
	}
	
}