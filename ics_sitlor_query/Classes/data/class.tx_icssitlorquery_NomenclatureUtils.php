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
 * Class 'tx_icssitlorquery_NomenclatureUtils' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_NomenclatureUtils {
	/*******************************************************
	 *
	 * Accomodation
	 *
	 *******************************************************/
	const HOTEL = 4000002;				// Type "Hôtel"
	const HOTEL_RESTAURANT = 4000003;	// Type "Hôtel - hôtel restaurant"
	const FURNISHED = 4000012;			// Type "Meublé"
	static $hotel = array(
		self::HOTEL_RESTAURANT,
		self::FURNISHED,
	);
	
	const HOLLIDAY_CAMPSITE = 4000007;	// Type "Terrain de Camping saisonnie"
	const CATEGORIZED_CAMPSITE = 4000004;	// Type "Terrain de Camping classé"
	const FARM_CAMPING = 4000006;	// Type "Camping à la ferme"
	const YOUTH_HOSTEL = 4000071;	// Type "Auberge de Jeunesse"
	static $campingAndYouthHostel = array(
		self::HOLLIDAY_CAMPSITE,
		self::CATEGORIZED_CAMPSITE,
		self::FARM_CAMPING,
		self::YOUTH_HOSTEL,
	);
	const GUESTHOUSE = 4000001;	// Category "Chambres d'hôtes"
	const HOLLIDAY_COTTAGE = 4000005; // Category "Meublé "
	
	
	/*******************************************************
	 *
	 * Restaurant
	 *
	 *******************************************************/
	const RESTAURANT = 4000007;	// Category "Restauration"

	/*******************************************************
	 *
	 * Event
	 *
	 *******************************************************/
	const EVENT = 4000003 ;	// Genre "A voir/ A faire"
}