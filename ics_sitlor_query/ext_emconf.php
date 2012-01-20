<?php

########################################################################
# Extension Manager/Repository config file for ext "ics_sitlor_query".
#
# Auto generated 09-12-2011 14:45
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'SITLOR query',
	'description' => 'ICS extension to query SITLOR',
	'category' => 'misc',
	'author' => 'In Cite Solution',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => 'ics_sit_query',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'ics_sit_query' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:35:{s:9:"ChangeLog";s:4:"a887";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"00dc";s:14:"ext_tables.php";s:4:"cfd8";s:13:"locallang.xml";s:4:"9a8d";s:16:"locallang_db.xml";s:4:"ee70";s:31:"classes/class.Accommodation.php";s:4:"eca8";s:23:"classes/class.Event.php";s:4:"fef8";s:35:"classes/class.FullAccommodation.php";s:4:"08cf";s:27:"classes/class.FullEvent.php";s:4:"6b52";s:32:"classes/class.FullRestaurant.php";s:4:"d0d2";s:28:"classes/class.Restaurant.php";s:4:"1726";s:29:"classes/class.SitlorQuery.php";s:4:"6c2d";s:36:"classes/class.SitlorQueryService.php";s:4:"54f3";s:40:"classes/filters/class.CategoryFilter.php";s:4:"7add";s:39:"classes/filters/class.EndDateFilter.php";s:4:"0358";s:41:"classes/filters/class.EquipmentFilter.php";s:4:"5f11";s:34:"classes/filters/class.IdFilter.php";s:4:"2a70";s:39:"classes/filters/class.KeywordFilter.php";s:4:"79c0";s:37:"classes/filters/class.NoFeeFilter.php";s:4:"8f69";s:39:"classes/filters/class.OpenDayFilter.php";s:4:"627b";s:41:"classes/filters/class.StartDateFilter.php";s:4:"0386";s:37:"classes/filters/class.TitleFilter.php";s:4:"c113";s:36:"classes/filters/class.TypeFilter.php";s:4:"b338";s:54:"classes/sorting/class.AccommodationSortingProvider.php";s:4:"987d";s:46:"classes/sorting/class.EventSortingProvider.php";s:4:"ec74";s:51:"classes/sorting/class.RestaurantSortingProvider.php";s:4:"748d";s:19:"doc/wizard_form.dat";s:4:"c72a";s:20:"doc/wizard_form.html";s:4:"aa4b";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:35:"pi1/class.tx_icssitlorquery_pi1.php";s:4:"fe13";s:43:"pi1/class.tx_icssitlorquery_pi1_wizicon.php";s:4:"0027";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"42d0";}',
);

?>