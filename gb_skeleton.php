<?php
/*
Plugin Name: Skeleton Plugin
Plugin URI: http://www.globalis-ms.com
Description: This plugin intends to be a development basis for a WordPress plugin, including most of best practices and functionnalities of WordPress plugins
Version: 1.0.0
Author: Lionel POINTET, GLOBALIS media systems
Author URI: http://www.globalis-ms.com
License: GPL2
*/

/**
 * Some configuration constants and/or variables included in this file
 */
require 'include/config.php';

/**
 * Load translation module (before lang.php inclusion)
 */
load_plugin_textdomain(GB_SK_DOMAIN, FALSE, GB_SK_PATH . '/translation' );

/**
 * On the admin pages, we need the admin library, on front, we need front one
 */
if(is_admin()) {
    require 'include/admin.php';

    // Handle AJAX requests
    add_action('wp_ajax_gb_sk_ajax', 'gb_sk_ajax');
    add_action('wp_ajax_nopriv_gb_sk_ajax', 'gb_sk_ajax');
}
else {
    require 'include/front.php';
}

/**
 * On admin or front, we need the global library, the lang file and widgets declaration
 */
require 'include/lang.php';
require 'include/lib.php';
require 'include/widget.php';

/**
 * Function called at plugin activation
 *
 * This function could create tables, set default options, register scheduled actions etc.
 *
 */
function gb_sk_activate() {
    // Here we create a new table for our plugin
    $sql = "CREATE TABLE " . GB_SK_CFG_TABLE_EXAMPLE . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  name tinytext NOT NULL,
	  text text NOT NULL,
	  url VARCHAR(55) DEFAULT '' NOT NULL,
	  UNIQUE KEY id (id)
	);";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    // dbDelta($sql); // This instruction will actually do the job

    // Launch a cron job
    wp_schedule_event(time()-3599, 'hourly', GB_SK_CRON_EVENT); // daily, twicedaily or hourly
}

/**
 * Function called at plugin deactivation
 *
 * This function could drop tables, delete options, unregister scheduled actions etc.
 *
 */
function gb_sk_deactivate() {
	wp_clear_scheduled_hook(GB_SK_CRON_EVENT);
}

// Register (de)activation functions
register_activation_hook( __FILE__, 'gb_sk_activate' );
register_deactivation_hook( __FILE__, 'gb_sk_deactivate' );
?>