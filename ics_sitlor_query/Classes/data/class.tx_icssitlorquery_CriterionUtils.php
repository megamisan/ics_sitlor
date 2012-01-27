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
 * Class 'tx_icssitlorquery_CriterionUtils' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_CriterionUtils {
	/*******************************************************
	 *
	 * Photos - Illustrations
	 *
	 *******************************************************/
	const PHOTO = 736000294;
	const PHOTO2 = 736001142;
	const PHOTO3 = 736001115;
	const PHOTO4 = 736001116;
	const PHOTO5 = 4000060;
	const PHOTO6 = 4000061;
	const CREDIT_PHOTO = 736001119;
	const CREDIT_PHOTO2 = 736001143;
	const CREDIT_PHOTO3 = 736001117;
	const CREDIT_PHOTO4 = 736001118;
	const CREDIT_PHOTO5 = 4000062;
	const CREDIT_PHOTO6 = 4000063;

	static $photos = array(
		self::PHOTO,
		self::PHOTO2,
		self::PHOTO3,
		self::PHOTO4,
		self::PHOTO5,
		self::PHOTO6,
	);
	static $creditPhotos = array(
		self::CREDIT_PHOTO,
		self::CREDIT_PHOTO2,
		self::CREDIT_PHOTO3,
		self::CREDIT_PHOTO4,
		self::CREDIT_PHOTO5,
		self::CREDIT_PHOTO6,
	);

	/**
	 * Add element in tuple list
	 *
	 * @param	tx_icssitlorquery_ValuedTermTupleList $list
	 * @param	tx_icssitlorquery_ValuedTerm $element
	 * @param	int $elIndex
	 * @param	int $searchIndex
	 * @param	int $searchedID
	 * @return	void
	 */
	public static function addToTupleList(tx_icssitlorquery_ValuedTermTupleList $list, tx_icssitlorquery_ValuedTerm $element, $elIndex, $searchIndex, $searchedID) {
		$tuple_exists = false;
		for ($i=0; $i<$list->Count(); $i++) {
			$tuple = $list->Get($i);
			$item = $tuple->Get($searchIndex);
			if (isset($item) && $item->ID == $searchedID) {
				$tuple_exists = true;
				$tuple->Set($elIndex, $element);
				$list->Set($i, $tuple);
				break;
			}
		}
		if (!$tuple_exists) {
			$tuple = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermTuple', 2);
			$tuple->Set($elIndex, $element);
			$list->Add($tuple);
		}
	}

	/*******************************************************
	 *
	 * Accomodation's category - Rating star
	 *
	 *******************************************************/
	const RATINGSTAR = 736000015;

	/*******************************************************
	 *
	 * Restaurant's category - chain/label
	 *
	 *******************************************************/
	const CHAIN_LABEL = 736000119;
	const RESTAURANT_CLASS = 736000114;

	/*******************************************************
	 *
	 * Reception
	 *
	 *******************************************************/
	const RECEPTION_LANGUAGE = 736000079;
	const RESERVATION_LANGUAGE = 736000005;
	const MENU_LANGUAGE = 736000254;
	const MOBILITY_IMPAIRED = 736000131;
	const PETS = 736001106;
	const ALLOWED_PETS = 736000010;
	const ALLOWED_GROUP = 736000012;
	const ALLOWED_GROUP_NUMBER = 736000409;
	const RECEPTION_GROUP = 736000142;
	const MOTORCOACH_PARK = 736000014;
	const OPENING_24_24 = 736000325;
	const SERVICEOPEN = 736000146;

	/*******************************************************
	 *
	 * Price
	 *
	 *******************************************************/
	const CURRENT_SINGLE_CLIENTS_RATE = 4000010;
	const CURRENT_SALE_FORMULA = 4000016;
	const CURRENT_CARTE_PRICE = 4000017;
	const CURRENT_MENU_PRICE = 4000018;

	/*******************************************************
	 *
	 * Hotel
	 *
	 *******************************************************/
	const COMFORT_ROOM = 736000018;
	const HOTEL_EQUIPMENT = 736000019;
	const HOTEL_SERVICE = 736000020;

	/*******************************************************
	 *
	 * Restaurant
	 *
	 *******************************************************/
	const RESTAURANT_CAPACITY = 736000151;
	
	/*******************************************************
	 *
	 * Event
	 *
	 *******************************************************/	
	const KIND_OF_EVENT = 736001150;
	const TYPE_EVENT = 736001148;
	const COMPLEMENTARY_INFORMATION = 736001151;
	const LORRAINE_FESTIVAL = 736001149;

}