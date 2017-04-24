<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In Cité Solution <technique@in-cite.net>
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

class tx_icssitlorquery_product_task  extends tx_scheduler_Task {

	public function execute() {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
		$cacheTime = intval($extConf['cacheTime']);
		$time = $_SERVER['REQUEST_TIME'];
		
		// Nettoie les requêtes de récupérations de "produits"
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_icssitlorquery_cache',
			'('.$time.'-crdate)>'.$cacheTime.' AND lifetime>0 OR (lifetime>0 AND lifetime<='.$cacheTime.')'
		);
		return true;
	}

}

class tx_icssitlorquery_cleanAll_task  extends tx_scheduler_Task {

	public function execute() {
		// Nettoie tous les caches
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_icssitlorquery_cache',
			'1'
		);
		return true;
	}

}
