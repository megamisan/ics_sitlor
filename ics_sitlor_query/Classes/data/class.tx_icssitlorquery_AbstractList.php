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
 * Abstract Class 'tx_icssitlorquery_AbstractList' for the 'ics_sitlor_query' extension.
 * Represents a list of elements.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
abstract class tx_icssitlorquery_AbstractList implements tx_icssitquery_IToString {
	private $elements = array();	/**< List elements */
	private static $separator = ',';	/**< Default list separator for string representation. */

	/**
	 * Initializes the list.
	 * Optionaly copy the elements from another list.
	 *
	 * @param	tx_icssitlorquery_AbstractList		$source: The source list to copy. Optional.
	 * @return	void
	 */
	protected function __construct(tx_icssitlorquery_AbstractList $source = null) {
		if ($source != null) {
			$this->elements = $source->elements;
		}
	}

	/**
	 * Adds an element to the list.
	 *
	 * @param	stdClass		$element: Element to add.
	 * @return	void
	 */
	protected function _Add($element) {
		$this->elements[] = $element;
	}

	/**
	 * Removes an element from the list.
	 * Only the first occurence is removed.
	 *
	 * @param	stdClass		$element: Element to remove.
	 * @return	void
	 */
	protected function _Remove($element) {
		$this->removeAt(array_search($element, $this->elements));
	}

	/**
	 * Removes the element at the specified position from the list.
	 *
	 * @param	int		$position: Position of the element to remove.
	 * @return void
	 */
	public function RemoveAt($position) {
		if (!is_int($position) || ($position < 0) || ($position >= count($this->elements))) {
			tx_icssitquery_Debug::error('Position not an integer or out of range, position ' . $position . ' given.');
			return;
		}
		array_splice($this->elements, $position, 1);
	}

	/**
	 * Obtains an element from the list.
	 *
	 * @param	int		$position: Position of element to obtain.
	 * @return	stdClass		The element at the specified poisition if exists.
	 */
	public function Get($position) {
		if (!is_int($position) || ($position < 0)) {
			tx_icssitquery_Debug::error('Position must be positive int or 0, position ' . $position . ' given.');
			return;
		}
		if ($position >= count($this->elements))
			return null;
		return $this->elements[$position];
	}

	/**
	 * Defines an element in the list.
	 * The position must exists.
	 *
	 * @param	int		$position: Position of the element to define.
	 * @param	stdClass		$element: Element to define.
	 * @return	void
	 */
	protected function _Set($position, $element) {
		if (!is_int($position) || ($position < 0) || ($position >= count($this->elements))) {
			tx_icssitquery_Debug::error('Position not an integer or out of range, position ' . $position . ' given.');
			return;
		}
		$this->elements[$position] = $element;
	}

	/**
	 * Obtains the size of the list.
	 *
	 * @return	int		The size of the list.
	 */
	public function Count() {
		return count($this->elements);
	}

	/**
	 * Converts this object to its string representation. PHP magic function.
	 *
	 * @return	string		Representation of the object.
	 */
	public function __toString() {
		$args = func_get_args();
		if (!empty($args) && is_string($args[0])) {
			return $this->toString($args[0]);
		}
		return $this->toString();
	}

	/**
	 * Converts this object to its string representation.
	 * Takes an unspecified parameter to define the element separator. It defaults to a comma.
	 *
	 * @return	string		Representation of the object.
	 */
	public function toString() {
		$separator = self::$separator;

		$args = func_get_args();
		if (!empty($args) && is_string($args[0])) {
			$separator = $args[0];
		}
		return implode($separator, $this->elements);
	}

	/**
	 * Defines the default element separator for string representation.
	 *
	 * @param	string		$separator The new separator.
	 * @return	void
	 */
	public static function setSeparator($separator) {
		if (is_string($separator)) {
			self::$separator = $separator;
		}
	}
}