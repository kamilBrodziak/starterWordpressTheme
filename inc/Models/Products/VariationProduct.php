<?php


namespace Inc\Models\Products;


class VariationProduct extends Product {
	private $attrs,
		$parentID = null;
	public function __construct( $id) {
		parent::__construct( $id );
	}

	public function getParams() {
		$params = parent::getParams();
		$params['attributes'] = $this->attrs;
		return $params;
	}

	public function get_parent_id() {
		return $this->parentID;
	}

	public function withParentID($id) {
		$this->parentID = $id;
		return $this;
	}

	public function get_variation_attributes() {
		return $this->attrs;
	}

	public function withVariationAttributes($attrs) {
		$this->attrs = $attrs;
		return $this;
	}
}