<?php


namespace Inc\Controllers\Woocommerce\Products\Options\Traits;


trait TUpsellOptions {
	private $withUpsellIDs = false;

	public function isWithUpsellIDs() {
		return $this->withUpsellIDs;
	}

	public function withUpsellIDs() {
		$this->withUpsellIDs = true;
		return $this;
	}
}