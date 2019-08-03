<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'wda_doctype', 'wda_do_doctype' );

function wda_do_doctype() {
?>
	<!DOCTYPE html>
	<html <?php language_attributes( 'html' ); ?>>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<head <?php echo wda_attr( 'head' ); ?>>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php
}

add_action( 'wda_meta', 'wda_seo_meta_description' );

function wda_seo_meta_description() {

	$description = get_bloginfo( 'title' );

	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	}
}

add_action( 'wda_meta', 'wda_seo_meta_keywords' );

function wda_seo_meta_keywords() {

	$keywords = get_bloginfo( 'title' );

	if ( $keywords ) {
		echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '" />' . "\n";
	}
}

add_action( 'wda_meta', 'wda_robots_meta' );

function wda_robots_meta() {

	if ( ! get_option( 'blog_public' ) ) {
		return;
	}

	$meta = get_bloginfo( 'title' );

	if ( $meta ) {
		?>
		<meta name="robots" content="<?php echo esc_attr( $meta ); ?>" />
		<?php
	}
}

add_action( 'wda_meta', 'wda_responsive_viewport' );

function wda_responsive_viewport() {

	$viewport_value = apply_filters( 'wda_viewport_value', 'width=device-width, initial-scale=1' );

	$viewport_value .= ',minimum-scale=1';

	printf(
		'<meta name="viewport" content="%s" />' . "\n",
		esc_attr( $viewport_value )
	);

}

add_action( 'wp_head', 'wda_load_favicon' );

function wda_load_favicon() {

	$favicon = esc_url( trailingslashit( get_template_directory_uri() ) . 'favicon.ico' );

	if ( $favicon ) {
		echo '<link rel="icon" href="' . esc_url( $favicon ) . '" />' . "\n";
	}

}

add_action( 'wp_head', 'wda_meta_name' );

function wda_meta_name() {

	if ( ! is_front_page() ) {
		return;
	}

	printf( '<meta itemprop="name" content="%s" />' . "\n", esc_html( get_bloginfo( 'name' ) ) );

}

add_action( 'wda_before', 'wda_wrap_open', 5 );

function wda_wrap_open() {
	wda_markup(
		array(
			'open'    => '<div %s>',
			'context' => 'wda_wrap',
		)
	);
}

add_action( 'wda_before', 'wda_menu_mobile', 10 );

function wda_menu_mobile() {
	$detect = new Mobile_Detect();
	if( $detect->isMobile() && !$detect->isTablet() ) { 
		printf('<div class="%s">', 'nav menu-mobile');
			printf('<h3>%s</h3>', esc_attr__( "Menu", TextDomain ));
			wp_nav_menu( array(
				'theme_location'  => 'menu-2',
				'menu'            => '',
				'container'       => false,
			) );
			printf('<button id="%s"><i class="%s"></i> %s</button>', esc_attr( "close-mobile-menu" ), esc_attr( "icon_wda icon_wda_close" ), esc_attr__( "Close Menu", TextDomain ));
		echo "</div>";
	}
}

add_action( 'wda_before', 'wda_body_open', 15 );

function wda_body_open() {
	wda_markup(
		array(
			'open'    => '<div %s>',
			'context' => 'body_content',
		)
	);
}

add_action( 'wda_header', 'wda_do_header' );

function wda_do_header() {
	$detect = new Mobile_Detect();
	if( $detect->isMobile() && !$detect->isTablet() ) {
		wda_markup(
			array(
				'open'    => '<div %s>',
				'context' => 'header-mobile',
			)
		);

			wda_markup(
				array(
					'open'    => '<div %s>',
					'context' => 'logo',
				)
			);
				wda_logo();
			wda_markup(
				array(
					'close'    => '</div>',
					'context' => 'logo'
				)
			);
			// End logo --------------- //
			wda_markup(
				array(
					'open'    => '<div %s>',
					'context' => 'wda_toggle_menu',
				)
			);
				wda_markup(
					array(
						'open'    => '<div %s>',
						'context' => 'search-mobile',
					)
				);
					wda_markup(
						array(
							'open'    => '<div %s>',
							'context' => 'btn-search-mobile',
						)
					);
						printf('<i class="%s"></i>', esc_attr( 'icon_wda icon_wda_search' ));
					wda_markup(
						array(
							'close'    => '</div>',
							'context' => 'btn-search-mobile',
						)
					);
					get_search_form( true );
				wda_markup(
					array(
						'close'    => '</div>',
						'context' => 'search-mobile',
					)
				);
				wda_markup(
					array(
						'open'    => '<span %s>',
						'context' => 'tray',
					)
				);
				wda_markup(
					array(
						'close'    => '</span>',
						'context' => 'tray',
					)
				);
				// End tray --------------- //
			wda_markup(
				array(
					'close'    => '</div>',
					'context' => 'wda_toggle_menu',
				)
			);
			// End wda_toggle_menu --------------- //
		wda_markup(
			array(
				'close'    => '</div>',
				'context' => 'header-mobile',
			)
		);
		// End header-mobile --------------- //
	}else {
		if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
			get_template_part( 'template-parts/header' );
		}
	}
}