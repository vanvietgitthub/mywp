<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action('template_redirect', 'wda_woocommerce_single_product', 10);

function wda_woocommerce_single_product() {
	if ( is_product() ) {
		// add class left of image
		add_action( 'woocommerce_before_single_product_summary', 'wda_single_left', 5);
		// </div> left
		add_action( 'woocommerce_before_single_product_summary', 'wda_container_close', 30);
		// add class right after summary
		add_action( 'woocommerce_after_single_product_summary', 'wda_single_right', 3);
		// content right
		add_action( 'woocommerce_after_single_product_summary', 'wda_content_single_right', 4);
		// </div> right
		add_action( 'woocommerce_after_single_product_summary', 'wda_container_close', 5);
		// guarantee
		add_action( 'woocommerce_after_single_product_summary', 'wda_guarantee', 4);
		// container
		add_action( 'woocommerce_before_single_product', 'wda_container_single_open', 5);
		// close div container
		add_action( 'woocommerce_single_product_summary', 'wda_container_close', 70 );
		// summary wrap
		add_action( 'woocommerce_single_product_summary', 'wda_entry_summary', 4 );
		// close summary wrap
		add_action( 'woocommerce_after_single_product', 'wda_container_close', 10);
		// remove sale bage
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
		// remove meta categories
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
		// add custom meta attribute
		add_action( 'woocommerce_single_product_summary', 'wda_woocommerce_template_single_meta', 6);
		// add attribute in to meta
		add_action( 'woocommerce_product_meta_end', 'thuong_hieu_swatches_html' );
		// add box freeship
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_freeship', 15 );
		// remove rating
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		// remove relate product
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
		// put relate product after summary
		add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 6);
		// add btn add to cart after content
		add_action( 'call_to_buy_product', 'do_call_to_buy_product', 5 );
		// rename heading title tab
		add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
		// output post per page relate product
		add_filter( 'woocommerce_output_related_products_args', 'wda_related_products_args', 20 );
		// style
		add_action( 'wp_head', 'wda_style_single_woocommerce', 999 );
		// script
		add_action( 'wp_footer', 'wda_script_single_woocommerce', 999 );
	}
}

function wda_single_left() {
	printf('<div class="%s">', esc_attr( 'left' ));
}
function wda_single_right() {
	printf('<div class="%s">', esc_attr( 'right' ));
}
function wda_content_single_right() {
	$term = get_queried_object();
	$infos = get_field('thong_tin_san_pham', $term);
	if ( !empty($infos) ) {
		?>
		<h3>Thông tin sản phẩm</h3>
		<ul>
			<?php foreach ($infos as $key => $info): ?>
				<li>
					<span class="label">- <?php echo $info['ten'] ?></span>
					<span class="item"><?php echo $info['chuc_nang'] ?></span>
				</li>
			<?php endforeach ?>
		</ul>
		<?php
	}
}
function wda_guarantee() {
	$term = get_queried_object();
	$year = !empty( get_field('thong_tin_san_pham', $term) ) ? get_field('thong_tin_san_pham', $term) : 1 ;
	?>
	<div class="wda_guarantee">
		<div class="icon">
			<img src="<?php echo esc_url( trailingslashit( get_template_directory_uri() ) . 'assets/images/baohanh.svg' ) ?>" alt="">
		</div>
		<div class="text">
			<span>Bảo hành 5 năm trên toàn quốc</span>
			<a href="#">
				(Xem Chính sách bảo hành)
			</a>
		</div>
	</div>
	<?php
}
function wda_container_single_open() {
	printf('<div class="%s">', esc_attr( 'container single-product' ));
}

function wda_related_products_args( $args ) {
	$args['posts_per_page'] = 12;
	$args['columns'] = 1;
	return $args;
}

function wda_woocommerce_template_single_meta() {
	global $product;
	?>
	<div class="product_meta">

		<?php do_action( 'woocommerce_product_meta_start' ); ?>

		<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

			<span class="sku_wrapper"><?php esc_html_e( 'Mã SP:', 'vms' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>

		<?php endif; ?>

		<?php do_action( 'woocommerce_product_meta_end' ); ?>

	</div>
	<?php
}

add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'remove_thuong_hieu_swatches_html', 10, 2 );

function remove_thuong_hieu_swatches_html($html, $args){
	if ( isset( $args['attribute'] ) && ( 'pa_thuong-hieu' === $args['attribute'] || 'pa_chat-lieu' === $args['attribute'] || 'pa_tinh-nang-dac-biet' === $args['attribute'] ) ) {
		$size_html = "";
		return $size_html;
	}

	return $html;
}

function thuong_hieu_swatches_html(){
	global $product;
	$output = array();
	$product_attributes = array('pa_thuong-hieu');
	foreach( $product_attributes as $taxonomy ){
		if( taxonomy_exists($taxonomy) ){
			if( $values = $product->get_attribute($taxonomy) ){
				$label_name = get_taxonomy($taxonomy)->labels->singular_name;

				$output[] = '<span class="'.$taxonomy.'"><label>'.$label_name . ': </label>' . $values.'</span>';
			}
		}
	}

	echo implode('', $output);
}

