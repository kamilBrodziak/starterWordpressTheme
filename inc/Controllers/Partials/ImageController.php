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
		return ['src' => wp_get_attachment_url($this->id)];
	}

	public function getImageRender($onlySrcSet = false) {
		$logo = [
			'sizes' => [],
			'alt' => get_post_meta( $this->id, '_wp_attachment_image_alt', true)
		];
		$attachment = wp_get_attachment_metadata($this->id);
		$isArray = is_array($attachment);
		$sizes = $isArray ? $attachment['sizes'] : [];
		$sizesEmpty = empty($sizes);
		$srcSet = "";
		$imgUrl = wp_get_attachment_url($this->id);
		$ind = 0;
		if(!$sizesEmpty) {
			uasort($sizes, function ($a, $b) {return $a['width'] - $b['width'];});
			foreach ($sizes as $size => $args) {
				if(!in_array($size, $this->imageSizes)) {
					unset($sizes[$size]);
				}
			}
			$baseName = wp_basename( $imgUrl );
			$baseUrl = str_replace($baseName, '', $imgUrl);
			foreach($sizes as $size => $args) {
				$url = "${baseUrl}${args['file']}";
				$srcSet .= (($ind++ > 0) ? ',' : '') . "${url} ${args['width']}w";
				if(!$onlySrcSet) {
					$logo['sizes'][$size] = ['src' => $url];
					if($args['width']) {
						$logo['sizes'][$size]['width'] = "${args['width']}px";
					}
				}
			}
		}
		$srcSet .= (($ind > 0) ? ',' : '') . "${imgUrl}";
		if(!$onlySrcSet) {
			$logo['sizes']['full'] = ['src' => $imgUrl];
		}
		$logo['srcSet'] = $srcSet;
		$this->clear();
		return $logo;
	}
}