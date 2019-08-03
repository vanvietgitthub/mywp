<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action('template_redirect', 'wda_woocommerce_shop', 10);

function wda_woocommerce_shop() {
	if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {

		remove_all_actions( 'woocommerce_before_shop_loop' );
		// remove sidebar default
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
		// remove title shop
		add_filter( 'woocommerce_show_page_title', 'remove_title_page_shop_product_cat' );
		// remove number of filter
		add_filter( 'woocommerce_layered_nav_term_html', 'wda_custom_widget_filter_attributes_woocommerce', 10, 4 );
		// customize sort product desktop
		add_action( 'woocommerce_before_shop_loop', 'wda_woocommerce_catalog_ordering', 30 );
		// customize sort product mobile
		add_action( 'woocommerce_before_shop_loop', 'vms_woocommerce_catalog_ordering_mobile', 40 );
		// show title page shop
		add_action( 'woocommerce_before_main_content', 'wda_woocommerce_show_page_title', 35);
		// add new active filter sidebar
		add_action( 'woocommerce_before_shop_loop', 'vms_woocommerce_active_filter', 45 );
		// add new shop sidebar
		add_action( 'woocommerce_sidebar', 'area_widget_shop', 10 );
		// add class container
		add_action( 'woocommerce_before_main_content', 'wda_container_open', 4 );
		// </div> container
		add_action( 'woocommerce_sidebar', 'wda_container_close', 20 );
		// remove rating
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		// style shop
		add_action( 'wp_head', 'wda_style_shop_woocommerce', 999 );
		// script shop
		add_action( 'wp_footer', 'wda_script_shop_woocommerce', 999 );
		// add color to filter by color
		add_filter( 'woocommerce_layered_nav_term_html', 'wda_display_code_color_filter', 10, 4 );
	}
}

function wda_display_code_color_filter( $term_html, $term, $link, $count ){

	if ( $term->taxonomy === "pa_color" ) {
		$id = $term->term_id;
		$term_key = 'product_attribute_color';
		$color = get_term_meta( $id, $term_key);
		if ( !empty( $color ) ) {
		    $color = $color[0];
        }
		$filter_color_html = "";
		if ( !empty($color) ) {
			$filter_color_html .= '<span style="border: 1px solid #E0E0E0;width:24px;height:24px;float:left;border-radius:50%;display:block;background:'.$color.'"></span>';
		}
		return $filter_color_html;
	}
}

function area_widget_shop() {
	if ( is_active_sidebar( 'shop' ) ) : ?>
		<div id="sidebar-shop">
			<?php dynamic_sidebar( 'shop' ); ?>
		</div>
	<?php endif;
}

function vms_woocommerce_active_filter() {
	if ( is_active_sidebar( 'active-filter' ) ) : ?>
		<div id="active-filter">
			<?php dynamic_sidebar( 'active-filter' ); ?>
		</div>
	<?php endif;
}

function remove_title_page_shop_product_cat(){
	return false;
}

function wda_custom_widget_filter_attributes_woocommerce($term_html, $term, $link, $count) {
	$link_wrap = '<a rel="nofollow" href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a>';
	echo $link_wrap;
}

function wda_woocommerce_show_page_title() {
	?>
	<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title() ?></h1>
	<?php
}

