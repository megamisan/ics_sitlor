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
	private $totalSize;		// Elements size
	private $randomSession;

	/**
	 * Initializes service access.
	 *
	 * @param	string		$login User identifier
	 * @param	string		$password User password
	 * @param	string		$url SITLOR service URL
	 * @return	void
	 */
	function __construct($login, $password, $url) {
		$this->login = $login;
		$this->password = $password;
		$this->url = $url;
	}

	/**
	 * Sets pager position.
	 *
	 * @param	int $page Page number.
	 * @param	int $size Number of element per page.
	 *
	 * @return void
	 */
	public function setPager($page, $size) {
		if (!is_int($page))
			throw new Exception('Page number must be integer.');
		if (!is_int($size))
			throw new Exception('Number of element must be integer');
			
		$this->page = $page;
		$this->pageSize = $size;
	}

	/**
	 * Resets added filters.
	 *
	 * @return	void
	 */
	public function resetFilters() {
		$this->filters = array();
	}

	/**
	 * Adds a filter.
	 *
	 * @param	IFilter		$filter Filter to use.
	 * @return	void
	 */
	public function addFilter(tx_icssitquery_IFilter $filter) {
		$this->filters[] = $filter;
	}

	
	/**
	 * Retrieves last total count
	 *
	 * @return	size
	 */
	public function getLastTotalCount() {
		return $this->totalSize;
	}
	
	/**
	 * Retrieves random session
	 *
	 * @return random session
	 */
	public function getLastRandomSession() {
		return $this->randomSession;
	}
	
	/**
	 * Retrieves the last query.
	 *
	 * @return tx_icssitquery_IQuery		The last executed query.
	 */
	public function getLastQuery() {
		return $this->query;
	}

	/**
	 * Retrieves accomodations.
	 *
	 * @param	ISortingProvider		$sorting Sorting provider to use.
	 * @return	array		The accomodations found by the API.
	 */
	public function getAccomodations(tx_icssitquery_ISortingProvider $sorting=null) {
		$this->query = t3lib_div::makeInstance('tx_icssitlorquery_SitlorQuery', $this->login, $this->password, $this->url);

		$full = false;
		foreach ($this->filters as $filter) {
			if ($filter instanceof tx_icssitlorquery_IdFilter)
				$full = true;
			$filter->apply($this->query);
		}
		if ($full) {
			$this->query->setCriteria(tx_icssitlorquery_FullAccomodation::getRequiredCriteria());
		} else {
			$this->query->setCriteria(tx_icssitlorquery_Accomodation::getRequiredCriteria());
			if (isset($sorting))
				$sorting->apply($this->query);
			$this->query->setPage($this->page, $this->pageSize);
		}
		
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
		$this->totalSize = intval($reader->getAttribute('TOTAL_FENETRE'));
		$this->randomSession = $reader->getAttribute('SESSIONALEA');
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
		t3lib_div::devLog('Accomodations count, total, random session', 'ics_sitlor_query', 0, array('Accomodations' => count($accomodations), 'Total' => $this->totalSize, 'Random session' =>$this->randomSession));
		return $accomodations;
	}

	/**
	 * Retrieves restaurants.
	 *
	 * @param	ISortingProvider		$sorting Sorting provider to use.
	 * @return	array		The restaurants found by the API.
	 */
	public function getRestaurants(tx_icssitquery_ISortingProvider $sorting=null) {
		$this->query = t3lib_div::makeInstance('tx_icssitlorquery_SitlorQuery', $this->login, $this->password, $this->url);

		$full = false;
		foreach ($this->filters as $filter) {
			if ($filter instanceof tx_icssitlorquery_IdFilter)
				$full = true;
			$filter->apply($this->query);
		}
		if ($full) {
			$this->query->setCriteria(tx_icssitlorquery_FullRestaurant::getRequiredCriteria());
		} else {
			$this->query->setCriteria(tx_icssitlorquery_Restaurant::getRequiredCriteria());
			if (isset($sorting))
				$sorting->apply($this->query);
			$this->query->setPage($this->page, $this->pageSize);
		}
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
		$this->totalSize = intval($reader->getAttribute('TOTAL_FENETRE'));
		$this->randomSession = $reader->getAttribute('SESSIONALEA');
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
		t3lib_div::devLog('Restaurants count, total, random session', 'ics_sitlor_query', 0, array('Restaurants' => count($restaurants), 'Total' => $this->totalSize, 'Random session' =>$this->randomSession));
		return $restaurants;
	}

	/**
	 * Retrieves events.
	 *
	 * @param	ISortingProvider		$sorting Sorting provider to use.
	 *
	 * @return	array		The accomodations found by the API.
	 */
	public function getEvents(tx_icssitquery_ISortingProvider $sorting=null) {
		$this->query = t3lib_div::makeInstance('tx_icssitlorquery_SitlorQuery', $this->login, $this->password, $this->url);
		
		$full = false;
		foreach ($this->filters as $filter) {
			if ($filter instanceof tx_icssitlorquery_IdFilter)
				$full = true;
			$filter->apply($this->query);
		}
		if ($full) {
			$this->query->setCriteria(tx_icssitlorquery_FullEvent::getRequiredCriteria());
		} else {
			$this->query->setCriteria(tx_icssitlorquery_Event::getRequiredCriteria());
			if (isset($sorting))
				$sorting->apply($this->query);
			$this->query->setPage($this->page, $this->pageSize);
		}
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
		$this->totalSize = intval($reader->getAttribute('TOTAL_FENETRE'));
		$this->randomSession = $reader->getAttribute('SESSIONALEA');
		$reader->read();
		$events = array();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				switch ($reader->name) {
					case 'sit_liste':
						if ($full)
							$event = t3lib_div::makeInstance('tx_icssitlorquery_FullEvent');
						else
							$event = t3lib_div::makeInstance('tx_icssitlorquery_Event');
						$event->parseXML($reader);
						t3lib_div::devLog('Event', 'ics_sitlor_query', 0, array($event));
						$events[] = $event;
						break;
						
					default:
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
		t3lib_div::devLog('Events count, total, random session', 'ics_sitlor_query', 0, array('Events' => count($events), 'Total' => $this->totalSize, 'Random session' =>$this->randomSession));
		return $events;
	}
}