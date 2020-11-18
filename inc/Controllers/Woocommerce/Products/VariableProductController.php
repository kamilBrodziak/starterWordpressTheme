<?php
namespace Inc\Controllers\Woocommerce\Products;

class VariableProductController extends BaseProductController {
	public function __construct( $product ) {
		parent::__construct( $product );
	}

	public function loadRenderDetails() {
		$product = $this->product;
		$this->details = $this->loadBasicProductRenderDetails($product);
		$this->withGallery();
		$this->withMinMaxPrices();
		$this->details['variations'] = [];
		$this->withAttributeLabels();
	}

	public function withVariations() {
		$product = $this->product;
		$variations = [];
		$combinations = [];
		foreach ($product->get_available_variations('objects') as $variation) {
			$variationAttrs = $variation->get_variation_attributes();
			$variationDetails = $this->loadBasicProductRenderDetails($variation,true);
			$this->withStock($variation, $variationDetails);
			$combination = [];
			$index = 0;
			foreach (array_reverse($variationAttrs) as $key => $value) {
				$attrName = str_replace(' ', '_', $value);
				if($index == 0) {
					$combination[$attrName] = $variationDetails;
				} elseif ($index > 0 ) {
					$temp = $combination;
					$combination = [];
					$combination[$attrName] = $temp;
				}
				$index++;
			}
			$combinations = array_merge_recursive($combinations, $combination);
			$variations[] = $variationDetails;
		}
		$this->details['variations']['items'] = json_encode($combinations);
	}

	private function withMinMaxPrices() {
		$product = $this->product;
		$minRegular = $product->get_variation_regular_price('min', true);
		$this->details['prices']['minRegular'] = wc_price($minRegular);
		$maxRegular = $product->get_variation_regular_price('max', true);
		if($minRegular != $maxRegular) {
			$this->details['prices']['maxRegular'] = wc_price($maxRegular);
		}
		$minSale = $product->get_variation_sale_price('min', true);
		$maxSale = $product->get_variation_sale_price('max', true);
		if($minSale != $minRegular || $maxSale != $maxRegular) {
			$this->details['prices']['minSale'] = wc_price($minSale);
			if($minSale != $maxSale) {
				$this->details['prices']['maxSale'] = wc_price($maxSale);
			}
		}
	}

	private function withAttributeLabels() {
		$product = $this->product;
		$this->details['variations'] = [
			'selected' => [
				'title' => 'Choose option'
			],
			'labels' => []
		];
		foreach ($product->get_variation_attributes() as $name => $arr) {
			$this->details['variations']['labels'][] = [
				'name' => $name,
				'items' => $arr
			];
		}
	}

	public function withVariableVariations() {
		$product = $this->product;
		$variations = [];
		$combinations = [];
		foreach ($product->get_available_variations('objects') as $variation) {
			$variationAttrs = $variation->get_variation_attributes();
			$variationDetails = $this->loadBasicProductRenderDetails($variation,true);
			$this->withStock($variation, $variationDetails);
			$combination = [];
			$index = 0;
			foreach (array_reverse($variationAttrs) as $key => $value) {
				$attrName = str_replace(' ', '_', $value);
				if($index == 0) {
					$combination[$attrName] = $variationDetails;
				} elseif ($index > 0 ) {
					$temp = $combination;
					$combination = [];
					$combination[$attrName] = $temp;
				}
				$index++;
			}
			$combinations = array_merge_recursive($combinations, $combination);
			$variations[] = $variationDetails;
		}
		$this->details['variations']['items'] = json_encode($combinations);
	}

	public function loadVariationRenderDetailsByAttributes($attr) {
		$variation = (new \WC_Product_Data_Store_CPT())->find_matching_product_variation($this->product, $attr);
		$details = $this->loadBasicProductRenderDetails($variation, true);
		$this->withStock($variation, $details);
		return $details;
	}
}