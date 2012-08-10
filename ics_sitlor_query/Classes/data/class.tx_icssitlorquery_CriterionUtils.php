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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   44: class tx_icssitlorquery_CriterionUtils
 *   90:     public static function addToTupleList(tx_icssitlorquery_ValuedTermTupleList $list, tx_icssitlorquery_ValuedTerm $element, $elIndex, $searchIndex, $searchedID, $tag)
 *  116:     public static function getCriterionFilter($criterionID, $terms=null)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
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
	 * Adds an element in a tuple list, with an associated element.
	 *
	 * @param	tx_icssitlorquery_ValuedTermTupleList		$list
	 * @param	tx_icssitlorquery_ValuedTerm		$element
	 * @param	int		$elIndex
	 * @param	int		$searchIndex
	 * @param	int		$searchedID
	 * @return	void
	 */
	public static function addToTupleList(tx_icssitlorquery_ValuedTermTupleList $list, tx_icssitlorquery_ValuedTerm $element, $elIndex, $searchIndex, $searchedID, $tag) {
		$tuple_exists = false;
		for ($i=0; $i<$list->Count(); $i++) {
			$tuple = $list->Get($i);
			$item = $tuple->Get($searchIndex);
			if (isset($item) && $item->Criterion->ID == $searchedID) {
				$tuple_exists = true;
				$tuple->Set($elIndex, $element);
				$list->Set($i, $tuple);
				break;
			}
		}
		if (!$tuple_exists) {
			$tuple = t3lib_div::makeInstance('tx_icssitlorquery_ValuedTermTuple', 2, $tag);
			$tuple->Set($elIndex, $element);
			$list->Add($tuple);
		}
	}

	/**
	 * Creates a criterion filter.
	 *
	 * @param	int		$criterionID: The criterion ID.
	 * @param	array		$terms: The criterions' terms IDs. Optional.
	 * @return	tx_icssitlorquery_CriterionFilter		The criterion filter.
	 */
	public static function getCriterionFilter($criterionID, $terms=null) {
		if (!is_int($criterionID))
			$criterionID = intval($criterionID);

		$criterion = tx_icssitlorquery_CriterionFactory::GetCriterion($criterionID);
		if (!$terms || empty($terms)) {
			$list = null;
		} else {
			$list = t3lib_div::makeInstance('tx_icssitlorquery_TermList');
			foreach ($terms as $term) {
				$list->Add(tx_icssitlorquery_CriterionFactory::GetCriterionTerm($criterion, intval($term)));
			}
		}
		return t3lib_div::makeInstance('tx_icssitlorquery_CriterionFilter', $criterion, $list);
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
	const ALLOWED_PETS_YES = 101;
	const ALLOWED_GROUP = 736000012;
	const ALLOWED_GROUP_NUMBER = 736000409;
	const RECEPTION_GROUP = 736000142;
	const MOTORCOACH_PARK = 736000014;
	const MOTORCOACH_PARK_YES = 107;
	const OPENING_24_24 = 736000325;
	const SERVICEOPEN = 736000146;
	const SERVICEOPEN_CLOSEDAY = 7;
	const OPENDAY = 736001020;

	/*******************************************************
	 *
	 * Price
	 *
	 *******************************************************/
	const CURRENT_SINGLE_CLIENTS_RATE = 4000010;
	const CURRENT_SINGLE_CLIENTS_RATE_DOUBLEROOM_MIN = 4000048;
	const CURRENT_SALE_FORMULA = 4000016;
	const CURRENT_CARTE_PRICE = 4000017;
	const CURRENT_MENU_PRICE = 4000018;
	const CURRENT_MENU_PRICE_CHILD = 4000089;
	const CURRENT_MENU_PRICE_ADULT = 4000090;
	const CURRENT_MENU_PRICE_GROUP = 4000091;
	
	const CURRENT_WEEKRATE = 4000006;	// Tarifs à la semaine (année en cours)
	const CURRENT_WEEKRATE_LOW_SEASON = 4000029;	// Basse saison

	/*******************************************************
	 *
	 * Hotel
	 *
	 *******************************************************/
	const COMFORT_ROOM = 736000018;
	const WIFI = 155;	// Term"// Term of Criterion COMFORT_ROOM
	const HOTEL_EQUIPMENT = 736000019;
	const HOTEL_SERVICE = 736000020;
	const STRANGE_ACCOMODATION = 4000099;
	const STRANGE_ACCOMODATION_YES = 4000396;

	/*******************************************************
	 *
	 * Restaurant
	 *
	 *******************************************************/
	const RESTAURANT_CAPACITY = 736000151;
	const RCATEGORIE = 736000114;
		// Term of Criterion RCATEGORIE
	const RCATEGORIE_FASTFOOD = 9;
	const RCATEGORIE_ICECREAM_THEAHOUSE = 12;
	const RCATEGORIE_CREPERIE = 3;

	const FOREIGN_FOOD = 736000237;
		// Term of Criterion FOREIGN_FOOD
	const FOREIGN_FOOD_ASIAN = 11;
	const FOREIGN_FOOD_SA = 8;
	const FOREIGN_FOOD_ORIENTAL = 13;

	/*******************************************************
	 *
	 * Event
	 *
	 *******************************************************/
	const KIND_OF_EVENT = 736001150;
	const TYPE_EVENT = 736001148;
	const COMPLEMENTARY_INFORMATION = 736001151;
	const LORRAINE_FESTIVAL = 736001149;
	const CURRENT_FREE = 4000003;
		// Term of Criterion CURRENT_FREE
	const CURRENT_FREE_YES = 4000014;
	const CURRENT_FREE_NO = 4000015;

	const CURRENT_BASE_PRICE = 4000004;

	/*******************************************************
	 *
	 * Free time
	 *
	 *******************************************************/
	const FREETIME = 737000034;
	const FT_FAMILY = 737000249;
	const FT_NOFEE = 737000248;
	const FT_EVENING = 737000250;
	const FT_OUTDOOR = 737000247;
	const FT_RAIN = 737000246;
	const FT_WEEKEND = 737000251;


	/*******************************************************
	 *
	 * Subscriber
	 *
	 *******************************************************/
	const ARTS_CRAFTS = 737000002;
	const SUBSCRIBER_JEWELS = 737000004;		// Term "Bijoux - Accessoires" of ARTS_CRAFTS
	const SUBSCRIBER_WOOD = 737000005;			// Term "Bois" of ARTS_CRAFTS
	const SUBSCRIBER_EMBROIDERY = 737000006;	// Term "Broderie" of ARTS_CRAFTS
	const SUBSCRIBER_CERAMIC_FAIENCE = 737000013;	// Term "Céramique - Faïence" of ARTS_CRAFTS
	const SUBSCRIBER_FASHION = 737000007;		// Term "Confection - Accessoires" of ARTS_CRAFTS
	const SUBSCRIBER_GRAPHIC_CREATION = 737000229;	// Term "Création Graphique" of ARTS_CRAFTS
	const SUBSCRIBER_CRYSTAL = 737000230;		// Term "Cristal" of ARTS_CRAFTS
	const SUBSCRIBER_MISCELLANEAOUS = 737000016;	// Term "Divers" of ARTS_CRAFTS
	const SUBSCRIBER_EMAIL = 737000008;		// Term "Email" of ARTS_CRAFTS
	const SUBSCRIBER_METAL = 737000009;		// Term "Métal" of ARTS_CRAFTS
	const SUBSCRIBER_MOSAIC = 737000015;		// Term "Mosaïque" of ARTS_CRAFTS
	const SUBSCRIBER_PAINT = 737000010;		// Term "Peinture" of ARTS_CRAFTS
	const SUBSCRIBER_SCULPTURE = 737000014;	// Term "Sculpture" of ARTS_CRAFTS
	const SUBSCRIBER_GLASS = 737000011;		// Term "Verre" of ARTS_CRAFTS
	const SUBSCRIBER_STAINED_GLASS = 737000012;	// Term "Vitrail" of ARTS_CRAFTS
	static $artsAndCrafts = array(
		'jewels' => self::SUBSCRIBER_JEWELS,
		'wood' => self::SUBSCRIBER_WOOD,
		'embroidery' => self::SUBSCRIBER_EMBROIDERY,
		'ceramic' => self::SUBSCRIBER_CERAMIC_FAIENCE,
		'fashion' => self::SUBSCRIBER_FASHION,
		'graphic_creation' => self::SUBSCRIBER_GRAPHIC_CREATION,
		'crystal' => self::SUBSCRIBER_CRYSTAL,
		'miscellaneaous' => self::SUBSCRIBER_MISCELLANEAOUS,
		'email' => self::SUBSCRIBER_EMAIL,
		'metal' => self::SUBSCRIBER_METAL,
		'mosaic' => self::SUBSCRIBER_MOSAIC,
		'paint' => self::SUBSCRIBER_PAINT,
		'sculpture' => self::SUBSCRIBER_SCULPTURE,
		'glass' => self::SUBSCRIBER_GLASS,
		'stained_glass' => self::SUBSCRIBER_STAINED_GLASS,
	);
	
	const COMMERCE = 737000009;
	const SUBSCRIBER_FOOD = 737000051;	// Alimentation
	const SUBSCRIBER_ART_AND_CRATF = 737000052;	// Art - Création
	const SUBSCRIBER_OTHER_COMMERCE = 737000063;	// Autres
	const SUBSCRIBER_TOURISTIC_DRIVE = 737000053;	// Balades touristisques
	const SUBSCRIBER_JEWELLERY = 737000054;	// Bijouterie
	const SUBSCRIBER_COSMETIC_PARFUM = 737000232;	// Cosmétiques / Parfums
	const SUBSCRIBER_LEARNED = 737000060;	// Culture
	const SUBSCRIBER_FRAMING = 737000055;	// Galerie - Encadrement
	const SUBSCRIBER_WEAR = 737000056;	// Habillement
	const SUBSCRIBER_ACCOMODATION = 737000057;	// Hébergement
	const SUBSCRIBER_ACTIVITIES = 737000058;	// Loisirs - Activités
	const SUBSCRIBER_SPORTS = 737000059;	// Loisirs - Sports
	const SUBSCRIBER_HOME_DECORATION = 737000061;	// Maison - Décoration
	const SUBSCRIBER_SERVICES = 737000062;	// Services
	const SUBSCRIBER_CATERER = 737000240;	// Traiteur
	static $commerces = array(
		'food' => self::SUBSCRIBER_FOOD,
		'art_and_craft' => self::SUBSCRIBER_ART_AND_CRATF,
		'other' => self::SUBSCRIBER_OTHER_COMMERCE,
		'touristic_drive' => self::SUBSCRIBER_TOURISTIC_DRIVE,
		'jewellery' => self::SUBSCRIBER_JEWELLERY,
		'cosmetic_parfum' => self::SUBSCRIBER_COSMETIC_PARFUM ,
		'learned' => self::SUBSCRIBER_LEARNED,
		'framing' => self::SUBSCRIBER_FRAMING,
		'wear' => self::SUBSCRIBER_WEAR,
		'accomodation' => self::SUBSCRIBER_ACCOMODATION,
		'activities' => self::SUBSCRIBER_ACTIVITIES,
		'sports' => self::SUBSCRIBER_SPORTS,
		'decoration' => self::SUBSCRIBER_HOME_DECORATION,
		'services' => self::SUBSCRIBER_SERVICES,
		'caterer' => self::SUBSCRIBER_CATERER,
	);




	const OTNANCY2011 = 737000028;
	const OTNANCY2012 = 737000029;
	
	const ONLINE_BOOKING = 737000020;	// Adhérent centrale de réservation
	const ONLINE_BOOKING_NO = 737000198;
	const ONLINE_BOOKING_YES = 737000197;
}