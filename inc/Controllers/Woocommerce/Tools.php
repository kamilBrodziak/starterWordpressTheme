<?php


namespace Inc\Controllers\Woocommerce;


final class Tools {
	private static $currency = null;
	private static $priceFormat = null;
	private static $decimalSeparator = null;
	private static $thousandSeparator = null;
	private static $decimals = null;
	private static $hideOutOfStock = null;
	private static $hideInvisible = null;
	public static function formatPrice($price) {
		$price = floatval($price);
		if($price < 0) $price *= -1;
		if(is_null(self::$currency)) {
			self::$currency = get_woocommerce_currency_symbol();
			self::$priceFormat = get_woocommerce_price_format();
			self::$decimalSeparator = wc_get_price_decimal_separator();
			self::$thousandSeparator = wc_get_price_thousand_separator();
			self::$decimals = wc_get_price_decimals();
		}
		$price = number_format($price, self::$decimals, self::$decimalSeparator, self::$thousandSeparator);

		$price = sprintf(self::$priceFormat, self::$currency, $price);
		return $price;
	}

	public static function hideOutOfStockItems() {
		if(is_null(self::$hideOutOfStock)) {
			self::$hideOutOfStock = 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' );
		}
		return self::$hideOutOfStock;
	}

	public static function hideInvisibleVariations() {
		if(is_null(self::$hideInvisible)) {
//			self::$hideInvisible = 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' );
			self::$hideInvisible = true;
		}
		return self::$hideInvisible;
	}
}