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
 * Class 'tx_icssitlorquery_CategoryList' for the 'ics_sitlor_query' extension.
 * Represents a list of category.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_CategoryList extends tx_icssitlorquery_AbstractList {

	/**
	 * Initializes the list.
	 * Optionaly copy the elements from another list.
	 *
	 * @param	tx_icssitlorquery_CategoryList $source: The source list to copy. Optional.
	 * @return	void
	 */
	public function __construct(tx_icssitlorquery_CategoryList $source=null) {
		parent::__construct($source);
	}

	/**
	 * Adds an element to the list.
	 *
	 * @param	tx_icssitlorquery_Category		$element: Element to add.
	 * @return	void
	 */
	public function Add(tx_icssitlorquery_Category $element) {
		parent::_Add($element);
	}

	/**
	 * Removes an element from the list.
	 * Only the first occurence is removed.
	 *
	 * @param	tx_icssitlorquery_Category		$element: Element to remove.
	 * @return	void
	 */
	public function Remove(tx_icssitlorquery_Category $element) {
		parent::_Remove($element);
	}

	/**
	 * Defines an element in the list.
	 * The position must exists.
	 *
	 * @param	int		$position: Position of the element to define.
	 * @param	tx_icssitlorquery_Category		$element: Element to define.
	 * @return	void
	 */
	public function Set($position, tx_icssitlorquery_Category $element) {
		parent::_Set($position, $element);
	}

}