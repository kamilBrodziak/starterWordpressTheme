<?php
namespace Inc\Models\Products;

class VariableProduct extends Product {
	private $variations = [];
	private $attributes = [];
	private $minRegular;
	private $maxRegular;
	private $minSale;
	private $maxSale;
	public function __construct( $args ) {
		parent::__construct( $args );
		$variations = [];
		if(is_array($args)) {
			$variations = $args['variations'];
			$this->maxRegular = $args['max_price'];
			$this->minRegular = array_key_exists('min_price', $args) ? $args['min_price'] : $this->maxRegular;
			$this->maxSale = array_key_exists('max_sale_price', $args) ? $args['max_sale_price'] : $this->maxRegular;
			$this->minSale = array_key_exists('min_sale_price', $args) ? $args['min_sale_price'] : $this->minRegular;
			$this->attributes = $args['attributes'];
		} elseif(is_object($args)) {
			$variations = $args->get_available_variations('objects');
			$this->maxRegular = $args->get_variation_regular_price('max');
			$this->minRegular = $args->get_variation_regular_price('min');
			$this->maxSale = $args->get_variation_sale_price('max');
			$this->minSale = $args->get_variation_sale_price('min');
			$this->attributes = $args->get_variation_attributes();
		}
		foreach ($variations as $variation) {
			$variation = new VariationProduct($variation);
			$this->variations[] = $variation;
		}

//		$minRegular = -1;
//		$maxRegular = -1;
//		$minSale = -1;
//		$maxSale = -1;
//		$ind = 0;
//		foreach ($variations as $variation) {
//			$variationProduct = new VariationProduct($variation);
//			$name = $variationProduct->get_name();
//			$attrs = explode(';', explode(' - ', $name)[1]);
//
//			foreach ($attrs as $attr) {
//				$attr = explode(':', $attr);
//				if(!isset($this->attributes[$attr[0]])) {
//					$this->attributes[$attr[0]] = [];
//				}
//				if(!in_array($attr[1], $this->attributes[$attr[0]])) {
//					$this->attributes[$attr[0]][] = $attr[1];
//				}
//			}
//			$regularPrice = $variationProduct->get_regular_price();
//			$salePrice = $variationProduct->get_sale_price();
//			if($ind == 0) {
//				$minRegular = $regularPrice;
//				$maxRegular = $regularPrice;
//				$minSale = $salePrice;
//				$maxSale = $salePrice;
//				$ind++;
//			} else {
//				if($minRegular > $regularPrice) $minRegular = $regularPrice;
//				if($maxRegular < $regularPrice) $maxRegular = $regularPrice;
//				if($minSale > $salePrice) $minSale = $salePrice;
//				if($maxSale < $salePrice) $maxSale = $salePrice;
//			}
//			$this->variations[] = $variationProduct;
//		}
//		$this->minRegular = $minRegular;
//		$this->maxRegular = $maxRegular;
//		$this->minSale = $minSale;
//		$this->maxSale = $maxSale;
	}

	public function getParams() {
		$params = parent::getParams();
		$params['max_price'] = $this->maxRegular;
		if($this->minRegular != $this->maxRegular) {
			$params['min_price'] = $this->minRegular;
		}
		if($this->is_on_sale()) {
			$params['max_sale_price'] = $this->maxSale;
			if($this->maxSale != $this->minSale) {
				$params['min_sale_price'] = $this->minSale;
			}
		}
		$params['attributes'] = $this->attributes;
		$params['variations'] = array_map(function($variation) {return $variation->getParams();}, $this->variations);
		return $params;
	}

	public function get_variation_regular_price($minOrMax, $t) {
		return $minOrMax == 'min' ? $this->minRegular: $this->maxRegular;
	}

	public function get_variation_sale_price($minOrMax, $t) {
		return $minOrMax == 'min' ? $this->minSale: $this->maxSale;
	}

	public function get_variation_attributes() {
		return $this->attributes;
	}

	public function get_available_variations($arrayOrObject) {
		return $this->variations;
	}
}