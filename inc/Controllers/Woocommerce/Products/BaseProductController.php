<?php
namespace Inc\Controllers\Woocommerce\Products;

use Inc\Controllers\Partials\ImageController;
use Inc\Controllers\Woocommerce\Tools;

abstract class BaseProductController {
	protected $product;
	protected $details = [];
	protected $options = null;
	public function __construct($product, $options) {
		$this->product = $product;
		$this->options = $options;
		$this->loadBasicProductRenderDetails();
		$this->loadRenderDetails();
	}
	public abstract function loadRenderDetails();

	public function getRenderDetails() {
		return $this->details;
	}

	protected function loadBasicProductRenderDetails() {
		$options = $this->options;
		if($options->isWithID()) {
			$this->details['id'] = $this->getID();
		}

		if($options->isWithName()) {
			$this->details['name'] = $this->getName();
		}
		if($options->isWithSKU()) {
			$this->details['sku'] = $this->getSKU();
		}

		if($options->isWithType()) {
			$this->details['type'] = $this->getType();
		}

		if($options->isWithStatus()) {
			$this->details['type'] = $this->getStatus();
		}

		if($options->isWithDateCreated()) {
			$this->details['date']['created'] = $this->getDateCreated();
		}

		if($options->isWithDateModified()) {
			$this->details['date']['created'] = $this->getDateModified();
		}

		if($options->isWithProductUrl()) {
			$this->details['url']['product'] = $this->getProductUrl();
		}

		if($options->isWithAddToCartUrl()) {
			$this->details['url']['addToCart'] = $this->addToCartUrl();
		}

		if($options->isWithTotalSales()) {
			$this->details['totalSales'] = $this->getTotalSales();
		}

		if($options->isWithAverageRating()) {
			$this->details['averageRating'] = $this->getAverageRating();
		}

		if($options->isWithOnSale()) {
			$this->details['onSale'] = $this->isOnSale();
		}

		if($options->isWithImage()) {
			$imageController = new ImageController($this->getImageID());
			$this->details['img'] = !$this->isVariation() ? $imageController->withProductTease()->getImageRender() :
				$imageController->getImageFullSrc();
		}
	}

