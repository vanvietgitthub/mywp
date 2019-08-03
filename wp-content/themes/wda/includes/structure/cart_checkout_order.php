<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action('template_redirect', 'wda_woocommerce_cart_checkout_page', 10);

function wda_woocommerce_cart_checkout_page() {
	if ( is_checkout() || is_cart() ) {
		add_action( 'wp_head', 'wda_style_cart_checkout_page_woocommerce', 999 );
		add_action( 'wp_footer', 'wda_script_cart_checkout_page_woocommerce', 999 );
    }
}
/*
 * Cart Page
 */

remove_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );

add_filter( 'woocommerce_cart_item_quantity', 'wda_woocommerce_cart_item_quantity' ,10 ,3);

function wda_woocommerce_cart_item_quantity($product_quantity, $cart_item_key, $cart_item) {
	$quantity_nav = '<div class="quantity-nav"><div class="quantity-button quantity-up">+</div><div class="quantity-button quantity-down">-</div></div>';
	return $product_quantity.$quantity_nav;
}

add_filter( 'woocommerce_quantity_input_min', 'wda_woocommerce_quantity_input_min_callback', 10, 2 );

function wda_woocommerce_quantity_input_min_callback( $min, $product ) {
	$min = 1;
	return $min;
}

add_filter( 'woocommerce_quantity_input_max', 'wda_woocommerce_quantity_input_max_callback', 10, 2 );

function wda_woocommerce_quantity_input_max_callback( $max, $product ) {
	$max = 90;
	return $max;
}


add_filter( 'wc_empty_cart_message', 'wda_empty_cart_messeage', $priority = 10 );

function wda_empty_cart_messeage() {
	return 'Hiện tại giỏ hàng của bạn đang trống';
}


add_filter( 'woocommerce_add_to_cart_redirect', 'wda_redirect_checkout_add_cart' );

function wda_redirect_checkout_add_cart( $url ) {
	$url = get_permalink( get_option( 'woocommerce_checkout_page_id' ) );
	return $url;
}

/*
 * Checkout Page
 */

add_filter( 'woocommerce_order_button_text', 'wda_custom_order_button_text' );

function wda_custom_order_button_text() {
	return __( 'Nhấn thanh toán', 'vms' );
}

add_filter( 'woocommerce_checkout_fields' , 'wda_labels_placeholders', 9999 );

function wda_labels_placeholders( $fields ) {
	$fields['billing']['billing_first_name']['placeholder'] = 'Họ và tên';
	$fields['billing']['billing_phone']['placeholder'] = 'Số điện thoại';
	$fields['order']['order_comments']['placeholder'] = 'Yêu cầu khác (Không bắt buộc)';

	unset($fields['billing']['billing_first_name']['label']);
	unset($fields['billing']['billing_company']['label']);
	unset($fields['billing']['billing_country']['label']);

	unset($fields['billing']['billing_last_name']);
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_address_1']);
	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_city']);
	unset($fields['billing']['billing_state']);
	unset($fields['billing']['billing_email']);
	unset($fields['billing']['shipping']);
	unset($fields['shipping']);
	return $fields;
}

add_action( 'woocommerce_after_checkout_billing_form', 'wda_select_field' );

function wda_select_field( $checkout ){
	woocommerce_form_field( 'sex', array(
		'type'          => 'radio',
		'required'  => true,
		'class'         => array(),
		'label'         => '',
		'label_class'   => '',
		'options'   => array(
			'Man'  => 'Anh',
			'Girl'  => 'Chị'
		)
	), $checkout->get_value( 'sex' ) );
}
add_action( 'woocommerce_checkout_update_order_meta', 'wda_save_what_we_added' );

function wda_save_what_we_added( $order_id ){
	if( !empty( $_POST['sex'] ) )
		update_post_meta( $order_id, 'sex', sanitize_text_field( $_POST['sex'] ) );
}

