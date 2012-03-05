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
	static $categories;	// tx_icssitlorquery_CategoryList
	static $types;		// tx_icssitlorquery_TypeList
	static $categoriesTypes = array();	// TypeList[]
	static $typesCategory = array();	// Category[]

	static $login;
	static $password;
	static $url;

	private static $hash;
	private static $cacheInstance;
	private static $lifetime = 0;

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
						$hasGenres = true;
						$elements = self::readElement_Genres($reader);
						$all = array_merge($all, $elements);
						break;

					default:
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
		if (!$hasGenres)
			throw new Exception('Nomenclature has any Genres.');

		self::$cacheInstance->set(self::$hash, serialize($all), array(), self::$lifetime);

		return $all;
	}

	/**
	 * Read Element
	 *
	 * @param	XMLReader		$reader : Reader to the parsed document
	 * @return	void
	 */
	private static function readElement_Genres(XMLReader $reader) {
		$reader->read();
		$elements = array();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				switch ($reader->name) {
					case 'Categories':
						$types = t3lib_div::makeInstance('tx_icssitlorquery_TypeList');
						if ($category = tx_icssitlorquery_Category::FromXML($reader, $types)) {
							$elements[] = array(
								$category,
								$types
							);
						}
						break;

					default:
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
		return $elements;
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

		self::$categories = t3lib_div::makeInstance('tx_icssitlorquery_CategoryList');
		self::$types = t3lib_div::makeInstance('tx_icssitlorquery_TypeList');

		self::$hash = md5(self::$url . self::$login . self::$password);
		if (!self::$cacheInstance->has(self::$hash))
			self::FetchValues();
		$all = unserialize(self::$cacheInstance->get(self::$hash));
		if ($all === false) {
			throw new Exception('Nomenclature on cache is broken.');
		}

		foreach ($all as $element) {
			self::$categories->Add($element[0]);
			self::$categoriesTypes[$element[0]->ID] = $element[1];
			for ($i=0; $i<$element[1]->Count(); $i++) {
				$type = $element[1]->Get($i);
				self::$types->Add($type);
				self::$typesCategory[$type->ID] = $element[0];
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
	 * Retrieves Category
	 *
	 * @param	int		$id : id of Category
	 * @return	Category
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
	 * @return	CategoryList
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
	 * @return	Type
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
	 * @return	TypeList
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
	 * Retrieves CategoryList
	 *
	 * @return	CategoryList
	 */
	public static function GetAllCategories() {
		self::initialize();
		return self::$categories;
	}

	/**
	 * Retrieves Types from Category
	 *
	 * @param	tx_icssitlorquery_Category		$Category
	 * @return	TypeList
	 */
	public static function GetCategoryTypes(tx_icssitlorquery_Category $category) {
		self::initialize();
		return self::$categoriesTypes[$category->ID];
	}

	/**
	 * Retrieves Category from Type
	 *
	 * @param	tx_icssitlorquery_Type		$type
	 * @return	Category
	 */
	public static function GetTypeCategory(tx_icssitlorquery_Type $type) {
		self::initialize();
		return self::$typesCategory[$type->ID];
	}

	/**
	 * Retrieves Types
	 *
	 * @param	tx_icssitlorquery_TypeList		$source
	 * @param	int		array $ids
	 * @return	TypeList
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
		self::$categories = null;
		self::$types = null;
		self::$categoriesTypes = array();
		self::$typesCategory = array();

		self::$login = $login;
		self::$password = $password;
		self::$url = $url;
	}
}