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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Class 'tx_icssitlorquery_accomodationListRenderer' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_accomodationListRenderer {
	/**
	 *
	 * @param	tx_icssitlorquery_pi1 $this->pi: plugin pi1
	 */
	function main (tx_icssitlorquery_pi1 $pi) {
		$this->pi = $pi;
		
		$categories = t3lib_div::trimExplode(',', $this->pi->conf['accomodationCategory'], true);
		foreach ($categories as $category) {
			$category = (string) strtoupper(trim($category));
			switch ($category) {
				case 'HOTEL' :
					// Filter on type "Hôtel",  "Hôtel - hôtel restaurant","Meublé"
					try {
						$types = tx_icssitlorquery_NomenclatureFactory::GetTypes(array(4000002, 4000003, 4000012));
					} catch (Exception $e) {
						tx_icssitquery_Debug::error('Retrieves Type for HOTEL failed : ' . $e);
					}
					$typeFilter = t3lib_div::makeInstance('tx_icssitlorquery_TypeFilter', $types);
					$this->pi->queryService->addFilter($typeFilter);
					break;
				case 'CAMPING' :
					// TODO : ajouter filtre sur camping
					break;
				case 'YOUTH_HOSTEL' :
					// TODO : ajouter filtre sur auberge de jeunesse
					break;
				case 'STRANGE' :
					// TODO : ajouter filtre sur insolite
					break;
				case 'HOLLIDAY_COTTAGE' :
					// TODO : ajouter filtre sur gîtes
					break;
				case 'GUESTHOUSE' :
					// TODO : ajouter filtre sur chambre d'hôtes
					break;
				default;
			}
		}
		
		// Set filter on date to get date data
		$StartDateFilter = t3lib_div::makeInstance('tx_icssitlorquery_StartDateFilter', mktime(0,0,0,1,1,2000));
		$this->pi->queryService->addFilter($StartDateFilter);
		
		try {
			$accomodations = $this->pi->queryService->getAccomodations($this->pi->sortingProvider);
		} catch (Exception $e) {
			tx_icssitquery_Debug::error('Retrieves Accomodation proccess failed : ');
		}
		if (empty($accomodations))
			return $this->pi->pi_getLL('no_data', 'There is any Accomodations', true);
		
		// var_dump($accomodations);
		
		return $this->renderAccomodations($accomodations);
	}
	
	/**
	 * Render accomodations
	 *
	 * @param	array $accomodation : Accomodations array
	 *
	 * @return string : Accomodations content
	 */
	function renderAccomodations(array $accomodations) {
		$subparts = array();
		$markers = array();
		$template = $this->pi->cObj->getSubpart($this->pi->templateCode, '###TEMPLATE_ACCOMODATION_LIST###');
		$subparts['GROUP_ACCOMODATION'] = $this->renderListRows($this->pi->cObj->getSubpart($template, '###GROUP_ACCOMODATION###'), $accomodations);
	
		$template = $this->pi->cObj->substituteSubpart($template, '###GROUP_ACCOMODATION###', $subparts['GROUP_ACCOMODATION']);
		return $this->pi->cObj->substituteMarkerArray($template, $markers, '###|###');
	}
	
	/**
	 * Render list rows
	 *
	 * @param	string $template
	 * @param	array $accomodation
	 *
	 * @return string : Accomodations content
	 */
	function renderListRows($template, array $accomodations) {
		if (!$template)
			return '';
		$subparts = array();
		$markers = array();
		$subparts['ITEM'] = '';
		foreach ($accomodations as $accomodation) {
			if ($accomodation instanceof tx_icssitlorquery_Accomodation) {
				$subparts['ITEM'] .= $this->renderListRow($this->pi->cObj->getSubpart($template, '###ITEM_ACCOMODATION###'), $accomodation);
			} else {
				$type = gettype($accomodation);
				if ($type=='object') {
					$type = get_class($accomodation);
				}
				tx_icssitquery_Debug::error('Accomodation expected, ' . $type . ' found.');
			}
		}
		$template = $this->pi->cObj->substituteSubpart($template, '###ITEM_ACCOMODATION###', $subparts['ITEM']);
		return $this->pi->cObj->substituteMarkerArray($template, $markers, '###|###');
	}
	
	/**
	 * Render list row
	 *
	 * @param	string $template
	 * @param	Accomodation $accomodation
	 *
	 * @return string : Accomodation content
	 */
	function renderListRow($template, tx_icssitlorquery_Accomodation $accomodation) {
		if (!$template)
			return '';
			
		$markers = array(
			'TYPE_TITLE' => $this->pi->pi_getLL('type', 'Type', true),
			'TYPE_VALUE' => $accomodation->__get('Type'),
			'TITLE_TITLE' => $this->pi->pi_getLL('title', 'Title', true),
			'TITLE_VALUE' => $accomodation->__get('Name'),
			'DESCRIPTION_TITLE' => $this->pi->pi_getLL('description', 'Description', true),
			'DESCRIPTION_VALUE' => $accomodation->__get('Description'),
		);
		//TODO : ILLUSTRATION, c'est une image

		
		return $this->pi->cObj->substituteMarkerArray($template, $markers, '###|###');
	}
}