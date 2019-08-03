<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Add cart to Header
 */
function wda_header_cart() {
	if ( is_cart() ) {
		$class = 'current-menu-item';
	} else {
		$class = '';
	}
	?>

	<ul id="site-header-cart" class="site-header-cart menu ulreset">
		<li class="<?php echo esc_attr( $class ); ?>">
			<?php wda_cart_link(); ?>
		</li>
		<li>
			<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
		</li>
	</ul>
	<?php
}

function wda_cart_link() {
	?>
	<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'wda' ); ?>">
		<?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?> <span class="count"><?php echo wp_kses_data( sprintf( _n( '%d', '%d', WC()->cart->get_cart_contents_count(), 'vms' ), WC()->cart->get_cart_contents_count() ) ); ?></span>
	</a>
	<?php
}

/*
 * d to VND
 */
 add_filter('woocommerce_currency_symbol', 'wda_change_existing_currency_symbol', 10, 2);

 function wda_change_existing_currency_symbol( $currency_symbol, $currency ) {
 	switch( $currency ) {
 		case 'VND': $currency_symbol = ' VND'; break;
 	}
 	return $currency_symbol;
 }

/*
 * Clear class on a link product
 */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );

add_action( 'woocommerce_before_shop_loop_item', 'wda_woocommerce_template_loop_product_link_open', 10 );

function wda_woocommerce_template_loop_product_link_open() {
	global $product;

	$link = apply_filters( 'wda_woocommerce_loop_product_link', get_the_permalink(), $product );

	echo '<a href="' . esc_url( $link ) . '">';
}

/*
 * Clear class on title product
 */
add_filter( 'woocommerce_product_loop_title_classes', 'wda_clear_class_on_title_product', 10 );

function wda_clear_class_on_title_product() {
	return;
}

/*
 * Customize rating html
 */
add_filter( 'woocommerce_product_get_rating_html', function ( $html, $rating, $count ) {

	$html = '<div class="star-rating">';
	$html .= wc_get_star_rating_html( $rating, $count );
	$html .= '</div>';

	return $html;
}, 10, 3 );

add_filter( 'woocommerce_product_get_rating_html', 'wda_wc_get_rating_html', 10, 3 );

function wda_wc_get_rating_html( $html, $rating, $count ) {

	$html = '<div class="star-rating">';
	$html .= '<span class="stars"><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg></span>';
	$html .= wc_get_star_rating_html( $rating, $count );
	$html .= '</div>';

	return $html;
}

add_filter( 'woocommerce_get_star_rating_html', 'wda_wc_get_star_rating_html', 10, 3 );

function wda_wc_get_star_rating_html( $html, $rating, $count ) {

	$modify = '';
	if ( 0 < $count ) {
		$modify = 'stars--fill';
	}

	$html = '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%; float:left">';
	$html .= '<span class="stars '.$modify.'"><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg><svg aria-hidden="true" data-prefix="fal" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 372.686L380.83 448l-33.021-142.066L458 210.409l-145.267-12.475L256 64l-56.743 133.934L54 210.409l110.192 95.525L131.161 448z"></path></svg></span>';

	$html .= '</span>';

	$html .= '</span>';

	return $html;
}

// display attribute and variation on homepage
add_action('woocommerce_shop_loop_item_title','display_attributes_variations_hompage', 15 );

function display_attributes_variations_hompage() {
	$detect = new Mobile_Detect();
	if( !$detect->isMobile() ) {
		global $product;

		$output = array();

		$termids = array();

		$colors = array();

		$colorhtml = '';

		if( is_front_page() || is_shop() || is_product_category() ) {

			if ( $product->get_type() == 'variable' ) {

				$product_attributes = array('pa_thuong-hieu');

				$attribute_name = 'pa_color';

				$term_key = 'product_attribute_color';

				$terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );

				foreach( $product_attributes as $taxonomy ){
					if( taxonomy_exists($taxonomy) ){
						if( $values = $product->get_attribute($taxonomy) ){
							$label_name = get_taxonomy($taxonomy)->labels->singular_name;

							$output[] = '<span class="'.$taxonomy.'">'.$values.'</span>';
						}
					}
				}
				foreach ( $terms as $term ) {
					$termids[] = $term->term_id;
				}
				foreach ( $termids as $id ) {
					$colors[] = get_term_meta( $id, $term_key)[0];
				}
				foreach ( $colors as $color ) {
					$colorhtml .= '<li style="background:'.$color.'"></li>';
				}
			}

			echo implode('', $output).'<ul class="variaton_colors">'.$colorhtml.'</ul>';
		}
	}
}

