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
 *
 *
 *
 *   45: class tx_icssitlorquery_XMLTools
 *   56:     public static function SkipChildren(XMLReader $reader)
 *   75:     public static function XMLMoveToRootElement(XMLReader $reader, $name)
 *   90:     public static function getXMLDocument($url, $timeout=5)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 
/**
 * Class 'tx_icssitlorquery_XMLTools' for the 'ics_sitlor_query' extension.
 *
 * @author	Tsi YANG <tsi@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class tx_icssitlorquery_XMLTools {

	/**
	 * Skips all child nodes of the current Element node.
	 *
	 * The method MUST be called only when the reader is on an Element node.
	 * After the call, the reader is on the corresponding EndElement node or not moved.
	 *
	 * @param	XMLReader		$reader The reader to manipulate.
	 * @return	void
	 */
	public static function SkipChildren(XMLReader $reader) {
		if (!$reader->isEmptyElement) {
			$reader->read();
			while (($reader->nodeType != XMLReader::END_ELEMENT) && ($reader->nodeType != XMLReader::NONE)) {
				if ($reader->nodeType == XMLReader::ELEMENT) {
					self::SkipChildren($reader);
				}
				$reader->read();
			}
		}
	}

	/**
	 * Places reader on the root element.
	 *
	 * @param	XMLReader		$reader
	 * @param	string		$name Expected node name
	 * @return	boolean		Wether the expected node is found.
	 */
	public static function XMLMoveToRootElement(XMLReader $reader, $name) {
		$reader->read();
		while (($reader->nodeType != XMLReader::ELEMENT) && ($reader->nodeType != XMLReader::NONE)) {
			$reader->read();
		}
		return $reader->name == $name;
	}

	/**
	 * Retrieves XML document.
	 *
	 * @param	string		$url Url to query.
	 * @param	int		$timeout Maximum time to wait for the answer.
	 * @return	string		Content of the document.
	 */
	public static function getXMLDocument($url, $timeout=30) {
		$old_timeout = ini_set('default_socket_timeout', $timeout);
		$handle = fopen($url, 'r');
		if (!$handle)
			throw new Exception('Unable to open ' . $url);
		ini_set('default_socket_timeout', $old_timeout);
		stream_set_timeout($handle, $timeout);
		stream_set_blocking($handle, false);
		$status = stream_get_meta_data ($handle);
		while ((!feof($handle)) && (!$status['timed_out'])) {
			$xmlContent .= fgets($handle, 1024);
			$status = stream_get_meta_data($handle);
        }
		fclose($handle);

        if ($status['timed_out'])
			throw new Exception('Connection timed out on ' . $url);

		return $xmlContent;
	}

}