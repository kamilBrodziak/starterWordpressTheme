<?php


namespace Inc\Controllers\Woocommerce\Products\Options;


use Inc\Controllers\Woocommerce\Products\Options\Traits\TAttributesOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TPostOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TUpsellOptions;

class GroupedProductOptions extends BaseProductOptions {
	use TAttributesOptions, TPostOptions, TUpsellOptions;
	private $withGroupedIDs = false;

	public function __construct() {
		parent::__construct();
		return $this;
	}

	public function withGroupedIDs() {
		$this->withGroupedIDs = true;
		return $this;
	}

	public function isWithGroupedIDs() {
		return $this->withGroupedIDs;
	}
}