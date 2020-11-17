<?php
namespace Inc\Controllers\Woocommerce;

use Inc\Controllers\Partials\ImageController;

class ProductController {
	private $product;
	private $variationRender = false;
	public function __construct($product) {
		$this->product = $product;
	}

	public function getProductRenderDetails() {
		if($this->isSimple()) {
			return $this->getSimpleProductRenderDetails();
		} else if($this->isVariable()) {
			return $this->getVariableProductRenderDetails();
		}
		return null;
	}

	private function getSimpleProductRenderDetails() {
		$details = $this->getBasicProductRenderDetails($this->product);
		$this->withStock($this->product, $details);
		$this->withGallery($details);
		return $details;
	}

	private function getBasicProductRenderDetails($product, $isVariation = false) {
		$imageController = new ImageController($product->get_image_id());
		$details = [
			'id' => $product->get_id(),
			'title' => $product->get_name(),
			'prices' => [
				'regular' => $product->get_regular_price()
			],
			'img' => !$isVariation ? $imageController->withProductTease()->getImageRender() :
				$imageController->getImageFullSrc(),
			'url' => [
				'addToCart' => $product->add_to_cart_url()
			]
		];
		if(!$isVariation) {
			$details['type'] = $product->get_type();
			$details['url']['productPage'] = get_permalink($product->get_id());
		}
		if($product->is_on_sale()) {
			$details['prices']['sale'] = $product->get_sale_price();
		}

		return $details;
	}

	private function withGallery(&$details) {
		$product = $this->product;
		if($this->hasGallery()) {
			$details['gallery'] = [];
			foreach ($product->get_gallery_image_ids() as $id) {
				$galleryImgController = new ImageController($id);
				$details['gallery'][] = $galleryImgController->withThumbnail()
				                                             ->withProductTease()
				                                             ->withMedium()
				                                             ->withMedium()
				                                             ->withMediumLarge()
				                                             ->withLarge()
				                                             ->withVeryLarge()
				                                             ->withLargest()
				                                             ->getImageRender();
			}
		}
	}

	public function withVariationRenderDetails() {
		$this->variationRender = true;
		return $this;
	}

	public function getVariationRenderDetailsByAttributes($attr) {
		$variation = (new \WC_Product_Data_Store_CPT())->find_matching_product_variation($this->product, $attr);
		$details = $this->getBasicProductRenderDetails($variation, true);
		$this->withStock($variation, $details);
		return $details;
	}

	private function getVariableProductRenderDetails() {
		$product = $this->product;
		$details = $this->getBasicProductRenderDetails($product);
		$this->withGallery($details);
		$minRegular = $product->get_variation_regular_price('min', true);
		$details['prices']['minRegular'] = $minRegular;
		$maxRegular = $product->get_variation_regular_price('max', true);
		if($minRegular != $maxRegular) {
			$details['prices']['maxRegular'] = $maxRegular;
		}
		$minSale = $product->get_variation_sale_price('min', true);
		$maxSale = $product->get_variation_sale_price('max', true);
		if($minSale != $minRegular || $maxSale != $maxRegular) {
			$details['prices']['minSale'] = $minSale;
			if($minSale != $maxSale) {
				$details['prices']['maxSale'] = $maxSale;
			}
		}

		$details['variations'] = [
			'selected' => [
				'title' => 'test2'
			],
			'labels' => [],
			'items' => []
		];
		foreach ($product->get_variation_attributes() as $name => $arr) {
			$details['variations']['labels'][] = [
				'name' => $name,
				'items' => $arr
			];
		}
		if($this->variationRender) {
			$variations = [];
			$combinations = [];
			foreach ($product->get_available_variations('objects') as $variation) {
				$variationAttrs = $variation->get_variation_attributes();
				$variationDetails = $this->getBasicProductRenderDetails($variation,true);
				$this->withStock($variation, $variationDetails);
				$combination = [];
				$index = 0;
				foreach (array_reverse($variationAttrs) as $key => $value) {
					$attrName = strtolower(str_replace(' ', '_', $value));
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
			$details['variations']['items'] = json_encode($combinations);
		}


		return $details;
	}

	private function withStock($product, &$details) {
		$inStock = $product->is_in_stock();
		$managingStock = $product->managing_stock();
		$details['stock'] = [
			'inStock' => $inStock,
			'managingStock' => $managingStock
		];

		if($managingStock) {
			$details['stock']['quantity'] = $product->get_stock_quantity();
			$backordersAllowed = $product->backorders_allowed();
			if($backordersAllowed) {
				$details['stock']['backorders'] = $product->backorders_allowed();
				$details['stock']['notify'] = $product->backorders_require_notification();
			}
		}
	}

	public function hasGallery() {
		return !empty($this->product->get_gallery_image_ids());
	}

	public function isOnSale() {
		return $this->product->is_on_sale();
	}

	public function isInStock() {
		return $this->product->is_in_stock();
	}

	public function isFeatured() {
		return $this->product->is_featured();
	}

	public function isVisible() {
		return $this->product->is_visible();
	}

	public function isDownloadable() {
		return $this->product->is_downloadable();
	}

	public function isPurchasable() {
		return $this->product->is_purchasable();
	}

	public function isOnBackorder($cartQuantity = 0) {
		return $this->product->is_on_backorder($cartQuantity);
	}

	public function isSimple() {
		return $this->product->is_type('simple');
	}

	public function isVariable() {
		return $this->product->is_type('variable');
	}
}

