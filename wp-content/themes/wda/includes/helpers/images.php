<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Return src
function wda_resize( $attachment, $width = false, $height = false, $crop = false ) {

	$attachment_info = wp_get_attachment_metadata( $attachment );

	if ( ! $attachment_info ) {
		return new WP_Error( 'invalid_attachment', 'Invalid Attachment', $attachment );
	}

	$upload    = wp_upload_dir();
	$file_path = $upload['basedir'] . '/' . $attachment_info['file'];
	$info      = pathinfo( $file_path );
	$dir       = $info['dirname'];
	$ext       = ( isset( $info['extension'] ) ) ? $info['extension'] : 'jpg';
	$name      = wp_basename( $file_path, ".$ext" );
	$name      = preg_replace( '/(.+)(\-\d+x\d+)$/', '$1', $name );

	if ( ! $width || ! $height ) {
		$editor = wp_get_image_editor( $file_path );

		if ( is_wp_error( $editor ) ) {
			return $editor;
		}

		$size        = $editor->get_size();
		$orig_width  = $size['width'];
		$orig_height = $size['height'];
		if ( ! $height && $width ) {
			$height = round( ( $orig_height * $width ) / $orig_width );
		} elseif ( ! $width && $height ) {
			$width = round( ( $orig_width * $height ) / $orig_height );
		} else {
			return $attachment;
		}
	}

	// Suffix applied to filename.
	$suffix = "{$width}x{$height}";

	// Get the destination file name.
	$destination_file_name = "{$dir}/{$name}-{$suffix}.{$ext}";

	// No need to resize & create a new image if it already exists.
	if ( ! file_exists( $destination_file_name ) ) {
		// Image Resize.
		$editor = ( isset( $editor ) ) ? $editor : wp_get_image_editor( $file_path );

		if ( is_wp_error( $editor ) ) {
			return new WP_Error( 'wp_image_editor', 'WP Image editor can\'t resize this attachment', $attachment );
		}

		// Get the original image size.
		$size        = $editor->get_size();
		$orig_width  = $size['width'];
		$orig_height = $size['height'];

		$src_x = 0;
		$src_y = 0;
		$src_w = $orig_width;
		$src_h = $orig_height;

		if ( $crop ) {

			$cmp_x = $orig_width / $width;
			$cmp_y = $orig_height / $height;

			if ( $cmp_x > $cmp_y ) {
				$src_w = round( $orig_width / $cmp_x * $cmp_y );
				$src_x = round( ( $orig_width - ( $orig_width / $cmp_x * $cmp_y ) ) / 2 );
			} elseif ( $cmp_y > $cmp_x ) {
				$src_h = round( $orig_height / $cmp_y * $cmp_x );
				$src_y = round( ( $orig_height - ( $orig_height / $cmp_y * $cmp_x ) ) / 2 );
			}
		}

		$editor->crop( $src_x, $src_y, $src_w, $src_h, $width, $height );

		$saved = $editor->save( $destination_file_name );

		$images = wp_get_attachment_metadata( $attachment );
		if ( ! empty( $images['resizes'] ) && is_array( $images['resizes'] ) ) {
			foreach ( $images['resizes'] as $image_size => $image_path ) {
				$images['resizes'][ $image_size ] = addslashes( $image_path );
			}
		}
		$uploads_dir                  = wp_upload_dir();
		$images['resizes'][ $suffix ] = $uploads_dir['subdir'] . '/' . $saved['file'];
		wp_update_attachment_metadata( $attachment, $images );
	}

	return str_replace( basename( wp_get_attachment_url( $attachment ) ), basename( $destination_file_name ), wp_get_attachment_url( $attachment ) );
}

// return img
function wda_get_img( $attachment_id, $width, $height, $attr = array(), $crop = true ) {

	$image = '<img src="' . wda_resize( $attachment_id, $width, $height, $crop ) . '" ';
	foreach ( $attr as $name => $value ) {
		$image .= " $name=\"$value\" ";
	}
	$image .= ' />';

	return $image;
}

// return src
function wda_get_image_url( $attachment_id, $width, $height, $crop = true ) {

	return wda_resize( $attachment_id, $width, $height, $crop );
}

// return img thumbnail
function wda_get_post_thumbnail( $post_id, $width, $height, $attr = array(), $crop = true ) {

	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	if (has_post_thumbnail( $post )) {
		return;
	}

	$post_thumbnail_id = get_post_thumbnail_id( $post );

	return wda_get_img( $post_thumbnail_id, $width, $height, $attr, $crop );
}

// return src thumbnail 
function wda_get_post_thumbnail_url( $post_id, $width, $height, $crop = true ) {

	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	if (has_post_thumbnail( $post )) {
		return;
	}
	$post_thumbnail_id = get_post_thumbnail_id( $post );

	return wda_resize( $post_thumbnail_id, $width, $height, $crop );
}

// upload image webp
add_filter( 'wp_check_filetype_and_ext', 'wda_file_and_ext_webp', 10, 4 );
function wda_file_and_ext_webp( $types, $file, $filename, $mimes ) {
	if ( false !== strpos( $filename, '.webp' ) ) {
		$types['ext'] = 'webp';
		$types['type'] = 'image/webp';
	}

	return $types;
}

add_filter( 'upload_mimes', 'wda_mime_types_webp' );
function wda_mime_types_webp( $mimes ) {
	$mimes['webp'] = 'image/webp';

	return $mimes;
}

// get id
function wda_get_image_id( $index = 0, $post_id = null ) {

	$image_ids = array_keys(
		get_children(
			array(
				'post_parent'    => $post_id ? $post_id : get_the_ID(),
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		)
	);

	if ( isset( $image_ids[ $index ] ) ) {
		return $image_ids[ $index ];
	}

	return false;

}