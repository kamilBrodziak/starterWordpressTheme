<?php


namespace Inc\Controllers\Woocommerce\Products\Options\Traits;


trait TStockOptions {
	private $withInStock = false,
			$withStockQuantity = false,
			$withStockManaging = false,
			$withStockBackorder = false,
			$withStockBackorderNotify = false,
			$withStockStatus = false;

	public function isWithInStock() {
		return $this->withInStock;
	}

	public function withInStock() {
		$this->withInStock = true;
		return $this;
	}

	public function isWithStockQuantity() {
		return $this->withStockQuantity;
	}

	public function withStockQuantity() {
		$this->withStockQuantity = true;
		return $this;
	}

	public function isWithStockManaging() {
		return $this->withStockManaging;
	}

	public function withStockManaging() {
		$this->withStockManaging = true;
		return $this;
	}

	public function isWithStockBackorder() {
		return $this->withStockBackorder;
	}

	public function withStockBackorder() {
		$this->withStockBackorder = true;
		return $this;
	}

	public function isWithStockBackorderNotify() {
		return $this->withStockBackorderNotify;
	}

	public function withStockBackorderNotify() {
		$this->withStockBackorderNotify = true;
		return $this;
	}

	public function isWithStockStatus() {
		return $this->withStockStatus;
	}

	public function withStockStatus() {
		$this->withStockStatus = true;
		return $this;
	}
}