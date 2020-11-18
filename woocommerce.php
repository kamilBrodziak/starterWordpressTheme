<?php

use Inc\Controllers\Woocommerce\ProductsController;

$context = Timber::context();
if ( is_singular( 'product' ) ) {
	$context['post']    = Timber::get_post();
	$product            = wc_get_product( $context['post']->ID );
	$context['product'] = $product;
	// Restore the context and loop back to the main query loop.
	wp_reset_postdata();

	Timber::render( 'single-product.twig', $context );
} else {
    //  if site category
	$productController = new ProductsController();
	$time = microtime(true);
	$context['products'] = $productController->limit(50)
	                                         ->page(1)
	                                         ->orderByName()
											 ->withVariationRender()
	                                         ->orderASC()
	                                         ->getProducts();

	$time2 = microtime(true);
	var_dump(($time2 - $time));
	session_start();
	$_SESSION['productController'] = $productController;
	Timber::render( 'page-shop.twig', $context );
//	$time3 = microtime(true);
//	var_dump(($time3 - $time));
}