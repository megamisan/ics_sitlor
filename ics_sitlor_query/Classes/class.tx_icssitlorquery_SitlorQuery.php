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
 * Class 'SitlorQuery' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_SitlorQuery implements tx_icssitquery_IQuery {

	private $login;		// The login
	private $password;	// The password
	private $url;		// The url
	
	private $start = 1;	// Begin of page
	private $end = 20;	// End of page
	
	private $filters = array();	// Associative array
	private $sortings = array();
	
	static private $outputList = array(	// SITLOR Output value
		'xml' => '1',
		'csv' => '3',
		'pdf' => '5'
	);
	private $output = 'xml';	// Output type
	private $fields = array();	// Fields to extract
	private $criteria = null;	// tx_icssitlorquery_CriterionList : Criteria to extract
	static private $xmlBodyList = array(	// Output body
		'small' => 'sit_listereduite',
		'complete' => 'sit_listecomplete',
		'criterion' => 'sit_listecrit',
		'nomenclature' => 'sit_nomenc',
	);
	static private $tableList = array(	// SITLOR tables/views
		'small' => 'SIT_LISTEREDUITE',
		'complete' => 'SIT_LISTECOMPLETE',
		'criterion' => 'LEI_LISTECRIT',
		'nomenclature' => 'LEI_NOMENCLATURE_DECOMPTE',
	);
	private $table = 'complete';
	static private $schemeList = array(
		'LEI',
		'WEBACCESS',
		'WEBACCESS_DE',
		'WEBACCESS_EN',
		'WEBACCESS_NL',
	);
	private $scheme = 'LEI';
	private $query = array();
	private $entity = '737';
	static private $startDate;
	static private $endDate = '01/01/2100 23:59:59';

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
	 * Executes the query.
	 *
	 * @return	void
	 */
	public function execute() {
		$params = array();
		$params['user']	= utf8_decode($this->login);
		$params['pwkey'] = utf8_decode($this->password);

		// Format params
		$params['typsor'] = utf8_decode(self::$outputList[$this->output]);
		if (!empty($this->fields))
			$params['leschamps'] = utf8_decode(implode(',', $this->fields));
		if (!empty($this->criteria)) {
			$params['lescritex'] = utf8_decode(implode(',', $this->criteria));
		}
		$params['rfrom'] = utf8_decode($this->start);
		$params['rto'] = utf8_decode($this->end);
		$params['latable'] = utf8_decode(self::$tableList[$this->table]);
		$params['lxml'] = utf8_decode(self::$xmlBodyList[$this->table]);
		switch ($GLOBALS['TSFE']->config['config']['language']) {
			case 'fr':
				$this->scheme = 'LEI';
				break;
			case 'en':
				$this->scheme = 'WEBACCESS_EN';
				break;
			case 'de':
				$this->scheme = 'WEBACCESS_DE';
				break;
			case 'nl':
				$this->scheme = 'WEBACCESS_NL';
				break;
			default:
				$this->scheme = 'LEI';
		}
		$params['SCHEMA'] = utf8_decode($this->scheme);
		//-- End of Format params

		// Filter on params
		$filterArray = array_keys($this->filters);
		$pnames = array();
		$pvalues = array();
		if (in_array('entity', $filterArray)) {
			$params['lentit'] = $this->filters['entity'];
			$pnames[] = 'lentidad';
			$pvalues[] = $this->filters['entity'];
		}
		if (in_array('idFilter', $filterArray)) {
			$pnames[] = 'elproducto';
			$pvalues[] = $this->filters['idFilter'];
		}
		if ($this->table=='small' || $this->table=='complete') {
			if (in_array('keyword', $filterArray)) {
				$pnames[] = 'libtext=' . $this->filters['keyword'];
			}
		
			if (in_array('gender', $filterArray)) { // && (!(in_array('category', $filterArray)) || !in_array('type', $filterArray))) {
				$pnames[] = 'elgendro';
				$pvalues[] = $this->filters['gender'];
			}
			if (in_array('category', $filterArray)) { // && (!in_array('type', $filterArray))) {
				$this->makeCategoryFilter($pnames, $pvalues);
			}
			if (in_array('type', $filterArray)) {
				$this->makeTypeFilter($pnames, $pvalues);
			}
			if (in_array('criterion', $filterArray)) {
				$this->makeCriterionFilter($pnames, $pvalues);
			}
			if (in_array('title', $filterArray)) {
				$pnames[] = 'elnombre';
				$pvalues[] = implode('|', $this->filters['title']);
			}
			if (in_array('reference', $filterArray)) {
				$pnames[] = 'larefe';
				$pvalues[] = implode('|', $this->filters['reference']);
			}
			if (in_array('zip', $filterArray)) {
				$pnames[] = 'elzipo';
				$pvalues[] = implode('|', $this->filters['zip']);
			}
			$this->makeDateFilter($params, $pnames, $pvalues, $filterArray);
			$this->makeValidFilter($params, $pnames, $pvalues, $filterArray);
			$this->makeAvailableFilter($params, $pnames, $pvalues, $filterArray);
		}

		//-- End of Filter on params

		$params['PNAMES'] = utf8_decode(implode(',', $pnames));
		$params['PVALUES'] = utf8_decode(implode(',', $pvalues));

		$urlQuery = $this->url . '?' . http_build_query($params);
		t3lib_div::devLog('Url', 'ics_sitlor_query', 0, array(urldecode($urlQuery)));
		return tx_icssitlorquery_XMLTools::getXMLDocument($urlQuery);
	}

	/**
	 * Sets result page.
	 *
	 * @param	int		$number Page number.
	 * @param	int		$size Elements per page.
	 * @return	void
	 */
	public function setPage($number, $size) {
		if (!is_int($number) || $number<1)
			tx_icssitquery_Debug::error('Number of page is not positive int other than 0.');
		if (!is_int($size) || $size<1)
			tx_icssitquery_Debug::error('Size of element is not positive int other than 0.');
		$this->start = (($number * $size) +1) -$size;
		$this->end = ($number * $size);
	}

	/**
	 * Sets a parameter.
	 *
	 * @param	string		$name Parameter name
	 * @param	mixed		$value Parameter value
	 * @return void
	 */
	public function setParameter($name, $value) {
		if (!is_string($name))
			throw new Exception('Parameter\'s name must be string.');
			
		switch ($name) {
			// Filters parameters
			case 'type':	// Type is an int array of types IDs
					if (!isset($this->filters[$name]))
						$this->filters[$name] = array();
					$this->filters[$name] = array_merge($this->filters[$name], array_diff($value, $this->filters[$name]));
				break;
			case 'category':	// Category is an int array of categories IDs
					if (!isset($this->filters[$name]))
						$this->filters[$name] = array();
					$this->filters[$name] = array_merge($this->filters[$name], array_diff($value, $this->filters[$name]));
				break;
			case 'criterion':	// Criterion is an array of pair values (criterionID, array of termIDs)
					$this->filters[$name][] = $value;
				break;
			case 'idFilter':
			case 'title':
			case 'keyword':
			case 'gender':
			case 'startDate':
			case 'endDate':
			case 'noDate':
			case 'startAvailable':
			case 'endAvailable':
			case 'notAvailable':
			case 'startValid':
			case 'endValid':
			case 'reference':
			case 'zip':
			case 'entity':
				$this->filters[$name] = $value;
				break;
				
			// Sorting parameters
			case 'randomSorting':
			case 'alphaSorting':
			case 'titleSorting':
			case 'ratingStarSorting':
			// case 'ratingEarsOfCornSorting':
			case 'minDualRoomSorting':
			case 'currentWeeklyPriceSorting':
			// case 'labelChainSorting':
			case 'adultMenuSorting':
			case 'dateSorting':
				$this->sortings[$name] = $value;
				break;
			
			default:
				tx_icssitquery_Debug::warning('Undefined parameter in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Sets output type.
	 *
	 * @param	string		$value The output type. See self::$outputList.
	 * @return	void
	 */
	public function setOutput($value) {
		$value = strtolower($value);
		if (in_array($value, array_keys(self::$outputList)))
			$this->output = $value;
		else
			tx_icssitquery_Debug::warning('Output ' . $value . ' is undefined.');
	}

	/**
	 * Sets fields.
	 *
	 * @param	array		$fields Field names.
	 * @return	void
	 */
	public function setFields(array $fields) {
		$this->fields = $fields;
	}

	/**
	 * Adds a field.
	 *
	 * @param	string	$field Fieldname.
	 * @return	void
	 */
	public function addField($field=null) {
		$this->fields[] = $field;
	}

	/**
	 * Sets criteria.
	 *
	 * @param	array		$criteria IDs of criterion.
	 * @return	void
	 */
	public function setCriteria(array $criteria) {
		$this->criteria = $criteria;
	}

	/**
	 * Adds a criterion.
	 *
	 * @param	int		$criterion Criterion ID.
	 * @return	void
	 */
	public function addCriterion($criterion) {
		$this->criteria[] = $criterion;
	}

	/**
	 * Sets table.
	 *
	 * @param	string		$value The table to query.
	 * @return	void
	 */
	public function setTable($value) {
		$value = strtolower($value);
		if (in_array($value, array_keys(self::$tableList)))
			$this->table = $value;
		else
			tx_icssitquery_Debug::warning('Table "' . $value . '" is undefined.');
	}
	
	/**
	 * Set scheme
	 *
	 * @param	string 		$value The scheme
	 * @return	void
	 */
	public function setScheme($value) {
		if (!in_array($value, self::$schemeList))
			throw new Exception('The scheme ' . $value . 'is not defined.');
		$this->scheme = $value;
	}
	
	/**
	 * Set entity
	 *
	 * @param	string 		$value The entity
	 */
	public function setEntity($value) {
		$this->entity = $value;
	}
	
	/**
	 * Makes a filter on category.
	 *
	 * @param	array&		$pnames Parameters names.
	 * @param	array&		$pvalues Parameters values.
	 * @return	void
	 */
	private function makeCategoryFilter(array &$pnames, array &$pvalues) {
		$pnames[] = 'alcat';
		$pvalues[] = implode('|', $this->filters['category']);
	}

	/**
	 * Makes a filter on Type.
	 *
	 * @param	array&		$pnames Parameters names.
	 * @param	array&		$pvalues Parameters values.
	 * @return	void
	 */
	private function makeTypeFilter(array &$pnames, array &$pvalues) {
		$pnames[] = 'eltypo';
		$pvalues[] = implode('|', $this->filters['type']);
	}

	/**
	 * Make filter on Criterion
	 *
	 * @param	array $pnames
	 * @param	array $pvalues
	 */
	private function makeCriterionFilter(array &$pnames, array &$pvalues) {
		foreach ($this->filters['criterion'] as $key=>$criterionTerms) {
			$pnames[] = 'elcriterio' . $key;
			$pvalues[] = $criterionTerms[0];
			if (is_array($criterionTerms[1]) && !empty($criterionTerms[1])) {
				$pnames[] = 'modalidad' . $key;
				$pvalues[] = implode('|', $criterionTerms[1]);
			}
		}
		
	}
	
	/**
	 * Makes filters on Date.
	 *
	 * @param	array&		$pnames Parameters names.
	 * @param	array&		$pvalues Parameters values.
	 * @param	array		$filterArray Filters to use.
	 * @return	void
	 */
	private function makeDateFilter(array &$params, array &$pnames, array &$pvalues, array $filterArray) {
		if (in_array('startDate', $filterArray) && $this->filters['startDate']) {
			$params['leshoraires'] = utf8_decode(date('d/m/Y', $this->filters['startDate']));
		}
		if (in_array('endDate', $filterArray) && $this->filters['endDate']) {
			$params['leshoraires'] .= utf8_decode('|' . date('d/m/Y', $this->filters['endDate']));
		}
		if (isset($params['leshoraires'])) {
			$pnames[] = 'horariodu';
			if ($this->filters['startDate']) {
				$startDate = date('d/m/Y h:i:s', $this->filters['startDate']);
			} else {
				$startDate = date('d/m/Y h:i:s');
			}
			$pvalues[] = $startDate;
			$pnames[] = 'horarioau';
			if ($this->filters['endDate']) {
				$endDate = date('d/m/Y h:i:s', $this->filters['endDate']);
			} else {
				$endDate = self::$endDate;
			}
			$pvalues[] = $endDate;
		}
		if (in_array('noDate', $filterArray) && $this->filters['noDate']) {
			$params['tshor'] = utf8_decode('Y');
		}
	}

	/**
	 * Makes filters on Valid Date.
	 *
	 * @param	array&		$pnames Parameters names.
	 * @param	array&		$pvalues Parameters values.
	 * @param	array		$filterArray Filters to use.
	 * @return	void
	 */
	private function makeValidFilter(array &$params, array &$pnames, array &$pvalues, array $filterArray) {
		if (in_array('startValid', $filterArray) && $this->filters['startValid']) {
			$params['lesvalid'] = utf8_decode(date('d/m/Y', $this->filters['startValid']));
		}
		if (in_array('endValid', $filterArray) && $this->filters['endValid']) {
			$params['lesvalid'] .= utf8_decode('|' . date('d/m/Y', $this->filters['endValid']));
		}
		if (isset($params['lesvalid'])) {
			$pnames[] = 'validaddu';
			if ($this->filters['startValid']) {
				$startDate = date('d/m/Y h:i:s', $this->filters['startValid']);
			} else {
				$startDate = date('d/m/Y h:i:s');
			}
			$pvalues[] = $startDate;
			$pnames[] = 'validadau';
			if ($this->filters['endValid']) {
				$endDate = date('d/m/Y h:i:s', $this->filters['endValid']);
			} else {
				$endDate = self::$endValid;
			}
			$pvalues[] = $endDate;
		}
	}

	/**
	 * Makes filters on Available Date.
	 *
	 * @param	array&		$pnames Parameters names.
	 * @param	array&		$pvalues Parameters values.
	 * @param	array		$filterArray Filters to use.
	 * @return	void
	 */
	private function makeAvailableFilter(array &$params, array &$pnames, array &$pvalues, array $filterArray) {
		if (in_array('startAvailable', $filterArray) && $this->filters['startAvailable']) {
			$params['lesdispos'] = utf8_decode(date('d/m/Y', $this->filters['startAvailable']));
		}
		if (in_array('endAvailable', $filterArray) && $this->filters['endAvailable']) {
			$params['lesdispos'] .= utf8_decode('|' . date('d/m/Y', $this->filters['endAvailable']));
		}
		if (isset($params['lesdispos'])) {
			$pnames[] = 'dispodu';
			if ($this->filters['startAvailable']) {
				$startDate = date('d/m/Y h:i:s', $this->filters['startAvailable']);
			} else {
				$startDate = date('d/m/Y h:i:s');
			}
			$pvalues[] = $startDate;
			$pnames[] = 'dispoau';
			if ($this->filters['endAvailable']) {
				$endDate = date('d/m/Y h:i:s', $this->filters['endAvailable']);
			} else {
				$endDate = self::$endAvailable;
			}
			$pvalues[] = $endDate;
		}
		if (in_array('notAvailable', $filterArray) && $this->filters['notAvailable']) {
			$params['tsdispo'] = utf8_decode('Y');
		}
	}

}