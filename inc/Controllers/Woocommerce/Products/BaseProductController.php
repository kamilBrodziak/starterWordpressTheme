<?php
namespace Inc\Controllers\Woocommerce\Products;

use Inc\Controllers\Partials\ImageController;
use Inc\Controllers\Woocommerce\Tools;

abstract class BaseProductController {
	protected $product;
	protected $details = [];
	public function __construct($product) {
		$this->product = $product;
		$this->loadRenderDetails();
	}

	protected function loadBasicProductRenderDetails($product, $isVariation = false) {
		$imageController = new ImageController($product->get_image_id());
		$details = [
			'id' => $product->get_id(),
			'title' => $product->get_name(),
			'prices' => [
				'regular' => Tools::formatPrice($product->get_regular_price())
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
			$details['prices']['sale'] = Tools::formatPrice($product->get_sale_price());
		}
		return $details;
	}
	protected function withGallery() {
		$product = $this->product;
		if($this->hasGallery()) {
			$this->details['gallery'] = [];
			foreach ($product->get_gallery_image_ids() as $id) {
				$galleryImgController = new ImageController($id);
				$this->details['gallery'][] = $galleryImgController->withThumbnail()
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
	protected function withStock($product, &$details) {
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
				$details['stock']['backorders'] = $backordersAllowed;
				$details['stock']['notify'] = $product->backorders_require_notification();
			}
		}
	}
	public function getRenderDetails() {
		return $this->details;
	}

	public abstract function loadRenderDetails();

	public function hasGallery() {
		return !empty($this->product->get_gallery_image_ids());
	}

	public function getID() {
		return $this->product->get_id();
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