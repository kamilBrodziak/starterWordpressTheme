<?php

namespace Inc\Base;

use Inc\DAO\Products\ProductsDAO;

class WooCommerceSettings {

    public function register() {
        $this->filters();
        $this->actions();
    }

    public function filters() {
        add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
        add_filter( 'woocommerce_is_sold_individually','__return_true', 10, 2 );
        add_filter('woocommerce_reset_variations_link', '__return_empty_string');
        add_filter( 'wc_add_to_cart_message', '__return');
    }

    public function actions() {
        remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
        add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 21 );
//	    add_action( 'woocommerce_product_query', [$this, 'react2wp_hide_products_without_price'] );
	    add_action( 'woocommerce_update_product', [$this, 'updateProductCustomTableAction'] );
	    add_action( 'woocommerce_new_product', [$this, 'insertProductCustomTableAction'] );
	    add_action( 'transition_post_status', [$this, 'deleteProductFromCustomTableAction'], 10, 3);
    }

    public function insertProductCustomTableAction($id) {
	    ProductsDAO::insertProductIntoTable($id);
    }

    public function updateProductCustomTableAction($id) {
    	ProductsDAO::updateProductInTable($id);
    }

	function deleteProductFromCustomTableAction($newStatus, $oldStatus, $post) {
    	$id = $post->ID;
    	$type = $post->post_type;
    	if($type == 'product' && $newStatus != $oldStatus) {
    		if($newStatus == 'trash') {
    			ProductsDAO::removeProductInTable($id);
		    } else {
    			ProductsDAO::insertProductIntoTable($id);
		    }
	    }
	}

//	function react2wp_hide_products_without_price( $q ){
//		$meta_query = $q->get( 'meta_query' );
//		$meta_query[] = array(
//			'key'       => '_price',
//			'value'     => '',
//			'compare'   => '!='
//		);
//		$q->set( 'meta_query', $meta_query );
//	}
}