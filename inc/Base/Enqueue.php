<?php
/**
 * @package starterWordpressTheme
 */

namespace Inc\Base;


class Enqueue extends BaseController {
    public function register() {
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdmin'));
//        if($this->isActivated('nameThemeFeature')) {
//            add_action('wp_enqueue_scripts', array($this, 'enqueueFeature'));
//            add_filter('script_loader_tag', array($this, 'addAsyncOrDefer'), 10, 2);
//        }
        add_action('wp_enqueue_scripts', array($this, 'enqueueFrontEnd'));
    }


    function enqueueAdmin($hook) {
	    wp_register_script('kbAdminJS',
		    get_template_directory_uri() . '/static/backend/js/admin.js', ['jquery'], null, true);
	    wp_localize_script( 'kbAdminJS', 'ajaxBackend', array(
		    'ajaxUrl' => admin_url('admin-ajax.php'),
	    ) );
	    wp_enqueue_script('kbAdminJS');
//        if('themeNameDashboard' == $hook) {
//            wp_enqueue_media();
//            wp_enqueue_style('kBPaStyle', $this->pluginUrl. 'assets/css/aStyle.css');
//            wp_enqueue_script('jqueryMin', 'https://code.jquery.com/jquery-3.5.0.min.js', NULL, NULL, false);
//            wp_enqueue_script('aScript', $this->pluginUrl . 'assets/aScript.js', array('jqueryMin'), null, true);
//        }
//
//        if('themeNameFeatureCSS' == $hook) {
//            wp_enqueue_script('ace', $this->pluginUrl . 'assets/js/ace/ace.js', array('jquery'), null, true);
//            wp_enqueue_script('customCSS', $this->pluginUrl . 'assets/js/admin/customCSS.js', array('jqueryMin'), null, true);
//        }
    }

    function enqueueFrontEnd() {
        // custom CSS
//        if($this->isActivated('') && file_exists($this->pluginPath . 'assets/css/fcStyle.min.css')) {
//            wp_enqueue_style('', $this->pluginUrl . 'assets/css/fcStyle.min.css');
//        } else {
//            wp_enqueue_style('s', $this->pluginUrl . 'assets/css/fStyle.min.css');
//        }
//        wp_enqueue_script('fScript-defer', $this->pluginUrl . 'assets/fScript.min.js', array('jquery'), null, true);

        wp_deregister_script('jquery');
        wp_deregister_script( 'wp-embed' );
        wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'),false, NULL, true);
        wp_dequeue_style( 'wc-block-style' );
        if(!is_product() && !is_checkout()) {
	        wp_dequeue_style( 'woocommerce_frontend_styles' );
	        wp_dequeue_style( 'woocommerce-general');
	        wp_dequeue_style( 'woocommerce-layout' );
	        wp_dequeue_style( 'woocommerce-smallscreen' );
	        wp_dequeue_style( 'woocommerce_fancybox_styles' );
	        wp_dequeue_style( 'woocommerce_chosen_styles' );
	        wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
	        wp_dequeue_script( 'selectWoo' );
	        wp_deregister_script( 'selectWoo' );
	        wp_dequeue_script( 'wc-add-payment-method' );
	        wp_dequeue_script( 'wc-lost-password' );
	        wp_dequeue_script( 'wc_price_slider' );
	        wp_dequeue_script( 'wc-single-product' );
	        wp_dequeue_script( 'wc-add-to-cart' );
	        wp_dequeue_script( 'wc-cart-fragments' );
	        wp_dequeue_script( 'wc-credit-card-form' );
	        wp_dequeue_script( 'wc-checkout' );
	        wp_dequeue_script( 'wc-add-to-cart-variation' );
	        wp_dequeue_script( 'wc-single-product' );
	        wp_dequeue_script( 'wc-cart' );
	        wp_dequeue_script( 'wc-chosen' );
	        wp_dequeue_script( 'woocommerce' );
	        wp_dequeue_script( 'prettyPhoto' );
	        wp_dequeue_script( 'prettyPhoto-init' );
//	        wp_dequeue_script( 'jquery-blockui' );
	        wp_dequeue_script( 'jquery-placeholder' );
	        wp_dequeue_script( 'jquery-payment' );
	        wp_dequeue_script( 'fancybox' );
	        wp_dequeue_script( 'jqueryui' );
	        wp_deregister_script('woocommerce');
	        wp_deregister_script('wc-cart-fragments');
        }
	    wp_enqueue_script('jquery');
        wp_register_script('siteJS', get_template_directory_uri() . '/static/frontend/js/site.js', ['jquery'], null, true);
	    wp_localize_script( 'siteJS', 'ajaxWoocommerce', array(
		    'ajaxUrl' => admin_url('admin-ajax.php'),
	    ) );
	    wp_enqueue_script( 'siteJS');

	    wp_enqueue_style('siteStyle', get_template_directory_uri() . '/static/frontend/css/style.css', null, null, null);
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
    }
}