<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_icssitlorquery_pi1.php', '_pi1', 'list_type', 0);

// Register cache 'icssitlorquery_cache'
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache'] = array();
}
// Define string frontend as default frontend, this must be set with TYPO3 4.5 and below
// and overrides the default variable frontend of 4.6
if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['frontend'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['frontend'] = 't3lib_cache_frontend_StringFrontend';
}
if (t3lib_div::int_from_ver(TYPO3_version) < '4006000') {
    // Define database backend as backend for 4.5 and below (default in 4.6)
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['backend'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['backend'] = 't3lib_cache_backend_DbBackend';
    }
    // Define data and tags table for 4.5 and below (obsolete in 4.6)
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['options'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['options'] = array();
    }
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['options']['cacheTable'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['options']['cacheTable'] = 'tx_icssitlorquery_cache';
    }
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['options']['tagsTable'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['icssitlorquery_cache']['options']['tagsTable'] = 'tx_icssitlorquery_cache_tags';
    }
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_icssitlorquery_product_task'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'Clean SITLOR products cache',
    'description'      => 'Clean SITLOR products cache',
);
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_icssitlorquery_cleanAll_task'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'Clean all SITLOR cache',
    'description'      => 'Clean all SITLOR cache',
);
?>