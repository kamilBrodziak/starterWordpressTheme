<?php


namespace Inc\Controllers\Woocommerce\Products\Options\Traits;


trait TTypeOptions {
	private $withDownloadable = false,
			$withVirtual = false;
	public function isWithDownloadable() {
		return $this->withDownloadable;
	}

	public function withDownloadable() {
		$this->withDownloadable = true;
		return $this;
	}

	public function isWithVirtual() {
		return $this->withVirtual;
	}

	public function withVirtual() {
		$this->withVirtual = true;
		return $this;
	}
}