<?php
namespace Inc\Controllers\Woocommerce;

class CartController {

	public static function addProduct($id, $quantity, $variationID = null) {
		if($variationID) {
			WC()->cart->add_to_cart((int)$id, (int)$quantity, $variationID);
		} else {
			WC()->cart->add_to_cart((int)$id, (int)$quantity);
		}
	}

	public static function removeProduct($key) {
		WC()->cart->remove_cart_item($key);
	}

	public static function changeQuantity($key, $quantity) {
		WC()->cart->set_quantity($key, $quantity);
	}

	public static function getCartItemQuantity() {
		return WC()->cart->get_cart_contents_count();
	}

	public static function applyCoupon($coupon) {
		return WC()->cart->apply_coupon($coupon);
	}

	public static function getCartRenderDetails() {
		$cartItems = WC()->cart->get_cart();
		$details = [
			'products' => []
		];
		foreach ($cartItems as $item => $values) {
			if(isset($values['variation_id']) && !empty($values['variation_id'])) {
				$productsController = new ProductController($values['variation_id']);
			} else {
				$productsController = new ProductController($values['product_id']);
			}
			$productDetails = $productsController->getProductRenderDetails();
			$productDetails['stock']['quantity'] = $values['quantity'];
			$productDetails['url'] = [
				'productPage' => get_permalink($values['product_id']),
				'removeFromCart' => wc_get_cart_remove_url($values['key'])
			];

			$details['products'] = $productDetails;
		}
		$details['total'] = self::getCartTotal();
		return $details;
	}

	public static function getCartTotal() {
		return WC()->cart->get_subtotal();
	}
}