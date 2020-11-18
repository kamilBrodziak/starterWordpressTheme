<?php
namespace Inc\Controllers\Woocommerce\Products;

class SimpleProductController extends BaseProductController {
	public function __construct( $product ) {
		parent::__construct( $product );
	}

	public function loadRenderDetails() {
		$this->details = $this->loadBasicProductRenderDetails($this->product);
		$this->withStock($this->product, $details);
		$this->withGallery();
	}

}