	protected function withPostInfo() {
		$options = $this->options;
		$product = $this->product;
		if($options->isWithTagIDs()) {
			foreach ($product->get_tag_ids() as $id) {
				$this->details['tags'][] = get_tag($id)->name;
			}
		}
		if($options->isWithCategoryIDs()) {
			foreach ($product->get_category_ids() as $id) {
				$this->details['categories'][] = get_cat_name($id);
			}
		}
		if($options->isWithGallery() && $this->hasGallery()) {
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
		if($options->isWithShortDescription()) {
			$this->details['shortDescription'] = $product->get_short_description();
		}
	}

	protected function withPrice() {
		$options = $this->options;
		if($options->isWithRegularPrice()) {
			$this->details['prices']['regular'] = Tools::formatPrice($this->getRegularPrice());
		}
		if($options->isWithSalePrice() && $this->isOnSale()) {
			$this->details['prices']['sale'] = Tools::formatPrice($this->getSalePrice());
		}
	}

	protected function withMinMaxPrice() {
		$options = $this->options;
		$maxRegular = null;
		if($options->isWithMaxRegularPrice()) {
			$maxRegular = Tools::formatPrice($this->getVariationRegularPrice('max'));
			$this->details['prices']['maxRegular'] = $maxRegular;
		}
		if($options->isWithMinRegularPrice()) {
			$minRegular = Tools::formatPrice($this->getVariationRegularPrice('min'));
			if(empty($maxRegular) || $minRegular != $maxRegular) {
				$this->details['prices']['minRegular'] = $minRegular;
			}
		}

		if($this->isOnSale()) {
			$maxSale = null;
			if($options->isWithMaxSalePrice()) {
				$maxSale = Tools::formatPrice($this->getVariationSalePrice('max'));
				$this->details['prices']['maxSale'] = $maxSale;
			}
			if($options->isWithMinSalePrice()) {
				$minSale = Tools::formatPrice($this->getVariationSalePrice('min'));
				if(empty($maxSale) || $minSale != $maxSale) {
					$this->details['prices']['minSale'] = $minSale;
				}
			}
		}
	}

	protected function withSaleDate() {
		if($this->isOnSale()) {
			$options = $this->options;
			if($options->isWithSaleStartDate()) {
				$this->details['date']['saleStart'] = $this->product->get_sale_start_date();
			}
			if($options->isWithSaleEndDate()) {
				$this->details['date']['saleEnd'] = $this->product->get_sale_end_date();
			}
		}
	}

	protected function withShipping() {
		$options = $this->options;
		if($options->isWithWeight()) {
			$this->details['shipping']['weight'] = $this->product->get_weight();
		}
		if($options->isWithDimensions()) {
			$this->details['shipping']['dimensions'] = $this->product->get_dimensions();
		}
		if($options->isWithShippingClass()) {
			$this->details['shipping']['class'] = $this->product->get_shipping_class();
		}
	}

	protected function withStock() {
		$options = $this->options;
		if($options->isWithInStock()) {
			$this->details['stock']['inStock'] = $this->isInStock();
		}

		if($options->isWithStockStatus()) {
			$this->details['stock']['status'] = $this->product->get_stock_status();
		}

		if($options->isWithStockManaging()) {
			$managingStock = $this->product->managing_stock();
			$this->details['stock']['managingStock'] = $managingStock;
			if($managingStock) {
				if($options->isWithStockQuantity()) {
					$this->details['stock']['quantity'] = $this->product->get_stock_quantity();
				}
				if($options->isWithStockBackorder()) {
					$backordersAllowed = $this->product->backorders_allowed();
					$this->details['stock']['backorders'] = $backordersAllowed;
					if($options->isWithStockBackorderNotify() && $backordersAllowed) {
						$this->details['stock']['notify'] = $this->product->backorders_require_notification();
					}
				}
			}
		}
	}

	protected function withSoldIndividually() {
		$options = $this->options;
		if($options->isWithSoldIndividually()) {
			$this->details['soldIndividually'] = $this->product->is_sold_individually();
		}
	}

	protected function withType() {
		if($this->options->isWithVirtual()) {
			$this->details['virtual'] = $this->product->is_virtual();
		}
		if($this->options->isWithDownloadable()) {
			$this->details['downloadable'] = $this->product->is_downloadable();
		}
	}

	protected function withUpsell() {
		if($this->options->isWithUpsellIDs()) {
			$this->details['upsellIDs'] = $this->product->get_upsell_ids();
		}
	}

	protected function withCrossSell() {
		if($this->options->isWithCrossSellIDs()) {
			$this->details['crossSellIDs'] = $this->product->get_cross_sell_ids();
		}
	}

	protected function withGrouped() {
		if($this->options->isWithGroupedIDs()) {
			$this->details['groupedIDs'] = $this->product->get_children();
		}
	}

	public function getName() {
		return $this->product->get_name();
	}

	public function getID() {
		return $this->product->get_id();
	}

	public function getSKU() {
		return $this->product->get_sku();
	}

	public function getType() {
		return $this->product->get_type();
	}

	public function getStatus() {
		return $this->product->get_status();
	}

	public function getDateCreated() {
		return $this->product->get_date_created();
	}

	public function getDateModified() {
		return $this->product->get_date_modified();
	}

	public function getProductUrl() {
		return $this->product->get_permalink();
	}

	public function addToCartUrl() {
		return $this->product->add_to_cart_url();
	}

	public function getTotalSales() {
		return $this->product->get_total_sales();
	}

	public function getAverageRating() {
		return $this->product->get_average_rating();
	}

	private function getVariationRegularPrice($minOrMax) {
		return $this->product->get_variation_regular_price($minOrMax, true);
	}

	private function getVariationSalePrice($minOrMax) {
		return $this->product->get_variation_sale_price($minOrMax, true);
	}




	private function hasGallery() {
		return !empty($this->product->get_gallery_image_ids());
	}



	public function getRegularPrice() {
		return $this->product->get_regular_price();
	}

	public function getSalePrice() {
		return $this->product->get_sale_price();
	}

	public function getImageID() {
		return $this->product->get_image_id();
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

	public function isVariation() {
		return $this->product->is_type('variation');
	}

	public function isGrouped() {
		return $this->product->is_type('grouped');
	}

	public function isExternal() {
		return $this->product->is_type('external');
	}
}