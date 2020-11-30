<?php


namespace Inc\Models\Products;


class Product {
	private $id,
		$name,
		$regularPrice,
		$salePrice,
		$quantity,
		$stockStatus,
		$stockManaging,
		$isBackorder,
		$type,
		$notifyBackorder,
		$imageID,
		$downloadable,
		$virtual,
		$onSale,
		$galleryImgIDs = [],
		$addToCartUrl;
	public function __construct($args) {
		if(is_array($args)) {
			$this->id = $args['ID'];
			$this->name = $args['name'];
			$this->regularPrice = $args['max_price'];
			$this->onSale = $args['on_sale'];
			$this->salePrice = array_key_exists('min_price', $args) ? $args['min_price'] : $this->regularPrice;
			$this->type = $args['type'];
			$this->imageID = $args['image_id'];
			$this->downloadable = $args['downloadable'];
			$this->virtual = $args['virtual'];
			$this->galleryImgIDs = $args['gallery_image_ids'];
			$this->stockStatus = $args['stock_status'];
			$this->stockManaging = $args['stock_managing'];
			$this->quantity = (array_key_exists('quantity', $args)) ? $args['quantity'] : 0;
			$this->isBackorder = (array_key_exists('is_backorder', $args)) ? $args['is_backorder'] : false;
			$this->notifyBackorder = (array_key_exists('notify_backorder', $args)) ? $args['notify_backorder'] : false;

			$this->addToCartUrl = $args['add_to_cart_url'];
		} elseif (is_object($args)) {
			$product = $args;
			$this->id = $product->get_id();
			$this->name = $product->get_name();
			$this->regularPrice = floatval($product->get_regular_price());
			$this->onSale = $product->is_on_sale();
			$this->salePrice = floatval($product->get_sale_price());
			$this->stockStatus = $product->get_stock_status();
			$this->stockManaging = $product->managing_stock();
			$this->quantity = $product->get_stock_quantity();
			$this->type = $product->get_type();
			$this->imageID = intval($product->get_image_id());
			$this->isBackorder = $product->backorders_allowed();
			$this->notifyBackorder = $product->backorders_require_notification();
			$this->downloadable = $product->is_downloadable();
			$this->virtual = $product->is_virtual();
			$this->galleryImgIDs = $product->get_gallery_image_ids();
			$this->addToCartUrl = $product->add_to_cart_url();
		}
	}

	public function getParams() {
		$params = [
			'ID' => $this->id,
			'name' => $this->name,
			'max_price' => $this->regularPrice,
			'on_sale' => $this->onSale,
			'type' => $this->type,
			'image_id' => $this->imageID,
			'downloadable' => $this->downloadable,
			'virtual' => $this->virtual,
			'gallery_image_ids' => $this->galleryImgIDs,
			'stock_status' => $this->stockStatus,
			'stock_managing' => $this->stockManaging,
			'add_to_cart_url' => $this->addToCartUrl
		];
		if($this->stockManaging) {
			$params['quantity'] = $this->quantity;
			$params['is_backorder'] = $this->isBackorder;
			if($params['is_backorder']) {
				$params['notify_backorder'] = $this->notifyBackorder;
			}
		}
		if($this->onSale) {
			$params['min_price'] = $this->salePrice;
		}
		return $params;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_image_id() {
		return $this->imageID;
	}

	public function get_name() {
		return $this->name;
	}

	public function add_to_cart_url() {
		return $this->addToCartUrl;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_gallery_image_ids() {
		return $this->galleryImgIDs;
	}

	public function get_regular_price() {
		return $this->regularPrice;
	}

	public function get_sale_price() {
		return $this->salePrice;
	}

	public function is_on_sale() {
		return $this->onSale;
	}

	public function is_in_stock() {
		return $this->stockStatus == 'instock' || $this->stockStatus == 'onbackorder';
	}

	public function is_downloadable() {
		return $this->downloadable;
	}

	public function is_virtual() {
		return $this->virtual;
	}

	public function managing_stock() {
		return $this->stockManaging > 0;
	}

	public function get_stock_quantity() {
		return $this->quantity;
	}

	public function is_on_backorder($cartQuantity = 0) {
		return $this->stockManaging >= 2;
	}

	public function backorders_allowed() {
		return $this->stockManaging >= 2;
	}

	public function backorders_require_notification() {
		return $this->stockManaging == 3;
	}

	public function is_type($type) {
		return $this->type == $type;
	}
}