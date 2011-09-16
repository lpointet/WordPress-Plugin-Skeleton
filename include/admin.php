<?php
/**
 * Add hooks declarations
 */
add_action('admin_menu', 'gb_sk_add_admin_menu');
add_action('admin_init', 'gb_sk_register_settings');

/**
 * Menu manager function
 *
 * This function adds a subpage of 'settings' menu in the WordPress administration
 *
 */
function gb_sk_add_admin_menu() {
    $mypage = add_options_page(GB_SK_STR_ADMIN_MENU_PAGE_TITLE, GB_SK_STR_ADMIN_MENU_TITLE, 'manage_options', 'gb_sk_admin_menu', 'gb_sk_display_admin_menu');

    // Add some scripts on this admin panel
    add_action('admin_print_scripts-'.$mypage, 'gb_sk_admin_menu_script' );
    // Add some style to this admin panel
    add_action('admin_print_styles-'.$mypage, 'gb_sk_admin_menu_style' );
}

/**
 * Settings API registering function
 *
 * This function registers the settings group, section and fields for the plugin
 *
 */
function gb_sk_register_settings() {
    register_setting( GB_SK_CFG_SETTINGS_GROUP, 'gb_sk_settings', 'gb_sk_settings_validate' );
    add_settings_section('gb_sk_section', GB_SK_STR_SETTINGS_SECTION_TITLE, 'gb_sk_settings_description', GB_SK_CFG_SETTINGS_SECTION);
    add_settings_field('gb_sk_show_example_ui', GB_SK_STR_SHOW_EXAMPLE_UI, 'gb_sk_show_example_ui_field', GB_SK_CFG_SETTINGS_SECTION, 'gb_sk_section');
}

/**
 * Options validation
 *
 * This function validates the options posted before saving them :
 * show_example_ui must be either 0 or 1
 *
 */
function gb_sk_settings_validate($input) {
    // Retrieve current options
    $newinput = get_option('gb_sk_settings');

    // Validate show_example_ui (0 or 1)
    $newinput['show_example_ui'] = trim($input['show_example_ui']);
    if(!in_array($newinput['show_example_ui'], array(0, 1))) {
        $newinput['show_example_ui'] = 1;
    }

    return $newinput;
}

/**
 * Settings description
 *
 * This function echoes a little description of the settings panel for the plugin, just after the title.
 *
 */
function gb_sk_settings_description() {
    echo '<p>'.GB_SK_STR_SETTINGS_DESCRIPTION.'</p>';
}

/**
 * Settings description
 *
 * This function echoes a little description of the settings panel for the plugin, just after the title.
 *
 */
function gb_sk_show_example_ui_field() {
    // Retrieve options
    $options = get_option('gb_sk_settings');

    // Default value = 1
    $val = !isset($options['show_example_ui']) || !empty($options['show_example_ui']) ? 1 : 0;

    // Display field
    echo '<label for="gb_sk_show_example_ui_yes">'.GB_SK_STR_YES.'&nbsp;</label>';
    echo '<input id="gb_sk_show_example_ui_yes" name="gb_sk_settings[show_example_ui]" type="radio" value="1" '.($val ? 'checked':'').' />';
    echo '&nbsp;&nbsp;&nbsp;';
    echo '<label for="gb_sk_show_example_ui_no">'.GB_SK_STR_NO.'&nbsp;</label>';
    echo '<input id="gb_sk_show_example_ui_no" name="gb_sk_settings[show_example_ui]" type="radio" value="0" '.($val ? '':'checked').' />';
}

/**
 * Admin menu displaying function
 *
 * This function handles the printing of HTML code for the admin menu
 *
 */
function gb_sk_display_admin_menu() {
    // Security check
    if (!current_user_can('manage_options'))  {
        wp_die( GB_SK_STR_ERROR_RIGHT_ACCESS );
    }

    echo '<div class="wrap">';
    // Header
    echo '<div id="icon-themes" class="icon32"><br></div>';

    // Title beginning
    echo '<h2>'.GB_SK_STR_ADMIN_MENU_PAGE_TITLE;

    // Cron job manual launch button
    echo '<form class="gb_sk_cron_button" method="post" action="options.php"><input class="button-primary" type="submit" value="'.GB_SK_STR_CRON_BUTTON.'" name="gb_sk_launch_cron" /></form>';

    // Title ending
    echo '</h2>';

    // Handling data submission for manual cron job launching
    if (!empty($_POST)) {
        // Launch manual cron!
        if(!empty($_POST['gb_sk_launch_cron'])) {
            wp_schedule_single_event(time(), GB_SK_CRON_EVENT, array(TRUE, time()));
        }
    }

    // Global form
    echo '<form method="post" action="options.php">';

    // Display registered settings fields
    settings_fields( GB_SK_CFG_SETTINGS_GROUP );
    do_settings_sections( GB_SK_CFG_SETTINGS_SECTION );

    // Submit button
    echo '<p class="submit">
    <input type="submit" class="button-primary" value="'.GB_SK_STR_SAVE_CHANGES.'" />
    </p>';
    
    echo '</form>'; // Global form end

    echo '</div>'; // .wrap
}

/**
 * Admin menu styles
 *
 * This function adds some stylesheets only on the plugin admin menu
 *
 */
function gb_sk_admin_menu_style() {
    wp_enqueue_style('gb_sk_admin_menu_style', GB_SK_URL.'/css/admin.css');
}

/**
 * Admin menu scripts
 *
 * This function adds some JS files only on the plugin admin menu
 *
 */
function gb_sk_admin_menu_script() {
    // wp_enqueue_script('gb_sk_admin_menu_script', GB_SK_URL.'/js/admin.js');
}