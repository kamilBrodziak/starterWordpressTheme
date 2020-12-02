<?php


namespace Inc\Models\Products;


class Product {
	private $id,
		$name = 'Unnammed',
		$regularPrice,
		$salePrice,
		$quantity = 0,
		$stockStatus = 'instock',
		$stockManaging = false,
		$isBackorder = false,
		$type = 'simple',
		$notifyBackorder = false,
		$imageID = 0,
		$downloadable = false,
		$virtual = false,
		$onSale = false,
		$galleryImgIDs = [],
		$addToCartUrl = "",
		$categoryIDs = [],
		$shortDescription = "",
		$description = "",
		$crossSellIDS = [],
		$upsellIDs = [],
		$soldIndividually = false,
		$weight = 0,
		$dimensions = "",
		$dateCreated = null,
		$tagIDs = [];
	public function __construct($id) {
		$this->id = $id;
	}

	public function get_tag_ids() {
		return $this->tagIDs;
	}

	public function withTagIDs($tagIDs) {
		$this->tagIDs = $tagIDs;
		return $this;
	}

	public function get_date_created() {
		return $this->dateCreated;
	}

	public function withDateCreated($dateCreated) {
		$this->dateCreated = $dateCreated;
		return $this;
	}

	public function get_weight() {
		return $this->weight;
	}

	public function withWeight($weight) {
		$this->weight = $weight;
		return $this;
	}

	public function get_dimensions() {
		return $this->dimensions;
	}

	public function withDimensions($dimensions) {
		$this->dimensions = $dimensions;
		return $this;
	}

	public function get_upsell_ids() {
		return $this->upsellIDs;
	}

	public function get_cross_sell_ids() {
		return $this->crossSellIDS;
	}

	public function is_sold_individually() {
		return $this->soldIndividually;
	}

	public function get_sold_individually() {
		return $this->is_sold_individually();
	}

	public function withSoldIndividually($isSold) {
		$this->soldIndividually = $isSold;
		return $this;
	}

	public function withUpsellIDs($ids) {
		$this->upsellIDs = $ids;
		return $this;
	}

	public function withCrossSellIDs($ids) {
		$this->crossSellIDS = $ids;
		return $this;
	}

	public function get_category_ids() {
		return $this->categoryIDs;
	}

	public function withCategoryIDs($categoryIDs) {
		$this->categoryIDs = $categoryIDs;
		return $this;
	}

	public function get_description() {
		return $this->description;
	}

	public function withDescription($description) {
		$this->description = $description;
		return $this;
	}

	public function get_short_description() {
		return $this->shortDescription;
	}

	public function withShortDescription($description) {
		$this->shortDescription = $description;
		return $this;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_image_id() {
		return $this->imageID;
	}

	public function withImageID($id) {
		$this->imageID = $id;
		return $this;
	}

	public function get_name() {
		return $this->name;
	}

	public function withName($name) {
		$this->name = $name;
		return $this;

	}

	public function add_to_cart_url() {
		return $this->addToCartUrl;
	}

	public function withAddToCartUrl($url) {
		$this->addToCartUrl = $url;
		return $this;
	}

	public function get_type() {
		return $this->type;
	}

	public function withType($type) {
		$this->type = $type;
		return $this;
	}

	public function get_gallery_image_ids() {
		return $this->galleryImgIDs;
	}

	public function withGalleryImageIDs($ids) {
		$this->galleryImgIDs = $ids;
		return $this;
	}

	public function get_regular_price() {
		return $this->regularPrice;
	}

	public function withRegularPrice($price) {
		$this->regularPrice = $price;
		$this->salePrice = $price;
		return $this;
	}

	public function get_sale_price() {
		return $this->salePrice;
	}

	public function withSalePrice($price) {
		$this->salePrice = $price;
		return $this;
	}

	public function is_on_sale() {
		return $this->onSale;
	}

	public function withOnSale($onSale) {
		$this->onSale = $onSale;
		return $this;
	}

	public function is_in_stock() {
		return $this->stockStatus == 'instock' || $this->stockStatus == 'onbackorder';
	}

	public function withStockStatus($status) {
		$this->stockStatus = $status;
		return $this;
	}

	public function is_downloadable() {
		return $this->downloadable;
	}

	public function withDownloadable($isDownloadable) {
		$this->downloadable = $isDownloadable;
		return $this;
	}

	public function is_virtual() {
		return $this->virtual;
	}

	public function withVirtual($isVirtual) {
		$this->virtual = $isVirtual;
		return $this;
	}

	public function managing_stock() {
		return $this->stockManaging;
	}

	public function withManagingStock($managingStock) {
		$this->stockManaging = $managingStock;
		return $this;
	}

	public function get_stock_quantity() {
		return $this->quantity;
	}

	public function withQuantity($stockQuantity) {
		$this->quantity = $stockQuantity;
		return $this;
	}

	public function is_on_backorder($cartQuantity = 0) {
		return $this->isBackorder;
	}

	public function backorders_allowed() {
		return $this->isBackorder;
	}

	public function withBackordersAllowed($isBackorder) {
		$this->isBackorder = $isBackorder;
		return $this;
	}

	public function backorders_require_notification() {
		return $this->notifyBackorder;
	}

	public function withBackordersNotifications($notify) {
		$this->notifyBackorder = $notify;
		return $this;
	}

	public function is_type($type) {
		return $this->type == $type;
	}
}