add_action( 'woocommerce_admin_order_data_after_order_details', 'wda_sex_display_order_data_in_admin' );

function wda_sex_display_order_data_in_admin( $order ){  ?>
	<div class="order_data_column">
		<h4><?php _e( 'Giới tính', 'woocommerce' ); ?><a href="#" class="edit_address"><?php _e( 'Sửa', 'woocommerce' ); ?></a></h4>
		<div class="address">
			<?php
			echo '<p><strong>' . __( 'Text Field' ) . ':</strong>' . get_post_meta( $order->id, 'sex', true ) . '</p>';?>
		</div>
		<div class="edit_address">
			<?php woocommerce_wp_text_input( array( 'id' => 'sex', 'label' => __( 'Some field' ), 'wrapper_class' => '_billing_company_field' ) ); ?>
		</div>
	</div>
<?php }

add_action( 'woocommerce_process_shop_order_meta', 'wda_sex_save_extra_details', 45, 2 );

function wda_sex_save_extra_details( $post_id, $post ){
	update_post_meta( $post_id, 'sex', wc_clean( $_POST[ 'sex' ] ) );
}

/*
 * Order Page
 */

add_filter( 'woocommerce_thankyou_order_received_text', 'wda_notice_checkout_success', 10, 1 );
function wda_notice_checkout_success($order) {
	$notice = '<span> <img width="19px" height="15px" src="'.esc_url( get_theme_file_uri( '/assets/images/tick.png' ) ).'">' ;
	$notice .= ' ĐẶT HÀNG THÀNH CÔNG</span>';

	return $notice;
}

