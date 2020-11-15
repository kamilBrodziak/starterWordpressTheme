<?php
namespace Inc\Controllers\Partials;

class ImageController {
	private $imageSizes = [];
	private $id;
	public function __construct($id) {
		$this->id = $id;
	}

	public function withThumbnail() {
		$this->imageSizes[] = 'thumbnail';
		return $this;
	}

	public function withProductTease() {
		$this->imageSizes[] = 'product_tease';
		return $this;
	}

	public function withMobile() {
		$this->imageSizes[] = 'mobile';
		return $this;
	}

	public function withMedium() {
		$this->imageSizes[] = 'medium';
		return $this;
	}

	public function withLarge() {
		$this->imageSizes[] = 'large';
		return $this;
	}

	public function withMediumLarge() {
		$this->imageSizes[] = 'medium_large';
		return $this;
	}

	public function withVeryLarge() {
		$this->imageSizes[] = 'very_large';
		return $this;
	}

	public function withLargest() {
		$this->imageSizes[] = 'largest';
		return $this;
	}

	public function clear() {
		$this->imageSizes = [];
	}

	public function getImageFullSrc() {
		return ['src' => wp_get_attachment_image_url( $this->id, 'full')];
	}

	public function getImageRender($onlySrcSet = false) {
		$logo = [
			'sizes' => [],
			'alt' => get_post_meta( $this->id, '_wp_attachment_image_alt', true)
		];
		$allSizes = wp_get_attachment_metadata($this->id)['sizes'];
		$sizes = [];
		foreach ($allSizes as $size => $args) {
			if(in_array($size, $this->imageSizes)) {
				$sizes[$size] = $args;
			}
		}
		uasort($sizes, function ($a, $b) {return $a['width'] - $b['width'];});
		$sizes['full'] = ['width' => false];
		$srcSet = [];
		foreach ($sizes as $size => $args) {
			$url = wp_get_attachment_image_url( $this->id, $size);
			$srcSet[] = "${url} ${args['width']}w";
			if(!$onlySrcSet) {
				$logo['sizes'][$size] = [];
				$logo['sizes'][$size]['src'] = $url;
				if($args['width']) {
					$logo['sizes'][$size]['width'] = "${args['width']}px";
				}
			}
		}
		$logo['srcSet'] = join(",", $srcSet);
		$this->clear();
		return $logo;
	}
}