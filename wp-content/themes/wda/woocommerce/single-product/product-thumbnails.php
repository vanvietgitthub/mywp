<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$attachment_ids = $product->get_gallery_image_ids();
?>
<div class="flickity-nav" data-flickity='{ "asNavFor": ".flickity-for", "contain": true, "pageDots": false, "lazyLoad": true, "imagesLoaded": true }'>

	<?php if ( !empty( $product->get_image_id() ) ): ?>
		<div class="item">
			<?php printf('<img src="%s" alt="%s">', wp_get_attachment_image_src($product->get_image_id(), 'full')[0], get_bloginfo( 'name' )) ?>
		</div>
	<?php endif ?>
	<?php if ( !empty( $attachment_ids ) ): ?>
		<?php foreach ($attachment_ids as $attachment_id): ?>
			<div class="item">
				<?php printf('<img src="%s" alt="%s">', wp_get_attachment_image_src($attachment_id, 'full')[0], get_bloginfo( 'name' )) ?>
			</div>
		<?php endforeach ?>
	<?php endif ?>

</div>