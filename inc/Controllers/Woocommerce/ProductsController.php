<?php
namespace Inc\Controllers\Woocommerce;

use Inc\Controllers\Woocommerce\Products\SimpleProductController;
use Inc\Controllers\Woocommerce\Products\VariableProductController;
use Inc\Controllers\Woocommerce\Products\VariationProductController;
use Inc\DAO\Products\ProductsDAO;

class ProductsController {
	private $attrs = [];
	private $renderVariation = false;
	private $products = [];
	private $simpleProductOptions = null,
			$variableProductOptions = null,
			$variationProductOptions = null,
			$externalProductOptions = null,
			$groupedProductOptions = null;


	public function __construct() {
//		add_filter( 'loop_shop_per_page', [$this, 'productsPerPage'], 20 );
//		add_action( 'pre_get_posts', [$this, 'customQueryPostsPerPage'] );
	}

	public function search($search) {
		$this->attrs['s'] = $search;
		return $this;
	}

	public function limit($limit) {
		$this->attrs['limit'] = $limit;
		return $this;
	}

	public function offset($offset) {
		$this->attrs['offset'] = $offset;
		return $this;
	}

	public function page($page) {
		$this->attrs['page'] = $page;
		return $this;
	}

	public function externalOnly() {
		$this->attrs['type'] = 'external';
		return $this;
	}

	public function groupedOnly() {
		$this->attrs['type'] = 'grouped';
		return $this;
	}

	public function simpleOnly() {
		$this->attrs['type'] = 'simple';
		return $this;
	}

	public function variableOnly() {
		$this->attrs['type'] = 'variable';
		return $this;
	}

	public function withVariationRender() {
		$this->renderVariation = true;
		return $this;
	}

	public function withIDs($ids) {
		$this->attrs['include'] = $ids;
		return $this;
	}

	public function withCategory($categories) {
		$category = (is_array($categories)) ? $categories : [$categories];
		$this->attrs['category'] = $category;
		return $this;
	}

	public function withoutIds($ids) {
		$this->attrs['exclude'] = $ids;
		return $this;
	}

	public function orderASC() {
		$this->attrs['order'] = "ASC";
		return $this;
	}

	public function orderDESC() {
		$this->attrs['order'] = "DESC";
		return $this;
	}

	public function orderByName() {
		$this->attrs['orderby'] = 'name';
		return $this;
	}

	public function orderByType() {
		$this->attrs['orderby'] = 'type';
		return $this;
	}

	public function orderByDate() {
		$this->attrs['orderby'] = 'date';
		return $this;
	}

	public function publishOnly() {
		$this->attrs['status'] = 'publish';
		return $this;
	}

	public function withSimpleProductOptions($options) {
		$this->simpleProductOptions = $options;
		return $this;
	}

	public function withVariableProductOptions($options) {
		$this->variableProductOptions = $options;
		return $this;
	}

	public function withVariationProductOptions($options) {
		$this->variationProductOptions = $options;
		return $this;
	}

	public function withExternalProductOptions($options) {
		$this->externalProductOptions = $options;
		return $this;
	}

	public function withGroupedProductOptions($options) {
		$this->groupedProductOptions = $options;
		return $this;
	}

	public function clear() {
		$this->attrs = [];
	}

	public function getProducts($products = null) {
		$customDb = true;
		if(empty($products)) {
			$customDb = false;
			$products = wc_get_products($this->attrs);
		}
		$productsDetails = [];
		$this->products = [];
		foreach ($products as $product) {

			$productController = $this->getProductController($product);
			$this->products[] = $product;
			if($this->renderVariation && $productController->isVariable()) {
				$variationsObjs = [];
				if(!$customDb) {
					foreach ( $product->get_children() as $id ) {
						$variation = new \WC_Product_Variation( $id );
						if ( ! $variation || ! $variation->exists() || ( Tools::hideOutOfStockItems() &&
                                             !$variation->is_in_stock() ) || !$variation->variation_is_visible() ) {
							continue;
						}
						$variationsObjs[] = $variation;
					}
				} else {
					$variationsObjs = $product->get_available_variations( 'objects' );
				}
				$variationControllers = [];
				foreach ($variationsObjs as $obj) {
					$variationControllers[] = $this->getProductController($obj);
				}

				$productController->withVariations($variationControllers);
			}
			$productsDetails[] = $productController->getRenderDetails();
//			}
		}
		$this->clear();
		return $productsDetails;

	}

	public function getProductsFromKBDB() {
		$products = ProductsDAO::getProducts($this->attrs, $this->renderVariation);
		return $this->getProducts($products);
	}

	public function getProductController($product) {
		switch ($product->get_type()) {
			case 'simple':
				return new SimpleProductController($product, $this->simpleProductOptions);
			case 'variable':
				return new VariableProductController($product, $this->variableProductOptions);
			case 'variation':
				return new VariationProductController($product, $this->variationProductOptions);
		}
		return null;
	}
}
