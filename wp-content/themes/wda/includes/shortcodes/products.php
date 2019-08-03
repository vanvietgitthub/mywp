<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Short
 */

add_shortcode( 'wda_products_offset', 'wda_products_offset' );

function wda_products_offset( $atts ) {
	$atts = shortcode_atts( array(
		'per_page' => '12',
		'columns'  => '4',
		'orderby'  => 'date',
		'order'    => 'desc',
		'offset'   => 4,
		'category' => '',
		'operator' => 'IN'
	), (array) $atts );

	ob_start();

	$query_args = array(
		'post_status'       => 'publish',
		'post_type'         => 'product',
		'posts_per_page'    => $atts['per_page'],
		'orderby'           => $atts['orderby'],
		'order'             => $atts['order'],
		'offset'            => $atts['offset'],
		'no_found_rows'     => 1,
	);

	if( ! empty( $atts['category'] ) ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => trim( $atts['category'] ),
				'operator' => $atts['operator']
			),
		);
	}

	?>
	<ul class="products">
		<?php
		$loop = new WP_Query( $query_args );
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile;
		} else {
			echo __( 'No products found' );
		}
		wp_reset_postdata();
		?>
	</ul>
	<?php

	return '<div class="woocommerce columns-' . $atts['columns'] . ' aside">' . ob_get_clean() . '</div>';
}

// filter price
add_shortcode( 'wda_filter_by_products', 'wda_do_filter_by_products' );