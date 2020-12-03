<?php


namespace Inc\Controllers\Woocommerce\Products\Options;


use Inc\Controllers\Woocommerce\Products\Options\Traits\TCrossSellOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TPostOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TPriceOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TShippingOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TSoldIndividuallyOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TStockOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TTypeOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TUpsellOptions;

class SimpleProductOptions extends BaseProductOptions {
	use TStockOptions,
		TTypeOptions,
		TPriceOptions,
		TPostOptions,
		TCrossSellOptions,
		TUpsellOptions,
		TStockOptions,
		TSoldIndividuallyOptions,
		TShippingOptions;

	public function __construct(){
		parent::__construct();
		return $this;
	}

}