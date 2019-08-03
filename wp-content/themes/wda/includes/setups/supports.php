<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Support for theme wordpress
add_action( 'after_setup_theme', 'wda_theme_support' );

function wda_theme_support() {

	add_theme_support( 'menus' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'body-open' );

	add_theme_support( 'custom-logo', array(
		'height'      => 22,
		'width'       => 172,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	if (class_exists('Woocommerce')) {
		add_theme_support( 'woocommerce' );
		remove_theme_support( 'wc-product-gallery-zoom' );
		remove_theme_support( 'wc-product-gallery-lightbox' );
		remove_theme_support( 'wc-product-gallery-slider' );
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
	}

	$defaults = array(
		'height'      => 22,
		'width'       => 172,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	);
	add_theme_support( 'custom-logo', $defaults );

	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
}

// add excerpt for page
add_post_type_support( 'page', 'excerpt' );

// https://www.isitwp.com/remove-code-wordpress-header/

// register all location elementor pro
add_action( 'elementor/theme/register_locations', 'wda_register_elementor_locations' );

function wda_register_elementor_locations( $elementor_theme_manager ) {

	$elementor_theme_manager->register_all_core_location();

}

// Autoptimize only frontend
add_filter('autoptimize_filter_noptimize','wda_pagebuilder_noptimize',10,0);

function wda_pagebuilder_noptimize() {
	return is_user_logged_in();
}

// widgets
add_action( 'widgets_init', 'wda_widgets_init' );

function wda_widgets_init() {

	register_sidebar( array(
		'name'          => 'Sidebar',
		'id'            => 'sidebar',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
	) );

}

add_action('widgets_init', 'wda_unregister_default_wp_widgets' );

function wda_unregister_default_wp_widgets() {
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Archives');
	unregister_widget('WP_Widget_Links');
	unregister_widget('WP_Widget_Media_Video');
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Tag_Cloud');
}

function sidebar_area_shop_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Shop', TextDomain ),
		'id'            => 'shop',
		'description'   => __( 'Widgets in this area show filter for shop.', TextDomain ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title-shop">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Active Shop', 'vms' ),
		'id'            => 'active-filter',
		'description'   => __( 'Widgets in this area show active filter for shop.', TextDomain ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>'
	) );

	register_sidebar( array(
		'name'          => __( 'Sidebar Single Product', 'vms' ),
		'id'            => 'sidebar-single-product',
		'description'   => __( 'Widgets in this area show single product sidebar.', 'vms' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>'
	) );
}

add_action( 'widgets_init', 'sidebar_area_shop_widgets_init' );