<?php
/**
 * Filters.
 *
 * @package    Cherry_Framework
 * @subpackage Functions
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2014, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Filters the body class.
add_filter( 'body_class',                 'cherry_add_control_classes' );

// Filters the `.cherry-container` class.
add_filter( 'cherry_get_container_class', 'cherry_get_the_container_classes' );

// Filters a sidebar visibility.
add_filter( 'cherry_display_sidebar',     'cherry_not_display_sidebar', 9, 2 );

add_filter( 'shortcode_atts_row',         'cherry_add_type_atts', 10, 3 );

add_filter( 'su/data/shortcodes',         'cherry_add_type_view' );

// Prints option styles.
add_action( 'wp_head', 'cherry_add_option_styles', 9999 );


// Add specific CSS class by filter.
function cherry_add_control_classes( $classes ) {
	$responsive       = cherry_get_option('grid-responsive');
	$grid_type        = cherry_get_option('grid-type');
	$sidebar_position = cherry_get_option('blog-sidebar-position');

	// Responsive.
	if ( 'true' == $responsive ) {
		$classes[] = 'cherry-responsive';
	} else {
		$classes[] = 'cherry-no-responsive';
	}

	// Grid type.
	if ( 'grid-wide' === $grid_type ) {
		$classes[] = 'cherry-wide';
	} elseif ( 'grid-boxed' === $grid_type ) {
		$classes[] = 'cherry-boxed';
	}

	// Sidebar.
	if ( cherry_display_sidebar( 'sidebar-main' ) ) {
		$classes[] = 'cherry-with-sidebar';
	} else {
		$classes[] = 'cherry-no-sidebar';
	}

	// Sidebar Position.
	$classes[] = sanitize_html_class( 'cherry-blog-layout-' . $sidebar_position );

	return $classes;
}

function cherry_get_the_container_classes( $class ) {
	$grid_type = cherry_get_option('grid-type');
	$classes   = array();
	$classes[] = $class;

	if ( 'grid-wide' == $grid_type ) {
		$classes[] = 'container-fluid';
	} elseif ( 'grid-boxed' == $grid_type ) {
		$classes[] = 'container';
	}
	$classes[] = 'clearfix';

	$classes = apply_filters( 'cherry_get_the_container_classes', $classes, $class );
	$classes = array_unique( $classes );

	return join( ' ', $classes );
}

function cherry_not_display_sidebar( $display, $id ) {
	$sidebar_position = cherry_get_option('blog-sidebar-position');

	if ( 'no-sidebar' == $sidebar_position ) {
		return false;
	}

	return $display;
}

function cherry_add_type_atts( $out, $pairs, $atts ) {
	$out['type'] = ( isset( $atts['type'] ) ) ? $atts['type'] : 'fixed-width';

	return $out;
}

function cherry_add_type_view( $shortcodes ) {
		$shortcode = ( !empty( $_REQUEST['shortcode'] ) ) ? sanitize_key( $_REQUEST['shortcode'] ) : '';

		if ( empty( $shortcode ) ) {
			return $shortcodes;
		}

		if ( 'row' != $shortcode ) {
			return $shortcodes;
		}

		$shortcodes[ $shortcode ]['atts']['type'] = array(
			'type'   => 'select',
			'values' => array(
				'fixed-width' => __( 'Fixed Width', 'cherry' ),
				'full-width'  => __( 'Full Width', 'cherry' ),
			),
			'default' => 'fixed-width',
			'name'    => __( 'Type', 'cherry' ),
			'desc'    => __( 'Type width', 'cherry' ),
		);

		return $shortcodes;
	}

function cherry_add_option_styles() {
	$responsive       = cherry_get_option('grid-responsive');
	$grid_type        = cherry_get_option('grid-type');
	$container_width  = intval( cherry_get_option('page-layout-container-width') );
	$sidebar_position = cherry_get_option('blog-sidebar-position');
	$output           = '';

	if ( !$container_width ) {
		$container_width = 1170; // get default value
	}

	// Check a layout type option.
	if ( 'grid-boxed' == $grid_type || 'false' == $responsive ) {
		$output .= ".cherry-container.container { max-width : {$container_width}px; }\n";
	}

	// Check a container width option.
	// if ( $container_width < 1170 ) {
		$output .= ".cherry-no-sidebar .cherry-container.container,\n";
		$output .= ".cherry-boxed .site-header .container,\n";
		$output .= ".cherry-boxed .site-footer .container,\n";
		$output .= ".cherry-no-responsive .site-header .container,\n";
		$output .= ".cherry-no-responsive .site-footer .container { max-width : {$container_width}px; }\n";
	// }

	$output .= ".cherry-no-responsive .cherry-container .container { max-width : " . ( $container_width - 30 ) . "px; }\n";

	if ( 'false' == $responsive ) {
		$output .= "body { min-width : {$container_width}px; }\n";
	}

	// Prepare a string with a styles.
	$output = trim( $output );

	if ( !empty( $output ) ) {

		// Print style if $output not empty.
		printf( "<style type='text/css'>\n%s\n</style>\n", $output );
	}
}