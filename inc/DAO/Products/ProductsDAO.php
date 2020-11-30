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
		$productsVariantsTable = "{$wpdb->base_prefix}kb_products_variants";
		self::removeTable($productsTable);
		self::removeTable($productsInfoTable);
		self::removeTable($productsVariantsTable);
		self::createProductsTable($productsTable);
		self::createProductsInfoTable($productsInfoTable);
		self::createProductsVariantsTable($productsVariantsTable);
		self::insertProductsIntoTable($productsTable, $productsInfoTable, $productsVariantsTable);

	}

	private static function createProductsTable($tableName) {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = " CREATE TABLE `{$tableName}` (
 			ID bigint(20) NOT NULL,
 			gallery_image_ids text,
 			min_sale_price decimal(19,4),
 			max_sale_price decimal(19,4),
 			PRIMARY KEY (ID)
 		) $charset_collate";
		$wpdb->query($sql);
	}

	private static function createProductsInfoTable($tableName) {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = " CREATE TABLE `{$tableName}` (
 			ID bigint(20) NOT NULL,
 			image_id bigint(20),
 			type text,
 			name text,
 			min_price decimal(19,4),
 			max_price decimal(19,4),
 			on_sale tinyint(1),
 			stock_managing tinyint(1),
 			quantity double,
 			stock_status varchar(100),
 			downloadable tinyint(1),
 			virtual tinyint(1),
 			add_to_cart_url text,
 			attributes text,
 			PRIMARY KEY (ID)
 		) $charset_collate";
		$wpdb->query($sql);
	}

	private static function createProductsVariantsTable($tableName) {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = " CREATE TABLE `{$tableName}` (
 			ID bigint(20) NOT NULL,
 			parent_id bigint(20),
 			PRIMARY KEY (ID),
	 		FOREIGN KEY (parent_id) REFERENCES {$wpdb->base_prefix}kb_products(ID)
 		) $charset_collate";
		$wpdb->query($sql);
	}

	private static function removeTable($tableName) {
		global $wpdb;
		$removeTable = "DROP TABLE IF EXISTS $tableName;";
		$wpdb->query($removeTable);
	}

	private static function insertProductsIntoTable($productsTable, $productsInfoTable, $productsVariantsTable) {
		global $wpdb;
		$products = wc_get_products([
			'status' => 'publish',
			'limit' => -1
		]);
		echo count($products);
		foreach ($products as $product) {
			$argsProductInfo = self::getQueryArgs($product);
			$argsProduct = ['ID' => $argsProductInfo['ID'],
                'gallery_image_ids' => implode(',',$argsProductInfo['gallery_image_ids']),
                'min_sale_price' => !empty($argsProductInfo['min_sale_price']) ? $argsProductInfo['min_sale_price'] : null,
                'max_sale_price' => !empty($argsProductInfo['max_sale_price']) ? $argsProductInfo['max_sale_price'] : null
			];
			unset($argsProductInfo['gallery_image_ids']);
			unset($argsProductInfo['min_sale_price']);
			unset($argsProductInfo['max_sale_price']);
			$stockManaging = 0;
			$stockManaging += $argsProductInfo['stock_managing'] ? 1 : 0;
			$stockManaging += ($stockManaging > 0 && $argsProductInfo['is_backorder']) ? 1 : 0;
			$stockManaging += ($stockManaging > 1 && $argsProductInfo['notify_backorder']) ? 1 : 0;
			$argsProductInfo['stock_managing'] = $stockManaging;
			unset($argsProductInfo['is_backorder']);
			unset($argsProductInfo['notify_backorder']);
			$isVariable = ($argsProductInfo['type'] == 'variable');
			$variations = [];
			if($isVariable) {
				$argsProductInfo['attributes'] = json_encode($argsProductInfo['attributes']);
				$variations = $argsProductInfo['variations'];
				unset($argsProductInfo['variations']);
			}
			$wpdb->insert($productsTable, $argsProduct);
			$wpdb->insert($productsInfoTable, $argsProductInfo);
			if($isVariable) {
				foreach ($variations as $variation) {
					$argsVariationTable = ['ID' => $variation['ID'],
					                       'parent_id' => $argsProduct['ID']];
					unset($variation['parent_id']);
					$stockManaging = 0;
					$stockManaging += $variation['stock_managing'] ? 1 : 0;
					$stockManaging += ($stockManaging > 0 && $variation['is_backorder']) ? 1 : 0;
					$stockManaging += ($stockManaging > 1 && $variation['notify_backorder']) ? 1 : 0;
					$variation['stock_managing'] = $stockManaging;
					unset($variation['is_backorder']);
					unset($variation['notify_backorder']);
					unset($variation['gallery_image_ids']);
					$attributes = [];
					foreach ($variation['attributes'] as $key => $value) {
							$attributes[] = "$key:$value";
					}
					$variation['attributes'] = implode(',', $attributes);
					var_dump($variation);
					$wpdb->insert($productsVariantsTable, $argsVariationTable);
					$wpdb->insert($productsInfoTable, $variation);
				}
			}
		}
	}

	private static function getQueryArgs($product) {
		$type = $product->get_type();
		$obj = null;
		if($type == 'variable') {
			$obj = new VariableProduct($product);
		} else if($type == 'variation') {
			$obj = new VariationProduct($product);
		} else {
			$obj = new Product($product);
		}
		return $obj->getParams();
	}

	public static function getProducts($args) {
		global $wpdb;
		$productsQuery = $wpdb->get_results("
					SELECT *
					FROM {$wpdb->base_prefix}kb_products
					INNER JOIN {$wpdb->base_prefix}kb_products_info
					ON {$wpdb->base_prefix}kb_products.ID = {$wpdb->base_prefix}kb_products_info.ID;
				", "ARRAY_A");
		$productsArgs = [];
		$variableIDs = [];
		foreach ($productsQuery as $productArg) {
			$productsArgs[$productArg['ID']] = self::parseBasicProductInfo($productArg);
			if($productArg['type'] === 'variable') {
				$variableIDs[] = $productArg['ID'];
			}
		}
		$variableIDs = implode("','", $variableIDs);
		$variationsQuery = $wpdb->get_results("
					SELECT *
					FROM {$wpdb->base_prefix}kb_products_variants AS variants
					INNER JOIN {$wpdb->base_prefix}kb_products_info
					ON variants.ID = {$wpdb->base_prefix}kb_products_info.ID
					WHERE variants.parent_id IN ('{$variableIDs}')
					ORDER BY variants.parent_id;
				", "ARRAY_A");
		foreach ($variationsQuery as $variationArgs) {
			$productsArgs[$variationArgs['parent_id']]['variations'][] = self::parseBasicProductInfo($variationArgs);
		}


		$products = [];
		foreach ($productsArgs as $key => $productArgs) {
			$products[] = self::getProduct($productArgs);
		}
		return $products;
	}

	private static function getProduct($args) {
		switch ($args['type']) {
			case 'variable':
				return new VariableProduct($args);
			case 'simple':
			default:
				return new Product($args);
		}
	}

	private static function parseBasicProductInfo($args) {
		$product = [];
		$product['ID'] = $args['ID'];
		$product['name'] = $args['name'];
		$product['max_price'] = $args['max_price'];
		$product['min_price'] = !empty($product['min_price']) ? $args['min_price'] : $args['max_price'];
		$product['type'] = $args['type'];
		$product['on_sale'] = ($args['on_sale'] == 1);
		$product['image_id'] = $args['image_id'];
		$product['downloadable'] = $args['downloadable'];
		$stockManaging = $args['stock_managing'];
		$product['stock_managing'] = $stockManaging > 0;
		$product['is_backorder'] = $stockManaging > 1;
		$product['notify_backorder'] = $stockManaging > 2;
		$product['stock_status'] = $args['stock_status'];
		$product['quantity'] = $args['quantity'];
		$product['virtual'] = $args['virtual'];
		$product['add_to_cart_url'] = $args['add_to_cart_url'];

		if($product['type'] == 'variable' || $product['type'] == 'variation') {

		}
		if($product['type'] != 'variation') {
			$product['gallery_image_ids'] = (!empty($args['gallery_image_ids'])) ?
				explode(',', $args['gallery_image_ids']) : [];
		} else {
			$product['parent_id'] = $args['ID'];
			$attrs = explode(',', $args['attributes']);
			$product['attributes'] = [];
			foreach ($attrs as $attr) {
				$attr = explode(':', $attr);
				if(count($attr) < 2) continue;
				$product['attributes'][$attr[0]] = $attr[1];
			}
		}

		if($product['type'] == 'variable') {
			$product['attributes'] = json_decode($args['attributes']);
			$product['variations'] = [];
			$product['max_sale_price'] = !empty($args['max_sale_price']) ? $args['max_sale_price'] : $args['max_price'];
			$product['min_sale_price'] = !empty($args['min_sale_price']) ? $args['min_sale_price'] : $args['min_price'];
		}
		return $product;
	}
}