function wda_do_filter_by_products() {

	global $wp;

	if ( ! is_shop() && ! is_product_taxonomy() ) {
		return;
	}

	if ( ! WC()->query->get_main_query()->post_count && ! isset( $_GET['min_price'] ) && ! isset( $_GET['max_price'] ) ) {
		return;
	}

	$prices    = get_filtered_price();
	$min_price = $prices->min_price;
	$max_price = $prices->max_price;

	$current_min_price = !empty($_GET['min_price']) ? sanitize_text_field($_GET['min_price']) : 1;
	$current_max_price = !empty($_GET['max_price']) ? sanitize_text_field($_GET['max_price']) : 1;

	if ( '' === get_option( 'permalink_structure' ) ) {
		$form_action = remove_query_arg( array( 'page', 'paged', 'product-page' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
	} else {
		$form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
	}

	return '<h2 class="widget-title-shop">Giá tiền</h2>
	<form id="'.esc_attr( 'wda_filter_by_prices' ).'" method="get" action="' . esc_url( $form_action ) . '">
	<div class="wda_filter_by_prices">
	<ul>
	<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
	<a href="javascript:;" min_price="' . esc_attr( $min_price ) . '" max_price="' . esc_attr( $max_price ) . '">Tất cả</a>
	</li>
	<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
	<a href="javascript:;" min_price="' . esc_attr( '1' ) . '" max_price="' . esc_attr( '10000000' ) . '">Dưới 10000000</a>
	</li>
	<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
	<a href="javascript:;" min_price="' . esc_attr( '10000000' ) . '" max_price="' . esc_attr( '15000000' ) . '">Từ 10000000 đến 15000000</a>
	</li>
	<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
	<a href="javascript:;" min_price="' . esc_attr( '15000000' ) . '" max_price="' . esc_attr( '20000000' ) . '">Từ 1500000 đến 20000000</a>
	</li>
	<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
	<a href="javascript:;" min_price="' . esc_attr( '20000000' ) . '" max_price="' . esc_attr( '50000000' ) . '">Từ 20000000 đến 50000000</a>
	</li>
	<li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term">
	<a href="javascript:;" min_price="' . esc_attr( '50000000' ) . '" max_price="' . esc_attr( '1' ) . '">Trên 50000000</a>
	</li>
	</ul>
	<input style="display:none" type="text" id="wda_min_price" name="min_price" value="' . esc_attr( $current_min_price ) . '" placeholder="' . esc_attr__( 'Min price', 'wda' ) . '" />
	<input style="display:none" type="text" id="wda_max_price" name="max_price" value="' . esc_attr( $current_max_price ) . '" placeholder="' . esc_attr__( 'Max price', 'wda' ) . '" />
	'. wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'paged' ), '', true ) . '
	</div>
	</form>
	';
}

function get_filtered_price() {
	global $wpdb;

	$args       = WC()->query->get_main_query()->query_vars;
	$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
	$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

	if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
		$tax_query[] = array(
			'taxonomy' => $args['taxonomy'],
			'terms'    => array( $args['term'] ),
			'field'    => 'slug',
		);
	}

	foreach ( $meta_query + $tax_query as $key => $query ) {
		if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
			unset( $meta_query[ $key ] );
		}
	}

	$meta_query = new WP_Meta_Query( $meta_query );
	$tax_query  = new WP_Tax_Query( $tax_query );
	$search     = WC_Query::get_main_search_query_sql();

	$meta_query_sql   = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
	$tax_query_sql    = $tax_query->get_sql( $wpdb->posts, 'ID' );
	$search_query_sql = $search ? ' AND ' . $search : '';

	$sql = "
		SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
		FROM {$wpdb->wc_product_meta_lookup}
		WHERE product_id IN (
		SELECT ID FROM {$wpdb->posts}
		" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
		WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
		AND {$wpdb->posts}.post_status = 'publish'
		" . $tax_query_sql['where'] . $meta_query_sql['where'] . $search_query_sql . '
	)';

	$sql = apply_filters( 'woocommerce_price_filter_sql', $sql, $meta_query_sql, $tax_query_sql );

	return $wpdb->get_row( $sql );
}

function get_current_page_url() {
	global $wp;
	return add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
}

function wda_woocommerce_catalog_ordering(){
	if ( is_shop() || is_product_category() || is_tag() || is_tax() ) {
		global $wp;
		$current_url = get_current_page_url();

		$tmp_url = explode($current_url, 'orderby');
		$link =  $tmp_url[0];
		$list_sorts = array(
			'date' => 'Mới',
			'title-asc' => 'Tên A - Z',
			'title-desc' => 'Tên Z - A',
			'price-asc' => 'Giá thấp đến cao',
			'price-desc' => 'Giá cao đến thấp',
		);

		echo '<div class="wda_sort_wrap"><div class="vms-title-sort" >Sắp xếp theo: </div><ul class="vms-sort-select" >';

		foreach($list_sorts as $key => $sort_label){
			$class_active = '';
			if(isset($_GET['orderby']) && $_GET['orderby'] == $key){
				$class_active = ' circle-radio--clicked';
			}
			echo '<li><a href="'. rtrim('?', $link) . '?orderby=' . $key .'">
			<span class="circle-radio '.$class_active.'">
			<span class="circle-radio--content"></span>
			</span>
			<span class="label-sort '.$class_active.'"> '. $sort_label .'</span>
			</a>
			</li>';

		}
		echo '</ul></div>';
	}
}

