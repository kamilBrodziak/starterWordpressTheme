<?php


namespace Inc\Controllers\Woocommerce\Products\Options\Traits;


trait TShippingOptions {
	private $withWeight = false,
			$withDimensions = false,
			$withShippingClass = false;

	public function isWithWeight() {
		return $this->withWeight;
	}

	public function withWeight() {
		$this->withWeight = true;
		return $this;
	}

	public function isWithDimensions() {
		return $this->withDimensions;
	}

	public function withDimensions() {
		$this->withDimensions = true;
		return $this;
	}

	public function isWithShippingClass() {
		return $this->withDimensions;
	}

	public function withShippingClass() {
		$this->withDimensions = true;
		return $this;
	}

}