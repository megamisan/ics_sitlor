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
 * Class 'tx_icssitlorquery_NomenclatureUtils' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */

class tx_icssitlorquery_NomenclatureUtils {
	const HOTEL = 4000002;				// Type "Hôtel"
	const HOTEL_RESTAURANT = 4000003;	// Type "Hôtel - hôtel restaurant"
	const FURNISHED = 4000012;			// Type "Meublé"
	static $hotel = array(
		self::HOTEL,
		self::HOTEL_RESTAURANT,
		self::FURNISHED,
	);
	
	const RESTAURANT = 4000007;	// Category "Restauration"
	
	const EVENT = 736001150;	// Type "Forme d'animation / manifestation"
}