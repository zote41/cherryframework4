<?php
/**
 * The functions file is used to initialize everything in the theme. It controls how the theme is loaded and
 * sets up the supported features, default actions, and default filters. If making customizations, users
 * should create a child theme and make changes to its functions.php file (not this one).
 *
 * Child themes should do their setup on the 'after_setup_theme' hook with a priority of 11 if they want to
 * override parent theme features. Use a priority of 9 if wanting to run before the parent theme.
 */

// Load the core Cherry Framework.
require_once( trailingslashit( get_template_directory() ) . 'lib/class-cherry-framework.php' );
new Cherry_Framework();

// Sets up theme defaults and registers support for various WordPress features.
add_action( 'after_setup_theme', 'cherry_theme_setup' );
function cherry_theme_setup() {

	// Load files.
	require_once( trailingslashit( get_template_directory() ) . 'inc/init.php' );

	// Load scripts.
	add_theme_support( 'cherry-scripts', array( 'comment-reply', 'drop-downs' ) );

	// Load styles.
	add_theme_support( 'cherry-styles', array( 'drop-downs', 'parent', 'style' ) );

	// Handle content width for embeds and images.
	cherry_set_content_width( 780 );

	// $movies = new Super_Custom_Post_Type( 'movie', 'Movie', 'Movies' );
	// $last_news = new Super_Custom_Post_Type( 'last-new', 'Last News', 'Last News' );

	// add_post_type_support( 'movie', 'post-formats' );
	// add_post_type_support( 'last-new', 'post-formats' );

	add_filter( 'cherry_wrap_base', 'cherry_wrap_base_cpts' );
	function cherry_wrap_base_cpts( $templates ) {
		$cpt = get_post_type(); // Get the current post type
		if ( $cpt && ( 'page' !== $cpt ) ) {
			array_unshift( $templates, 'base-single-' . $cpt . '.php' ); // Shift the template to the front of the array
		}
		return $templates; // Return modified array with base-$cpt.php at the front of the queue
	}
}