<?php


namespace Inc\Models\Products;


class VariationProduct extends Product {
	private $attrs;
	public function __construct( $args) {
		parent::__construct( $args );
		if(is_array($args)) {
			$this->attrs = $args['attributes'];
		} elseif (is_object($args)) {
			$this->attrs = $args->get_variation_attributes();
		}
//		$attrs = explode(';', explode(' - ', $this->get_name())[1]);
//		foreach ($attrs as $attr) {
//			$attr = explode(":", $attr);
//			$this->attrs[$attr[0]] = $attr[1];
//		}
	}

	public function getParams() {
		$params = parent::getParams();
		$params['attributes'] = $this->attrs;
		return $params;
	}

	public function get_variation_attributes() {
		return $this->attrs;
	}

	public function setAttributes($attrs) {
		$this->attrs = $attrs;
	}
}