function vms_woocommerce_catalog_ordering_mobile(){
	$orderby = 'title-asc';
	if(isset($_GET['orderby']) && $_GET['orderby']){
		$orderby = $_GET['orderby'];
	}
	$show_default_orderby = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
	$catalog_orderby_options = array(
		'menu_order' => "Sắp xếp theo",
		'date' => 'Mới',
		'title-asc' => 'Tên A - Z',
		'title-desc' => 'Tên Z - A',
		'price-asc' => 'Giá thấp đến cao',
		'price-desc' => 'Giá cao đến thấp',
	);

	if ( ! $show_default_orderby ) {
		unset( $catalog_orderby_options['menu_order'] );
	}

	wc_get_template(
		'loop/orderby.php',
		array(
			'catalog_orderby_options' => $catalog_orderby_options,
			'orderby'                 => $orderby,
			'show_default_orderby'    => $show_default_orderby,
		)
	);
}

function wda_style_shop_woocommerce() {
?>
	<style id="shop-woocommerce">
		.woocommerce-products-header__title.page-title {
			font-style: normal;
			font-weight: bold;
			font-size: 18px;
			line-height: 22px;
			text-transform: uppercase;
			color: #333333;
			padding-left: 10px;
			margin: 0;
		}
		.container.shop.product-cat {
            max-width: 1170px;
            margin: 0 auto;
		}
		.container.shop.product-cat #primary {
			width: 75%;
			float: right;
		}
		.container.shop.product-cat #primary #main {
			display: flex;
			flex-direction: column;
		}
		.container.shop.product-cat #primary #main header {
			order: 2;
		}
		.container.shop.product-cat #primary ul.products {
			order: 1;
		}
		.woocommerce-pagination {
			order: 99;
		}
		.container.shop.product-cat #sidebar-shop {
			width: 25%;
			float: left;
		}
		.container.shop.product-cat::after{
             content: "";
             display: table;
             clear: both;
        }
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
		/*End product*/
        /*
        * Style sidebar
        */
		#sidebar-shop .widget {
			padding-right: 20px;
			margin-bottom: 25px;
		}
        /*title*/
		.widget-title-shop {
			font-weight: bold;
			font-size: 18px;
			line-height: 22px;
			text-transform: uppercase;
			color: #333333;
			margin: 0;
			margin-bottom: 15px;
		}
        /*categories product*/
		.product-categories {
			list-style: none;
			padding: 0;
			margin: 0;
			border: 1px solid #E0E0E0;
			padding: 15px;
		}
		.product-categories > li > a {
			font-weight: normal;
			font-size: 14px;
			line-height: 17px;
			text-transform: uppercase;
			color: #333;
		}
		.product-categories > li {
			position: relative;
			padding-bottom: 13px;
			margin-bottom: 17px;
			border-bottom: 1px solid #e0e0e0;
		}
		.product-categories > li:last-child {
			padding-bottom: 0;
			margin-bottom: 0;
			border-bottom: 0;
		}
		.product-categories li a {
			text-decoration: none;
		}
		.product-categories > li::after {
			content: "+";
			position: absolute;
			top: -2px;
			right: 10px;
			font-weight: normal;
			font-size: 16px;
			line-height: 20px;
			text-transform: uppercase;
			color: #333333;
		}
		.product-categories > li.current-cat a,
		.product-categories > li.current-cat-parent a,
		.product-categories > li.current-cat-parent .current-cat a {
			color: #D22333;
			font-weight: bold;
		}
		.product-categories > li.current-cat::after,
		.product-categories > li.current-cat-parent::after {
			content: "-";
			font-weight: bold;
			color: #D22333;
		}
		.product-categories .cat-parent .children {
			list-style: none;
			padding: 0;
			margin: 0;
			margin-top: 16px;
			padding-left: 30px;
		}
		.product-categories .cat-parent .children li {
			margin-bottom: 17px;
		}
		.product-categories .cat-parent .children li:last-child {
			margin-bottom: 0;
		}
		.product-categories .cat-parent .children a {
			font-weight: normal;
			font-size: 14px;
			line-height: 17px;
			color: #333333;
		}
		/*End categories*/
        /*Style filter*/
		.woocommerce-widget-layered-nav-list {
			list-style: none;
			padding: 0;
			margin: 0;
			border: 1px solid #E0E0E0;
			padding: 18px;
		}
		.woocommerce-widget-layered-nav-list li {
			margin-bottom: 12px;
		}
		.woocommerce-widget-layered-nav-list li:last-child {
			margin-bottom: 0;
		}
		.woocommerce-widget-layered-nav-list li a {
			color: #555555;
			position: relative;
			text-decoration: none;
			padding-left: 25px;
		}
		.woocommerce-widget-layered-nav-list li a::before {
			content: '';
			width: 16px;
			height: 16px;
			background-image: url("<?php echo esc_url( trailingslashit( get_template_directory_uri() ) . "assets/images/no-check.png" ) ?>");
			background-size: 100%;
			background-repeat: no-repeat;
			position: absolute;
			left: 0;
			top: -1px;
		}
        #woocommerce_layered_nav-2 .woocommerce-widget-layered-nav-list {
            display: flex;
        }
        #woocommerce_layered_nav-2 .woocommerce-widget-layered-nav-list li {
            display: inline-block;float: left;margin-bottom: 0;margin-right: 8px;
        }
        #woocommerce_layered_nav-2 .woocommerce-widget-layered-nav-list li a {
            text-indent: -999em;
            float: left;
            padding-left: 0;
            display: block;
            width: 25px;
            height: 25px;
            position: absolute;
        }
        #woocommerce_layered_nav-2 .woocommerce-widget-layered-nav-list li a::before {
            background-image: unset;
        }
        ul li.wc-layered-nav-term.woocommerce-widget-layered-nav-list__item--chosen a::before {
            background-image: url("<?php echo esc_url( trailingslashit( get_template_directory_uri() ) . "assets/images/checked.png" ) ?>");
        }
        #woocommerce_layered_nav-2 ul li.wc-layered-nav-term.woocommerce-widget-layered-nav-list__item--chosen a::before {
            background-image: url("<?php echo esc_url( trailingslashit( get_template_directory_uri() ) . "assets/images/tick_1.png" ) ?>");
            width: 12px;
            height: 12px;
            left: 7px;
            top: 7px;
        }
        #woocommerce_layered_nav-2 ul li.wc-layered-nav-term.woocommerce-widget-layered-nav-list__item--chosen span {
            border-color: #D22333 !important;
        }
		ul li.wc-layered-nav-term.woocommerce-widget-layered-nav-list__item--chosen a::before {
			background-image: url("<?php echo esc_url( trailingslashit( get_template_directory_uri() ) . "assets/images/checked.png" ) ?>");
		}
		.wda_filter_by_prices ul {
			list-style: none;
			padding: 0;
			margin: 0;
			border: 1px solid #E0E0E0;
			padding: 15px;
		}
		.wda_filter_by_prices ul li {
			margin-bottom: 12px;
		}
		.wda_filter_by_prices ul li a {
			font: 400 14px/16px Quicksand;
			color: #555555;
			position: relative;
			padding-left: 25px;
			text-decoration: none;
		}
		.wda_filter_by_prices ul li a::before {
			content: '';
			width: 16px;
			height: 16px;
			background-image: url("<?php echo esc_url( trailingslashit( get_template_directory_uri() ) . "assets/images/no-check.png" ) ?>");
			background-size: 100%;
			background-repeat: no-repeat;
			position: absolute;
			left: 0;
			top: -1px;
		}
		.wda_filter_by_prices ul li.wc-layered-nav-term.woocommerce-widget-layered-nav-list__item--chosen a::before {
			background-image: url("<?php echo esc_url( trailingslashit( get_template_directory_uri() ) . "assets/images/checked.png" ) ?>");
		}
		/*end filter attribute*/
		.wda_sort_wrap {
			padding-left: 10px;
			border-bottom: 1px solid #e0e0e0;
			padding-bottom: 15px;
			margin-bottom: 15px;
		}
		.archive .woocommerce-products-header .woocommerce-products-header__title{
			margin-bottom: 10px;
		}
		.vms-title-sort{
			font-size: 14px;
			line-height: 16px;
			color: #333;
			display: inline-block;
			font-weight: bold;
			padding-right: 20px;
		}
		ul.vms-sort-select{
			display: inline-block;
			padding-left: 0;
			margin: 0;
		}
		ul.vms-sort-select li{
			list-style-type: none;
			padding: 0  20px 0 0;
			font-size: 14px;
			line-height: 32px;
			color: #555;
			display: inline-block;
			color: #333333;
			text-decoration: none;
		}
		ul.vms-sort-select li a {
			text-decoration: none;
			color: #333333;
		}
		.vms-sort-select .title-pricefilter{
			font-size: 18px;
			font-weight: bold;
			line-height: 21px;
			padding-bottom: 17px;
			text-transform: uppercase;
			border-bottom: 1px solid #222222;
			margin: 0
		}
		.circle-radio {
			border: 1px solid #e0e0e0;
			box-sizing: border-box;
			border-radius: 8px;
			margin-bottom: -2px;
			width: 16px;
			height: 16px;
			position: relative;
			display: inline-block
		}
		ul.vms-sort-select li:hover .circle-radio,
		.circle-radio.circle-radio--clicked{
			border: 1px solid #D22333;
			background: none;
		}
		.vms-sort-select a:hover,
		.vms-sort-select .label-sort.circle-radio--clicked{
			color: #D22333;
		}
		ul.vms-sort-select li:hover .circle-radio--content,
		.circle-radio--clicked .circle-radio--content{
			margin: auto;
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			background-color: #D22333;
			width: 8px;
			height: 8px;
			border-radius: 4px;
		}
		.sidebar .widget_media_image img{
			width: 100%;
		}
		.woocommerce-ordering {
			display: none;
		}
		@media screen and (max-width: 767px){
			.woocommerce-ordering {
				display: block;
			}
			ul.products:not(.list) .actions .button{
				margin: 0 5px 5px 5px;
			}
			ul.products:not(.list) li .insider {
				padding: 0;
			}
		}
        .wda_sort_filters_mobile {
            display: none;
        }
		/* End sort archive */
		#active-filter {
			margin-bottom: 40px;
		}
		#active-filter ul {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		#active-filter ul li {
			display: inline-block;
			background: #F9B42E;
			border-radius: 4px;
			padding: 5px 32px 5px 10px;
			text-align: left;
			margin-right: 10px;
			position: relative;
			cursor: pointer;
		}
        #active-filter ul li::after {
            content: "";
            position: absolute;
            top: 11px;
            right: 10px;
            height: 7px;
            width: 12px;
            background-position: 0px 40px;
            padding-top: 0;
            background-image: url("<?php echo esc_url( trailingslashit( get_template_directory_uri() ) . "assets/images/sprite.png" ) ?>");
        }
		#active-filter ul li a {
			text-align: left;
			text-decoration: none;
			font-weight: normal;
			font-size: 14px;
			line-height: 17px;
			color: #333333;
		}
        /*End active filter*/
		#custom_html-2 .custom-html-widget {
			display: flex;
			justify-content: center;
		}
		#custom_html-2 .btn-cancel {
			text-align: center;
			width: 140px;
			padding: 10px;
		}
		.btn-cancel {
			background: #FFFFFF;
			border-radius: 4px;
			border: 1px solid #D22333;
			font-style: normal;
			font-weight: bold;
			font-size: 14px;
			line-height: 17px;
			text-align: center;
			text-transform: uppercase;
			color: #D22333;
			display: none;
		}
		/*End sidebar*/
		@media (max-width: 1199px) {
			.container.shop.product-cat, #breadcrumbs {
				padding: 0 20px;
			}
		}
		@media (max-width: 991px) {
			.container.shop.product-cat #primary,
			.container.shop.product-cat #sidebar-shop {
				width: 100%;
			}
		}
		@media (max-width: 767px) {
			.btn-cancel {
				display: block;
			}
			.container.shop.product-cat #primary ul.products {
				grid-template-columns: repeat(2,1fr);
				grid-column-gap: 20px;
				grid-row-gap: 20px;
			}
			#breadcrumbs,
			.woocommerce-products-header__title.page-title {
				margin-bottom: 10px;
			}
			.woocommerce-products-header__title.page-title,
			.wda_sort_wrap {
				padding-left: 0;
			}
			.wda_sort_wrap {
				display: none;
			}
			.woocommerce-ordering select {
				border: 1px solid #e0e0e0;
				height: 40px;
				padding: 0 10px;
				width: 100%;
			}
			.archive.woocommerce ul.products:not(.list){
				grid-column-gap: 20px;
				grid-row-gap: 20px;
				grid-template-columns: repeat(2,1fr);
			}
			.wda_sort_filters_mobile {
				float: left;
				width: 100%;
				display: block;
				/*display: flex;
				justify-content: space-between;*/
			}
			.wda_sort_filters_mobile::after {
				content: "";
				display: table;
				clear: both;
			}
			.woocommerce-ordering {
				width: 240px;
				float: left;
			}
			.btn-filter-mobile {
				height: 40px;
				float: right;
				border: 1px solid #e0e0e0;
				background: #ffffff;
				cursor: pointer;
			}
			.btn-filter-mobile .icon_wda {
				float: right;
				margin-left: 5px;
			}
			#sidebar-shop {
				width: 300px;
				background: #fff;
				z-index: 1;
				top: 0;
				left: 0;
				padding: 15px 0px;
				display: none;
				height: 100%;
				position: absolute;
				z-index: 1;
				display: none;
			}/*
				.sidebar-shop-mobile #sidebar-shop {
					display: block;
				}*/
			#sidebar-shop .widget {
				padding: 0 15px;
			}
			.widget-title-shop {
				font-size: 16px;
				line-height: 20px;
			}
		}
		/*End responsive*/
		/*End archive product*/
	</style>
