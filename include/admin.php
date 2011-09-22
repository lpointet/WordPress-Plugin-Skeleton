<?php
/**
 * Add hooks declarations
 */
add_action('admin_menu', 'gb_sk_add_admin_menu');
add_action('admin_init', 'gb_sk_register_settings');
add_action('init', 'gb_sk_add_shortcode_button');

// Handle AJAX requests, even for front ones
add_action('wp_ajax_gb_sk_ajax', 'gb_sk_ajax');
add_action('wp_ajax_nopriv_gb_sk_ajax', 'gb_sk_ajax');

add_action('admin_print_scripts', 'gb_sk_add_ajax_script'); // Include JS file to do ajax calls on admin (same function called from front.php)

/**
 * Menu manager function
 *
 * This function adds a subpage of 'settings' menu in the WordPress administration
 *
 */
function gb_sk_add_admin_menu() {
    // Setup the settings page
    $mypage = add_options_page(GB_SK_STR_ADMIN_MENU_PAGE_TITLE, GB_SK_STR_ADMIN_MENU_TITLE, 'manage_options', 'gb_sk_admin_menu', 'gb_sk_display_admin_menu');

    // Add some scripts on this admin panel
    add_action('admin_print_scripts-'.$mypage, 'gb_sk_admin_menu_script' );
    // Add some style to this admin panel
    add_action('admin_print_styles-'.$mypage, 'gb_sk_admin_menu_style' );

    // Add a link to settings page in plugins list
    add_filter('plugin_action_links', 'gb_sk_add_settings_link', 10, 2);
}

/**
 * Settings API registering function
 *
 * This function registers the settings group, section and fields for the plugin
 *
 */
function gb_sk_register_settings() {
    // We need a group, with the option name and data validation function (so that WordPress can automatically save our option)
    register_setting( GB_SK_CFG_SETTINGS_GROUP, 'gb_sk_settings', 'gb_sk_settings_validate' );
    // We need a section (regroupment of fields) with an id, a title, a description displaying function and a name of page on which it will be shown
    add_settings_section('gb_sk_section', GB_SK_STR_SETTINGS_SECTION_TITLE, 'gb_sk_settings_description', GB_SK_CFG_SETTINGS_SECTION);

    // We can now add our different fields
    // in the section we created earlier.
    // We pass an id, a label, a callback to display input (or other form element needed), the section page and section id
    add_settings_field('gb_sk_show_example_ui', GB_SK_STR_SHOW_EXAMPLE_UI, 'gb_sk_show_example_ui_field', GB_SK_CFG_SETTINGS_SECTION, 'gb_sk_section');
    add_settings_field('gb_sk_send_mail', GB_SK_STR_SEND_MAIL, 'gb_sk_send_mail_field', GB_SK_CFG_SETTINGS_SECTION, 'gb_sk_section');
}

/**
 * Options validation
 *
 * This function validates the options posted before saving them :
 * show_example_ui must be either 0 or 1
 *
 * @param   array  $input    The options posted by the user
 * @return  array  $newinput The options to save in DB
 */
function gb_sk_settings_validate($input) {
    // Retrieve current options
    $newinput = get_option('gb_sk_settings');

    // Validate show_example_ui (0 or 1)
    $newinput['show_example_ui'] = trim($input['show_example_ui']);
    if(!in_array($newinput['show_example_ui'], array(0, 1))) {
        $newinput['show_example_ui'] = 1;
    }

    // Validate send_mail (0 or 1)
    $newinput['send_mail'] = trim($input['send_mail']);
    if(!in_array($newinput['send_mail'], array(0, 1))) {
        $newinput['send_mail'] = 1;
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
 * 'Show example UI' field
 *
 * This function echoes the field for the 'Show example UI' field in the settings form
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
 * 'Send mail' field
 *
 * This function echoes the field for the 'Send mail' field in the settings form
 *
 */
function gb_sk_send_mail_field() {
    // Retrieve options
    $options = get_option('gb_sk_settings');

    // Default value = 1
    $val = !isset($options['send_mail']) || !empty($options['send_mail']) ? 1 : 0;

    // Display field
    echo '<select name="gb_sk_settings[send_mail]">';
    echo '<option value="1" '.($val ? 'selected':'').'>'.GB_SK_STR_YES.'</option>';
    echo '<option value="0" '.($val ? '':'selected').'>'.GB_SK_STR_NO.'</option>';
    echo '</select>';
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
    echo '<form class="gb_sk_cron_button" method="post" action=""><input class="button-primary" type="submit" value="'.GB_SK_STR_CRON_BUTTON.'" name="gb_sk_launch_cron" /></form>';

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

    // Needed to display security hidden fields (_wp_nonce, referer etc.)
    settings_fields( GB_SK_CFG_SETTINGS_GROUP );
    // Display registered settings fields
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

/**
 * Adds a link to settings page
 *
 * This function adds a link in the plugins page of WordPress, near the 'deactivate' link, pointing to our settings page
 *
 * @param  array  $links The current links that will be displayed for this plugin
 * @param  string $file  The current plugin file name (bootstrap)
 * @return array  $links The links to display, potentially with new ones
 */
function gb_sk_add_settings_link($links, $file) {
    // Retrieve the folder only (we don't need the php file)
    $file = explode('/', $file);
    $file = $file[0];

    // Check if it's ours, and add our link
    if( $file == GB_SK_PATH ) {
        $settings_link = '<a href="options-general.php?page=gb_sk_admin_menu">'.GB_SK_STR_SETTINGS.'</a>';
        array_unshift( $links, $settings_link ); // before other links
    }

    return $links;
}

/**
 * Attach shortcode button
 *
 * @return boolean
 */
function gb_sk_add_shortcode_button() {
    // Only for users with rights
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return FALSE;
    }

    // Has user WYSIWYG enabled?
    if (get_user_option('rich_editing')) {
        add_filter('mce_external_plugins', 'gb_sk_attach_button_script');
        add_filter('mce_buttons', 'gb_sk_register_button');

        // We need some strings in the JS, localize it!
        add_filter('mce_external_languages', 'gb_sk_shortcode_localization');

        return TRUE;
    }
}

/**
 * Adds shortcode button js
 *
 * @param  array $plugin_array The existing tinyMCE plugins
 * @return array $plugin_array The plugins array, with ours
 */
function gb_sk_attach_button_script($plugin_array) {
    $plugin_array['gb_sk_shortcode'] = GB_SK_URL.'/js/shortcode.js';

    return $plugin_array;
}

/**
 * Register shortcodes buttons
 *
 * @param  array $buttons The existing tinyMCE buttons
 * @return array $buttons The buttons array, with ours
 */
function gb_sk_register_button($buttons) {
    array_push($buttons, '|', 'gb_sk_shortcode');

    return $buttons;
}

/**
 * Set localization file for shortcode tinymce plugin
 *
 * @return array          The path for language PHP file of our tinyMCE plugin
 */
function gb_sk_shortcode_localization() {
    return array(
        'gb_sk_shortcode' => GB_SK_COMPLETE_PATH.'/include/localize_tinymce.php',
    );
}

/**
 * Ajax response
 *
 * This function returns a little text at each ajax call
 *
 */
function gb_sk_ajax() {
    // Check to see if the submitted nonce matches with the generated nonce we created earlier (see lib.php)
    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'gb_sk_ajax_nonce' ) )
        die('Busted!');

    // Return a randomized string
    echo GB_SK_STR_AJAX.' '.mt_rand();

    // We MUST exit
    exit;
}