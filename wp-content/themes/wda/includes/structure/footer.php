<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'wda_footer', 'wda_do_footer' );

function wda_do_footer() {
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
		get_template_part( 'template-parts/footer' );
	}
}

add_action( 'wda_after', 'wda_wrap_close', 5 );

function wda_wrap_close() {
	wda_markup(
		array(
			'close'    => '</div>',
			'context' => 'wda_wrap',
		)
	);
}

add_action( 'wda_after', 'wda_body_close', 15 );

function wda_body_close() {
	wda_markup(
		array(
			'close'    => '</div>',
			'context' => 'body_content',
		)
	);
}

add_action( 'wda_after', 'wda_scroll_top_top', 999 );

function wda_scroll_top_top() {
	printf('<a id="%s" title="%s"><span>%s</span></a>', esc_attr( "scrolltop" ), esc_attr( get_bloginfo( "title" ) ), esc_html( "To Top" ));
}