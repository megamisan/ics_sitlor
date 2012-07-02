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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */

/**
 * Provides helper to batch configure settings for various data class.
 * Support defining default connection parameters, default TypoScript rendering,
 * default list rendering and type guessing.
 *
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_Configurator {
	/**
	 * Sets connection parameters to factories.
	 *
	 * @param	string		$login: The login.
	 * @param	string		$password: The password.
	 * @param	string		$nomenclatureUrl: The url for nomenclature query.
	 * @param	string		$criterionUrl: The url for criterion query.
	 * @return	void
	 */
	public static function setConnection($login, $password, $nomenclatureUrl, $criterionUrl) {
		tx_icssitlorquery_NomenclatureFactory::SetConnectionParameters($login, $password, $nomenclatureUrl);
		tx_icssitlorquery_CriterionFactory::SetConnectionParameters($login, $password, $criterionUrl);
	}
	
	/**
	 * Sets the default type guessing rules to the specified query service.
	 *
	 * @param	tx_icssitlorquery_SitlorQueryService		$queryService: Service to set settings to.
	 * @return	void
	 */
	public static function setDefaultTypeGuessing(tx_icssitlorquery_SitlorQueryService $queryService) {
		$queryService->setTypeGuessingConf(
			array(
				'tx_icssitlorquery_FullAccomodation' => array('id', tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::ACCOMODATION)),
				'tx_icssitlorquery_Accomodation' => array(tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::ACCOMODATION)),
				'tx_icssitlorquery_FullRestaurant' => array('id', tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESTAURANT)),
				'tx_icssitlorquery_Restaurant' => array(tx_icssitlorquery_NomenclatureFactory::GetCategory(tx_icssitlorquery_NomenclatureUtils::RESTAURANT)),
				'tx_icssitlorquery_FullEvent' => array('id', tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::EVENT), tx_icssitlorquery_CriterionUtils::getCriterionFilter(tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT)),
				'tx_icssitlorquery_Event' => array(tx_icssitlorquery_NomenclatureFactory::GetKind(tx_icssitlorquery_NomenclatureUtils::EVENT), tx_icssitlorquery_CriterionUtils::getCriterionFilter(tx_icssitlorquery_CriterionUtils::KIND_OF_EVENT)),
			)
		);
	}

	/**
	 * Sets default TypoScript configuration for data rendering.
	 *
	 * @param	array		$defaultConf: The default TypoScript configuration for each types.
	 * @return	void
	 */
	public static function setDefaultConf(array $defaultConf) {
		$common = array('Address', 'Coordinates', 'Link', 'Name', 'Phone', 'Picture');
		foreach ($defaultConf as $type => $conf) {
			if ($type{strlen($type) - 1} != '.')
				continue;
			$type = substr($type, 0, -1);
			if (in_array($type, $common)) {
				$class = 'tx_icssitquery_' . $type;
			}
			else {
				$class = 'tx_icssitlorquery_' . $type;
			}
			if ($type == 'ValuedTermTuple') {
				foreach ($conf as $tag => $subconf) {
					if ($tag{strlen($tag) - 1} != '.')
						continue;
					$tag = substr($tag, 0, -1);
					call_user_func(array($class, 'SetDefaultConf'), $tag, $subconf);
				}
			}
			else {
				try {
					call_user_func(array($class, 'SetDefaultConf'), $conf);
				}
				catch (Exception $e) {
					t3lib_div::devLog($class . ' default conf', 'ics_sitlor_query', 0, $conf);
				}
			}
		}
	}

	/**
	 * Sets default separator for list rendering.
	 *
	 * @param	array		$defaultConf: The default separators for each list types.
	 * @return	void
	 */
	public static function setDefaultSeparator(array $defaultConf) {
		foreach ($defaultConf as $type => $conf) {
			$class = 'tx_icssitlorquery_' . $type;
			if ($class != 'tx_icssitlorquery_TimeTableList')
				$class .= 'List';
			tx_icssitlorquery_AbstractList::setDefaultSeparator($conf, $class);
		}
	}
}
