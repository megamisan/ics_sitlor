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
	 * @param	array		$pi_row		l'enregistrement de la table � �diter
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
		$xmlFlexPart = '';
		$dataGroup = $flexData['data']['paramSelect']['lDEF']['dataGroup']['vDEF'];
		switch ($dataGroup) {
			case 'ACCOMODATION':
				$xmlFlexPart = $this->flexPart_Accomodation();
				break;
			case 'RESTAURANT':
				$xmlFlexPart = $this->flexPart_Restaurant();
				break;
			case 'EVENT':
				$xmlFlexPart = $this->flexPart_Event();
				break;
			default:
				$xmlFlexPart = '';
		}
		
		return  str_replace(
			'<!-- ###PARAMSELECT_SPECIFIC### -->',
			$xmlFlexPart,
			file_get_contents(t3lib_div::getFileAbsFileName($this->ffds['pi1']))
		);
	}
	
	/**
	 * Retrieves Accomodation flex part
	 *
	 * @return	string		XML flex part content
	 */
	private function flexPart_Accomodation() {
		$llang_ffds = $this->llang_ffds['pi1'];
		$xmlFlexPart = '';
		$flexArray = array();
		
		// Subdata group
		$subDataGroup_options = array(
			array('', ''),
			array($llang_ffds . ':subDataGroup_hotel', 'HOTEL'),
			array($llang_ffds . ':subDataGroup_camping_youthHostel', 'CAMPING_YOUTHHOSTEL'),
			array($llang_ffds . ':subDataGroup_strange', 'STRANGE'),
			array($llang_ffds . ':subDataGroup_hollidayCottage_guesthouse', 'HOLLIDAY_COTTAGE_GUESTHOUSE'),
		);
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':subDataGroup',
			'config' => array(
				'type' => 'select',
				'items' => $subDataGroup_options,
				'size' => '1',
				'minitems' => '0',
				'maxitems' => '1',
			),
		);
		$xmlFlexPart = t3lib_div::array2xml($flexArray, '', 0, 'subDataGroup');
		
		// Hotel types
		$hotelType_options = array(
			array('', ''),
			array($llang_ffds . ':hotelType_hotel_restaurant',tx_icssitlorquery_NomenclatureUtils::HOTEL_RESTAURANT),
			array($llang_ffds . ':hotelType_furnished', tx_icssitlorquery_NomenclatureUtils::FURNISHED),
		);
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':hotelType',
			'config' => array(
				'type' => 'select',
				'items' => $hotelType_options,
				'size' => '3',
				'minitems' => '0',
				'maxitems' => '2',
			),
		);
		$xmlFlexPart .= t3lib_div::array2xml($flexArray, '', 0, 'hotelTypes');
		
		// Hotel equipments
		$hotelEquipment_options = array(
			array('', ''),
			array($llang_ffds . ':hotelEquipment_park', tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK.':'.tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK_YES),
			array($llang_ffds . ':hotelEquipment_allowedPets', tx_icssitlorquery_CriterionUtils::ALLOWED_PETS.':'.tx_icssitlorquery_CriterionUtils::ALLOWED_PETS_YES),
			array($llang_ffds . ':hotelEquipment_wifi', tx_icssitlorquery_CriterionUtils::COMFORT_ROOM.':'.tx_icssitlorquery_CriterionUtils::WIFI),
		);
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':hotelEquipment',
			'config' => array(
				'type' => 'select',
				'items' => $hotelEquipment_options,
				'size' => '4',
				'minitems' => '0',
				'maxitems' => '3',
			),
		);
		$xmlFlexPart .= t3lib_div::array2xml($flexArray, '', 0, 'hotelEquipments');
		
		return $xmlFlexPart;
	}
	
	/**
	 * Retrieves Restaurant flex part
	 *
	 * @return	string		XML flex part content
	 */
	private function flexPart_Restaurant() {
		$llang_ffds = $this->llang_ffds['pi1'];
		$xmlFlexPart = '';
		$flexArray = array();
		
		// Restaurant categories
		$category_options = array(
			array('', ''),
			array($llang_ffds . ':restaurantCategory_fastfood', tx_icssitlorquery_CriterionUtils::RCATEGORIE.':'.tx_icssitlorquery_CriterionUtils::RCATEGORIE_FASTFOOD),
			array($llang_ffds . ':restaurantCategory_icecream_theahouse', tx_icssitlorquery_CriterionUtils::RCATEGORIE.':'.tx_icssitlorquery_CriterionUtils::RCATEGORIE_ICECREAM_THEAHOUSE),
			array($llang_ffds . ':restaurantCategory_creperie', tx_icssitlorquery_CriterionUtils::RCATEGORIE.':'.tx_icssitlorquery_CriterionUtils::RCATEGORIE_CREPERIE),
		);
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':restaurantCategory',
			'config' => array(
				'type' => 'select',
				'items' => $category_options,
				'size' => '4',
				'minitems' => '0',
				'maxitems' => '3',
			),
		);
		$xmlFlexPart = t3lib_div::array2xml($flexArray, '', 0, 'restaurantCategories');
		
		// Restaurant sp�ciality
		$foreignFood_options = array(
			array('', ''),
			array($llang_ffds . ':foreignFood_asian', tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD.':'.tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD_ASIAN),
			array($llang_ffds . ':foreignFood_sa', tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD.':'.tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD_SA),
			array($llang_ffds . ':foreignFood_oriental', tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD.':'.tx_icssitlorquery_CriterionUtils::FOREIGN_FOOD_ORIENTAL),
		);
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':foreignFood',
			'config' => array(
				'type' => 'select',
				'items' => $foreignFood_options,
				'size' => '4',
				'minitems' => '0',
				'maxitems' => '3',
			),
		);
		$xmlFlexPart .= t3lib_div::array2xml($flexArray, '', 0, 'foreignFoods');
		
		return $xmlFlexPart;
	}
	
	/**
	 * Retrieves Event flex part
	 *
	 * @return	string		XML flex part content
	 */
	private function flexPart_Event() {
		$llang_ffds = $this->llang_ffds['pi1'];
		$xmlFlexPart = '';
		$flexArray = array();
	
		// Dates
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':startDate',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'eval' => 'date',
			),
		);
		$xmlFlexPart = t3lib_div::array2xml($flexArray, '', 0, 'startDate');
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':endDate',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'eval' => 'date',
			),
		);
		$xmlFlexPart .= t3lib_div::array2xml($flexArray, '', 0, 'endDate');
		
		// Period
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':period',
			'config' => array(
				'type' => 'check',
			),
		);
		$xmlFlexPart .= t3lib_div::array2xml($flexArray, '', 0, 'period');
		
		// No fee
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':noFeeEvent',
			'config' => array(
				'type' => 'check',
			),
		);
		$xmlFlexPart .= t3lib_div::array2xml($flexArray, '', 0, 'noFeeEvent');
		
		return $xmlFlexPart;
	}
}