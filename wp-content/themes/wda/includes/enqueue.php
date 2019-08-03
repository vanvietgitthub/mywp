<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'wp_enqueue_scripts', 'wda_style_script' );

function wda_style_script() {
	// wp_enqueue_style( 'wda-font', 'https://fonts.googleapis.com/css?family=Quicksand:400,500,600&display=swap&subset=vietnamese' );
	wp_enqueue_style( 'wda-flickity-css', esc_url( trailingslashit( get_template_directory_uri() ) . 'assets/css/flickity.min.css' ), array(), '2.2.1', 'all' );
	wp_enqueue_style( 'wda-fancybox-css', esc_url( trailingslashit( get_template_directory_uri() ) . 'assets/css/jquery.fancybox.min.css' ), array(), '3.5.7', 'all' );
	wp_enqueue_style( 'wda-vms-css', esc_url( trailingslashit( get_template_directory_uri() ) . 'assets/css/vms.css' ), array(), '1.0.0', 'all' );
	wp_enqueue_script( 'wda-flickity-js', esc_url( trailingslashit( get_template_directory_uri() ) . 'assets/js/flickity.pkgd.min.js' ), array('jquery'), '2.2.1', false );
	wp_enqueue_script( 'wda-fancybox-js', esc_url( trailingslashit( get_template_directory_uri() ) . 'assets/js/jquery.fancybox.min.js' ), array('jquery'), '3.5.7', false );
	wp_enqueue_script( 'wda-vms-js', esc_url( trailingslashit( get_template_directory_uri() ) . 'assets/js/vms.js' ), array(), '1.0.0', false );
}

add_action( 'admin_enqueue_scripts', 'load_admin_style' );

function load_admin_style() {
	wp_enqueue_style( "admin_custom", esc_url( trailingslashit( get_template_directory_uri() ) . "assets/css/admin.css" ), array(), "1.0.0", $media = 'all' );
}

add_filter( 'script_loader_tag', 'wda_defer_scripts', 10, 3 );
function wda_defer_scripts( $tag, $handle, $src ) {

    // The handles of the enqueued scripts we want to defer
	$defer_scripts = array( 
		'jquery-migrate',
		'wda-flickity-js',
		'wda-fancybox-js',
		'wda-vms-js'
	);

	if ( in_array( $handle, $defer_scripts ) ) {
		return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
	}

	return $tag;
} 