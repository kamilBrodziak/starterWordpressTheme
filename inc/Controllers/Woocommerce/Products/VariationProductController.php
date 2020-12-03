<?php
namespace Inc\Controllers\Woocommerce\Products;
use Inc\Controllers\Woocommerce\Products\Options\VariationProductOptions;

class VariationProductController extends BaseProductController {
	public function __construct($product, VariationProductOptions $options ) {
		parent::__construct( $product, $options);
	}

	public function loadRenderDetails() {
		$this->withAttributes();
		$this->withPrice();
		$this->withShipping();
		$this->withStock();
		$this->withType();
	}

	public function withAttributes() {
		if($this->options->isWithAttributes()) {
			$this->details['attributes'] = $this->product->get_variation_attributes();
		}
	}

	public function getAttributes() {
		return $this->details['attributes'];
	}

	public function withParentID() {
		if($this->options->isWithParentID()) {
			$this->details['parent_id'] = $this->product->get_parent_id();
		}
	}
}