// freeship
function woocommerce_template_single_freeship() {
	$imgUrlFreeShip = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAQCAYAAAAFzx/vAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAHhSURBVHgBnVVbTsJAFD1Dyrf4Z6KYuoO6AlkCrkD5N4GuAFhBywqEFaArAFcgO7A8TPyzxE+U8dy2gyVSKD3JZKYzc+fee+6jCjtw5sEuK7RRAEphMmuil3VuZWzanO41MOEcIq8yymmNGpfHKTQoW6i/PWCKnLjw0KGHzXMP92avRINXNPzDRbClkJdrvHyXfFZQHJWSwmN6o8xx6cGdufCtlCXOOqYyAul5+lxhif9e1BYuxshGMG/hynzYzIdvhocPepSdKBwBUuXQ+lcy0WFidHcYI5TepRVuznyMRJl12UObgbaRHyHvd0jRUijKK1TSeNbMfIvCjj4iZoaSNP158ENDGbaKRfdv8wpJ/KhxBI0B49hCAWRlqSD80uiG7l8d8nKw1nG2oSCysrRC6pzTMvwwVfhBXEuFlW0pTKyOHttQl4CJ1ZREYawDzo13N+pAmUhao9SiwzFekRVztrMsEnpHfLzH+QTS5kgv1zfyiOzvkjPniFuitLmBhImGhlQUcK+usiy0FIYqzl4ZodRW1UOdJg4RC++DzdGnTKPqR57W5Q1h6GDhG2+5HEfeUWjRwvU+GdZoizXnJTI1RXYYso6c5eo0JoNJ0ZRB9wP38B+k6kXt7IYl9DJ30Tf7v1UFqO0PrOPVAAAAAElFTkSuQmCC';
	?>
	<div class="wda_template_single_product_freeship">
		<div class="icon">
			<?php printf('<img src="%s" alt="%s">', $imgUrlFreeShip, get_bloginfo( 'name' )) ?>
		</div>
		<div class="text">
			<span>vận chuyển toàn quốc</span>
		</div>
	</div>
	<?php
}

add_filter( 'woocommerce_product_single_add_to_cart_text', 'custom_add_to_cart_text_and_price' );

function custom_add_to_cart_text_and_price() {
	global $product;
	if ( $product->get_type() === 'variable' ) {
		$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
		$price  = $prices[0] > $prices[1] ? $prices[1] : $prices[0];
		$btn    = 'Mua ngay với giá ' . $price . 'đ';

	}else {
	    $pricesale = $product->get_sale_price();
	    $price = $product->get_regular_price();
		$btn    = 'Mua ngay với giá ' . $pricesale . 'đ';
    }
	return $btn;
}

add_action( 'woocommerce_single_product_summary', 'wda_single_hotline', 35 );
function wda_single_hotline() {
	?>
	<div class="wda_single_hotline">
		<i class="fas fa-phone-alt"></i>
		<span>Hotline mua hàng:</span>
		<span>0948 756 268</span>
	</div>
	<?php
}

function wda_entry_summary() {
	echo "<div class='wda_entry_summary_wrap'>";
}

function woo_rename_tabs( $tabs ) {

	$tabs['description']['title'] = __( 'Mô tả sản phẩm' );
	$tabs['reviews']['title'] = __( 'Đánh giá sản phẩm' );

	return $tabs;

}

add_filter( 'gettext', 'wda_variable_product_message', 97, 2 );
function wda_variable_product_message( $translated_text, $untranslated_text )
{
	if ($untranslated_text == 'Please select some product options before adding this product to your cart.') {
		$translated_text = 'Bạn hãy chọn màu cho sản phẩm trước khi mua hàng !';
	}
	return $translated_text;
}

add_filter( 'woocommerce_product_description_heading', 'change_text_product_description_heading' );
function change_text_product_description_heading() {
	return "Mô tả sản phẩm";
}

add_filter( 'woocommerce_product_additional_information_heading', function() {return;} );
remove_all_actions( 'woocommerce_product_additional_information' );

function do_call_to_buy_product() {
	global $product;?>
	<div class="call_to_buy_product">
		<?php if ( !empty( $product->get_image_id() ) ): ?>
			<a href="<?php echo get_permalink( $product->get_id() ) ?>">
				<div class="image">
					<?php printf('<img src="%s" alt="%s">', wp_get_attachment_image_src($product->get_image_id(), 'full')[0], get_bloginfo( 'name' )) ?>
				</div>
				<div class="text">
					<h4><?php echo get_the_title( $product->get_id() ) ?></h4>
					<?php wc_get_template( 'single-product/price.php' ); ?>
				</div>
			</a>
			<?php do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' ); ?>
		<?php endif; ?>
	</div>
	<?php
}

add_action( 'woocommerce_before_cart', 'wda_view_shop_cart_checkout' );

function wda_view_shop_cart_checkout() {
	printf('<a class=%s href="%s">%s</a>', esc_attr( 'continue_shop' ), esc_url( site_url( '/shop' ) ), esc_html( 'Mua thêm sản phẩm' ));
}

