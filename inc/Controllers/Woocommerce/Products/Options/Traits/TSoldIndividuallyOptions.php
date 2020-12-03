<?php


namespace Inc\Controllers\Woocommerce\Products\Options\Traits;


trait TSoldIndividuallyOptions {
	private $withSoldIndividually = false;

	public function isWithSoldIndividually() {
		return $this->withSoldIndividually;
	}

	public function withSoldIndividually() {
		$this->withSoldIndividually = true;
		return $this;
	}
}