/*
* onsale: text to percentage of variations product
*/

add_filter('woocommerce_sale_flash', '__return_false');

add_filter('woocommerce_format_sale_price', 'wda_sale_price_percentage', 20, 3 );

add_filter( 'woocommerce_variable_sale_price_html', 'wda_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wda_variation_price_format', 10, 2 );

function wda_variation_price_format($price, $product) {
	global $post, $product;
	$percentage ="";
	$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
	$price = $prices[0] !== $prices[1] ? sprintf( '%1$s', wc_price( $prices[0] ) ) : wc_price( $prices[0] );

	$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
	sort( $prices );
	$saleprice = $prices[0] !== $prices[1] ? sprintf( '%1$s', wc_price( $prices[0] ) ) : wc_price( $prices[0] );

	$priceConvert = strip_tags( $price );
	$priceConvert = (float) preg_replace('/[^0-9.]+/', '', $priceConvert);
	$saleConvert = strip_tags( $saleprice );
	$saleConvert = (float) preg_replace('/[^0-9.]+/', '', $saleConvert);
	$percentage .= round( ( $saleConvert - $priceConvert ) / $saleConvert * 100 );
	if ( $price !== $saleprice ) {
		$price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins><span class="onsale">'. $percentage .'%</span>';
	}

	return $price;
}

function wda_sale_price_percentage( $price, $regular_price, $sale_price ){
	global $post, $product;
	$percentage ="";
	$regular_price = strip_tags( $regular_price );
	$regular_price = (float) preg_replace('/[^0-9.]+/', '', $regular_price);
	$sale_price = strip_tags( $sale_price );
	$sale_price = (float) preg_replace('/[^0-9.]+/', '', $sale_price);
	$percentage .= round( ( $regular_price - $sale_price ) / $regular_price * 100 );

	return $price . ' <span class="onsale">' . $percentage . '%</span>';
}

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
/*
 * Change html del and ins
 */
add_filter( 'woocommerce_get_price_html', 'wda_woocommerce_price_html', 100, 2 );

function wda_woocommerce_price_html( $price, $product ){
	return preg_replace('@(<del>.*?</del>).*?(<ins>.*?</ins>)@misx', '$2 $1', $price);
}

/*
 * Remove breadcrumb woocommerce
 * Add breadcrumb yoatseo
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
add_action( 'woocommerce_before_main_content', 'wda_breadcrumb', 3 );

add_action( 'wp_head', 'wda_style_Woocommerce', 999 );

function wda_style_Woocommerce() {
	?>
	<style id="style-woocommerce">
		.star-rating {
			overflow: hidden;
			position: relative;
			height: 1em;
			line-height: 1;
			width: 5.625em;
			font-size: 1.2em;
			margin-left: 0;
		}
		.star-rating .stars {
			color: #bebebe;
			top: 0;
			left: 0;
			position: absolute;
			overflow: hidden;
			z-index: 1;
		}
		.star-rating .stars--fill {
			color: #fec233;
			top: 0;
			position: relative;
			left: 0;
			z-index: 2;
		}
		.star-rating .stars svg {
			display: inline-block;
			font-size: inherit;
			height: 1em;
			overflow: visible;
			vertical-align: -.125em;
			width: 1em;
		}
		.woocommerce-product-rating {
			margin-top: 15px;
		}
		.woocommerce-product-rating .star-rating {
			float: left;
		}
	</style>
	<?php
}