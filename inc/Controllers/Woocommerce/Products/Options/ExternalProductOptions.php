<?php


namespace Inc\Controllers\Woocommerce\Products\Options;


use Inc\Controllers\Woocommerce\Products\Options\Traits\TAttributesOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TPostOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TPriceOptions;
use Inc\Controllers\Woocommerce\Products\Options\Traits\TUpsellOptions;

class ExternalProductOptions extends BaseProductOptions {
	use TAttributesOptions, TPostOptions, TPriceOptions, TUpsellOptions;

	public function __construct() {
		parent::__construct();
		return $this;
	}
}