<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . ( $product->get_image_id() ? 'with-images' : 'without-images' ),
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
) );
$attachment_ids = $product->get_gallery_image_ids();
?>
    <div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?> flickity-for" data-flickity='{ "contain": true, "pageDots": false, "prevNextButtons": false }'>
        <figure class="woocommerce-product-gallery__wrapper item">
			<?php
			if ( $product->get_image_id() ) {
				?>
                <div data-thumb="http://localhost/vali/wp-content/uploads/2019/07/single-sanh.png" data-thumb-alt="" class="woocommerce-product-gallery__image">
                    <a data-fancybox="images" href="<?php echo wp_get_attachment_image_src($post_thumbnail_id, 'full')[0] ?>">
                        <img src="<?php echo wp_get_attachment_image_src($post_thumbnail_id, 'full')[0] ?>" class="wp-post-image" alt="">
                    </a>
                </div>
				<?php
			}
			?>
        </figure>
		<?php if ( !empty( $attachment_ids ) ): ?>
			<?php foreach ($attachment_ids as $attachment_id): ?>
                <div class="item">
                    <a data-fancybox="images" href="<?php echo wp_get_attachment_image_src($attachment_id, 'full')[0] ?>">
						<?php printf('<img src="%s" alt="%s">', wp_get_attachment_image_src($attachment_id, 'full')[0], get_bloginfo( 'name' )) ?>
                    </a>
                </div>
			<?php endforeach ?>
		<?php endif ?>
    </div>

<?php do_action( 'woocommerce_product_thumbnails' ); ?>