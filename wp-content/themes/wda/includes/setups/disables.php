<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// remove size default of wordpress
add_action('init', 'wda_remove_image_sizes');

function wda_remove_image_sizes() {
	foreach ( get_intermediate_image_sizes() as $size ) {
		remove_image_size( $size );
	}
}

// remove size default of wordpress
add_filter( 'intermediate_image_sizes_advanced', 'wda_remove_default_images' );
function wda_remove_default_images( $sizes ) {
	unset( $sizes['small']);
	unset( $sizes['medium']);
	unset( $sizes['large']);
	unset( $sizes['medium_large']);
	return $sizes;
}

// remove block of new wordpress version
add_action( 'wp_print_styles', 'wda_deregister_styles_block_wordpress', 100 );

function wda_deregister_styles_block_wordpress() {
	wp_dequeue_style( 'wp-block-library' );
	wp_deregister_style( 'wc-block-style' );
}

// remove version of wordpress
add_filter('the_generator', 'wda_remove_version');

function wda_remove_version() {
	return '';
}

// remove login errors of wordpress
add_filter( 'login_errors', 'wda_wordpress_errors' );
remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
function wda_wordpress_errors(){
	return 'Đã có lỗi xảy ra, vui lòng thử lại!';
}


// remove field website of comment template
add_filter( 'comment_form_default_fields', 'wda_website_remove' );

function wda_website_remove( $fields ) {

	if ( isset( $fields['url'] ) ) {
		unset( $fields['url'] );
	}

	return $fields;
}

// wraning index website
add_action( 'admin_notices', function () {

	if ( 0 != get_option( 'blog_public' ) ) {
		return;
	}
	?>
	<div class="notice notice-warning">
		<p><b>QUAN TRỌNG:</b> Website của bạn đang được chặn bot để chỉnh sửa. Sau khi hoàn thành, hãy mở bot để website
			của bạn được Google index <a href="<?php echo admin_url( 'options-reading.php' ); ?>">tại đây</a>.</p>
		</div>
		<?php
	});

// remove emoji
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// remove jetpack
add_filter( 'jetpack_sharing_counts', '__return_false', 99 );
add_filter( 'jetpack_implode_frontend_css', '__return_false', 99 );

// disable xmlrpc
add_filter( 'xmlrpc_enabled', '__return_false' );

// disable RSS
function wda_wpb_disable_feed() {
	wp_die( __('Tính năng RSS Feed đã bị vô hiệu hóa. Vui lòng truy cập <a href="'. get_bloginfo('url') .'">trang chủ</a> để xem danh sách bài viết!') );
}
add_action('do_feed', 'wda_wpb_disable_feed', 1);
add_action('do_feed_rdf', 'wda_wpb_disable_feed', 1);
add_action('do_feed_rss', 'wda_wpb_disable_feed', 1);
add_action('do_feed_rss2', 'wda_wpb_disable_feed', 1);
add_action('do_feed_atom', 'wda_wpb_disable_feed', 1);
add_action('do_feed_rss2_comments', 'wda_wpb_disable_feed', 1);
add_action('do_feed_atom_comments', 'wda_wpb_disable_feed', 1);
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );

// remove embed
add_action('init', 'wda_stop_loading_wp_embed');
function wda_stop_loading_wp_embed() {
	if (!is_admin()) {
		wp_deregister_script('wp-embed');
	}
}

// disable rest api
remove_action( 'rest_api_init', 'wp_oembed_register_route' );

// disable other
add_filter( 'embed_oembed_discover', '__return_false' );
remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head, 10, 0');

// dont show admin bar
add_filter('show_admin_bar', '__return_false');

// remove query string
function wda_remove_cssjs_ver( $src ) {
	if( strpos( $src, '?ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}
add_filter( 'style_loader_src', 'wda_remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'wda_remove_cssjs_ver', 10, 2 );

add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');
remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );