<?php
namespace Inc\Controllers\Woocommerce\Products;

use Inc\Controllers\Woocommerce\Tools;

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

	public function withVariations($customDb = false) {
		$product      = $this->product;
		$variations   = [];
		$combinations = [];
		$variationsObjs = [];
		if(!$customDb) {
			foreach ( $product->get_children() as $id ) {
				$variation = new \WC_Product_Variation( $id );
				if ( ! $variation || ! $variation->exists() ||
				     ( Tools::hideOutOfStockItems() && ! $variation->is_in_stock() ) ) {
					continue;
				}
				if ( apply_filters( 'woocommerce_hide_invisible_variations', true, $variation->get_id(), $variation )
				     && ! $variation->variation_is_visible() ) {
					continue;
				}
				$variationsObjs[] = $variation;
			}
		} else {
			$variationsObjs = $product->get_available_variations( 'objects' );
		}
		foreach ( $variationsObjs as $variation ) {
//			if($variation->is_purchasable()) {
			$variationAttrs   = $variation->get_variation_attributes();
			$variationDetails = $this->loadBasicProductRenderDetails( $variation, true );
			$this->withStock( $variation, $variationDetails );

			$combination = [];
			$index       = 0;
			foreach ( array_reverse( $variationAttrs ) as $key => $value ) {
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
//			}

		}

		$items = json_encode( $combinations);
		$this->details['variations']['items'] = $items;

	}

	private function withMinMaxPrices() {
		$product = $this->product;
		$minRegular = $product->get_variation_regular_price('min', true);
		$this->details['prices']['minRegular'] = Tools::formatPrice($minRegular);
		$maxRegular = $product->get_variation_regular_price('max', true);
		if($minRegular != $maxRegular) {
			$this->details['prices']['maxRegular'] = Tools::formatPrice($maxRegular);
		}
		if($this->isOnSale()) {
			$minSale = $product->get_variation_sale_price('min', true);
			$maxSale = $product->get_variation_sale_price('max', true);
			if($minSale != $minRegular || $maxSale != $maxRegular) {
				$this->details['prices']['minSale'] = Tools::formatPrice($minSale);
				if($minSale != $maxSale) {
					$this->details['prices']['maxSale'] = Tools::formatPrice($maxSale);
				}
			}
		}
	}

	private function withAttributeLabels() {
		$product = $this->product;
		$this->details['variations'] = [
			'selected' => [
				'title' => 'Choose option'
			]
		];
		$labels = [];
		$variations = $product->get_variation_attributes();
		foreach ($variations as $name => $arr) {
			$labels[] = [
				'name' => $name,
				'items' => $arr
			];
		}
		$this->details['variations']['labels'] = $labels;
	}

	public function loadVariationRenderDetailsByAttributes($attr) {
		$variation = (new \WC_Product_Data_Store_CPT())->find_matching_product_variation($this->product, $attr);
		$details = $this->loadBasicProductRenderDetails($variation, true);
		$this->withStock($variation, $details);
		return $details;
	}
}