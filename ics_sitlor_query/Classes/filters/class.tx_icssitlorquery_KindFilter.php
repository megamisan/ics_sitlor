<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In Cite Solution <technique@in-cite.net>
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
 * Class 'tx_icssitlorquery_KindFilter' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_KindFilter implements tx_icssitquery_IFilter {
	private $value = array();

	/**
	 * Constructor
	 *
	 * @param	tx_icssitlorquery_Kind		$kinds
	 * @return	void
	 */
	public function __construct(tx_icssitlorquery_KindList $kinds) {
		for ($i = 0; $i < $kinds->Count(); $i++) {
			$kind = $kinds->Get($i);
			$this->value[] = $kind->ID;
		}
	}

	/**
	 * Apply filter
	 *
	 * @param	IQuery		$query : The IQuery
	 * @return	void
	 */
	function apply(tx_icssitquery_IQuery $query) {
		$query->setParameter('kind', $this->value);
	}
}