<?php
namespace Inc\Controllers\Woocommerce;

use Inc\Controllers\Partials\ImageController;

class ProductController {
	private $product;
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
		$details = $this->withStock($this->product, $details);
		$details = $this->withGallery($details);
		return $details;
	}

	private function getBasicProductRenderDetails($product, $isVariation = false) {
		$imageController = new ImageController($product->get_image_id());
		$details = [
			'id' => $product->get_id(),
			'name' => $product->get_name(),
			'prices' => [
				'regularPrice' => $product->get_regular_price()
			],
			'img' => !$isVariation ? $imageController->withProductTease()->getImageRender() :
				$imageController->getImageFullSrc(),
			'type' => $product->get_type()
		];
		if($product->is_on_sale()) {
			$details['prices']['salePrice'] = $product->get_sale_price();
		}

		return $details;
	}

	private function withGallery($details) {
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
		return $details;
	}

	private function getVariableProductRenderDetails() {
		$product = $this->product;
		$details = $this->getBasicProductRenderDetails($product);
		$details = $this->withGallery($details);

		$details['variations'] = [];
		$variationsIDs = $product->get_children(true);
		foreach ($variationsIDs as $id) {
			$variation = wc_get_product($id);
			$variationDetails = $this->getBasicProductRenderDetails(wc_get_product($id),false);
			$variationDetails = $this->withStock($variation, $variationDetails);
			$details['variations'][] = $variationDetails;
		}

		return $details;
	}

	private function withStock($product, $details) {
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
		return $details;
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