add_filter( 'get_product_search_form' , 'woo_custom_product_searchform' );
function woo_custom_product_searchform( $form ) {
	$form = '
	<form role="search" method="get" id="search-form-woocommerce-frontpage" action="' . esc_url( home_url( '/'  ) ) . '">
	<input type="text" value="' . get_search_query() . '" name="s" id="s-search-form-woocommerce-frontpage" placeholder="' . esc_html__( 'Tìm kiếm...', 'vms' ) . '" />
	<button type="submit" id="search-form-woocommerce-submit" ><i class="icon_wda icon_wda_search"></i></button>
	<input type="hidden" name="post_type" value="product" />
	</form>';
	return $form;
}

function wda_style_single_woocommerce() {
	?>
	<style id="single-woocommerce">
        /*
        * Container
        */
		@media (min-width: 320px){
			.container.single-product {
				padding: 0 20px;
			}
		}
		@media (min-width: 1199px){
			.container.single-product {
				padding: 0;
			}
		}
		.container.single-product {
			max-width: 1170px;
			margin: 0 auto;
		}
		.container.single-product::after{
			content: "";
			display: table;
			clear: both;
		}
        /*End container*/
        /*Left*/
		.container.single-product .left {
			width: 33.33%;
			float: left;
		}
        .flickity-for {
            width: 100%;
            height: auto;
        }
        .flickity-for .item {
            width: 100%;
        }
        .flickity-for figure {
            padding: 0;
            margin: 0;
        }
        .flickity-nav .item {
            width: 20%;
        }
        .flickity-for .item img,
        .flickity-nav .item img {
            max-width: 100%;
            height: auto;
        }
        .flickity-button:hover,
        .flickity-button,
        .flickity-button:focus {
            background: transparent;
            box-shadow: none;
            outline: none;
        }
        .flickity-prev-next-button .flickity-button-icon {
            width: 10px;
            height: 10px;
        }
        .flickity-prev-next-button {
            width: auto;
            height: auto;
        }
        .flickity-prev-next-button.previous {
            left: -15px;
        }
        .flickity-prev-next-button.next {
            right: -15px;
        }
        @media (max-width: 576px){
            .flickity-for .item {
                text-align: center;
            }
            .flickity-for .item img{
                max-width: 320px;
                height: auto;
            }
        }
		@media(max-width: 768px){
			.container.single-product .left {
				width: 45%;
			}
		}
		@media(max-width: 576px){
			.container.single-product .left {
				width: 100%;
			}
		}
        /*End left*/
        /*Summary*/
		.container.single-product .summary.entry-summary {
			width: 41.67%;
			float: left;
		}
		@media(max-width: 768px){
			.container.single-product .summary.entry-summary {
				width: 55%;
			}
		}
		@media(max-width: 576px){
			.container.single-product .summary.entry-summary {
				width: 100%;
			}
		}
		.container.single-product .summary.entry-summary .wda_entry_summary_wrap {
			padding-left: 60px;
			padding-right: 30px;
		}
        .container.single-product .summary.entry-summary .wda_entry_summary_wrap > * {
            width: 100%;
            float: left;
        }
		@media(max-width: 768px){
			.container.single-product .summary.entry-summary .wda_entry_summary_wrap {
				padding-left: 30px;
				padding-right: 0;
			}
		}
		@media(max-width: 576px){
			.container.single-product .summary.entry-summary .wda_entry_summary_wrap {
				padding-left: 0;
			}
		}
        .sku_wrapper {
            font-weight: normal;
            font-size: 14px;
            line-height: 17px;
            text-transform: uppercase;
            color: #333333;
            padding-right: 10px;
            margin-right: 10px;
            border-right: 1px solid #e0e0e0;
        }
        .sku {
            font-weight: bold;
            font-size: 14px;
            line-height: 17px;
            text-transform: uppercase;
            color: #333333;
        }
        /*End meta*/
        .container.single-product .price {
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }
        .container.single-product .price del {
            text-decoration: line-through;
            color: #333333;
        }
        .container.single-product .price del .amount {

            font-weight: normal;
            font-size: 16px;
            line-height: 20px;
            color: #555555;
        }
        .container.single-product .price ins {
            text-decoration: none
        }
        .container.single-product .price ins .amount {

            font-weight: bold;
            font-size: 24px;
            line-height: 30px;
            color: #D22333;
        }
        @media (max-width: 576px){
            .container.single-product .price ins .amount {
                font-size: 20px;
                line-height: 25px;
            }
            .container.single-product .price del .amount {
                font-size: 16px;
                line-height: 20px;
            }
            .container.single-product .price {
                margin: 5px 0;
            }
        }
        .container.single-product .summary.entry-summary .price .onsale {
            display: none;
        }
        /*End price*/
        .variations {
            float: left;
            width: 100%;
            margin-bottom: 30px;
        }
        @media (max-width: 576px){
            .variations {
                margin-bottom: 10px;
            }
        }
        .variable-items-wrapper {
            display: -webkit-flex;
            display: -moz-flex;
            display: -ms-flex;
            display: -o-flex;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        .variations .label {

            font-weight: bold;
            font-size: 14px;
            line-height: 17px;
            color: #333333;
        }
        .variations .variable-items-wrapper {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        .variations .variable-items-wrapper .variable-item {
            float: left;
            margin-right: 7px;
            width: 32px;
            height: 32px;
            position: relative;
            transition: all 0.3s ease;
            border-radius: 50%;
        }
        .variations .variable-items-wrapper .variable-item .variable-item-span {
            border: 1px solid #E0E0E0;
            width: 32px;
            height: 32px;
            float: left;
            border-radius: 50%;
        }
        .variations .variable-items-wrapper .variable-item.selected {
            border: 1px solid #D22333;
            width: 38px;
            height: 38px;
            background: transparent;
            border-radius: 50%;
            display: -webkit-flex;
            display: -moz-flex;
            display: -ms-flex;
            display: -o-flex;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }
        .variations .item:first-child {
            display: -webkit-flex;
            display: -moz-flex;
            display: -ms-flex;
            display: -o-flex;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        .variations .item:last-child .label {
            margin-bottom: 15px;
        }
        .variations .item:first-child .label {

            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 17px;
            color: #333333;
        }
        .variations .item .thuong-hieu {

            font-style: normal;
            font-weight: 500;
            font-size: 14px;
            line-height: 17px;
            color: #148FD5;
        }
        /*End variation color*/
        .container.single-product .pa_thuong-hieu {

            font-style: normal;
            font-weight: bold;
            font-size: 14px;
            line-height: 17px;
            color: #148FD5;
        }
        .container.single-product .pa_thuong-hieu label {
            color: #333333;
            font-weight: normal;
        }
        /*End thuonghieu next meta*/
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
            margin: 10px 0;
        }
        .woocommerce-product-rating .star-rating {
            float: left;
        }
        .woocommerce-product-rating .woocommerce-review-link {

            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 17px;
            color: #148FD4;
            text-decoration: none;
        }
        /*End style rating*/
        .wda_template_single_product_freeship {
            display: flex;
            flex-direction: row;
            align-items: center;
            background: #E8F2E5;
            border: 1px solid #C2D8BD;
            box-sizing: border-box;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        @media (max-width: 576px){
            .wda_template_single_product_freeship {
                margin-bottom: 10px;
            }
        }
        .wda_template_single_product_freeship .icon {
            padding: 10px;
            border-right: 1px solid #D1D8BD;
            margin-right: 10px;
        }
        .wda_template_single_product_freeship .text span {

            font-style: normal;
            font-weight: bold;
            font-size: 14px;
            line-height: 17px;
            text-transform: uppercase;
            color: #1D9000;
        }
        /*End style freeship*/
        .container.single-product .single_add_to_cart_button {
            background: #D22333;
            border-radius: 4px;
            border:0;
            padding-top: 10px;
            padding-bottom: 30px;
            width: 100%;

            font-style: normal;
            font-weight: bold;
            font-size: 18px;
            line-height: 22px;
            text-align: center;
            text-transform: uppercase;
            color: #FFFFFF;
            position: relative;
            margin-bottom: 15px;
            cursor: pointer;
        }
        .container.single-product .single_add_to_cart_button::after {
            content: 'Giao hàng & thanh toán tại nhà';
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);

            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 17px;
            text-align: center;
            color: #FFFFFF;
            text-transform: capitalize;
            width: 100%;
        }
        /*End style btn add to cart*/
        .wda_single_hotline {
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #D22333;
            box-sizing: border-box;
            border-radius: 4px;
            padding: 13px;
            cursor: pointer;
        }
        .wda_single_hotline i,
        .wda_single_hotline span:last-child {
            color: #D22333;
        }
        .wda_single_hotline i {
            margin-right: 10px;
        }
        .wda_single_hotline span {
            font-weight: 500;
            font-size: 14px;
            line-height: 17px;
            color: #333333;

        }
        .wda_single_hotline span:last-child {
            margin-left: 5px;
            font-weight: bold;

            font-size: 14px;
            line-height: 17px;
        }
        @media (max-width: 576px){
            .container.single-product .wda_entry_summary_wrap .product_title.entry-title,
            .wda_entry_summary_wrap .product_meta,
            .wda_entry_summary_wrap .woocommerce-product-rating
            {
                display: none;
            }
            .container.single-product .product_title.entry-title {
                font-size: 16px;
                line-height: 20px;
                margin-bottom: 5px;
            }
            .woocommerce-product-rating {
                margin-top: 0;
            }
            .star-rating .stars svg {
                width: 12px;
            }
            .star-rating {
                width: 70px;
            }
        }
        /*End hotline*/
        /*End summary*/
        /*Right*/
		.container.single-product .right {
			width: 25%;
			float: left;
		}
        .container.single-product .right h3 {

            font-style: normal;
            font-weight: bold;
            font-size: 16px;
            line-height: 20px;
            text-align: center;
            text-transform: uppercase;
            color: #333333;
            background: #F9B42E;
            border-radius: 4px 4px 0px 0px;
            padding: 15px 0;
            margin: 0;
        }
        .container.single-product .right ul {
            padding: 0;
            margin: 0;
            list-style: none;
            border: 1px solid #F9B42E;
            box-sizing: border-box;
            border-radius: 0px 0px 4px 4px;
            padding: 15px;
        }
        .container.single-product .right ul li {

            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 17px;
            color: #333333;
            margin-bottom: 13px;
        }
        .container.single-product .right ul li .item {
            font-weight: bold;
        }
        .wda_guarantee {
            float: left;
            margin-top: 15px;
            border: 1px solid #F9B42E;
            box-sizing: border-box;
            border-radius: 4px;
            padding: 18px;
        }
        .wda_guarantee::after {

        }
        .wda_guarantee .icon {
            float: left;
        }
        .wda_guarantee .text {

            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 20px;
            color: #333333;
            padding-left: 20px;
        }
        .wda_guarantee .text a {
            color: #D22333;
        }
		@media(max-width: 768px){
			.container.single-product .right {
				display: none;
			}
		}
        /*End right*/
        /*
        * Style product
        */
        ul.products h2 {
            font-weight: normal;
            font-size: 14px;
            line-height: 17px;
            color: #333333;
            transition: all 0.3s ease;
        }
        ul.products {
            padding: 0;
            margin: 0;
            list-style: none;
            grid-template-columns: repeat(4,1fr);
            grid-column-gap: 30px;
            grid-row-gap: 25px;
            display: grid;
            padding-left: 10px;
        }
        ul.products li.product .price {
            transition: all 0.3s ease;
        }
        ul.products li.product .price del {
            text-decoration: line-through;
            color: #333333;
        }
        ul.products li.product .price del .amount {
            font-weight: normal;
            font-size: 14px;
            line-height: 17px;
            color: #333333;
        }
        ul.products li.product .price ins {
            text-decoration: none
        }
        ul.products li.product .price ins .amount {
            font-weight: bold;
            font-size: 14px;
            line-height: 17px;
            color: #D22333;
        }
        ul.products li {
            position: relative;
            padding-bottom: 30px;
            padding-top: 30px;
            transition: all 0.3s ease;
        }
        ul.products li .onsale{
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 22px;
            background: #D22333;
            text-align: center;
            font-size: 12px;
            line-height: 15px;
            text-align: center;
            text-transform: uppercase;
            color: #FFFFFF;
            font-weight: bold;
            padding-top: 4px;
        }
        ul.products .view-more-product-home {
            position: absolute;
            bottom: 0;
            width: 100%;
            left: 0;
            font-style: normal;
            font-weight: bold;
            font-size: 14px;
            line-height: 17px;
            text-align: center;
            text-transform: uppercase;
            color: #FFFFFF;
            background: #D22333;
            padding: 12px 0;
            display: none;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        ul.products .pa_thuong-hieu {
            font-weight: bold;
            font-size: 14px;
            line-height: 17px;
            text-transform: uppercase;
            color:#333333;
            float: left;
            width: 100%;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        ul.products .variaton_colors {
            margin: 0;
            padding: 0;
            position: absolute;
            top: 25px;
            left: 15px;
            display: none;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            list-style: none;
        }
        ul.products .variaton_colors li {
            width: 24px;
            height: 24px;
            padding: 0 !important;
            border-radius: 50%;
            margin-bottom: 5px;
            float: none;
        }
        ul.products li img {
            max-width: 100%;
            height: auto;
        }
        ul.products li a {
            text-decoration: none;
        }
        ul.products li:hover {
            background: #FFFFFF;
            box-shadow: 0px 0px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        ul.products li:hover .variaton_colors,
        ul.products li:hover .view-more-product-home{
            display: block;
            opacity: 1;
            visibility: visible;
            transition: all 0.3s ease;
        }
        ul.products li:hover .pa_thuong-hieu,
        ul.products li:hover h2,
        ul.products li:hover .price{
            padding-left: 10px;
            transition: all 0.3s ease;
        }
		.container.single-product ul.products {
			grid-template-columns: repeat(5,1fr);
		}
		@media(max-width: 576px){
			.container.single-product ul.products {
				grid-template-columns: repeat(2,1fr);
			}
			.container.single-product .right {
				width: 100%;
				display: block;
				padding-top: 25px;
				margin-top: 25px;
				border-top: 6px solid #f0f0f0;
				padding-bottom: 25px;
				margin-bottom: 25px;
				border-bottom: 6px solid #f0f0f0;
			}
			.container.single-product .product.type-product {
				display: flex;
				flex-direction: column;
			}
			.container.single-product .product.type-product .woocommerce-tabs.wc-tabs-wrapper{
				order: 5;
			}
			.container.single-product .product.type-product .related.products{
				order: 10;
			}
		}
        /*End Relate product*/
        /*Style data tabs*/
		.tabs.wc-tabs {
			display: none;
		}
		#tab-additional_information, #tab-reviews, #tab-description, .related.products {
			width: 100%;
			float: left;
			display: block !important;
		}
        /*Style tab*/
        #tab-description p {
            font-style: normal;
            font-weight: normal;
            font-size: 15px;
            line-height: 26px;
            text-align: justify;
            margin: 0;
            padding: 0;
            margin-bottom: 25px;
        }
        #tab-description p img {
            display: block;
            margin: 0 auto;
        }
        #tab-description h1,h2,h3,h4,h5,h6 {

            font-style: normal;
            font-weight: bold;
            font-size: 18px;
            line-height: 22px;
            text-align: justify;
            margin: 0;
            padding: 0;
            margin-bottom: 15px;
        }
        .woocommerce-tabs.wc-tabs-wrapper {
            float: left;
            width: 100%;
        }
        /*Style title archive*/
		.container.single-product .product_title.entry-title {
			font-weight: normal;
			font-size: 24px;
			line-height: 30px;
			text-transform: uppercase;
			color: #333333;
			margin-top: 0;
			margin-bottom: 11px;
		}
		.related.products {
			margin-top: 55px;
		}
		@media (max-width: 576px){
			.related.products {
				margin-top: 25px;
			}
		}
		.related.products ul {
			display: block;
		}
		.related.products ul li {
			width: 20%;
            float:left;
		}
		@media (max-width: 576px){
			.related.products ul li {
				width: 50%;
			}
		}
		.related.products ul li a {
			display: block;
			padding: 15px;
		}
        /*Style heading*/
		.related.products > h2,
		#tab-description > h2:first-child,
		#sidebar-single-product-wrap .widgettitle,
		.woocommerce-Reviews-title {
			font-style: normal;
			font-weight: bold;
			font-size: 18px;
			line-height: 22px;
			text-transform: uppercase;
			color: #333333;
			border-bottom: 1px solid #E0E0E0;
			padding-bottom: 15px;
			position: relative;
		}
		.related.products > h2::after,
		#tab-description > h2:first-child::after,
		#sidebar-single-product-wrap .widgettitle::after,
		.woocommerce-Reviews-title::after {
			content: "";
			position: absolute;
			top: 36px;
			width: 80px;
			height: 2px;
			background: #D22333;
			left: 0;
		}
		.related.products > h2 {
			margin-bottom: 50px;
		}
		@media (max-width: 576px){
			.related.products > h2 {
				margin-bottom: 25px;
			}
		}
        /*End heading*/
        /*style secription and sidebar*/
		#main-tabs {
			float: left;
			width: 66.67%;
		}
		@media (max-width: 768px){
			#main-tabs {
				width: 100%;
			}
		}
		#sidebar-single-product-wrap {
			float: left;
			width: 33.33%;
		}
		@media (max-width: 768px){
			#sidebar-single-product-wrap {
				width: 100%;
			}
		}
		#sidebar-single-product-wrap #sidebar-single-product {
			padding-left: 30px;
		}
		@media (max-width: 768px){
			#sidebar-single-product-wrap #sidebar-single-product {
				padding-left: 0;
			}
		}
		/*End data tabs*/
		.call_to_buy_product {
			float: left;
			display: flex;
			align-items: center;
			width: 100%;
			border: 1px solid #E0E0E0;
			box-sizing: border-box;
			border-radius: 4px;
			margin-top: 25px;
			margin-bottom: 40px;
			padding: 20px;
		}
		@media (max-width: 576px){
			.call_to_buy_product {
				flex-direction: column;
			}
		}
		.call_to_buy_product::after {
			content: "";
			clear: both;
			display: table;
		}
		.call_to_buy_product > a {
			text-decoration: none;
			float: left;
			width: 50%;
			display: block;
		}
        .call_to_buy_product form {
            float: left;
            width: 50%;
            display: block;
        }
		.call_to_buy_product > a .image {
			width: 70px;
			height: 70px;
			float: left;
		}
		.call_to_buy_product > a .image img {
			max-width: 100%;
		}
		.call_to_buy_product > a h4 {
			font-style: normal;
			font-weight: bold;
			font-size: 16px;
			line-height: 20px;
			color: #333333;
			margin: 0;
			margin-bottom: 6px;
		}
		.call_to_buy_product .price {
			margin: 0;
			font-weight: bold;
		}
		.container.single-product .call_to_buy_product .price ins .amount,
		.container.single-product .call_to_buy_product .price del .amount {
			font-size: 14px;
			line-height: 17px;
			font-weight: bold;
		}
		.container.single-product .call_to_buy_product .price del .amount {
			font-weight: normal;
		}
		.call_to_buy_product .variations_form.cart {
			width: 50%;
		}
		@media (max-width: 576px){
			.call_to_buy_product .variations_form.cart,
			.call_to_buy_product > a {
				width: 100%;
			}
		}
		.call_to_buy_product .variations_button {
			float: left;
			width: 100%;
		}
		.container.single-product .call_to_buy_product .variations_button .single_add_to_cart_button {
			margin-bottom: 0;
		}
		.call_to_buy_product .variations {
			display: none;
		}
		.call_to_buy_product .onsale {
			display: none;
		}
		/*end call_to_buy_product*/
		.woocommerce-Tabs-panel--reviews .commentlist li {
			border:0;
		}
		.single-product #comments .woocommerce-Reviews-title{
			font-size: 18px;
			
		}
		.single-product #comments .woocommerce-Reviews-title {
			margin-top: 0;
		}
		.single-product #comments .comment-form-cookies-consent{
			display: none
		}
		.single-product #comments .comment-formcomment-form--cookies-consent{
			display: none
		}
		.single-product #comments .stars span a::before {
			content: "\2605";
			position: absolute;
			top: -5px;
			left: 5px;
			width: 1em;
			height: 1em;
			line-height: 1;
			text-indent: 0;
			color: #bebebe;
		}
		.single-product #comments .stars span {
			unicode-bidi: bidi-override;
			direction: rtl;
		}
		.single-product #comments .stars span a:hover::before,
		.single-product #comments .stars span a:hover ~ a::before,
        .single-product #comments .stars.selected span a.active::before {
			color: #f9d529;
		}
		.single-product #comments p.stars{
			margin: 0;
		}
		.single-product #comments #review_form #respond .form-submit input{
			color: #fff;
			background-color: #d22333;
			box-shadow: none;
			border:none;
			line-height: 45px;
			outline: none;
			
		}
		.single-product #comments #review_form #respond .form-submit input:hover,
		.single-product #comments #review_form #respond .form-submit input:focus {
			border:none;
			box-shadow: none;
			outline: none;
			transform: none;
		}
		.single-product .woocommerce-Tabs-panel--reviews .comment-form-rating {
			position: relative;
		}
		.single-product #comments #review_form {
			background-color: #f8f8f8;
			padding: 20px;
			float: left;
			width: 100%;
		}
		.woocommerce-Tabs-panel--reviews .comment-respond textarea {
			height: 100px;
			padding: 8px 10px;
			line-height: 1.6;
			border: 0;
			border: 1px solid #e0e0e0;
			border-radius: 4px;
		}
		.woocommerce-Tabs-panel--reviews .comment-form{
			overflow: hidden;
		}
		.vms-review-box{
			border-radius: 5px;
			margin-bottom: 20px;
			padding: 5px 15px;
			overflow: hidden;
		}
		.vms-review-box .box-average {
			display: block;
			float: left;
			width: 100%;
		}
		.box-average .bgb{
			width: 55%;
			background-color: #e9e9e9;
			height: 5px;
			display: inline-block;
			margin: 0 10px;
			border-radius: 5px;
		}
		.box-average .bgb-in{
			width: 100%;
			background-color: #e21b1b;
			background-image: linear-gradient(90deg,#ff7d26 0%,#e21b1b 97%);
			height: 5px;
			border-radius: 5px 0 0 5px;
			max-width: 100%;
		}
		.box-average .fa-star{
			color: #f9d529;
		}
		.box-write-review p.stars a{
			font-size: 22px;
			width: 22px;
			
			position: relative;
			text-indent: -999em;
			display: inline-block;
		}
		.box-average .rv-average{
			width: 17%;
			float: left;
			border-right: solid 1px #eee;
			padding: 30px 0;
			height: 90%;
			text-align: center;
			box-sizing: border-box;
			margin: 5px 10px 5px 5px;
		}
		.box-average .rv-average span{
			font-size: 40px;
			color: #e21b1b;
			line-height: 40px;
			font-weight: bold;
			
		}
		.box-average .rv-detail{
			font-size: 13px;
			overflow: hidden;
			box-sizing: border-box;
			padding: 10px 0;
			width: 45%;
			float: left;
			border-right: solid 1px #eee;
			
		}
		.box-average .rv-detail .rv-c{
			color: #4497E3;
		}
		.btn-rv{
			font-size: 13px;
			overflow: hidden;
			width: auto;
			padding: 10px;
			
		}
		.btn-rv a{
			display: block;
			width: 200px;
			margin: 30px auto;
			padding: 10px;
			color: #222;
			background-color: #d22333;
			border-radius: 5px;
			text-align: center;
			box-sizing: border-box;
			font-weight: bold;
			
			text-decoration: none;
		}
		.box-write-review .form-submit{
			display: inline-block;
			width: calc(50% - 40px);
			margin-top: 0px;
		}
		.box-write-review .form-submit #submit{
			width: 100%;
		}
		.box-write-review .comment-form-author{
			float: left;
			margin-bottom: 10px;
			width: calc(50% - 40px);
			margin-top: 0;
		}
		.box-write-review .comment-form-comment{
			width: calc(50% - 10px);
			float: left;
			margin-right: 20px;
			margin-top: 0;
		}
		.box-write-review{
			display:none;
		}
		.single-product .btn-showreview {color: white !important;}
		.comment-form-rating {
			display: flex;
			align-items: center;
		}
		.comment-form-rating label {
			
			font-style: normal;
			font-weight: bold;
			font-size: 18px;
			line-height: 22px;
			text-transform: uppercase;
			color: #333333;
			margin-bottom: 15px;
		}
		.commentlist {
			margin: 0;
			padding: 0;
			list-style: none;
		}
		.comment_container img,
		.woocommerce-review__dash {
			display: none;
		}
		.comment-text {
			position: relative;
		}
		.comment-text .star-rating {
			position: absolute;
			top: 3px;
			right: 0;
		}
		.comment-text .meta {
			padding: 0;
			margin: 0;
		}
		.comment-text span:last-child .stars svg {
			color: #f9d529;
		}
		.comment-text .description p {
			
			font-style: normal;
			font-weight: normal;
			font-size: 15px;
			line-height: 26px;
			text-align: justify;
			color: #555555;
			padding: 0;
			margin: 0;
		}
		.woocommerce-review__author {
			
			font-style: normal;
			font-weight: bold;
			font-size: 14px;
			line-height: 26px;
			text-align: justify;
			color: #333333;
			text-transform: capitalize;
			margin-right: 15px;
		}
		.woocommerce-review__published-date {
			
			font-style: normal;
			font-weight: normal;
			font-size: 13px;
			line-height: 16px;
			text-align: justify;
			color: #888888;
		}
		@media screen and (max-width: 767px){
			.box-average .rv-average{
				width: 27%;
				padding: 30px 0px;
				margin: 0;
			}
			.box-average .rv-detail{
				width: 67%;
			}
			.box-average .bgb{
				width: 34%;
			}
			.box-average .rv-average span {
				font-size: 22px;
			}
			.vms-review-box{
				padding: 0;
			}
			.box-average .rv-detail{
				border-right: none;
				padding-left: 10px;
			}
			.btn-rv a{
				margin: 0 auto;
				width: 100%;
			}
			.box-write-review .form-submit,
			.box-write-review .comment-form-comment{
				width: 100%;
			}
			.box-write-review .comment-form-author{
				width: 100%;
			}
			.btn-rv{
				width: 100%;
				padding: 10px 0;
			}
		}
		@media all and (min-width: 800px) {
			.fancybox-thumbs {
				top: auto;
				width: auto;
				bottom: 0;
				left: 0;
				right : 0;
				height: 95px;
				padding: 10px 10px 5px 10px;
				box-sizing: border-box;
				background: rgba(0, 0, 0, 0.3);
			}

			.fancybox-show-thumbs .fancybox-inner {
				right: 0;
				bottom: 95px;
			}
		}
		/*end comment template*/
		/*End single product*/
	</style>
	<?php
}

