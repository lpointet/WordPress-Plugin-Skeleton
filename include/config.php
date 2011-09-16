<?php
/**
 * Remember plugin path & URL
 */
define('GB_SK_PATH', plugin_basename( realpath(dirname( __FILE__ ).'/..')  ));
define('GB_SK_COMPLETE_PATH', WP_PLUGIN_DIR.'/'.GB_SK_PATH);
define('GB_SK_URL', WP_PLUGIN_URL.'/'.GB_SK_PATH);

/**
 * Translation domain name for this plugin
 */
define('GB_SK_DOMAIN', 'gb_skeleton');

/**
 * Table names + prefix
 */
define('GB_SK_CFG_PREFIX', 'gb_sk_');
define('GB_SK_CFG_TABLE_EXAMPLE', GB_SK_CFG_PREFIX.'example');

/**
 * Cron event name
 */
define('GB_SK_CRON_EVENT', 'gb_sk_do_event');

/**
 * Custom post types / taxonomies names
 */
define('GB_SK_CFG_EXAMPLE_POST_TYPE', 'gb_sk_examples');
define('GB_SK_CFG_FAMILY_TAXONOMY', 'gb_sk_family');

/**
 * Settings group name
 */
define('GB_SK_CFG_SETTINGS_GROUP', 'gb_sk_settings_group');
define('GB_SK_CFG_SETTINGS_SECTION', 'gb_sk_settings_section');