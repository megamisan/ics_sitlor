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
 * Interface 'IQueryService' for the 'ics_sit_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitquery
 */
interface tx_icssitquery_IQueryService {
	/**
	 * Set pager
	 *
	 * @param	int		$page: The page
	 * @param	int		$size: Size of elements
	 * @return	void
	 */
	public function setPager($page, $size);

	/**
	 * Reset filters
	 *
	 * @return	void
	 */
	public function resetFilters();

	/**
	 * Add filter
	 *
	 * @param	tx_icssitquery_IFilter $filter: The filter
	 * @return	void
	 */
	public function addFilter(tx_icssitquery_IFilter $filter);

	/**
	 * Retrieves the last query
	 *
	 * @return	tx_icssitquery_IQuery
	 */
	public function getLastQuery();

	/**
	 * Retrieves Records
	 *
	 * @param	tx_icssitquery_ISortingProvider $sorting: The sorting
	 * @return	mixed		array of Records
	 */
	public function getRecords(tx_icssitquery_ISortingProvider $sorting=null);

	/**
	 * Retrieves Accomodations
	 *
	 * @param	ISortingProvider $sorting: The sorting
	 * @return	mixed		array of Accomodations
	 */
	public function getAccomodations(tx_icssitquery_ISortingProvider $sorting=null);

	/**
	 * Retrieves Restaurants
	 *
	 * @param	ISortingProvider $sorting: The sorting
	 * @return	mixed		array of Restaurants
	 */
	public function getRestaurants(tx_icssitquery_ISortingProvider $sorting=null);

	/**
	 * Retrieves Events
	 *
	 * @param	ISortingProvider $sorting: The sorting
	 * @return	mixed		array of Events
	 */
	public function getEvents(tx_icssitquery_ISortingProvider $sorting=null);

}