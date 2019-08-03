<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// get cats id on single
function wda_get_cats_id() {

	global $post;
	$cats              = array();
	$current_post_cats = get_the_category( $post->ID );
	foreach ( $current_post_cats as $key => $value ) {
		$cats[] = $value->term_id;
	};

	return $cats;
}