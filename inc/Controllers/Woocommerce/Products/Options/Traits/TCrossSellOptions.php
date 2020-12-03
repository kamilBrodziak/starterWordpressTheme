<?php


namespace Inc\Controllers\Woocommerce\Products\Options\Traits;


trait TCrossSellOptions {
	private $withCrossSellIDs = false;

	public function isWithCrossSellIDs() {
		return $this->withCrossSellIDs;
	}

	public function withCrossSellIDs() {
		$this->withCrossSellIDs = true;
		return $this;
	}
}