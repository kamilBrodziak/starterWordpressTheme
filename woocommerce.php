<?php
$context            = Timber::context();
if ( is_singular( 'product' ) ) {
	$context['post']    = Timber::get_post();
	$product            = wc_get_product( $context['post']->ID );
	$context['product'] = $product;
	// Restore the context and loop back to the main query loop.
	wp_reset_postdata();

	Timber::render( 'single-product.twig', $context );
} else {
    //  if site category
	$productController = new \Inc\Controllers\Woocommerce\ProductsController();
	$context['products'] = $productController->limit(20)
	                                         ->page(1)
	                                         ->orderByName()
	                                         ->orderASC()
	                                         ->withVariationRender()
	                                         ->withCategory('test')
	                                         ->getProducts();
	Timber::render( 'page-shop.twig', $context );
}