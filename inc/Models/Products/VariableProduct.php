<?php
namespace Inc\Models\Products;

class VariableProduct extends Product {
	private $variations = [];
	private $attributes = [];
	private $minRegular;
	private $maxRegular;
	private $minSale;
	private $maxSale;
	public function __construct( $id) {
		parent::__construct( $id );
		$this->minRegular = parent::get_regular_price();
		$this->maxRegular = parent::get_regular_price();
		$this->minSale = parent::get_sale_price();
		$this->maxSale = parent::get_sale_price();
	}


	public function get_variation_regular_price($minOrMax, $t) {
		return $minOrMax == 'min' ? $this->minRegular: $this->maxRegular;
	}

	public function withVariationRegularPrice($minOrMax, $value) {
		if($minOrMax = 'min') {
			$this->minRegular = $value;
		} elseif($minOrMax = 'max') {
			$this->maxRegular = $value;
		}
		return $this;
	}

	public function get_variation_sale_price($minOrMax, $t) {
		return $minOrMax == 'min' ? $this->minSale: $this->maxSale;
	}

	public function withVariationSalePrice($minOrMax, $value) {
		if($minOrMax = 'min') {
			$this->minSale = $value;
		} elseif($minOrMax = 'max') {
			$this->maxSale = $value;
		}
		return $this;
	}

	public function get_variation_attributes() {
		return $this->attributes;
	}

	public function withVariationAttributes($attributes) {
		$this->attributes = $attributes;
		return $this;
	}

	public function get_available_variations($arrayOrObject) {
		return $this->variations;
	}

	public function withVariations($array) {
		$this->variations = $array;
		return $this;
	}

	public function addVariation($variation) {
		$this->variations[] = $variation;
	}
}