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
 *   55: class tx_icssitlorquery_ValuedTermTuple implements tx_icssitquery_IToStringObjConf
 *   69:     public function __construct($count, $tag='')
 *   86:     public function __get($name)
 *  108:     public function __set($name, $value)
 *  127:     public function Get($number)
 *  141:     public function Set($number, tx_icssitlorquery_ValuedTerm $value = null)
 *  154:     public function SetDefaultConf($tag, array $conf)
 *  163:     public function __toString()
 *  174:     public function toString()
 *  199:     public function toStringConf(array $conf)
 *  212:     public function toStringObj(tslib_cObj $cObj)
 *  233:     public function toStringObjConf(tslib_cObj $cObj, array $conf)
 *
 * TOTAL FUNCTIONS: 11
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Class 'tx_icssitlorquery_ValuedTermTuple' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_ValuedTermTuple implements tx_icssitquery_IToStringObjConf {
	private $count;
	private $tag;
	private $items = array();

	private static $lConf = array();

	/**
	 * Constructor
	 *
	 * @param	int		$count
	 * @param	string		$tag
	 * @return	void
	 */
	public function __construct($count, $tag='') {
		$this->count = $count;
		$this->tag = $tag;
		for ($i=0; $i<$this->count; $i++) {
			$this->items[$i] = null;
		}
		if (!isset(self::$lConf[$this->tag])) {
			self::$lConf[$this->tag] = array();
		}
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @return	mixed		The property's value if exists.
	 */
	public function __get($name) {
		if ($name=='Count')
			return $this->count;

		if ((strlen($name)>=5) && (substr($name, 0, 4)=='Item')) {
			$numItem = substr($name, 4);
			if (is_numeric($numItem) && $numItem<=$this->count && $numItem>0)
				return $this->Get(intval($numItem) - 1);
			else
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		} else {
			tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Obtains a property. PHP magic function.
	 *
	 * @param	string		$name: Property's name.
	 * @param	mixed		$value: Property's value.
	 * @return	void
	 */
	public function __set($name, $value) {
		if ((strlen($name)>=5) && (substr($name,0,4)=='Item')) {
			$numItem = substr($name, 4);
			if (is_numeric($numItem) && $numItem<=$this->count && $numItem>0) {
				$this->Set(intval($numItem) - 1, $value);
			} else {
				tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
			}
		} else {
			tx_icssitquery_debug::notice('Undefined property in ' . __CLASS__ . ' via ' . __FUNCTION__ . '(): ' . $name);
		}
	}

	/**
	 * Retrieves ValuedTerm
	 *
	 * @param	int		$number
	 * @return	ValuedTerm
	 */
	public function Get($number) {
		if ($number<$this->count && $number>=0)
			return $this->items[$number];
		else
			tx_icssitquery_debug::warning('Index out of range.');
	}

	/**
	 * Set ValuedTerm
	 *
	 * @param	int		$number
	 * @param	ValuedTerm		$value
	 * @return	void
	 */
	public function Set($number, tx_icssitlorquery_ValuedTerm $value = null) {
		if ($number<$this->count)
			$this->items[$number] = $value;
		else
			tx_icssitquery_debug::warning('Index out of range.');
	}

	/**
	 * Sets default TypoScript configuration.
	 *
	 * @param	array		$conf: The new default configuration.
	 * @return	void
	 */
	public function SetDefaultConf($tag, array $conf) {
		self::$lConf[$tag] = $conf;
	}

	/**
	 * Converts this object to its string representation. PHP magic function.
	 *
	 * @return	string		Representation of the object.
	 */
	public function __toString() {
		$args = func_get_args();
		return (string)call_user_func_array(array($this, 'toString'), $args);
	}

	/**
	 * Converts this object to its string representation.
	 * Using default output settings.
	 *
	 * @return	string		Representation of the object.
	 */
	public function toString() {
		switch (func_num_args()) {
			case 0:
				return $this->toStringConf(self::$lConf[$this->tag]);
			case 1:
				$a1 = func_get_arg(0);
				if (is_array($a1)) {
					return $this->toStringConf($a1);
				}
				else if ($a1 instanceof tslib_cObj) {
					return $this->toStringObj($a1);
				}
			default:
				$args = func_get_args();
				return call_user_func_array(array($this, 'toStringObjConf'), $args);
		}
	}

	/**
	 * Converts this object to its string representation.
	 * Uses the specified TypoScript configuration.
	 *
	 * @param	array		$conf: TypoScript configuration to use to render this object.
	 * @return	string		Representation of the object.
	 */
	public function toStringConf(array $conf) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start(array(), '');
		return $this->toStringObjConf($cObj, $conf);
	}

	/**
	 * Converts this object to its string representation.
	 * Uses the specified content object.
	 *
	 * @param	tslib_cObj		$cobj: Content object used as parent.
	 * @return	string		Representation of the object.
	 */
	public function toStringObj(tslib_cObj $cObj) {
		return toStringObjConf($cObj, self::$lConf[$this->tag]);
	}

	/**
	 * Converts this object to its string representation.
	 * Uses the specified TypoScript configuration and content object.
	 * Data fields:
	 * * item_n_: The value of an item. _n_ is the item number, starting at one (1).
	 * * count: Number of items.
	 * TypoScript special elements:
	 * * item_n__conf: The rendering configuration of an item. _n_ is the item number, starting at one (1).
	 *
	 * is a item_n__conf is rendered using default rendering for other items.
	 * Finally, stdWrap is called on the updated data to give the final value.
	 *
	 * @param	tslib_cObj		$cobj: Content object used as parent.
	 * @param	array		$conf: TypoScript configuration to use to render this object.
	 * @return	string		Representation of the object.
	 * @remarks The rendering is done in two pass. First, each item for which there
	 */
	public function toStringObjConf(tslib_cObj $cObj, array $conf) {
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$data = array(
			'count' => $this->count,
		);
		for ($i = 0; $i < $this->count; $i++) {
			$item = 'item' . ($i + 1);
			$data[$item] = $this->items[$i];
		}
		$local_cObj->start($data, 'ValuedTermTuples');
		tx_icssitlorquery_getDataHook::pushContext($data);
		for ($i = 0; $i < $this->count; $i++) {
			$item = 'item' . ($i + 1);
			if (($this->items[$i] != null) && isset($conf[$item . '_conf.'])) {
				$data[$item] = $this->items[$i]->toStringConf($conf[$item . '_conf.']);
			}
		}
		tx_icssitlorquery_getDataHook::popContext();
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$local_cObj->start($data, 'ValuedTermTuples');
		$local_cObj->setParent($cObj->data, $cObj->currentRecord);
		return $local_cObj->stdWrap('', $conf);
	}
}