<?php


namespace Inc\Controllers\Woocommerce\Products\Options\Traits;


trait TPriceOptions {
	private $withRegularPrice = false,
			$withSalePrice = false,
			$withSaleStartDate = false,
			$withSaleEndDate = false;

	public function isWithRegularPrice() {
		return $this->withRegularPrice;
	}

	public function withRegularPrice() {
		$this->withRegularPrice = true;
		return $this;
	}

	public function isWithSalePrice() {
		return $this->withSalePrice;
	}

	public function withSalePrice() {
		$this->withSalePrice = true;
		return $this;
	}

	public function isWithSaleStartDate() {
		return $this->withSaleStartDate;
	}

	public function withSaleStartDate() {
		$this->withSaleStartDate = true;
		return $this;
	}

	public function isWithSaleEndDate() {
		return $this->withSaleEndDate;
	}

	public function withSaleEndDate() {
		$this->withSaleEndDate = true;
		return $this;
	}
}