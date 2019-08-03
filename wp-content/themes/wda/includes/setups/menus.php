<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

register_nav_menus(array(
    'menu-1' => esc_html__('Main', TextDomain),
    'menu-2' => esc_html__('Mobile', TextDomain)
));

// remove class li
add_filter('nav_menu_css_class', 'wda_css_attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'wda_css_attributes_filter', 100, 1);
add_filter('page_css_class', 'wda_css_attributes_filter', 100, 1);
function wda_css_attributes_filter($var) {
    return is_array($var) ? array_intersect($var, array('current-menu-item')) : '';
}