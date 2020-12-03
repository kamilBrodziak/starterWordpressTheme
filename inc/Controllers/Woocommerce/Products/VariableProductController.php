<?php
namespace Inc\Controllers\Woocommerce\Products;
use Inc\Controllers\Woocommerce\Products\Options\VariableProductOptions;

class VariableProductController extends BaseProductController {
	public function __construct($product, VariableProductOptions $options ) {
		parent::__construct( $product, $options);
	}

	public function loadRenderDetails() {
		$this->withAttributes();
		$this->withCrossSell();
		$this->withPostInfo();
		$this->withShipping();
		$this->withSoldIndividually();
		$this->withMinMaxPrice();
		$this->withStock();
		$this->withUpsell();
	}

	public function withVariations($variationsControllers) {
		$variations   = [];
		$combinations = [];
		foreach ($variationsControllers as $variationController) {
			$variationDetails = $variationController->getRenderDetails();
			$combination = [];
			$index       = 0;
			foreach ( array_reverse( $variationController->getAttributes() ) as $key => $value ) {
				$attrName = str_replace( ' ', '_', $value );
				if ( $index++ > 0 ) {
					$temp                     = $combination;
					$combination              = [];
					$combination[ $attrName ] = $temp;
				} else {
					$combination[ $attrName ] = $variationDetails;
				}
				$index ++;
			}
			$combinations = array_merge_recursive( $combinations, $combination );
			$variations[] = $variationDetails;
		}
		$this->details['variations']['items'] = json_encode( $combinations);
	}



	private function withAttributes() {
		if($this->options->isWithAttributes()) {
			$this->details['variations'] = [
				'selected' => [
					'title' => 'Choose option'
				]
			];
			$labels = [];
			$variations = $this->product->get_variation_attributes();
			foreach ($variations as $name => $arr) {
				$labels[] = [
					'name' => $name,
					'items' => $arr
				];
			}
			$this->details['variations']['labels'] = $labels;
		}

	}

//	public function loadVariationRenderDetailsByAttributes($attr) {
//		$variation = (new \WC_Product_Data_Store_CPT())->find_matching_product_variation($this->product, $attr);
//		$details = $this->loadBasicProductRenderDetails($variation, true);
//		$this->withStock($variation, $details);
//		return $details;
//	}
}