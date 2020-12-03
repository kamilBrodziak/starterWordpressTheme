<?php


namespace Inc\Controllers\Woocommerce\Products\Options;


use Inc\Controllers\Woocommerce\Products\Options\Traits\TAttributesOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TPriceOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TShippingOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TStockOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TTypeOptions;

class VariationProductOptions extends BaseProductOptions {
	use TAttributesOptions,
		TPriceOptions,
		TShippingOptions,
		TStockOptions,
		TTypeOptions;
	private $withParentID = false;

	public function __construct() {
		parent::__construct();
		return $this;
	}

	public function isWithParentID() {
		return $this->withParentID;
	}

	public function withParentID() {
		$this->withParentID = true;
		return $this;
	}
}