function wda_style_cart_checkout_page_woocommerce() {
    ?>
    <style id ="cart-checkout-order">
        .page-content {
            display: flex;
            max-width: 1170px;
            margin: 0 auto;
        }
        .page-content .woocommerce {
            max-width: 600px;
            margin: auto;
            background: #fff;
            border: 1px solid #d8d8d8;
            -moz-box-shadow: 0 0 20px rgba(0, 0, 0, .15);
            -webkit-box-shadow: 0 0 20px rgba(0, 0, 0, .15);
            box-shadow: 0 0 20px rgba(0, 0, 0, .15);
            padding: 30px 30px;
            margin-top: 50px;
            position: relative;
        }
        .continue_shop {
            color: #d22333;
            font-family: "Quicksand";
            font-size: 14px;
            font-weight: bold;
            position: absolute;
            left: 0;
            top: -25px;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item {
            display: flex;
            flex-direction: row;
            border-bottom: 1px solid #e9e9e9;
            padding-bottom: 10px;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .leftImage {
            flex: 100px 0 0;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: column;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .leftImage img {
            max-width: 100%;
            height: auto;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .leftImage .product-remove {
            text-align: center;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo {
            width: 100%;
            padding-left: 20px;
            padding-top: 10px;
            position: relative;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-name a {
            color: #000;
            font-family: "Quicksand";
            font-size: 15px;
            font-weight: bold;
            text-decoration: none;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-price {
            position: absolute;
            top: 5px;
            right: 0;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-price::before {
            content: unset;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-price .woocommerce-Price-amount {
            color: #c10017;
            font-family: "Quicksand";
            font-size: 18px;
            font-weight: bold;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-quantity {
            width: 100%;
            float: left;
            position: relative;
            margin-top: 10px;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-quantity .quantity {
            float: right;
            position: relative;
            width: 100px;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-quantity .quantity input {
            width: 100%;
            height: 43px;
            line-height: 1.65;
            float: left;
            display: block;
            padding: 0;
            margin: 0;
            text-indent: 10px;
            border: 1px solid #eee;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-quantity .quantity input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
            -moz-appearance: textfield;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-quantity .quantity-nav {
            float: left;
            position: absolute;
            right: -19px;
            top: 0;
            height: 43px;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-quantity .quantity-nav .quantity-button {
            position: relative;
            cursor: pointer;
            border-left: 1px solid #eee;
            width: 20px;
            text-align: center;
            color: #333;
            font-size: 13px;
            font-family: "Quicksand";
            line-height: 1.7;
            -webkit-transform: translateX(-100%);
            transform: translateX(-100%);
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            -o-user-select: none;
            user-select: none;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-quantity .quantity-nav .quantity-button .quantity-up {
            position: absolute;
            height: 50%;
            top: 0;
            border-bottom: 1px solid #eee;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-quantity .quantity-nav .quantity-button .quantity-down {
            position: absolute;
            bottom: -1px;
            height: 50%;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-quantity::after {
            content: "";
            display: table;
            clear: both;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-subtotal {
            float: left;
            width: 100%;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
            padding: 7px 0;
            margin-bottom: 10px;
            margin-top: 20px;
            display: none;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-subtotal::before {
            margin-bottom: 0;
            font-family: "Quicksand";
            font-size: 14px;
            font-weight: normal;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-name .variation {
            display: flex;
            align-items: center;
            font-family: "Quicksand";
            font-size: 14px;
            font-weight: bold;
            padding: 0;
            margin: 0;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-name .variation .variation-Musc,
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-name .variation .variation-Musc p {
            padding: 0;
            margin: 0;
        }
        .page-content .woocommerce .woocommerce-cart-form__cart-item.cart_item .rightInfo .product-name .variation dd.variation-Musc {
            margin-left: 5px;
        }
        .page-content .actions {
            font-family: "Quicksand";
            font-size: 14px;
            font-weight: normal;
            color: #288ad6;
        }
        .page-content .actions button[name='update_cart'] {
            display: none !important;
        }
        .page-content .actions .coupon {
            display: none;
            margin-top: 5px;
            border-bottom: 1px solid #e9e9e9;
            padding-bottom: 5px;
        }
        .page-content .actions .coupon label {
            cursor: pointer;
        }
        .page-content .actions .coupon .box_coupon_wrapper {
            display: none;
            transition: all 0.3s ease;
        }
        .page-content .actions .coupon .box_coupon {
            display: flex;
            flex-direction: row;
            justify-content: flex-end;
            margin-top: 10px;
        }
        .page-content .actions .coupon input {
            width: 200px;
            height: 40px;
            margin-right: 10px;
        }
        .page-content .actions .coupon button {
            height: 40px;
            line-height: 15px;
        }
        .page-content .actions .coupon button:hover {
            box-shadow: none;
            background: #1a93cd;
            border: 1px solid #1a93cd;
            -webkit-transform: unset;
            -ms-transform: unset;
            transform: unset;
        }
        .page-content .cart-collaterals {
            width: 100%;
        }
        .page-content .cart-collaterals::after {
            content: "";
            display: table;
            clear: both;
        }
        .page-content .cart-collaterals .cart_totals.calculated_shipping {
            width: 100%;
            float: left;
        }
        .page-content .cart-collaterals .cart_totals.calculated_shipping::after {
            content: "";
            display: table;
            clear: both;
        }
        .page-content .cart-collaterals .cart-subtotal {
            display: none;
        }
        .page-content .cart-collaterals table {
            border: none;
            font-family: "Quicksand";
        }
        .page-content .cart-collaterals table .Total {
            border: none;
        }
        .page-content .cart-collaterals .wc-proceed-to-checkout {
            padding: 0;
        }
        .page-content a.remove {
            font-size: 12px;
            font-weight: bold;
            color: #222;
            font-family: 'Quicksand';
            width: 100%;
            text-decoration: none;
        }
        .page-content .woocommerce-info {
            text-align: center;
            margin-bottom: 5px;
            font-family: 'Quicksand';
            font-size: 14px;
            font-weight: normal;
            font-weight: 500;
            padding-top: 5px;
        }
        .page-content .woocommerce-info::before {
            content: none;
        }
        .page-content .checkout_coupon.woocommerce-form-coupon {
            justify-content: center;
            align-items: center;
            transform: translateY(50px);
            transition: all 300ms ease;
            display: none;
            opacity: 1;
            visibility: visible;
        }
        .page-content .checkout_coupon.woocommerce-form-coupon.active {
            transform: translateY(0);
            transition: all 300ms ease;
            display: flex;
            opacity: 1;
            visibility: visible;
        }
        .page-content .checkout_coupon.woocommerce-form-coupon p:first-child {
            display: none;
        }
        .page-content .checkout_coupon.woocommerce-form-coupon input, .page-content .checkout_coupon.woocommerce-form-coupon button {
            height: 40px;
            padding-left: 10px;
            width: 100%;
            border: none;
        }
        .page-content .checkout_coupon.woocommerce-form-coupon .form-row.form-row-first {
            width: 70%;
        }
        .page-content .checkout_coupon.woocommerce-form-coupon .form-row.form-row-last {
            width: 30%;
        }
        .page-content .checkout_coupon.woocommerce-form-coupon button {
            background: #d22333;
            color: #fff;
            font-weight: bold;
            font-family: 'Quicksand';
            font-size: 14px;
            text-transform: uppercase;
            border: 0;
        }
        .page-content .checkout_coupon.woocommerce-form-coupon input {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            width: 90%;
        }
        .page-content .checkout_coupon.woocommerce-form-coupon button {
            cursor: pointer;
        }
        .page-content .checkout.woocommerce-checkout h3, .page-content .checkout.woocommerce-checkout label {
            display: none;
        }
        .page-content .checkout.woocommerce-checkout .woocommerce-input-wrapper label {
            display: block;
        }
        .page-content .checkout.woocommerce-checkout .col2-set {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-1 {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-1 .woocommerce-billing-fields {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-1 .woocommerce-billing-fields #sex_field {
            order: 0;
            margin: 10px 0;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-1 .woocommerce-billing-fields #sex_field .woocommerce-input-wrapper {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-1 .woocommerce-billing-fields #sex_field .woocommerce-input-wrapper input, .page-content .checkout.woocommerce-checkout .col2-set .col-1 .woocommerce-billing-fields #sex_field .woocommerce-input-wrapper label {
            margin-right: 5px;
            font-weight: bold;
            font-family: 'Quicksand';
            font-size: 14px;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-1 .woocommerce-billing-fields__field-wrapper {
            order: 1;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-1 .woocommerce-billing-fields__field-wrapper p {
            width: 48%;
            padding: 0;
            /*margin-right: 1%;*/
            margin: 0;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-1 .woocommerce-billing-fields__field-wrapper p:last-child {
            margin-right: 0;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-1 .woocommerce-billing-fields__field-wrapper p input {
            height: 36px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            width: 100%;
            text-indent: 10px;
            padding: 0;
            margin: 0;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-2 {
            width: 100%;
        }
        .page-content .checkout.woocommerce-checkout .col2-set .col-2 textarea {
            height: 80px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            width: 100%;
            text-indent: 10px;
            margin: 0;
            padding: 0;
        }
        .page-content .checkout.woocommerce-checkout table {
            border: none;
        }
        .page-content .checkout.woocommerce-checkout table thead, .page-content .checkout.woocommerce-checkout table tbody {
            display: none;
        }
        .page-content .checkout.woocommerce-checkout table tfoot {
            color: #333;
            font-weight: bold;
            font-family: 'Quicksand';
            font-size: 14px;
            text-transform: uppercase;
        }
        .page-content .checkout.woocommerce-checkout table tfoot .cart-subtotal {
            display: none;
        }
        .page-content .checkout.woocommerce-checkout table tfoot td, .page-content .checkout.woocommerce-checkout table tfoot th {
            border: none;
        }
        .page-content .checkout.woocommerce-checkout table tfoot td {
            text-align: right;
        }
        .page-content .woocommerce-terms-and-conditions-wrapper {
            display: none;
        }
        .page-content #payment label {
            display: block;
            cursor: pointer;
        }
        .page-content #payment .form-row.place-order {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .page-content #payment .form-row.place-order button {
            background: #d22333;
            color: #fff;
            font-weight: bold;
            font-family: 'Quicksand';
            font-size: 14px;
            text-transform: uppercase;
            border: 0;
            cursor: pointer;
            padding: 7px 25px;
            border-radius: 30px;
        }
        .page-content #payment .wc_payment_method {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            color: #333;
            font-weight: bold;
            font-family: 'Quicksand';
            font-size: 14px;
        }
        .page-content #payment .wc_payment_method div {
            display: none !important;
        }
        .cart-empty {
            text-align: center;
            font-family: "Quicksand";
            font-size: 18px;
            font-weight: normal;
            color: #333;
            margin: 0;
        }
        .return-to-shop {
            text-align: center;
            margin-bottom: 20px;
        }
        .return-to-shop a.button.wc-backward {
            background-color: #d22333;
            color: #fff;
            font-weight: bold;
            font-family: "Quicksand";
            font-size: 14px;
            text-align: center;
            padding: 7px 30px;
            border-radius: 4px;
            text-decoration: none;
        }
        .bg-header-thankyou img {
            width: 100%;
            height: 202px;
            object-fit: cover;
        }
        .woocommerce-order {
            position: relative;
            padding-top: 5px;
        }
        .woocommerce-order .woocommerce-notice.woocommerce-notice--success.woocommerce-thankyou-order-received {
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            color: #00af1d;
            font-weight: 600;
            text-transform: uppercase;
            font-family: "Quicksand";
        }
        .woocommerce-order .woocommerce-notice.woocommerce-notice--success.woocommerce-thankyou-order-received span {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .16);
            padding: 2px 20px;
        }
        .woocommerce-order .thank {
            margin-top: 10px;
            margin-bottom: 10px;
            color: #333;
            font-weight: normal;
            font-size: 16px;
            font-family: "Quicksand";
            line-height: 24px;
        }
        .woocommerce-order .titlebill {
            display: block;
            line-height: 30px;
            font-size: 14px;
            color: #333;
            background: #f3f3f3;
            text-transform: uppercase;
            padding-left: 10px;
            font-family: "Quicksand";
            font-weight: bold;
        }
        .woocommerce-order .woocommerce-order-overview.woocommerce-thankyou-order-details.order_details {
            margin: 0;
            padding: 0;
            padding-left: 10px;
            display: flex;
            flex-direction: column;
        }
        .woocommerce-order .woocommerce-order-overview.woocommerce-thankyou-order-details.order_details li {
            font-weight: normal;
            font-family: "Quicksand";
            font-size: 14px;
            display: flex;
            flex-direction: row;
            align-items: center;
            line-height: 30px;
            border-right: none;
        }
        .woocommerce-order .woocommerce-order-overview.woocommerce-thankyou-order-details.order_details li strong {
            font-size: 14px;
            margin-left: 5px;
        }
        table.shop_table tbody td {
            border: none !important;
        }
        table.shop_table tbody td span {
            font-family: "Quicksand";
            font-size: 14px;
            font-weight: normal;
        }
        table tfoot tr:not(:last-child) {
            display: none;
        }
        .woocommerce-table__product-name.product-name {
            display: flex;
            align-items: flex-start;
            font-weight: bold;
            font-family: "Quicksand";
            font-size: 14px;
            position: relative;
        }
        .woocommerce-table__line-item.order_item {
            position: relative;
        }
        .woocommerce-table__product-name.product-name img {
            max-width: 60px;
            height: auto;
            margin-right: 5px;
        }
        .woocommerce-table__product-name.product-name .product-quantity {
            width: 30px;
        }
        .wc_payment_methods.payment_methods.methods {
            padding: 0;
            margin: 0;
        }
        .woocommerce-table__product-name.product-name .wc-item-meta {
            list-style: none;
            padding: 0;
            margin: 0;
            position: absolute;
            left: 65px;
            top: 10px;
        }
        .woocommerce-table__product-name.product-name .wc-item-meta li {
            display: flex;
            align-items: center;
        }
        .woocommerce-table__product-name.product-name .wc-item-meta li p {
            margin-left: 5px;
        }
        .woocommerce-table__product-name.product-name a {
            font-weight: 500;
            font-family: "Quicksand";
            font-size: 14px;
            position: relative;
            color: #333;
            text-decoration: none;
        }
        .woocommerce-table.woocommerce-table--order-details.shop_table.order_details {
            border: 1px solid #e0e0e0;
            float: left;
            width: 100%;
            margin-top: 10px;
        }
        .woocommerce-table.woocommerce-table--order-details.shop_table.order_details tfoot {
            margin-top: 20px;
            display: block;
        }
        .woocommerce-table.woocommerce-table--order-details.shop_table.order_details tfoot tr:last-child {
            font-weight: bold;
            font-family: "Quicksand";
            font-size: 14px;
            position: relative;
            color: #333;
        }
        .woocommerce-table.woocommerce-table--order-details.shop_table.order_details tfoot tr:last-child td {
            color: #d22333;
        }
        .woocommerce-notices-wrapper {
            display: none;
        }
    </style>
    <?php
}

function wda_script_cart_checkout_page_woocommerce() {
    if ( is_cart() || is_checkout() ) { ?>
        <script>
            jQuery(document).ready(function($) {

                jQuery("body").on('click', '.woocommerce-info', function() {
                    console.log('aaaa');
                    jQuery(this).parent(".woocommerce-form-coupon-toggle").next(".woocommerce-form-coupon").toggleClass('active');
                });

                if ( ! String.prototype.getDecimals ) {
                    String.prototype.getDecimals = function() {
                        var num = this,
                            match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
                        if ( ! match ) {
                            return 0;
                        }
                        return Math.max( 0, ( match[1] ? match[1].length : 0 ) - ( match[2] ? +match[2] : 0 ) );
                    }
                }

                jQuery('div.woocommerce').on('click', '.quantity-button', function(){
                    jQuery("[name='update_cart']").trigger("click");
                });

                jQuery('.quantity').each(function() {
                    var spinner = jQuery(this),
                        input = spinner.find('input[type="number"]'),
                        btnUp = spinner.next('.quantity-nav').find('.quantity-up'),
                        btnDown = spinner.next('.quantity-nav').find('.quantity-down'),
                        min = parseFloat(input.attr('min')),
                        max = parseFloat(input.attr('max')),
                        step = input.attr( 'step' ),
                        currentVal = parseFloat(input.val()),
                        updatecart = jQuery('.shop_table.cart .actions > button');
                    if ( max === '' || max === 'NaN' ) max = '';
                    if ( min === '' || min === 'NaN' ) min = 0;
                    if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;
                    if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
                    btnUp.click(function() {
                        if ( max && ( currentVal >= max ) ) {
                            input.val( max );
                        } else {
                            input.val( ( currentVal + parseFloat( step )).toFixed( step.getDecimals() ) );
                        }
                        // spinner.find("input").val(newVal);
                        spinner.find("input").trigger("change");
                        updatecart.css('display', 'block');
                    });

                    btnDown.click(function() {
                        if ( min && ( currentVal <= min ) ) {
                            input.val( min );
                        } else if ( currentVal > 0 ) {
                            input.val( ( currentVal - parseFloat( step )).toFixed( step.getDecimals() ) );
                        }
                        // spinner.find("input").val(newVal);
                        spinner.find("input").trigger("change");
                        updatecart.css('display', 'block');
                    });
                });
            })
        </script>
    <?php }
}