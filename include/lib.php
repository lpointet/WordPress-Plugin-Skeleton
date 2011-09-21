<?php
/**
 * Add hooks declarations
 */
add_action(GB_SK_CRON_EVENT, 'gb_sk_send_mail');
add_action('init', 'gb_sk_add_post_type');

/**
 * Cron job function handler
 *
 * This function will run once a day (or twice, or even once an hour) AND once at each pressure on the button available in admin panel
 * In this example, it sends a mail to the site admin
 *
 * @param   bool  $manual     Is this a manual call or not?
 */
function gb_sk_send_mail($manual = FALSE) {
    // Retrieve plugin options to check if we have to show ui or not
    $options = get_option('gb_sk_settings');

    // Default value = TRUE
    if(!isset($options['send_mail']) || !empty($options['send_mail'])) {
        $admin_email = get_bloginfo('admin_email');
        $message = 'This mail has been '.($manual ? 'manually' : 'automatically').' sent.';
        wp_mail($admin_email, 'This is a Cron Job example', $message);
    }
}

/**
 * Initialization function
 *
 * This function will add custom post types and taxonomies to handle some more contents and not only posts or pages
 *
 */
function gb_sk_add_post_type() {
    // Retrieve plugin options to check if we have to show ui or not
    $options = get_option('gb_sk_settings');

    // Default value = TRUE
    $show_ui = !isset($options['show_example_ui']) || !empty($options['show_example_ui']) ? TRUE : FALSE;

    // A custom non-hierarchical (like 'posts') post type (see http://codex.wordpress.org/Function_Reference/register_post_type for all available options)
    register_post_type(GB_SK_CFG_EXAMPLE_POST_TYPE, array(
        'label' => GB_SK_STR_LABEL_EXAMPLES,
        'public' => TRUE,
        'show_ui' => $show_ui,
        'has_archive' => TRUE,
        'rewrite' => array(
            'slug' => 'examples',
        ),
        'supports' => array('title', 'editor', 'comments', 'author', 'thumbnail')
    ));
    // A custom hierarchical (like 'categories') taxonomy (see http://codex.wordpress.org/Function_Reference/register_taxonomy for all available options)
    register_taxonomy( GB_SK_CFG_FAMILY_TAXONOMY, GB_SK_CFG_EXAMPLE_POST_TYPE, array(
        'hierarchical' => TRUE,
        'label' => GB_SK_STR_LABEL_FAMILIES,
        'query_var' => TRUE,
        'rewrite' => array(
            'slug' => 'family',
        ),
    ));
}

/**
 * Adding JS for ajax
 *
 * This function adds some JS files to do AJAX calls from admin or front
 *
 */
function gb_sk_add_ajax_script() {
    // Embed the javascript file that makes the AJAX request
    wp_enqueue_script( 'gb_sk_ajax', GB_SK_URL . '/js/ajax.js', array( 'jquery' ) );

    wp_localize_script( 'gb_sk_ajax', 'GB_SK_AJAX', array(
        // URL to wp-admin/admin-ajax.php to process the request
        'ajaxurl'          => admin_url( 'admin-ajax.php' ),

        // generate a nonce with a unique ID "gb_sk_ajax_nonce"
        // so that you can check it later when an AJAX request is sent
        'nonce' => wp_create_nonce( 'gb_sk_ajax_nonce' ),
    ));
}