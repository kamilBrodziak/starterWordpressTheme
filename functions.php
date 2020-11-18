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
        remove_action('template_redirect', 'rest_output_link_header', 11);
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
        $context['ftrMenu'] = new Timber\Menu('footer');
        $context['site']  = $this;

        $logoID = get_theme_mod('custom_logo');
        if($logoID) {
        	$imgContoller = new \Inc\Controllers\Partials\ImageController($logoID);
        	$context['siteLogo'] = $imgContoller->withMobile()
	                                            ->withMedium()
	                                            ->withMediumLarge()
	                                            ->withLarge()
	                                            ->withVeryLarge()
	                                            ->withLargest()
	                                            ->getImageRender();
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

include_once __DIR__ . '/inc/Controllers/Partials/ImageController.php';

include_once __DIR__ . '/inc/Controllers/Woocommerce/ProductsController.php';
include_once __DIR__ . '/inc/Controllers/Woocommerce/ProductsController.php';
include_once __DIR__ . '/inc/Controllers/Woocommerce/ProductsController.php';
include_once get_template_directory() . '/inc/Controllers/Woocommerce/Ajax.php';
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