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
 * Interface 'KeywordFilter' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_KeywordFilter implements tx_icssitquery_IFilter {
	private $value;

	/**
	 * Constructor
	 *
	 * @param	string $value : Keywords
	 */
	public function __construct($value) {
		if (!is_string($value))
			throw new Exception('Keywords must be string.');
		$this->value = $value;
	}
	
	/**
	 * Apply filter
	 *
	 * @param	IQuery $query : The IQuery
	 *
	 * @return void
	 */
	function apply(tx_icssitquery_IQuery $query) {
		$query->setParameter('keyword', $this->value);
	}
}