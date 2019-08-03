<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// if( function_exists('acf_add_local_field_group') ):

// 	acf_add_local_field_group(array(
// 		'key' => 'group_5bd197923a347',
// 		'title' => 'Mega Menu',
// 		'fields' => array(
// 			array(
// 				'key' => 'field_5bd19795234df',
// 				'label' => 'Mega Menu',
// 				'name' => 'mega_menu',
// 				'type' => 'post_object',
// 				'instructions' => '',
// 				'required' => 0,
// 				'conditional_logic' => 0,
// 				'wrapper' => array(
// 					'width' => '',
// 					'class' => '',
// 					'id' => '',
// 				),
// 				'post_type' => array(
// 					0 => 'elementor_library',
// 				),
// 				'taxonomy' => array(
// 					0 => 'elementor_library_type:section',
// 					1 => 'elementor_library_type:page',
// 				),
// 				'allow_null' => 0,
// 				'multiple' => 0,
// 				'return_format' => 'id',
// 				'ui' => 1,
// 			),
// 		),
// 		'location' => array(
// 			array(
// 				array(
// 					'param' => 'nav_menu_item',
// 					'operator' => '==',
// 					'value' => 'all',
// 				),
// 			),
// 		),
// 		'menu_order' => 0,
// 		'position' => 'normal',
// 		'style' => 'seamless',
// 		'label_placement' => 'top',
// 		'instruction_placement' => 'label',
// 		'hide_on_screen' => '',
// 		'active' => 1,
// 		'description' => '',
// 	));

// endif;

// add_filter( 'walker_nav_menu_start_el', function ( $item_output, $item, $depth, $args ) {

// 	if ( $depth > 0 ) {
// 		return $item_output;
// 	}

// 	$mega_menu = get_field( 'mega_menu', $item );

// 	if ( ! $mega_menu ) {
// 		return $item_output;
// 	}

// 	$item_output .= do_shortcode( '[elementor-template id="' . $mega_menu . '"]' );

// 	return $item_output;

// }, 10, 4 );