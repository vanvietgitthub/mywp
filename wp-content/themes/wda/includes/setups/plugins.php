<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// remove yoatseo on homepage
// add_action('template_redirect','wda_remove_wpseo');

function wda_remove_wpseo(){
	if (is_home() || is_front_page() ) {
		global $wpseo_front;
		if(defined($wpseo_front)){
			remove_action('wp_head',array($wpseo_front,'head'),1);
		}
		else {
			$wp_thing = WPSEO_Frontend::get_instance();
			remove_action('wp_head',array($wp_thing,'head'),1);
		}
	}
}

// remove style contact form 7
add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );
// condition include contact form 7
// if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
// 	wpcf7_enqueue_scripts();
// }

// if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
// 	wpcf7_enqueue_styles();
// }

// remove style inline kirki


// disable script and style woocomerce on hompage & shop & product category
add_action( 'wp_enqueue_scripts', 'wda_disable_woocommerce_loading_css_js' );

function wda_disable_woocommerce_loading_css_js() {

	// Check if WooCommerce plugin is active
	if ( class_exists( 'WooCommerce' ) ) {

		// Check if it's any of WooCommerce page
		if(! is_product() && ! is_cart() && ! is_checkout() ) { 		
			
			## Dequeue WooCommerce styles
			wp_dequeue_style('woocommerce-layout'); 
			wp_dequeue_style('woocommerce-general'); 
			wp_dequeue_style('woocommerce-smallscreen'); 	
			wp_dequeue_style('woocommerce-inline'); 
			remove_action( 'wp_head', 'wc_gallery_noscript' );	

			## Dequeue WooCommerce scripts
			wp_dequeue_script('wc-cart-fragments');
			wp_dequeue_script('woocommerce'); 
			wp_dequeue_script('wc-add-to-cart'); 

			wp_deregister_script( 'js-cookie' );
			wp_dequeue_script( 'js-cookie' );

			wp_dequeue_script( 'wp-util' );	
			wp_deregister_script( 'wp-util' );

		}
	}	
}

// move jquery to footer
// add_action( 'wp_default_scripts', 'wda_move_jquery_to_footer' );

function wda_move_jquery_to_footer( $wp_scripts ) {

	if( is_admin() ) {
		return;
	}

	$wp_scripts->add_data( 'jquery', 'group', 1 );
	$wp_scripts->add_data( 'jquery-core', 'group', 1 );
	$wp_scripts->add_data( 'jquery-migrate', 'group', 1 );
}

// remove css js of elm
// check \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id );
add_action( 'wp_print_styles', 'wda_elementor_disable_css' );

function wda_elementor_disable_css() {

	if ( !is_admin() ) {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() === false && \Elementor\Plugin::$instance->preview->is_preview_mode() === false ) {

			wp_dequeue_style( 'elementor-icons' );
			wp_deregister_style( 'elementor-icons' );

			wp_dequeue_style( 'elementor-animations' );
			wp_deregister_style( 'elementor-animations' );

			wp_dequeue_style( 'flatpickr' );
			wp_deregister_style( 'flatpickr' );		

			wp_dequeue_style( 'font-awesome' );
			wp_deregister_style( 'font-awesome' );	
		}
	}
}

add_action( 'wp_print_scripts', 'wda_elementor_disable_js' );

function wda_elementor_disable_js() {

	if ( !is_admin() ) {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() === false && \Elementor\Plugin::$instance->preview->is_preview_mode() === false ) {

			wp_dequeue_script( 'elementor-frontend-modules' );
			wp_deregister_script( 'elementor-frontend-modules' );

			wp_dequeue_script( 'elementor-waypoints' );
			wp_deregister_script( 'elementor-waypoints' );

			wp_dequeue_script( 'flatpickr' );	
			wp_deregister_script( 'flatpickr' );

			wp_dequeue_script( 'imagesloaded' );
			wp_deregister_script( 'imagesloaded' );

			wp_dequeue_script( 'jquery-numerator' );
			wp_deregister_script( 'jquery-numerator' );

			wp_dequeue_script( 'swiper' );
			wp_deregister_script( 'swiper' );

			wp_dequeue_script( 'jquery-slick' );
			wp_deregister_script( 'jquery-slick' );

			wp_dequeue_script( 'elementor-dialog' );	
			wp_deregister_script( 'elementor-dialog' );

			wp_dequeue_script( 'elementor-frontend' );	
			wp_deregister_script( 'elementor-frontend' );

			wp_dequeue_script( 'smartmenus' );	
			wp_deregister_script( 'smartmenus' );

		}
	}
	
}

// remove font google of elm
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

// remove font fa of elm
add_action( 'elementor/frontend/after_register_styles',function() {
	foreach( [ 'solid', 'regular', 'brands' ] as $style ) {
		wp_deregister_style( 'elementor-icons-fa-' . $style );
	}
}, 20 );

// debug and disable style
add_action( 'wp_print_styles', 'wda_custom_disable_css' );

function wda_custom_disable_css() {

	wp_dequeue_style( 'elementor-common' );
	wp_deregister_style( 'elementor-common' );

	if ( class_exists( 'WooCommerce' ) ) {

		// Check if it's any of WooCommerce page
		if(! is_woocommerce() && ! is_cart() && ! is_checkout() ) { 

			wp_dequeue_style( 'woo-variation-swatches' );
			wp_deregister_style( 'woo-variation-swatches' );

			wp_dequeue_style( 'woo-variation-swatches-tooltip' );
			wp_deregister_style( 'woo-variation-swatches-tooltip' );

		}
	}

}

// debug and disable script
add_action( 'wp_enqueue_scripts', 'wda_custom_disable_woocommerce_loading_css_js' );

function wda_custom_disable_woocommerce_loading_css_js() {

	wp_dequeue_style( 'elementor-common' );
	wp_deregister_style( 'elementor-common' );

	wp_dequeue_style( 'woo-variation-swatches' );
	wp_deregister_style( 'woo-variation-swatches' );

	wp_dequeue_style( 'woo-variation-swatches-tooltip' );
	wp_deregister_style( 'woo-variation-swatches-tooltip' );

	wp_dequeue_style( 'elementor-pro' );
	wp_deregister_style( 'elementor-pro' );

}