<?php
}

function wda_script_shop_woocommerce() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            var wrapFilter = jQuery('.wda_filter_by_prices');
            var linkClick = wrapFilter.find('a');
            var min = jQuery('#wda_min_price');min.css('display', 'none');
            var max = jQuery('#wda_max_price');max.css('display', 'none');
            var bodyForm = jQuery("body form#wda_filter_by_prices");
            function filterSubmitForm() {
                if (!bodyForm) {return}
                var form = jQuery('#wda_filter_by_prices');
                linkClick.on('click', function() {
                    var parentThis = jQuery(this).parent('.wc-layered-nav-term');
                    var min_price, max_price;
                    min_price = jQuery(this).attr('min_price');
                    max_price = jQuery(this).attr('max_price');
                    if ( typeof(min_price) === 'string' && min_price !== undefined ) {
                        min.val(min_price);
                        sessionStorage.minPrice = min_price;
                    }else {
                        min.val('1');
                        sessionStorage.minPrice = '1';
                    }
                    if ( typeof(max_price) === 'string' && max_price !== undefined ) {
                        max.val(max_price);
                        sessionStorage.maxPrice = max_price;
                    }else {
                        max.val('1');
                        sessionStorage.maxPrice = '1';
                    }
                    form.submit();
                })
            }

            filterSubmitForm();

            function activeLi() {
                var minPrice = sessionStorage.minPrice;
                var maxPrice = sessionStorage.maxPrice;
                linkClick.each(function(i)
                {
                    if ( min.val() === jQuery(this).attr('min_price') && max.val() === jQuery(this).attr('max_price') ) {
                        var parentThis = jQuery(this).parent('.wc-layered-nav-term');
                        parentThis.addClass('woocommerce-widget-layered-nav-list__item--chosen');
                    }
                });
            }

            activeLi();
        });
    </script>
    <?php
}