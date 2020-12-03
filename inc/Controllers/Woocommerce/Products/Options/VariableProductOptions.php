<?php


namespace Inc\Controllers\Woocommerce\Products\Options;


use Inc\Controllers\Woocommerce\Products\Options\Traits\TAttributesOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TCrossSellOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TPostOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TShippingOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TSoldIndividuallyOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TStockOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TUpsellOptions;

class VariableProductOptions extends BaseProductOptions {
	use TAttributesOptions,
		TCrossSellOptions,
		TPostOptions,
		TShippingOptions,
		TSoldIndividuallyOptions,
		TStockOptions,
		TUpsellOptions;
	private $withMinSalePrice = false,
			$withMaxSalePrice = false,
			$withMinRegularPrice = false,
			$withMaxRegularPrice = false;

	public function __construct() {
		parent::__construct();
		return $this;
	}

	public function isWithMinSalePrice() {
		return $this->withMinSalePrice;
	}

	public function withMinSalePrice() {
		$this->withMinSalePrice = true;
		return $this;
	}

	public function isWithMaxSalePrice() {
		return $this->withMaxSalePrice;
	}

	public function withMaxSalePrice() {
		$this->withMaxSalePrice = true;
		return $this;
	}

	public function isWithMinRegularPrice() {
		return $this->withMinRegularPrice;
	}

	public function withMinRegularPrice() {
		$this->withMinRegularPrice = true;
		return $this;
	}

	public function isWithMaxRegularPrice() {
		return $this->withMaxRegularPrice;
	}

	public function withMaxRegularPrice() {
		$this->withMaxRegularPrice = true;
		return $this;
	}
}