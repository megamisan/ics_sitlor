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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class tx_icssitlorquery_dynflex
 *   71:     function getSingleField_preProcess($pi_table, $pi_field, & $pi_row, $pi_altName, $pi_palette, $pi_extra, $pi_pal, &$pi_tce)
 *   97:     function preProcess_pi1(&$pi_row)
 *  125:     private function flexPartFilter_Accomodation()
 *  195:     private function flexPartFilter_Restaurant()
 *  244:     private function flexPartFilter_Event()
 *  296:     private function flexPartSorting(&$pi_row)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
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
	 * @param	string		$pi_table	Table name
	 * @param	string		$pi_field	Field name
	 * @param	array		$pi_row		Table record to edit
	 * @param	string		$pi_altName	Alternative field name
	 * @param	integer		$pi_palette
	 * @param	string		$pi_extra
	 * @param	integer		$pi_pal
	 * @param	object		&$pi_tce
	 * @return	void
	 */
	function getSingleField_preProcess($pi_table, $pi_field, & $pi_row, $pi_altName, $pi_palette, $pi_extra, $pi_pal, &$pi_tce) {

		if (($pi_table != 'tt_content') || ($pi_field != 'pi_flexform') || ($pi_row['CType'] != 'list') || !in_array($pi_row['list_type'], $this->list_types))
			return;

		t3lib_div::loadTCA($pi_table);
		$conf = &$GLOBALS['TCA'][$pi_table]['columns'][$pi_field];
		$this->id = $pi_row['pid'];

		switch ($pi_row['list_type']) {
			case 'ics_sitlor_query_pi1' :
				if ($xmlFlex = $this->preProcess_pi1($pi_row))
					$conf['config']['ds']['ics_sitlor_query_pi1,list'] = $xmlFlex;
			break;

			default:
		}

	}

	/**
	 * Pre process pi1
	 *
	 * @param	array		$pi_row		Table record to edit
	 * @return	string		Xml flexform
	 */
	function preProcess_pi1(&$pi_row) {
		$flexData = (!empty($pi_row['pi_flexform'])) ? (t3lib_div::xml2array($pi_row['pi_flexform'])) : (array('data' => array()));
		$dataGroup = $flexData['data']['paramSelect']['lDEF']['dataGroup']['vDEF'];
		$content = file_get_contents(t3lib_div::getFileAbsFileName($this->ffds['pi1']));
		switch ($dataGroup) {
			case 'ACCOMODATION':
				$content = str_replace('<!-- ###PARAMSELECT_SPECIFIC### -->', $this->flexPartFilter_Accomodation(), $content);
				if (!isset($flexData['data']['paramSelect']['lDEF']['subDataGroup']['vDEF']) || in_array($flexData['data']['paramSelect']['lDEF']['subDataGroup']['vDEF'], array('HOTEL', 'HOLLIDAY_COTTAGE_GUESTHOUSE')))
					$content = str_replace('<!-- ###PARAMSORTING### -->', $this->flexPartSorting($pi_row), $content);
				break;
			case 'RESTAURANT':
				$content = str_replace('<!-- ###PARAMSELECT_SPECIFIC### -->', $this->flexPartFilter_Restaurant(), $content);
				$content = str_replace('<!-- ###PARAMSORTING### -->', $this->flexPartSorting($pi_row), $content);
				break;
			case 'EVENT':
				$content = str_replace('<!-- ###PARAMSELECT_SPECIFIC### -->', $this->flexPartFilter_Event(), $content);
				break;
			case 'FREETIME':
				$content = str_replace('<!-- ###PARAMSELECT_SPECIFIC### -->', $this->flexPartFilter_FreeTime(), $content);
				break;
			case 'SUBSCRIBER':
				$content = str_replace('<!-- ###PARAMSELECT_SPECIFIC### -->', $this->flexPartFilter_Subscriber(), $content);
				break;
			default:
		}

		return $content;
	}

	/**
	 * Retrieves Accomodation flex part
	 *
	 * @return	string		XML flex part content
	 */
	private function flexPartFilter_Accomodation() {
		$llang_ffds = $this->llang_ffds['pi1'];
		$xmlFlexPart = '';
		$flexArray = array();

		// Subdata group
		$subDataGroup_options = array(
			array(),
			array($llang_ffds . ':subDataGroup_hotel', 'HOTEL'),
			array($llang_ffds . ':subDataGroup_camping_youthHostel', 'CAMPING_YOUTHHOSTEL'),
			array($llang_ffds . ':subDataGroup_strange', 'STRANGE'),
			array($llang_ffds . ':subDataGroup_hollidayCottage_guesthouse', 'HOLLIDAY_COTTAGE_GUESTHOUSE'),
		);
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':subDataGroup',
			'onChange' => 'reload',
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
			array($llang_ffds . ':hotelType_hotel_restaurant',tx_icssitlorquery_NomenclatureUtils::HOTEL_RESTAURANT),
			// array($llang_ffds . ':hotelType_furnished', tx_icssitlorquery_NomenclatureUtils::FURNISHED),
			array($llang_ffds . ':hotelType_furnished', tx_icssitlorquery_NomenclatureUtils::RESIDENCE),
		);
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':hotelType',
			'displayCond' => 'FIELD:subDataGroup:=:HOTEL',
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
			array($llang_ffds . ':hotelEquipment_park', tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK.':'.tx_icssitlorquery_CriterionUtils::MOTORCOACH_PARK_YES),
			array($llang_ffds . ':hotelEquipment_allowedPets', tx_icssitlorquery_CriterionUtils::ALLOWED_PETS.':'.tx_icssitlorquery_CriterionUtils::ALLOWED_PETS_YES),
			array($llang_ffds . ':hotelEquipment_wifi', tx_icssitlorquery_CriterionUtils::COMFORT_ROOM.':'.tx_icssitlorquery_CriterionUtils::WIFI),
		);
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':hotelEquipment',
			'displayCond' => 'FIELD:subDataGroup:=:HOTEL',
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
	private function flexPartFilter_Restaurant() {
		$llang_ffds = $this->llang_ffds['pi1'];
		$xmlFlexPart = '';
		$flexArray = array();

		// Restaurant categories
		$category_options = array(
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
	private function flexPartFilter_Event() {
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

		// With illustration
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':illustration',
			'config' => array(
				'type' => 'check',
			),
		);
		$xmlFlexPart .= t3lib_div::array2xml($flexArray, '', 0, 'illustration');
		
		return $xmlFlexPart;
	}

	
	/**
	 * Retrieves FreeTime flex part
	 *
	 * @return	string		XML flex part content
	 */
	private function flexPartFilter_FreeTime() {
		$llang_ffds = $this->llang_ffds['pi1'];
		$xmlFlexPart = '';
		$flexArray = array();
		
		$themes = array(
			array('', ''),
			array($llang_ffds . ':ft_family', tx_icssitlorquery_CriterionUtils::FREETIME.':'.tx_icssitlorquery_CriterionUtils::FT_FAMILY),
			array($llang_ffds . ':ft_noFee', tx_icssitlorquery_CriterionUtils::FREETIME.':'.tx_icssitlorquery_CriterionUtils::FT_NOFEE),
			array($llang_ffds . ':ft_evening', tx_icssitlorquery_CriterionUtils::FREETIME.':'.tx_icssitlorquery_CriterionUtils::FT_EVENING),
			array($llang_ffds . ':ft_outdoor', tx_icssitlorquery_CriterionUtils::FREETIME.':'.tx_icssitlorquery_CriterionUtils::FT_OUTDOOR),
			array($llang_ffds . ':fr_rain', tx_icssitlorquery_CriterionUtils::FREETIME.':'.tx_icssitlorquery_CriterionUtils::FT_RAIN),
			array($llang_ffds . ':ft_weekend', tx_icssitlorquery_CriterionUtils::FREETIME.':'.tx_icssitlorquery_CriterionUtils::FT_WEEKEND),
		);
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':freeTimeThemes',
			'config' => array(
				'type' => 'select',
				'items' => $themes,
				'size' => '1',
				'minitems' => '0',
				'maxitems' => '1',
			),
		);
		$xmlFlexPart .= t3lib_div::array2xml($flexArray, '', 0, 'freeTimeThemes');
		
		
		return $xmlFlexPart;
	}
	
	/**
	 * Retrieves Subscriber flex part
	 *
	 * @return	string		XML flex part content
	 */
	private function flexPartFilter_Subscriber() {
		$llang_ffds = $this->llang_ffds['pi1'];
		$xmlFlexPart = '';
		$flexArray = array();
				
		// $subscriber_types = array(
			// array($llang_ffds . ':subscriber_types_anyType', ''),
		// );
		// foreach (tx_icssitlorquery_CriterionUtils::$artsAndCrafts as $key=>$artCraft) {
			// $subscriber_types[] = array(
				// $llang_ffds . ':artsAndCrafts_' . $key, 
				// 'CRITERION,' . tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS . ',' . tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS . ':' . $artCraft
			// );
		// }

		$pagesTSC = t3lib_BEfunc::getPagesTSconfig($this->id);
		$conf = $pagesTSC['tx_icssitlorquery.']['sitlor.'];
		// Check login, password, urls
		if (!$conf['login']) {
			tx_icssitquery_Debug::error('Login is required.');
			return;
		}
		if (!$conf['password']) {
			tx_icssitquery_Debug::error('Password is required.');
			return;
		}
		if (!$conf['url']) {
			tx_icssitquery_Debug::error('Url is required.');
			return;
		}
		if (!$conf['nomenclatureUrl']) {
			tx_icssitquery_Debug::error('Nomenclature url is required.');
			return;
		}
		if (!$conf['criterionUrl']) {
			tx_icssitquery_Debug::error('Criterion url is required.');
			return;
		}
		tx_icssitlorquery_Configurator::setConnection($conf['login'], $conf['password'], $conf['nomenclatureUrl'], $conf['criterionUrl']);		

		$subscriber_types = array(
			array($llang_ffds . ':subscriber_types_anyType', ''),
		);
		$subscriber_artsAndCrafts = array();
		$termList = tx_icssitlorquery_CriterionFactory::GetCriterionTerms(tx_icssitlorquery_CriterionFactory::GetCriterion(tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS));
		for ($i=0; $i<$termList->Count(); $i++) {
			$term = $termList->Get($i);
			$subscriber_artsAndCrafts[$term->Name] = array($term->Name, 'CRITERION,' . tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS . ',' . tx_icssitlorquery_CriterionUtils::ARTS_CRAFTS . ':' . $term->ID);
		}
		ksort($subscriber_artsAndCrafts);		
		$subscriber_types = array_merge($subscriber_types, $subscriber_artsAndCrafts);

		// foreach (tx_icssitlorquery_CriterionUtils::$commerces as $key=>$com) {
			// $subscriber_types[] = array(
				// $llang_ffds . ':commerces_' . $key, 
				// 'CRITERION,' . tx_icssitlorquery_CriterionUtils::COMMERCE . ',' . tx_icssitlorquery_CriterionUtils::COMMERCE . ':' . $com
			// );
		// }
		
		$subscriber_commerce = array();
		$termList = tx_icssitlorquery_CriterionFactory::GetCriterionTerms(tx_icssitlorquery_CriterionFactory::GetCriterion(tx_icssitlorquery_CriterionUtils::COMMERCE));
		for ($i=0; $i<$termList->Count(); $i++) {
			$term = $termList->Get($i);
			$subscriber_commerce[$term->Name] = array($term->Name, 'CRITERION,' . tx_icssitlorquery_CriterionUtils::COMMERCE . ',' . tx_icssitlorquery_CriterionUtils::COMMERCE . ':' . $term->ID);
		}
		ksort($subscriber_commerce);		
		$subscriber_types = array_merge($subscriber_types, $subscriber_commerce);
		
		$subscriber_types[] = array(
			$llang_ffds . ':hotel',
			'NOMENCLATURE,CATEGORY,' . tx_icssitlorquery_NomenclatureUtils::HOTEL
		);
		$subscriber_types[] = array(
			$llang_ffds . ':hollidayCottage',
			'NOMENCLATURE,CATEGORY,' . tx_icssitlorquery_NomenclatureUtils::HOLLIDAY_COTTAGE
		);
		$subscriber_types[] = array(
			$llang_ffds . ':residence',
			'NOMENCLATURE,CATEGORY,' . tx_icssitlorquery_NomenclatureUtils::RESIDENCE
		);
		$subscriber_types[] = array(
			$llang_ffds . ':guesthouse',
			'NOMENCLATURE,CATEGORY,' . tx_icssitlorquery_NomenclatureUtils::GUESTHOUSE
		);
		$subscriber_types[] = array(
			$llang_ffds . ':restaurant',
			'NOMENCLATURE,CATEGORY,' . tx_icssitlorquery_NomenclatureUtils::RESTAURANT
		);
		$subscriber_types[] = array(
			$llang_ffds . ':association',
			'NOMENCLATURE,CATEGORY,' . tx_icssitlorquery_NomenclatureUtils::ASSOCIATION
		);
		
		$flexArray['TCEforms'] = array(
			'label' => $llang_ffds . ':subscriber_types',
			'config' => array(
				'type' => 'radio',
				'items' => $subscriber_types,
				'minitems' => '0',
				'maxitems' => '1',
			),
		);
		$xmlFlexPart .= t3lib_div::array2xml($flexArray, '', 0, 'subscriber_types');
		
		return $xmlFlexPart;
	}
	
	/**
	 * Retrieves sorting flex part
	 *
	 * @param	array		$pi_row		Table record to edit
	 * @return	string		XML flex part content
	 */
	private function flexPartSorting(&$pi_row) {
		$flexData = (!empty($pi_row['pi_flexform'])) ? (t3lib_div::xml2array($pi_row['pi_flexform'])) : (array('data' => array()));
		if ($flexData['data']['paramSelect']['lDEF']['dataGroup']['vDEF']=='ACCOMODATION') {
			if ($flexData['data']['paramSelect']['lDEF']['subDataGroup']['vDEF']=='HOTEL') {
				return '
				<paramSorting>
				<ROOT>
					<TCEforms>
						<sheetTitle>LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:paramSorting</sheetTitle>
					</TCEforms>
					<el>
						<hotel_sortName>
							<TCEforms>
								<label>LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName</label>
								<config type="array">
									<type>select</type>
									<displayCond>FIELD:subDataGroup:=:HOTEL</displayCond>
									<items type="array">
										<numIndex index="0" type="array">
											<numIndex index="0">LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName_alpha</numIndex>
											<numIndex index="1">ALPHA</numIndex>
										</numIndex>
										<numIndex index="1" type="array">
											<numIndex index="0">LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName_hotelRating</numIndex>
											<numIndex index="1">HOTELRATING</numIndex>
										</numIndex>
										<numIndex index="2" type="array">
											<numIndex index="0">LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName_price</numIndex>
											<numIndex index="1">PRICE</numIndex>
										</numIndex>
									</items>
									<size>1</size>
									<minitems>0</minitems>
									<maxitems>1</maxitems>
								</config>
							</TCEforms>
						</hotel_sortName>
					</el>
				</ROOT>
				</paramSorting>
				';
			}
			if ($flexData['data']['paramSelect']['lDEF']['subDataGroup']['vDEF']=='HOLLIDAY_COTTAGE_GUESTHOUSE') {
				return '
				<paramSorting>
				<ROOT>
					<TCEforms>
						<sheetTitle>LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:paramSorting</sheetTitle>
					</TCEforms>
					<el>
						<hCandGH_sortName>
							<TCEforms>
								<label>LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName</label>
								<config type="array">
									<type>select</type>
									<displayCond>FIELD:subDataGroup:=:HOLLIDAY_COTTAGE_GUESTHOUSE</displayCond>
									<items type="array">
										<numIndex index="0" type="array">
											<numIndex index="0">LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName_random</numIndex>
											<numIndex index="1">RANDOM</numIndex>
										</numIndex>
										<numIndex index="1" type="array">
											<numIndex index="0">LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName_alpha</numIndex>
											<numIndex index="1">ALPHA</numIndex>
										</numIndex>
									</items>
									<size>1</size>
									<minitems>0</minitems>
									<maxitems>1</maxitems>
								</config>
							</TCEforms>
						</hCandGH_sortName>
					</el>
				</ROOT>
			</paramSorting>
				';
			}
		}
		if ($flexData['data']['paramSelect']['lDEF']['dataGroup']['vDEF']=='RESTAURANT') {
			return '
			<paramSorting>
			<ROOT>
				<TCEforms>
					<sheetTitle>LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:paramSorting</sheetTitle>
				</TCEforms>
				<el>
					<restaurant_sortName>
						<TCEforms>
							<label>LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName</label>
							<config type="array">
								<type>select</type>
								<displayCond>FIELD:dataGroup:=:RESTAURANT</displayCond>
								<items type="array">
									<numIndex index="0" type="array">
										<numIndex index="0">LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName_random</numIndex>
										<numIndex index="1">RANDOM</numIndex>
									</numIndex>
									<numIndex index="1" type="array">
										<numIndex index="0">LLL:EXT:ics_sitlor_query/locallang_flexform_pi1.xml:sortName_price</numIndex>
										<numIndex index="1">PRICE</numIndex>
									</numIndex>
								</items>
								<size>1</size>
								<minitems>0</minitems>
								<maxitems>1</maxitems>
							</config>
						</TCEforms>
					</restaurant_sortName>
				</el>
			</ROOT>
			</paramSorting>
			';
		}
	}

}
