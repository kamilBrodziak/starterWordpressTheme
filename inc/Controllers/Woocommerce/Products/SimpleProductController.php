<?php
namespace Inc\Controllers\Woocommerce\Products;
use Inc\Controllers\Woocommerce\Products\Options\SimpleProductOptions;

class SimpleProductController extends BaseProductController {
	public function __construct( $product, SimpleProductOptions $options ) {
		parent::__construct( $product, $options);
	}

	public function loadRenderDetails() {
		$this->withStock();
		$this->withType();
		$this->withPrice();
		$this->withPostInfo();
		$this->withCrossSell();
		$this->withUpsell();
		$this->withStock();
		$this->withSoldIndividually();
		$this->withShipping();
	}

}