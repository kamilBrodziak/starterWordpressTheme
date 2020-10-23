<?php
/**
 * @package starterWordpressTheme
 */

//defined('ABSPATH') or die( 'Hey, you can\t access this file!' );

$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
    $timber = new Timber\Timber();
}

if ( ! class_exists( 'Timber' ) ) {
    add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

Timber::$dirname = ['templates/frontEnd', 'templates/backend'];
Timber::$autoescape = false;

function activateTheme() {
    Inc\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'activateTheme');

function deactivateTheme() {
    Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivateTheme');

class StarterSite extends Timber\Site {
    /** Add timber support. */
    public function __construct() {
        add_action( 'after_setup_theme', array( $this, 'themeSupports' ) );
        add_filter( 'timber/context', array( $this, 'addToContext' ) );
        add_filter( 'timber/twig', array( $this, 'addToTwig' ) );
        add_action( 'init', array( $this, 'registerPostTypes' ) );
        add_action( 'init', array( $this, 'registerTaxonomies' ) );
	    add_action('init', array($this, 'adjustImgSizes'));
	    $this->removeWordpressUnnecessaryActions();
        parent::__construct();
    }

    public function removeWordpressUnnecessaryActions() {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action ('wp_head', 'rsd_link');
        remove_action( 'wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
        remove_action('template_redirect', 'rest_output_link_header', 11, 0);
    }

	public function adjustImgSizes() {
		remove_image_size( 'woocommerce_thumbnail' );
		remove_image_size( 'woocommerce_single' );
		remove_image_size( 'woocommerce_gallery_thumbnail' );
		remove_image_size( 'shop_catalog' );
		remove_image_size( 'shop_single' );
		remove_image_size( 'shop_thumbnail' );
		add_image_size('product_tease', 250);
		add_image_size('mobile', 480);
		add_image_size('very_large', 1280);
		add_image_size('largest', 1920);
	}

	public function registerPostTypes() {}
	public function registerTaxonomies() {}

    public function addToContext( $context ) {
        $context['menu']  = new Timber\Menu('header');
        $context['site']  = $this;

        $logoID = get_theme_mod('custom_logo');
        if($logoID) {
            $context['siteLogo'] = getImgObjForTwig($logoID, ['medium', 'mobile', 'medium_large', 'large', 'very_large', 'largest']);
        }
        return $context;
    }

	public function themeSupports() {
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support('html5',
                          array(
                              //				'comment-form',
                              //				'comment-list',
                              //				'gallery',
                              //				'caption',
                          )
        );
        add_theme_support('post-formats',
                          array(
                              'aside',
                              'image',
                              'video',
                              'quote',
                              'link',
                              'gallery',
                              'audio',
                          )
        );
//        add_theme_support( 'menus' );
        add_theme_support( 'woocommerce' );

        register_nav_menus( array(
            'header' => 'Header menu',
            'footer' => 'Footer menu'
        ) );
        add_theme_support( 'custom-logo' );
    }

	public function addToTwig( $twig ) {
        $twig->addExtension( new Twig\Extension\StringLoaderExtension() );
        //        $twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
        return $twig;
    }
}
new StarterSite();

if(class_exists('Inc\\Init')) {
    Inc\Init::registerServices();
}

function getImgObjForTwig($imgID, $specificSizes = []) {
	$logo = [
		'sizes' => [],
		'alt' => get_post_meta( $imgID, '_wp_attachment_image_alt', true)
	];
	$sizes = wp_get_attachment_metadata($imgID)['sizes'];
	if(!empty($specificSizes)){
		$allSizes = $sizes;
		$sizes = [];
		foreach($allSizes as $size => $args) {
			if(in_array($size, $specificSizes)) {
				$sizes[$size] = $args;
			}
		}
	}
	uasort($sizes, function ($a, $b) {return $a['width'] - $b['width'];});
	$sizes['full'] = ['width' => false];
	$srcSet = [];
	foreach ($sizes as $size => $args) {
		$imgAttrs = wp_get_attachment_image_src( $imgID, $size );
		$srcSet[] = "${imgAttrs[0]} ${imgAttrs[1]}w";
		$url = wp_get_attachment_image_url( $imgID, $size);
		$logo['sizes'][$size] = [];
        $logo['sizes'][$size]['src'] = $url;
		if($args['width']) {
			$logo['sizes'][$size]['width'] = "${args['width']}px";
		}
	}
	$logo['srcSet'] = join(",", $srcSet);
	return $logo;
}

//add_filter('wp_generate_attachment_metadata', 'uploadedImgToWebp', 10, 2);
//function uploadedImgToWebp($metadata, $id) {
//    if(wp_attachment_is_image($id)) {
//        $sizes = ['large', 'medium_large', 'medium', 'thumbnail'];
//        $srcFull = wp_get_attachment_image_url( $id, 'full');
//        Timber\ImageHelper::img_to_webp($srcFull);
//        $metadata['webpSrc'] = [];
//
//        $metadata['webpSrc']['full'] = substr_replace($srcFull , 'webp', strrpos($srcFull , '.') +1);
//
//        foreach ($sizes as $size) {
//            $src = wp_get_attachment_image_url( $id, $size);
//            if($src != $srcFull ) {
//                Timber\ImageHelper::img_to_webp($src);
//	            $metadata['webpSrc'][$size] = substr_replace($src , 'webp', strrpos($src , '.') +1);
//            }
//        }
//    }
//    return $metadata;
//}

add_action('delete_attachment', 'deleteImgWebp', 10, 2);
function deleteImgWebp($id) {
	if(wp_attachment_is_image($id)) {
		$sizes = wp_get_attachment_metadata($id)['sizes'];
		$sizes[] = ['full' => []];
		foreach ($sizes as $size => $attrs) {
			$src = wp_get_attachment_image_url($id, $size);
			$path = parse_url(substr_replace($src , 'webp', strrpos($src , '.') +1), PHP_URL_PATH);
			$fullPath = $_SERVER['DOCUMENT_ROOT'] . $path;
			if(file_exists($fullPath)) {
				unlink($fullPath);
			}
		}
	}
}



/**
 * WOOCOMMERCE
 **/



function timber_set_product( $post ) {
	global $product;

	if ( is_woocommerce() ) {
		$product = wc_get_product( $post->ID );
	}
}

add_filter('add_to_cart_redirect', 'addToCartRedirectToCheckout');
function addToCartRedirectToCheckout() {
	global $woocommerce;
	return $woocommerce->cart->get_checkout_url();
}

add_filter( 'woocommerce_add_to_cart_validation', 'remove_cart_item_before_add_to_cart', 20, 3 );
function remove_cart_item_before_add_to_cart( $passed, $product_id, $quantity ) {
	if( ! WC()->cart->is_empty() )
		WC()->cart->empty_cart();
	return $passed;
}

add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'wc_remove_options_text');
function wc_remove_options_text( $args ){
	$args['show_option_none'] = '';
	return $args;
}


function isSimpleProduct($id) {
	return wc_get_product($id)->is_type('simple');
}


add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text' );
function woocommerce_custom_single_add_to_cart_text() {
	return __( 'Kup teraz', 'woocommerce' );
}


add_filter('woocommerce_billing_fields','wpb_custom_billing_fields');
function wpb_custom_billing_fields( $fields = array() ) {
	unset($fields['billing_company']);
	return $fields;
}


function getWCProducts() {
	return Timber::get_posts( [
		'post_type'      => 'product',
		'orderby' => [
			'date' => 'ASC'
		]
   ] );
}

