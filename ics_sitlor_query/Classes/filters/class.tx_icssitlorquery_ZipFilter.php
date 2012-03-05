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
 * Class 'tx_icssitlorquery_ZipFilter' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_ZipFilter implements tx_icssitquery_IFilter {
	private $value;

	/**
	 * Constructor
	 *
	 * @param	string		$value : The zip
	 * @return	void
	 */
	public function __construct($value) {
		$this->value = $value;
	}

	/**
	 * Applies filter.
	 *
	 * @param	tx_icssitquery_IQuery		$query The query to apply the filter to.
	 * @return	void
	 */
	function apply(tx_icssitquery_IQuery $query) {
		$query->setParameter('zip', $this->value);
	}
}