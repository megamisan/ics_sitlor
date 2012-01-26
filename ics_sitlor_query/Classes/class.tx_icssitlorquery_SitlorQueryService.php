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
 * Class 'SitlorQueryService' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_SitlorQueryService implements tx_icssitquery_IQueryService {
	private $login;		// The login
	private $password;	// The password
	private $url;		// The url request
	private $page;		// The page
	private $pageSize;	// The page size
	private $filters = array();	// Array of IFilters
			
	/**
	 * Constructor
	 *
	 * @param	string $login : The login
	 * @param	string $password : The password
	 * @param	string $url : The url
	 *
	 */
	function __construct($login, $password, $url) {
		$this->login = $login;
		$this->password = $password;
		$this->url = $url;		
	}
	
	
	
	/**
	 * Set pager
	 *
	 * @param	int $page : The page
	 * @param	int $size : Size of elements
	 *
	 * @return void	 
	 */
	public function setPager($page, $size) { // TODO: Type checking (int)
		$this->page = $page;
		$this->pageSize = $size;
	}
	
	/**
	 * Reset filters
	 *
	 * @return void
	 */
	public function resetFilters() {
		$this->filters = array();
	}
	
	/**
	 * Add filter
	 *
	 * @param	IFilter $filter : The filter
	 */
	public function addFilter(tx_icssitquery_IFilter $filter) {
		$this->filters[] = $filter;
	}
	
	/**
	 * Retrieves the last query
	 *
	 * @return IQuery
	 */
	public function getLastQuery() {
		return $this->query;
	}
	
	/**
	 * Retrieves Accomodations
	 *
	 * @param	ISortingProvider $sorting : The sorting
	 *
	 * @return mixed : array of Accomodations
	 */
	public function getAccomodations(tx_icssitquery_ISortingProvider $sorting) {
		$this->query = t3lib_div::makeInstance('tx_icssitlorquery_SitlorQuery', $this->login, $this->password, $this->url);
		
		$full = false;
		foreach ($this->filters as $filter) {
			if ($filter instanceof tx_icssitlorquery_IdFilter)
				$full = true;
			$filter->apply($this->query);
		}
		if ($full)
			$this->query->setCriteria(tx_icssitlorquery_FullAccomodation::getRequiredCriteria());
		else
			$this->query->setCriteria(tx_icssitlorquery_Accomodation::getRequiredCriteria());
		$this->query->setPage($this->page, $this->pageSize);
		$xmlContent = $this->query->execute();
		
		$reader = new XMLReader();
		$reader->XML($xmlContent);

		if (!tx_icssitlorquery_XMLTools::XMLMoveToRootElement($reader, 'LEI')) {
			tx_icssitquery_Debug::error('Invalid response from SITLOR nomenclature.');
			return false;
		}
		$reader->read();
		if (!$reader->next('Resultat')) {
			tx_icssitquery_Debug::error('Can not reach "Resultat" node from SITLOR nomenclature.');
			return false;
		}
		$reader->read();
		$accomodations = array();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				switch ($reader->name) {
					case 'sit_liste':
						if ($full)
							$accomodation = t3lib_div::makeInstance('tx_icssitlorquery_FullAccomodation');
						else
							$accomodation = t3lib_div::makeInstance('tx_icssitlorquery_Accomodation');
						$accomodation->parseXML($reader);
						t3lib_div::devLog('Accomodation', 'ics_sitlor_query', 0, array($accomodation));
						$accomodations[] = $accomodation;
						break;
						
					default:
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
		t3lib_div::devLog('Accomodations count', 'ics_sitlor_query', 0, array(count($accomodations)));
		return $accomodations;
	}

	/**
	 * Retrieves Restaurants
	 *
	 * @param	ISortingProvider $sorting : The sorting
	 *
	 * @return mixed : array of Restaurants
	 */
	public function getRestaurants(tx_icssitquery_ISortingProvider $sorting) {
		$this->query = t3lib_div::makeInstance('tx_icssitlorquery_SitlorQuery', $this->login, $this->password, $this->url);
		
		$full = false;
		foreach ($this->filters as $filter) {
			if ($filter instanceof tx_icssitlorquery_IdFilter)
				$full = true;
			$filter->apply($this->query);
		}
		if ($full)
			$this->query->setCriteria(tx_icssitlorquery_FullRestaurant::getRequiredCriteria());
		else
			$this->query->setCriteria(tx_icssitlorquery_Restaurant::getRequiredCriteria());
		$this->query->setPage($this->page, $this->pageSize);
		$xmlContent = $this->query->execute();
		
		$reader = new XMLReader();
		$reader->XML($xmlContent);

		if (!tx_icssitlorquery_XMLTools::XMLMoveToRootElement($reader, 'LEI')) {
			tx_icssitquery_Debug::error('Invalid response from SITLOR.');
			return false;
		}
		$reader->read();
		if (!$reader->next('Resultat')) {
			tx_icssitquery_Debug::error('Can not reach "Resultat" node from SITLOR.');
			return false;
		}
		$reader->read();
		$restaurants = array();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				switch ($reader->name) {
					case 'sit_liste':
						if ($full)
							$restaurant = t3lib_div::makeInstance('tx_icssitlorquery_FullRestaurant');
						else
							$restaurant = t3lib_div::makeInstance('tx_icssitlorquery_Restaurant');
						$restaurant->parseXML($reader);
						t3lib_div::devLog('Restaurant', 'ics_sitlor_query', 0, array($restaurant));
						$restaurants[] = $restaurant;
						break;
						
					default:
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
		t3lib_div::devLog('Restaurants count', 'ics_sitlor_query', 0, array(count($restaurants)));
		return $restaurants;
	}
	
	/**
	 * Retrieves Events
	 *
	 * @param	ISortingProvider $sorting : The sorting
	 *
	 * @return mixed : array of Events
	 */
	public function getEvents(tx_icssitquery_ISortingProvider $sorting) {
	}

}