<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 In Cite Solution <technique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the NomenclatureFactorys of the GNU General Public License as published by
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
 *   57: class tx_icssitlorquery_NomenclatureFactory
 *   76:     private static function FetchValues()
 *  133:     private static function readElement_Genres(XMLReader $reader)
 *  164:     private static function LoadFromCache()
 *  204:     private static function initialize()
 *  220:     public static function GetCategory($id)
 *  237:     public static function GetCategories(array $ids)
 *  255:     public static function GetType($id)
 *  272:     public static function GetTypes(array $ids)
 *  289:     public static function GetAllCategories()
 *  300:     public static function GetCategoryTypes(tx_icssitlorquery_Category $category)
 *  311:     public static function GetTypeCategory(tx_icssitlorquery_Type $type)
 *  323:     public static function FilterTypesByIds(tx_icssitlorquery_TypeList $source, array $ids)
 *  343:     public static function SetConnectionParameters($login, $password, $url)
 *
 * TOTAL FUNCTIONS: 13
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitlorquery_NomenclatureFactory' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_NomenclatureFactory {
	static $kinds;	// tx_icssitlorquery_KindList
	static $kindsCategories = array();	// tx_icssitlorquery_CategoryList[]
	static $categories;	// tx_icssitlorquery_CategoryList
	static $types;		// tx_icssitlorquery_TypeList
	static $categoriesTypes = array();	// tx_icssitlorquery_TypeList[]
	static $categoriesKind = array();	// tx_icssitlorquery_Kind[]
	static $typesCategory = array();	// tx_icssitlorquery_Category[]

	static $login;
	static $password;
	static $url;

	private static $hash;
	private static $cacheInstance;
	

	/**
	 * Fetch values
	 *
	 * @return	mixed
	 */
	private static function FetchValues() {
		$params = array(
			'user' => utf8_decode(self::$login),
			'pwkey' => utf8_decode(self::$password),
		);
		$url = self::$url . '?' . http_build_query($params);

		$xmlContent = tx_icssitlorquery_XMLTools::getXMLDocument($url);
		if (!$xmlContent) {
			tx_icssitquery_Debug::error('Unable to read nomenclature XML document at ' . $url);
			return false;
		}

		$reader = new XMLReader();
		file_put_contents(t3lib_div::getFileAbsFileName('typo3temp/sitlor_nomenclature_out.xml'), $xmlContent);
		$reader->XML($xmlContent);
		if (!tx_icssitlorquery_XMLTools::XMLMoveToRootElement($reader, 'LEI')) {
			tx_icssitquery_Debug::error('Invalid response from SITLOR nomenclature.');
			return false;
		}
		$reader->read();
		if (!$reader->next('Resultat')) {
			tx_icssitquery_Debug::error('Can not reach "Resultat" node from SITLOR nomenclature.');
			return false;
		}

		$reader->read();
		$all = array();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				switch ($reader->name) {
					case 'Genres':
						$categories = t3lib_div::makeInstance('tx_icssitlorquery_CategoryList');
						$categoriesTypes = array();
						if ($kind = tx_icssitlorquery_Kind::FromXML($reader, $categories, $categoriesTypes)) {
							$all[] = array(
								$kind,
								$categories,
								$categoriesTypes
							);
						}
						break;
					default:
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
		$reader->close();
		
		if (empty($all))
			throw new Exception('Nomenclature has no Kind.');

		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
		$lifetime = intval($extConf['nomenclature_cacheTime']);
		self::$cacheInstance->set(self::$hash, serialize($all), array(), $lifetime);

		return $all;
	}

	/**
	 * Load from cache
	 *
	 * @return	void
	 */
	private static function LoadFromCache() {
		t3lib_cache::initializeCachingFramework();
        try {
            self::$cacheInstance = $GLOBALS['typo3CacheManager']->getCache('icssitlorquery_cache');
        } catch (t3lib_cache_exception_NoSuchCache $e) {
            self::$cacheInstance = $GLOBALS['typo3CacheFactory']->create(
                'icssitlorquery_cache',
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['frontend'],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['backend'],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['options']
            );
        }

		self::$kinds = t3lib_div::makeInstance('tx_icssitlorquery_KindList');
		self::$categories = t3lib_div::makeInstance('tx_icssitlorquery_CategoryList');
		self::$types = t3lib_div::makeInstance('tx_icssitlorquery_TypeList');

		self::$hash = md5(self::$url . self::$login . self::$password);
		if (!self::$cacheInstance->has(self::$hash))
			self::FetchValues();
		$all = unserialize(self::$cacheInstance->get(self::$hash));
		if ($all === false) {
			throw new Exception('Nomenclature on cache is broken.');
		}

		foreach ($all as $kindDef) {
			self::$kinds->Add($kindDef[0]);
			self::$kindsCategories[$kindDef[0]->ID] = $kindDef[1];
			foreach ($kindDef[2] as $categoryDef) {
				self::$categories->Add($categoryDef[0]);
				self::$categoriesTypes[$categoryDef[0]->ID] = $categoryDef[1];
				self::$categoriesKind[$categoryDef[0]->ID] = $kindDef[0];
				for ($i = 0; $i < $categoryDef[1]->Count(); $i++) {
					$type = $categoryDef[1]->Get($i);
					self::$types->Add($type);
					self::$typesCategory[$type->ID] = $categoryDef[0];
				}
			}
		}
	}

	/**
	 * Initialize the factory
	 *
	 * @return	void
	 */
	private static function initialize() {
		if (!self::$login || !self::$password || !self::$url)
			throw new Exception('Nomenclature connection parameters must be set.');

		if (isset(self::$categories))
			return;

		self::LoadFromCache();
	}

	/**
	 * Retrieves Kind
	 *
	 * @param	int		$id : id of Kind
	 * @return	tx_icssitlorquery_Kind
	 */
	public static function GetKind($id) {
		self::initialize();
		for ($i = 0; $i < self::$kinds->Count(); $i++) {
			$kind = self::$kinds->Get($i);
			if ($kind->ID == $id) {
				return $kind;
			}
		}
		return null;
	}

	/**
	 * Retrieves KindList
	 *
	 * @param	int		array $ids : ids of kinds
	 * @return	tx_icssitlorquery_KindList
	 */
	public static function GetKinds(array $ids) {
		self::initialize();
		$kinds = t3lib_div::makeInstance('tx_icssitlorquery_KindList');
		for ($i = 0; $i < self::$categories->Count(); $i++) {
			$kind = self::$kinds->Get($i);
			if (in_array($kind->ID, $ids)) {
				$kinds->Add($kind);
			}
		}
		return $kinds;
	}

	/**
	 * Retrieves Category
	 *
	 * @param	int		$id : id of Category
	 * @return	tx_icssitlorquery_Category
	 */
	public static function GetCategory($id) {
		self::initialize();
		for ($i=0; $i<self::$categories->Count(); $i++) {
			$category = self::$categories->Get($i);
			if ($category->ID == $id) {
				return $category;
			}
		}
		return null;
	}

	/**
	 * Retrieves CategoryList
	 *
	 * @param	int		array $ids : ids of categories
	 * @return	tx_icssitlorquery_CategoryList
	 */
	public static function GetCategories(array $ids) {
		self::initialize();
		$categories = t3lib_div::makeInstance('tx_icssitlorquery_CategoryList');
		for ($i=0; $i<self::$categories->Count(); $i++) {
			$category = self::$categories->Get($i);
			if (in_array($category->ID, $ids)) {
				$categories->Add($category);
			}
		}
		return $categories;
	}

	/**
	 * Retrieves Type
	 *
	 * @param	int		$id : id of type
	 * @return	tx_icssitlorquery_Type
	 */
	public static function GetType($id) {
		self::initialize();
		for ($i=0; $i<self::$types->Count(); $i++) {
			$type = self::$types->Get($i);
			if ($type->ID == $id) {
				return $type;
			}
		}
		return null;
	}

	/**
	 * Retrieves TypeList
	 *
	 * @param	int		array $ids : ids of types
	 * @return	tx_icssitlorquery_TypeList
	 */
	public static function GetTypes(array $ids) {
		self::initialize();
		$types = t3lib_div::makeInstance('tx_icssitlorquery_TypeList');
		for ($i=0; $i<self::$types->Count(); $i++) {
			$type = self::$types->Get($i);
			if (in_array($type->ID, $ids)) {
				$types->Add($type);
			}
		}
		return $types;
	}

	/**
	 * Retrieves KindList
	 *
	 * @return	tx_icssitlorquery_KindList
	 */
	public static function GetAllKinds() {
		self::initialize();
		return self::$kinds;
	}

	/**
	 * Retrieves CategoryList
	 *
	 * @return	tx_icssitlorquery_CategoryList
	 */
	public static function GetAllCategories() {
		self::initialize();
		return self::$categories;
	}

	/**
	 * Retrieves Categories from Kind
	 *
	 * @param	tx_icssitlorquery_Kind		$kind
	 * @return	tx_icssitlorquery_CategoryList
	 */
	public static function GetKindCategories(tx_icssitlorquery_Category $kind) {
		self::initialize();
		return self::$kindsCategories[$kind->ID];
	}

	/**
	 * Retrieves Types from Category
	 *
	 * @param	tx_icssitlorquery_Category		$Category
	 * @return	tx_icssitlorquery_TypeList
	 */
	public static function GetCategoryTypes(tx_icssitlorquery_Category $category) {
		self::initialize();
		return self::$categoriesTypes[$category->ID];
	}

	/**
	 * Retrieves Kind from Category
	 *
	 * @param	tx_icssitlorquery_Category		$category
	 * @return	tx_icssitlorquery_Kind
	 */
	public static function GetCategoryKind(tx_icssitlorquery_Category $category) {
		self::initialize();
		return self::$categoriesKind[$category->ID];
	}

	/**
	 * Retrieves Category from Type
	 *
	 * @param	tx_icssitlorquery_Type		$type
	 * @return	tx_icssitlorquery_Category
	 */
	public static function GetTypeCategory(tx_icssitlorquery_Type $type) {
		self::initialize();
		return self::$typesCategory[$type->ID];
	}

	/**
	 * Retrieves Categories
	 *
	 * @param	tx_icssitlorquery_CategoryList		$source
	 * @param	int		array $ids
	 * @return	tx_icssitlorquery_CategoryList
	 */
	public static function FilterCategoriesByIds(tx_icssitlorquery_CategoryList $source, array $ids) {
		self::initialize();
		$categories = t3lib_div::makeInstance('tx_icssitlorquery_CategoryList');
		for ($i = 0; $i < $source->Count(); $i++) {
			$category = $source->Get($i);
			if (in_array($category->ID, $ids)) {
				$categories->Add($category);
			}
		}
		return $categories;
	}

	/**
	 * Retrieves Types
	 *
	 * @param	tx_icssitlorquery_TypeList		$source
	 * @param	int		array $ids
	 * @return	tx_icssitlorquery_TypeList
	 */
	public static function FilterTypesByIds(tx_icssitlorquery_TypeList $source, array $ids) {
		self::initialize();
		$types = t3lib_div::makeInstance('tx_icssitlorquery_TypeList');
		for ($i=0; $i<$source->Count(); $i++) {
			$type = $source->Get($i);
			if (in_array($type->ID, $ids)) {
				$types->Add($type);
			}
		}
		return $types;
	}

	/**
	 * Set connection parameters
	 *
	 * @param	string		$login
	 * @param	string		$password
	 * @param	string		$url
	 * @return	void
	 */
	public static function SetConnectionParameters($login, $password, $url) {
		self::$kinds = null;
		self::$kindsCategories = array();
		self::$categories = null;
		self::$types = null;
		self::$categoriesTypes = array();
		self::$categoriesKind = array();
		self::$typesCategory = array();

		self::$login = $login;
		self::$password = $password;
		self::$url = $url;
	}
}