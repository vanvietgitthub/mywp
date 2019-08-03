<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action('template_redirect', 'wda_woocommerce_front_page', 10);

function wda_woocommerce_front_page() {
	if ( is_front_page() ) {
		// Disable style and script contact form 7
		add_filter( 'wpcf7_load_js', '__return_false' );
		add_filter( 'wpcf7_load_css', '__return_false' );
		// add text view more
		add_action( 'woocommerce_after_shop_loop_item_title', 'add_btn_view_detail_product_before_title', 15 );
	}
}

function add_btn_view_detail_product_before_title() {
	$detect = new Mobile_Detect();
	if( !$detect->isMobile() ) {
		?>
		<span class="<?php echo esc_attr("view-more-product-home") ?>"><?php echo apply_filters( 'wda_text_view_detail_product', esc_attr__( "Xem chi tiết", "vms" ) ) ?></span>
	<?php }
}