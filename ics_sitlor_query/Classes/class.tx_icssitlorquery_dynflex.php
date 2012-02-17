<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In cite Solution <technique@in-cite.net>
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
 * Class 'tx_icsistlorquery_dynflex' for 'ics_sitlor_query' extension
 * Generates dynamic flex.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
 
class tx_icssitlorquery_dynflex {
	var	$list_types = array('ics_sitlor_query_pi1');	// List of available list_type to process on dynamic dynflex
	var $ffds = array(
		'pi1' => 'EXT:ics_sitlor_query/flexform_ds_pi1.xml'
	);
	var $llang_ffds = array(
		'pi1' => 'LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml'
	);

	/**
	 * Pre process flexform's field
	 *
	 * @param	string		$pi_table	Le nom de la table
	 * @param	string		$pi_field	Le nom du champ
	 * @param	array		$pi_row		l'enregistrement de la table à éditer
	 * @param	string		$pi_altName	Le nom alternatif du champ
	 * @param	integer		$pi_palette
	 * @param	string		$pi_extra
	 * @param	integer		$pi_pal
	 * @param	object		&$pi_tce
	 * @return	[type]		...
	 */
	function getSingleField_preProcess($pi_table, $pi_field, & $pi_row, $pi_altName, $pi_palette, $pi_extra, $pi_pal, &$pi_tce) {

		if (($pi_table != 'tt_content') || ($pi_field != 'pi_flexform') || ($pi_row['CType'] != 'list') || !in_array($pi_row['list_type'], $this->list_types))
			return;

		t3lib_div::loadTCA($pi_table);
		$conf = &$GLOBALS['TCA'][$pi_table]['columns'][$pi_field];
		$this->id = $pi_row['pid'];
		$flexData = (!empty($pi_row['pi_flexform'])) ? (t3lib_div::xml2array($pi_row['pi_flexform'])) : (array('data' => array()));
		
		switch ($pi_row['list_type']) {
			case 'ics_sitlor_query_pi1' :
				if ($xmlFlex = $this->preProcess_pi1($flexData))
					$conf['config']['ds']['ics_sitlor_query_pi1,list'] = $xmlFlex;
			break;
			
			default:			
		}
		
	}

	/**
	 * Pre process pi1
	 *
	 * @param	array		$flexData	Flexform data
	 * @return	string		Xml flexform
	 */
	function preProcess_pi1(array $flexData) {
		
		$ffds = $this->ffds['pi1'];
		$llang_ffds = $this->llang_ffds['pi1'];
		
		$xmlFlexPart = '';
		$flexArray = array();
		switch ($flexData['data']['paramSelect']['lDEF']['dataGroup']['vDEF']) {
			case 'ACCOMODATION':
				$optionListArray = array(
					array('', ''),
					array($llang_ffds . ':subDataGroup_hotel', 'HOTEL'),
					array($llang_ffds . ':subDataGroup_campingAndYouthHostel', 'CAMPING_AND_YOUTHHOSTEL'),
					array($llang_ffds . ':subDataGroup_strange', 'STRANGE'),
					array($llang_ffds . ':subDataGroup_hollidayCottageAndGuesthouse', 'HOLLIDAY_COTTAGE_AND_GUESTHOUSE'),
				);
				$flexArray['TCEforms'] = array(
					'label' => $llang_ffds . ':subDataGroup',
					'config' => array(
						'type' => 'select',
						'items' => $optionListArray,
						'size' => '5',
						'minitems' => '0',
						'maxitems' => '100',
					),
				);
				$xmlFlexPart = t3lib_div::array2xml($flexArray, '', 0, 'subDataGroups');
				break;
			case 'RESTAURANT':
				break;
			case 'EVENT':
				break;
			default:
				$flexArray = array();
		}
		$content =  str_replace(
			'<!-- ###SUBDATAGROUP### -->',
			$xmlFlexPart,
			file_get_contents(t3lib_div::getFileAbsFileName($ffds))
		);
		return $content;
	}
}