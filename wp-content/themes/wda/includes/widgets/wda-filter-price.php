<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Wda_Filter_Price_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'wda_filter_product_woocommerce', 'description' => 'Filter Product by Price Woocommerces' );
		parent::__construct( 'wda_filter_product_woocommerce', 'Filter by Pirce', $widget_ops );
	}

	function widget( $args, $instance ) {
		global $wp;

		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}

		if ( ! WC()->query->get_main_query()->post_count && ! isset( $_GET['min_price'] ) && ! isset( $_GET['max_price'] ) ) {
			return;
		}

		echo $args['before_widget'];
		echo $args['before_title'];
		echo 'Giá tiền'; // Can set this with a widget option, or omit altogether
		echo $args['after_title'];

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$widget_id = 'widget_' . $args['widget_id'];

		$lte = get_field( 'lte', $widget_id );
		$gte = get_field( 'gte', $widget_id );
		$listPrices = get_field( 'prices_list', $widget_id );

		$prices    = get_filtered_price();
		$min_price = $prices->min_price;
		$max_price = $prices->max_price;

		if ( $gte > $max_price ) {
			$gte = $max_price;
		}

		$current_min_price = !empty($_GET['min_price']) ? sanitize_text_field($_GET['min_price']) : 1;
		$current_max_price = !empty($_GET['max_price']) ? sanitize_text_field($_GET['max_price']) : 1;

		if ( '' === get_option( 'permalink_structure' ) ) {
			$form_action = remove_query_arg( array( 'page', 'paged', 'product-page' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
		} else {
			$form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
		}

		$list_price_html = "";

		foreach ($listPrices as $ipirce) {
			$list_price_html .= '<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
				<a href="javascript:;" min_price="'.esc_attr( $ipirce['form'] ).'" max_price="'. esc_attr( $ipirce['to'] ) .'">Từ '.esc_attr( $ipirce['form'] ).' đến '. esc_attr( $ipirce['to'] ) .'</a>
			</li>';
		}
		echo '
		<form id="'.esc_attr( 'wda_filter_by_prices' ).'" method="get" action="' . esc_url( $form_action ) . '">
		<div class="wda_filter_by_prices">
		<ul>
		<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
		<a href="javascript:;" min_price="' . esc_attr( $min_price ) . '" max_price="' . esc_attr( $max_price ) . '">Tất cả</a>
		</li>
		<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
		<a href="javascript:;" min_price="' . esc_attr( '1' ) . '" max_price="' . esc_attr( $lte ) . '">Dưới '.$lte.'</a>
		</li>'
		     .$list_price_html.'
		<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
		<a href="javascript:;" min_price="' . esc_attr( $gte ) . '" max_price="' . esc_attr( $max_price ) . '">Trên '.$gte.'</a>
		</li>
		</ul>
		<input style="display:none" type="text" id="wda_min_price" name="min_price" value="' . esc_attr( $current_min_price ) . '" placeholder="' . esc_attr__( 'Min price', 'wda' ) . '" />
		<input style="display:none" type="text" id="wda_max_price" name="max_price" value="' . esc_attr( $current_max_price ) . '" placeholder="' . esc_attr__( 'Max price', 'wda' ) . '" />
		'. wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'paged' ), '', true ) . '
		</div>
		</form>
		';

		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {

		// update logic goes here
		$updated_instance = $new_instance;
		return $updated_instance;
	}

	function form( $instance ) {
		// $instance = wp_parse_args( (array) $instance, array(
		// 	array of option_name => value pairs
		// ) );

		// display field names here using:
		// $this->get_field_id( 'option_name' ) - the CSS ID
		// $this->get_field_name( 'option_name' ) - the HTML name
		// $instance['option_name'] - the option value
	}
}

function wda_register_widget_filter_price() {
	register_widget( 'Wda_Filter_Price_Widget' );
}
add_action( 'widgets_init', 'wda_register_widget_filter_price' );