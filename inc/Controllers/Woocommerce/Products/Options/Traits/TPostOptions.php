<?php


namespace Inc\Controllers\Woocommerce\Products\Options\Traits;


trait TPostOptions {
	private $withGallery = false,
			$withTagIDs = false,
			$withCategoryIDs = false,
			$withShortDescription = false;

	public function isWithGallery() {
		return $this->withGallery;
	}

	public function withGallery() {
		$this->withGallery = true;
		return $this;
	}

	public function isWithTagIDs() {
		return $this->withTagIDs;
	}

	public function withTagIDs() {
		$this->withTagIDs = true;
		return $this;
	}

	public function isWithCategoryIDs() {
		return $this->withCategoryIDs;
	}

	public function withCategoryIDs() {
		$this->withCategoryIDs = true;
		return $this;
	}

	public function isWithShortDescription() {
		return $this->withShortDescription;
	}

	public function withCategoryShortDescription() {
		$this->withCategoryIDs = true;
		return $this;
	}
}