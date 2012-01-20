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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Class 'tx_icssitlorquery_Criterion' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_Criterion implements tx_icssitquery_IToString {
	private $id;
	private $name;
	private $class;
	private $type;
	
	/**
	 * Private constructor ?
	 *
	 */
	private function __construct() {
	}
	
	/**
	 * Retrieves Criterion from XML
	 *
	 * @return Criterion
	 */
	public static function FromXML(XMLReader $reader, tx_icssitlorquery_TermList $terms) {
		$criterion = new tx_icssitlorquery_Criterion();
		$reader->read();
		while ($reader->nodeType != XMLReader::END_ELEMENT) {
			if ($reader->nodeType == XMLReader::ELEMENT) {
				switch ($reader->name) {
					case 'CRITERE':
						$criterion->id = intval($reader->readString());
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;					
					case 'CRITERE_NOM':
						$criterion->name = $reader->readString();
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'CRITERE_TYPEVAL':
						$criterion->type = intval($reader->readString());
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;					
					case 'CRITERE_CLASSE':
						$criterion->class = intval($reader->readString());
						tx_icssitlorquery_XMLTools::skipChildren($reader);
						break;
					case 'Modalites':
						if ($term = tx_icssitlorquery_Term::FromXML($reader)) {
							$terms->Add($term);
						}
						break;
					default : 
						tx_icssitlorquery_XMLTools::skipChildren($reader);
				}
			}
			$reader->read();
		}
		return $criterion;
	}

		/**
	 * Retrieves properties
	 *
	 * @param	string $name : Property's name
	 *
	 * @return name 's value
	 */
	public function __get($name) {
		switch ($name) 	{
			case 'ID':
				return $this->id;
			case 'Name':
				return $this->name;
			case 'Type':
				return $this->type;
			case 'Class':
				return $this->class;
			case 'Modalites':
				return tx_icssitlorquery_CriterionFactory::GetCriterionTerms($this);			
			default :
				tx_icssitquery_debug::notice('Undefined property of Criterion via __get(): ' . $name);
		}
	}

	
	/**
	 * Convert object to display as string
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}
	
}