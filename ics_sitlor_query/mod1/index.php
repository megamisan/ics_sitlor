<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In-Cite Solutions <technique@in-cite.net>
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


$LANG->includeLLFile('EXT:ics_sitlor_query/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'SITLOR cache' for the 'ics_sitlor_query' extension.
 *
 * TODO: Transform to a clear cache command. And an item to the clear cache dropdown.
 * @author	In-Cite Solutions <technique@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icssitlorquery
 */
class  tx_icssitlorquery_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				function menuConfig()	{
					global $LANG;
					$this->MOD_MENU = Array (
						'function' => Array (
							'1' => $LANG->getLL('function1'),
						)
					);
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *
				 * @return	[type]		...
				 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;
				
						// initialize doc
					$this->doc = t3lib_div::makeInstance('template');
					$this->doc->setModuleTemplate(t3lib_extMgm::extPath('ics_sitlor_query') . 'mod1/mod_template.html');
					$this->doc->backPath = $BACK_PATH;
					$docHeaderButtons = $this->getButtons();

					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the form
						$this->doc->form = '<form action="" method="post" enctype="multipart/form-data">';

							// JavaScript
						$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
							</script>
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';
							// Render content:
						$this->moduleContent();
					} else {
							// If no access or if ID == zero
						$docHeaderButtons['save'] = '';
						$this->content.=$this->doc->spacer(10);
					}

						// compile document
					$markers['FUNC_MENU'] = t3lib_BEfunc::getFuncMenu(0, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']);
					$markers['CONTENT'] = $this->content;

							// Build the <body> for the module
					$this->content = $this->doc->startPage($LANG->getLL('title'));
					$this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
					$this->content.= $this->doc->endPage();
					$this->content = $this->doc->insertStylesAndJS($this->content);
				
				}

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
					switch((string)$this->MOD_SETTINGS['function'])	{
						case 1:
							$content.=$this->cacheOperationsForm();
							if (isset($_POST['icssitlorquery_mod1']['clearcache']))
								$content.=$this->clearCache();
								
							if (isset($_POST['icssitlorquery_mod1']['cleancache']))
								$content.=$this->cleanCache();
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case 2:
							$content='<div align=center><strong>Menu item #2...</strong></div>';
							$this->content.=$this->doc->section('Message #2:',$content,0,1);
						break;
						case 3:
							$content='<div align=center><strong>Menu item #3...</strong></div>';
							$this->content.=$this->doc->section('Message #3:',$content,0,1);
						break;
					}
				}
				

				/**
				 * Create the panel of buttons for submitting the form or otherwise perform operations.
				 *
				 * @return	array	all available buttons as an assoc. array
				 */
				protected function getButtons()	{

					$buttons = array(
						'csh' => '',
						'shortcut' => '',
						'save' => ''
					);
						// CSH
					$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']);

						// SAVE button
					$buttons['save'] = '<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/savedok.gif', '') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />';


						// Shortcut
					if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
						$buttons['shortcut'] = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
					}

					return $buttons;
				}
				
				/**
				 * display Cache Operations Form
				 *
				 * @return	html
				 */
				function cacheOperationsForm()	{
				global $LANG;
					$html='
					<fieldset style="border: 0;">
						<!--<input type="submit" name="icssitlorquery_mod1[cleancache]" value="'.$LANG->getLL('cleanCache').'"/>-->
						<input type="submit" name="icssitlorquery_mod1[clearcache]" value="'.$LANG->getLL('clearCache').'"/>
					</fieldset>';
					return $html;
				}
				
				/**
				 * Clean Cache 
				 *
				 * @return	html
				 */
				function cleanCache()	{
				global $LANG;
					$html='';
					$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ics_sitlor_query']);
					$cacheTime = 0;
					if ($extConf)	{
						$cacheTime = intval($extConf['cacheTime']);
					}
					$flashMessage = t3lib_div::makeInstance('t3lib_FlashMessage',$LANG->getLL('cleanCache_msg'),'',t3lib_FlashMessage::OK);
					if($GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_icssitlorquery_cache','tstamp < '.(time() - $cacheTime)))
						$html.=$flashMessage->render();
				
					return $html;
				}
				
				/**
				 * Clear Cache 
				 *
				 * @return	html
				 */
				function clearCache()	{
					global $LANG;
					$html='';
					$flashMessage = t3lib_div::makeInstance('t3lib_FlashMessage',$LANG->getLL('clearCache_msg'),'',t3lib_FlashMessage::OK);
					if($GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_icssitlorquery_cache', '1'))
						$html.=$flashMessage->render();
						
					return $html;
				}
		}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_sitlor_query/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_icssitlorquery_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>