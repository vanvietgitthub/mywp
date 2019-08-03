<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function wda_truncate_phrase( $text, $max_characters ) {

	if ( ! $max_characters ) {
		return '';
	}

	$text = trim( $text );

	if ( mb_strlen( $text ) > $max_characters ) {

		$text = mb_substr( $text, 0, $max_characters + 1 );

		$text_trim = trim( mb_substr( $text, 0, mb_strrpos( $text, ' ' ) ) );

		$text = empty( $text_trim ) ? $text : $text_trim;

	}

	return $text;
}

function get_the_content_limit( $max_characters, $more_link_text = '(more...)', $stripteaser = false ) {

	$content = get_the_content( '', $stripteaser );

	$content = strip_tags( strip_shortcodes( $content ), apply_filters( 'get_the_content_limit_allowedtags', '<script>,<style>' ) );

	$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

	$content = wda_truncate_phrase( $content, $max_characters );

	if ( $more_link_text ) {
		$link   = apply_filters( 'get_the_content_more_link', sprintf( '&#x02026; <a href="%s" class="more-link">%s</a>', get_permalink(), $more_link_text ), $more_link_text );
		$output = sprintf( '<p>%s %s</p>', $content, $link );
	} else {
		$output = sprintf( '<p>%s</p>', $content );
		$link   = '';
	}

	return apply_filters( 'get_the_content_limit', $output, $content, $link, $max_characters );

}

function the_content_limit( $max_characters, $more_link_text = '(more...)', $stripteaser = false ) {

	$content = get_the_content_limit( $max_characters, $more_link_text, $stripteaser );
	echo apply_filters( 'the_content_limit', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}

function wda_formatting_allowedtags() {

	return apply_filters(
		'wda_formatting_allowedtags',
		array(
			'a'          => array(
				'href'  => array(),
				'title' => array(),
			),
			'b'          => array(),
			'blockquote' => array(),
			'br'         => array(),
			'div'        => array(
				'align' => array(),
				'class' => array(),
				'style' => array(),
			),
			'em'         => array(),
			'i'          => array(),
			'p'          => array(
				'align' => array(),
				'class' => array(),
				'style' => array(),
			),
			'span'       => array(
				'align' => array(),
				'class' => array(),
				'style' => array(),
			),
			'strong'     => array(),
		)
	);

}

function wda_formatting_kses( $string ) {

	return wp_kses( $string, wda_formatting_allowedtags() );

}

function wda_strip_p_tags( $content ) {

	return preg_replace( '/<p\b[^>]*>(.*?)<\/p>/i', '$1', $content );

}

function wda_container_open() {
	printf('<div class="%s">', esc_attr( 'container shop product-cat' ));
}

function wda_container_close() {
	echo "</div>";
}

function wda_breadcrumb() {
	?>
	<div class="wda_breadcrumb">
		<?php
		if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
		}
		?>
	</div>
	<?php
}

function wda_logo() {
	$site_name = get_bloginfo( 'name' );
	$tagline   = get_bloginfo( 'description', 'display' );
	if ( has_custom_logo() ) {
		the_custom_logo();
	} elseif ( $site_name ) {
		wda_markup(
			array(
				'open'    => '<h1 %s>',
				'context' => 'site-title',
			)
		);
			printf('<a href="%s" title="%s" rel ="%s">%s</a>', esc_url( home_url( '/' )), esc_attr_e( 'Home', TextDomain ), esc_attr__( 'home', TextDomain ), esc_html( $site_name )); 
		wda_markup(
			array(
				'close'    => '</h1>',
				'context' => 'site-title',
			)
		);
		wda_markup(
			array(
				'open'    => '<p %s>',
				'context' => 'site-description',
			)
		);
			if ( $tagline ) {
				echo esc_html( $tagline );
			}
		wda_markup(
			array(
				'close'    => '</p>',
				'context' => 'site-description',
			)
		); 
	}
}