function wda_script_single_woocommerce() {
	?>
    <script>
        jQuery(document).ready(function($) {
            var index = 1;
            const body = jQuery("body");
            const flickityFor = jQuery(".flickity-for");
            const formCartTop = jQuery('.wda_entry_summary_wrap .variations_form').offset().top;

            jQuery(".color-variable-item").click(function(event) {
                flickityFor.flickity('select', 0);
                setTimeout(function(){
                    var formCart = jQuery('.wda_entry_summary_wrap .variations_form');
                    var imageId = formCart.attr("current-image");
                    var optionVal = jQuery('.wda_entry_summary_wrap #pa_color').val();
                    var variation_id = jQuery('.wda_entry_summary_wrap input[name="variation_id"]').val();
                    var formCartCall = jQuery('.call_to_buy_product .variations_form');
                    var selectCall = jQuery('.call_to_buy_product #pa_color');
                    formCartCall.attr("current-image", imageId);
                    selectCall.val(optionVal);
                    jQuery('.call_to_buy_product .variable-item[data-value="'+optionVal+'"]').addClass('selected');
                    jQuery('.call_to_buy_product input[name="variation_id"]').val(variation_id);
                    jQuery('.call_to_buy_product .woocommerce-variation-add-to-cart').removeClass('woocommerce-variation-add-to-cart-disabled');
                    jQuery('.call_to_buy_product .woocommerce-variation-add-to-cart').addClass('woocommerce-variation-add-to-cart-enabled');
                    jQuery('.call_to_buy_product .single_add_to_cart_button').removeClass('disabled');
                    jQuery('.call_to_buy_product .single_add_to_cart_button').removeClass('wc-variation-selection-needed');
                }, 2000);

            });

            jQuery('.call_to_buy_product .single_add_to_cart_button').on('click', function() {
                jQuery('html, body').animate({ scrollTop: formCartTop }, 1000);
            });
        });
        jQuery(document).ready(function($) {
            jQuery('.related.products ul.products').flickity({
                freeScroll: false,
                contain: true,
                prevNextButtons: true,
                pageDots: false,
                wrapAround: true,
                lazyLoad: true,
                cellAlign: 'left'
            })

            jQuery('[data-fancybox="images"]').fancybox({
                margin : [44,0,22,0],
                thumbs : {
                    autoStart : true,
                    axis      : 'x'
                }
            })
        });
        jQuery(document).ready(function($) {
            jQuery('.btn-showreview').click(function(){
                jQuery(this).text(function(i, text){
                    return text === "Gửi đánh giá của bạn" ? "Đóng lại" : "Gửi đánh giá của bạn";
                });
                jQuery('.box-write-review').toggle(300, 'linear');
            });
            jQuery('body #commentform').on( 'click', '.stars a', function () {
                d = jQuery(this);
                var startsAcitve = jQuery(this).nextAll();
                for (var i = 0; i< startsAcitve.length; i++ ) {

                }
            })
        });
    </script>
	<?php
}