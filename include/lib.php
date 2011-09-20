<?php
/**
 * Add hooks declarations
 */
add_action(GB_SK_CRON_EVENT, 'gb_sk_send_mail');
add_action('init', 'gb_sk_add_post_type');
add_action('widgets_init', 'gb_sk_register_widgets');

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
 * Widgets registering function
 *
 * Register some widgets to WordPress
 *
 */
function gb_sk_register_widgets() {
    // Recent examples widget
    register_widget('Gb_Sk_Example_Widget');
}

/**
 * Last examples posts widget
 *
 * Creates a sample widget listing the "n" last posts from "example" post type
 *
 */
class Gb_Sk_Example_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_example', 'description' => GB_SK_STR_EXAMPLE_WIDGET_DESCRIPTION );
		parent::__construct('recent-examples', GB_SK_STR_EXAMPLE_WIDGET_TITLE, $widget_ops);
		$this->alt_option_name = 'widget_recent_example';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_recent_examples', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? GB_SK_STR_EXAMPLE_WIDGET_TITLE : $instance['title'], $instance, $this->id_base);
		if ( ! $number = absint( $instance['number'] ) )
 			$number = 5;

		$r = new WP_Query(array('posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'post_type' => GB_SK_CFG_EXAMPLE_POST_TYPE));

		if ($r->have_posts()) {
            echo $before_widget;
            if ( $title )
                echo $before_title . $title . $after_title;
            echo '<ul>';
            while ($r->have_posts()) {
                $r->the_post();
                ?>
                <li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li>
                <?php
            }
            echo '</ul>';
            echo $after_widget;

            // Reset the global $the_post as this query will have stomped on it
            wp_reset_postdata();
        }

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_examples', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_example']) )
			delete_option('widget_recent_example');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_recent_examples', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php echo GB_SK_STR_EXAMPLE_WIDGET_LBL_TITLE; ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php echo GB_SK_STR_EXAMPLE_WIDGET_LBL_NUMBER_TO_SHOW; ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}
