<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$theme = wp_get_theme();

define('TextDomain' , $theme->TextDomain);
define('PrefixTheme' , $theme->Name.'_');
define('Inc', trailingslashit( get_template_directory() ) . 'includes/');
define('Setups', Inc . 'setups/');
define('Shortcodes', Inc . 'shortcodes/');
define('Widgets', Inc . 'widgets/');
define('Helpers', Inc . 'helpers/');
define('Libs', Inc . 'libs/');
define('Structure', Inc . 'structure/');
define('Assets', esc_url( trailingslashit( get_template_directory_uri() ) . 'assets/' ));
define('Css', Assets . 'css/');
define('Js', Assets . 'js/');
define('Img', Inc . 'images/');

require_once Setups.'disables.php';
require_once Setups.'supports.php';
require_once Setups.'plugins.php';
require_once Setups.'menus.php';

require_once Inc.'enqueue.php';

require_once Libs.'Mobile_Detect.php';

require_once Helpers.'functions.php';
require_once Helpers.'images.php';
require_once Helpers.'mega-menu.php';
require_once Helpers.'post-view.php';
require_once Helpers.'taxonomies.php';

require_once Structure.'header.php';
require_once Structure.'footer.php';
require_once Structure.'woocommerce.php';
require_once Structure.'home.php';
require_once Structure.'shop.php';
require_once Structure.'single-product.php';
require_once Structure.'cart_checkout_order.php';

require_once Widgets.'wda-filter-price.php';
require_once Widgets.'wda-active-filter.php';