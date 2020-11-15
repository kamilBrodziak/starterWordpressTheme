<?php
namespace Inc\Controllers\Woocommerce;

class ProductsController {
//	private $productsPerPage = 20;
	private $attrs = [];

	public function __construct() {
//		add_filter( 'loop_shop_per_page', [$this, 'productsPerPage'], 20 );
//		add_action( 'pre_get_posts', [$this, 'customQueryPostsPerPage'] );
	}

//	function customQueryPostsPerPage( $query ) {
//		if ( $query->is_main_query() && !is_admin() ) {
//			$query->set( 'posts_per_page', $this->productsPerPage());
//		}
//	}
//	function productsPerPage( $cols = 0 ) {
//		return $this->productsPerPage;
//	}

	public function search($search) {
		$this->attrs['s'] = $search;
		return $this;
	}

	public function categories($category) {
		if(is_array($category)) {
			$this->attrs['category'] = $category;
		} else {
			$this->attrs['category'] = [$category];
		}
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

	public function withIDs($ids) {
		$this->attrs['include'] = $ids;
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

	public function clear() {
		$this->attrs = [
			'status' => 'publish'
		];
	}

	public function getProducts() {
		$products = wc_get_products($this->attrs);
		$productsDetails = [];
		foreach ($products as $product) {
			$product = new ProductController($product);
			$productsDetails[] = $product->getProductRenderDetails();
		}
		$this->clear();
		return $productsDetails;
	}
}
