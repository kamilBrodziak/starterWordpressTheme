<?php

use Inc\Controllers\Woocommerce\Products\Options\SimpleProductOptions;
use Inc\Controllers\Woocommerce\Products\Options\VariableProductOptions;
use Inc\Controllers\Woocommerce\Products\Options\VariationProductOptions;
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
	$time = microtime(true);
	$productController = new ProductsController();
	$variableProductOptions = (new VariableProductOptions())->withID()
	                       ->withName()
	                       ->withMaxRegularPrice()
	                       ->withMinRegularPrice()
	                       ->withMaxSalePrice()
	                       ->withMinSalePrice()
	                       ->withAttributes()->withType()
	                       ->withImage()->withProductUrl()->withAddToCartUrl()->withGallery();
	$variationProductOptions = (new VariationProductOptions())->withAddToCartUrl()
	                     ->withImage()
	                     ->withAttributes()
	                     ->withName()
	                     ->withID()
	                     ->withType()
	                     ->withParentID()->withSalePrice()->withRegularPrice();
	$simpleProductOptions = (new SimpleProductOptions())
						->withRegularPrice()
						->withSalePrice()
						->withID()
						->withName()
						->withImage()
						->withAddToCartUrl()
						->withGallery()
						->withType()
						->withProductUrl();
	$time2 = microtime(true);
	var_dump(($time2 - $time));
	$context['products'] = $productController->limit(-1)
	                                         ->page(1)
											 ->publishOnly()
	                                         ->orderByName()
											 ->withVariationRender()
	                                         ->orderASC()
											 ->withVariableProductOptions($variableProductOptions)
											 ->withSimpleProductOptions($simpleProductOptions)
											 ->withVariationProductOptions($variationProductOptions)
	                                         ->getProductsFromKBDB();


//	$context['products'] = $productController->withVariationRender()->getProductsFromKBDB();
//	$context['products'] = $productController->limit(80)->withVariationRender()->getProductsFromTransients();
//	var_dump(count($context['products']));


//	$time3 = microtime(true);
//	var_dump($time3 - $time2);
//	session_start();
//	$_SESSION['productController'] = $productController;
	Timber::render( 'page-shop.twig', $context );
//	$time3 = microtime(true);
//	var_dump(($time3 - $time));
}