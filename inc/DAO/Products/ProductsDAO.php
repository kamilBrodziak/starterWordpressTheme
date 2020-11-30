<?php


namespace Inc\DAO\Products;

use Inc\Models\Products\Product;
use Inc\Models\Products\VariableProduct;
use Inc\Models\Products\VariationProduct;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

class ProductsDAO {
	public static function createTables() {
		global $wpdb;
		$productsTable = "{$wpdb->base_prefix}kb_products";
		$productsInfoTable = "{$wpdb->base_prefix}kb_products_info";
		self::removeTable($productsTable);
		self::removeTable($productsInfoTable);
		self::createProductsTable($productsTable);
		self::createProductsInfoTable($productsInfoTable);
		self::insertProductsIntoTable($productsTable, $productsInfoTable);

	}

	private function createProductsTable($tableName) {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = " CREATE TABLE `{$tableName}` (
 			ID bigint(20) NOT NULL,
 			type text,
 			PRIMARY KEY (ID)
 		) $charset_collate";
		$wpdb->query($sql);
	}

	private function createProductsInfoTable($tableName) {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = " CREATE TABLE `{$tableName}` (
 			ID bigint(20) NOT NULL,
 			image_id bigint(20),
 			title text,
 			min_price decimal(19,4),
 			max_price decimal(19,4),
 			onsale tinyint(1),
 			stock_managing tinyint(1),
 			stock_quantity double,
 			stock_status varchar(100),
 			downloadable tinyint(1),
 			virtual tinyint(1),
 			parent_id bigint(20),
 			PRIMARY KEY (ID)
 		) $charset_collate";
		$wpdb->query($sql);
	}

	private function removeTable($tableName) {
		global $wpdb;
		$removeTable = "DROP TABLE IF EXISTS $tableName;";
		$wpdb->query($removeTable);
	}

	private function insertProductsIntoTable($productsTable, $productsInfoTable) {
		global $wpdb;
		$products = wc_get_products([
			'status' => 'publish',
			'limit' => -1
		]);
		echo count($products);
		foreach ($products as $product) {
			$argsProductsInfo = self::getQueryArgs($product);
			$argsProduct = ['ID' => $argsProductsInfo['ID'], 'type' => $argsProductsInfo['type']];
			$isVariable = $argsProductsInfo['type'] == 'variable';
			$variations = [];
			if($isVariable) {
				$variations = self::getVariations($product);
				if(isset($variations['min_price'])) {
					$argsProductsInfo['min_price'] = $variations['min_price'];
				}
				if(isset($variations['max_price'])) {
					$argsProductsInfo['max_price'] = $variations['max_price'];
				}
			}
			unset($argsProductsInfo['type']);
			$wpdb->insert($productsTable, $argsProduct);
			$wpdb->insert($productsInfoTable, $argsProductsInfo);
			if($isVariable) {
				foreach ($variations['args'] as $argsVariationInfo) {
//					$argsVariation = ['ID' => $argsVariationInfo['ID'], 'parent_id' => $argsProductsInfo['ID']];
//					$wpdb->insert($productsTable, $argsVariation);
					if($argsVariationInfo['type'] === 'variation') {
						$argsVariationInfo['parent_id'] = $argsProductsInfo['ID'];
					}
					unset($argsVariationInfo['type']);
					$wpdb->insert($productsInfoTable, $argsVariationInfo);
				}
			}
		}
	}

	private static function getVariations($product) {
		$variations = [
			'args' => [],
		];
		$minPrice = -1;
		$maxPrice = -1;
		$ind = 0;
		foreach ($product->get_available_variations('objects') as $variation) {
			$variationAttrs = $variation->get_variation_attributes();
			$variationTitle = $product->get_name() . ' - ';
			$variationAttrsTitles = array_keys($product->get_variation_attributes());
			$jind = 0;
			foreach ($variationAttrs as $key => $value) {
				$variationTitle .= $variationAttrsTitles[$jind] . ':' . $value;
				if($jind != count($variationAttrs) - 1) {
					$variationTitle .= ';';
				}
				$jind++;
			}

			$args = self::getQueryArgs($variation);
			$args['title'] = $variationTitle;
			if($ind == 0) {
				$minPrice = (isset($args['min_price'])) ? $args['min_price'] : $args['max_price'];
				$maxPrice = $args['max_price'];
				$ind++;
			} else {
				$min = (isset($args['min_price'])) ? $args['min_price'] : $minPrice;
				if($min < $minPrice) {
					$minPrice = $min;
				}
				if($args['max_price'] > $maxPrice) {
					$maxPrice = $args['max_price'];
				}
			}
			$variations['args'][] = $args;
		}
		if($minPrice > -1 && $minPrice != $maxPrice) {
			$variations['min_price'] = $minPrice;
		}
		if($maxPrice > -1) {
			$variations['max_price'] = $maxPrice;
		}
		return $variations;
	}

	private static function getQueryArgs($product) {
		$args = [];
		$args['ID'] = $product->get_id();
		$args['image_id'] = intval($product->get_image_id());
		$args['title'] = $product->get_name();
		$args['type'] = $product->get_type();
		$args['onsale'] = $product->is_on_sale() ? 1 : 0;
		$args['max_price'] = floatval($product->get_regular_price());
		if($args['onsale']) {
			$args['min_price'] = floatval($product->get_sale_price());
		}

		$args['stock_status'] = $product->get_stock_status();
		$stockManaging = ($product->managing_stock()) ? 1 : 0;
		if($stockManaging == 1) {
			$args['stock_quantity'] = $product->get_stock_quantity();
			$stockManaging = $product->backorders_allowed() ? 2 : 1;
			if($stockManaging == 2) {
				$stockManaging = $product->backorders_require_notification() ? 3 : 2;
			}
		}
		$args['stock_managing'] = $stockManaging;
		$args['downloadable'] = $product->is_downloadable() ? 1 : 0;
		$args['virtual'] = $product->is_virtual() ? 1 : 0;
		return $args;
	}

	public static function getProducts($args) {
		global $wpdb;
		$productsArgs = $wpdb->get_results("
				SELECT * 
				FROM {$wpdb->base_prefix}kb_products 
				INNER JOIN {$wpdb->base_prefix}kb_products_info
				ON {$wpdb->base_prefix}kb_products.ID = {$wpdb->base_prefix}kb_products_info.ID;
				", "ARRAY_A");
		$products = [];
		foreach ($productsArgs as $productArgs) {
			if($productArgs['type'] === 'variable') {
				$id = $productArgs['ID'];
				$variationsArgs = $wpdb->get_results("
					SELECT *
					FROM {$wpdb->base_prefix}kb_products_info
					WHERE parent_id = $id;
				", 'ARRAY_A');
//				if(count($variationsArgs)) {
					$productArgs['variations'] = $variationsArgs;
					$products[] = new VariableProduct($productArgs);
//				}
			} else {
				$products[] = new Product($productArgs);
			}
		}
		return $products;
	}
}