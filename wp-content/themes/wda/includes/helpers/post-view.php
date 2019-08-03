<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function wda_get_post_views( $postID ) {

	$count_key = 'post_views_count';
	$count     = get_post_meta( $postID, $count_key, true );
	if ( $count == '' ) {
		delete_post_meta( $postID, $count_key );
		add_post_meta( $postID, $count_key, '0' );

		return 0;
	}

	return $count;
}

function wda_set_post_views() {

	$postID    = get_the_ID();
	$count_key = 'post_views_count';
	$count     = get_post_meta( $postID, $count_key, true );
	if ( $count == '' ) {
		$count = 0;
		delete_post_meta( $postID, $count_key );
		add_post_meta( $postID, $count_key, '0' );
	} else {
		$count ++;
		update_post_meta( $postID, $count_key, $count );
	}
}

add_filter( 'manage_posts_columns', 'wda_admin_posts_column_views' );

function wda_admin_posts_column_views( $defaults ) {

	$defaults['post_views'] = __( 'Views', 'wda' );

	return $defaults;
}

add_action( 'manage_posts_custom_column', 'wda_admin_posts_custom_column_views', 5, 2 );

function wda_admin_posts_custom_column_views( $column_name, $id ) {

	if ( $column_name === 'post_views' ) {
		echo wda_get_post_views( get_the_ID() );
	}
}

add_action( 'template_redirect', 'wda_post_views_counter_hooks' );

function wda_post_views_counter_hooks() {

	if ( is_single() || is_page() ) {
		add_action( 'wp_head', 'wda_set_post_views' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	}
}