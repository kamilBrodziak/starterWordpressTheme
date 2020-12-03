<?php


namespace Inc\Controllers\Woocommerce\Products\Options\Traits;


trait TAttributesOptions {
	private $withAttributes = false;

	public function isWithAttributes() {
		return $this->withAttributes;
	}

	public function withAttributes() {
		$this->withAttributes = true;
		return $this;
	}
}