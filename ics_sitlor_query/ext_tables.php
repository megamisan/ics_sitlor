<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getSingleFieldClass'][] = 'EXT:ics_sitlor_query/Classes/class.tx_icssitlorquery_dynflex.php:&tx_icssitlorquery_dynflex';


t3lib_extMgm::addPlugin(array(
    'LLL:EXT:ics_sitlor_query/locallang_db.xml:tt_content.list_type_pi1',
    $_EXTKEY . '_pi1',
    t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_icssitlorquery_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_icssitlorquery_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','SITLOR query');
?>