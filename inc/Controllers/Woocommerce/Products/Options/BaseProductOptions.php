<?php


namespace Inc\Controllers\Woocommerce\Products\Options;


class BaseProductOptions {
	private $withID = false,
		$withName = false,
		$withSKU = false,
		$withType = false,
		$withStatus = false,
		$withDateCreated = false,
		$withDateModified = false,
		$withProductUrl = false,
		$withAddToCartUrl = false,
		$withTotalSales = false,
		$withAverageRating = false,
		$withOnSale = false,
		$withImage = false;

	public function __construct() {
		return $this;
	}

	public function isWithID() {
		return $this->withID;
	}

	public function withID() {
		$this->withID = true;
		return $this;
	}

	public function isWithName() {
		return $this->withName;
	}

	public function withName() {
		$this->withName = true;
		return $this;
	}

	public function isWithSKU() {
		return $this->withSKU;
	}

	public function withSKU() {
		$this->withSKU = true;
		return $this;
	}

	public function isWithType() {
		return $this->withType;
	}

	public function withType() {
		$this->withType = true;
		return $this;
	}

	public function isWithStatus() {
		return $this->withStatus;
	}

	public function withStatus() {
		$this->withStatus = true;
		return $this;
	}

	public function isWithDateCreated() {
		return $this->withDateCreated;
	}

	public function withDateCreated() {
		$this->withDateCreated = true;
		return $this;
	}

	public function isWithDateModified() {
		return $this->withDateModified;
	}

	public function withDateModified() {
		$this->withDateModified = true;
		return $this;
	}

	public function isWithProductUrl() {
		return $this->withProductUrl;
	}

	public function withProductUrl() {
		$this->withProductUrl = true;
		return $this;
	}

	public function isWithAddToCartUrl() {
		return $this->withAddToCartUrl;
	}

	public function withAddToCartUrl() {
		$this->withAddToCartUrl = true;
		return $this;
	}







	public function isWithTotalSales() {
		return $this->withTotalSales;
	}

	public function withTotalSales() {
		$this->withTotalSales = true;
		return $this;
	}

	public function isWithAverageRating() {
		return $this->withAverageRating;
	}

	public function withAverageRating() {
		$this->withAverageRating = true;
		return $this;
	}

	public function isWithOnSale() {
		return $this->withOnSale;
	}

	public function withOnSale() {
		$this->withOnSale = true;
		return $this;
	}

	public function isWithImage() {
		return $this->withImage;
	}

	public function withImage() {
		$this->withImage = true;
		return $this;
	}
}