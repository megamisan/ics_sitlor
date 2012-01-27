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
 * Class 'tx_icssitlorquery_StartValidFilter' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_StartValidFilter implements tx_icssitquery_IFilter {
	private $value=0;

	/**
	 * Constructor
	 *
	 * @param	int timestamp $value : The start valid
	 */
	public function __construct($value) {
		if (is_int($value))
			$this->value = $value;
		else
			tx_icssitquery_Debug::warning('Start valid ' . $value . ' is not a timestamp.');
	}

	/**
	 * Apply filter
	 *
	 * @param	IQuery $query : The IQuery
	 *
	 * @return void
	 */
	function apply(tx_icssitquery_IQuery $query) {
		$query->setParameter('startValid', $this->value);
	}
}