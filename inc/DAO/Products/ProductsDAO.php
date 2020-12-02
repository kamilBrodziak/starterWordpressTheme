<?php
namespace Inc\DAO\Products;

use Inc\Models\Products\Product;
use Inc\Models\Products\VariableProduct;
use Inc\Models\Products\VariationProduct;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

class ProductsDAO {
	private static $productsTable = "wp_kb_products";
	private static $productsInfoTable = "wp_kb_products_info";
	private static $productsVariantsTable = "wp_kb_products_variants";
	public static function createTables() {
		self::removeTable(self::$productsTable);
		self::removeTable(self::$productsInfoTable);
		self::removeTable(self::$productsVariantsTable);
		self::createProductsTable();
		self::createProductsInfoTable();
		self::createProductsVariantsTable();
		self::insertProductsIntoTable();
	}

	private static function createProductsTable() {
		$name = self::$productsTable;
		$sql = " CREATE TABLE `{$name}` (
 			ID bigint(20) NOT NULL,
 			status text,
 			date date,
 			category_names text,
 			category_ids text,
 			short_description text,
 			description text,
 			cross_sell_ids text,
 			upsell_ids text,
 			gallery_image_ids text,
 			min_regular_price decimal(19,4),
 			max_regular_price decimal(19,4),
 			min_sale_price decimal(19,4),
 			max_sale_price decimal(19,4),
 			sold_individually tinyint(1),
 			tag_ids text,
 			PRIMARY KEY (ID)
 		)";
		self::createTable($sql);
	}

	private static function createTable($sql) {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql .= " $charset_collate";
		$wpdb->query($sql);

	}

	private static function createProductsInfoTable() {
		$name = self::$productsInfoTable;
		$sql = " CREATE TABLE `{$name}` (
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
 			weight text,
			dimensions text, 			
 			PRIMARY KEY (ID)
 		)";
		self::createTable($sql);
	}

	private static function createProductsVariantsTable() {
		$name = self::$productsVariantsTable;
		$sql = " CREATE TABLE `{$name}` (
 			ID bigint(20) NOT NULL,
 			parent_id bigint(20),
 			PRIMARY KEY (ID)
 		)";
		self::createTable($sql);
	}

	private static function removeTable($tableName) {
		global $wpdb;
		$removeTable = "DROP TABLE IF EXISTS $tableName;";
		$wpdb->query($removeTable);
	}

	private static function getProductArrayForInfoTable($product) {
		$productInfoTableDetails = [
			'ID' => $product->get_id(),
			'image_id' => $product->get_image_id(),
			'type' => $product->get_type(),
			'name' => $product->get_name(),
			'max_price' => floatval($product->get_regular_price()),
			'on_sale' => $product->is_on_sale(),
			'downloadable' => $product->is_downloadable() ? 1 : 0,
			'virtual' => $product->is_virtual() ? 1 : 0,
			'stock_status' => $product->is_in_stock()
		];
		$managingStock = $product->managing_stock() ? 1 : 0;
		if($managingStock) {
			$managingStock += $product->backorders_allowed() ? 1 : 0;
			$managingStock += $product->backorders_require_notification() ? 1 : 0;
			$productInfoTableDetails['quantity'] = $product->get_stock_quantity();
		}
		$productInfoTableDetails['stock_managing'] = $managingStock;
		if($productInfoTableDetails['on_sale']) {
			$productInfoTableDetails['min_price'] = floatval($product->get_sale_price());
		}
		if($productInfoTableDetails['type'] != 'variable') {
			$productInfoTableDetails['add_to_cart_url'] = $product->add_to_cart_url();
		}
		if($productInfoTableDetails['type'] == 'variable' || $productInfoTableDetails['type'] == 'variation') {
			$productInfoTableDetails['attributes'] = json_encode($product->get_variation_attributes());
		}
		$weight = $product->get_weight();
		if(!empty($weight)) {
			$productInfoTableDetails['weight'] = $weight;
		}
		$dimensions = $product->get_dimensions();
		if(!empty($dimensions) && $dimensions != 'N/A') {
			$productInfoTableDetails['dimensions'] = $dimensions;
		}
		return $productInfoTableDetails;
	}

	private static function getProductArrayForProductTable($product) {
		$details = [
			'ID' => $product->get_id(),
			'date' => $product->get_date_created(),
			'status' => $product->get_status(),
			'sold_individually' => $product->is_sold_individually() ? 1 : 0,
			'description' => $product->get_description(),
			'short_description' => $product->get_short_description()
		];

		$categoryIDs = $product->get_category_ids();
		$crossSellIDs = $product->get_cross_sell_ids();
		if(!empty($crossSellIDs)) {
			$details['cross_sell_ids'] = json_encode($crossSellIDs);
		}
		$upsellIDs = $product->get_upsell_ids();
		if(!empty($upsellIDs)) {
			$details['upsell_ids'] = json_encode($upsellIDs);
		}
		if(!empty($categoryIDs)) {
			$categoryNames = [];
			usort($categoryIDs, function($a, $b) {return $a - $b; });
			foreach ($categoryIDs as $id) {
				$categoryNames[] = get_term_by( 'id', $id, 'product_cat' )->name;
			}
			$details['category_names'] = json_encode($categoryNames);
			$details['category_ids'] = json_encode($categoryIDs);

		}
		$tagIDs = $product->get_tag_ids();
		if(!empty($tagIDs)) {
			$details['tag_ids'] = json_encode($tagIDs);
		}
		$galleryImgIds = $product->get_gallery_image_ids();
		if(!empty($galleryImgIds)) {
			$details['gallery_image_ids'] = json_encode($galleryImgIds);
		}
		if($product->is_type('variable')) {
			$details['max_regular_price'] = floatval($product->get_variation_regular_price('max'));
			$minRegularPrice = floatval($product->get_variation_regular_price('min'));
			if($minRegularPrice != $details['max_regular_price']) {
				$details['min_regular_price'] = $minRegularPrice;
			}

			if($product->is_on_sale()) {
				$details['max_sale_price'] = floatval($product->get_variation_sale_price('max'));
				$minSalePrice = floatval($product->get_variation_sale_price('min'));
				if($minSalePrice != $details['max_sale_price']) {
					$details['min_sale_price'] = $minSalePrice;
				}
			}
		}
		return $details;
	}

	private static function getProductArrayForVariantTable($product) {
		$details = [
			'ID' => $product->get_id(),
			'parent_id' => $product->get_parent_id()
		];

		return $details;
	}

	public static function insertProductIntoTable($product) {
		global $wpdb;
		if(is_int($product)) $product = wc_get_product($product);
		if(!is_object($product)) return;
		$variationIDs = [];
		if($product->is_type('variable')) {
			foreach ($product->get_available_variations('objects') as $variation) {
				$variationIDs[] = $variation->get_id();
				$wpdb->replace(self::$productsVariantsTable, self::getProductArrayForVariantTable($variation));
				$wpdb->replace(self::$productsInfoTable, self::getProductArrayForInfoTable($variation));
			}
		}
		$wpdb->replace(self::$productsTable, self::getProductArrayForProductTable($product));
		$wpdb->replace(self::$productsInfoTable, self::getProductArrayForInfoTable($product));
		return $variationIDs;
	}

	public static function updateProductInTable($product) {
		global $wpdb;
		if(is_int($product)) $product = wc_get_product($product);
		if(!is_object($product)) return;
		$variantsTable = self::$productsVariantsTable;
		$infoTable = self::$productsInfoTable;
		$variationIDs = self::insertProductIntoTable($product);
		if(!empty($variationIDs)) {
			$wpdb->query("DELETE FROM `${variantsTable}` WHERE ID NOT IN (" . implode(",", $variationIDs) . ")");
			$wpdb->query("DELETE FROM `${$infoTable}` WHERE ID NOT IN (" . implode(",", $variationIDs) . ")");
		}
	}

	public static function removeProductInTable($id) {
		global $wpdb;
		$variantsTable = self::$productsVariantsTable;
		$infoTable = self::$productsInfoTable;
		$productTable = self::$productsTable;
		$wpdb->delete($variantsTable, ['parent_id' => $id]);
		$wpdb->delete($infoTable, ['ID' => $id]);
		$wpdb->delete($productTable, ['ID' => $id]);
	}

	private static function insertProductsIntoTable() {
		$products = wc_get_products([
			'limit' => -1
		]);
		foreach ($products as $product) {
			self::insertProductIntoTable($product);

		}
	}

	private static function getQuerySorting($args) {
		if(empty($args)) return "";
		$order = "";
		if(!empty($args['orderby'])) {
			$order = "ORDER BY ${args['orderby']} ";
			$order .= (!empty($args['order'])) ? $args['order'] : "ASC";
		}

		$where = [];
		if(!empty($args['status'])) {
			$where[] = "status='${args['status']}'";
		}

		if(!empty($args['category'])) {
			$where[] = "category_names LIKE %" . implode("%", $args['category']) . "%";
		}

		if(!empty($args['exclude_category'])) {
			$where[] = "category_names NOT LIKE %" . implode("%", $args['category']) . "%";
		}

		if(!empty($args['s'])) {
			$where[] = "name LIKE '${args['s']}'";
		}

		if(!empty($args['type'])) {
			$where[] = "type='${args['type']}'";
		}

		$where = (!empty($where)) ? "WHERE " . implode(" AND ", $where) : "";
		$limit = "";
		$offset = "";
		if(!empty($args['offset'])) {
			$offset = "OFFSET ${args['offset']}";
		}
		if(!empty($args['limit']) && $args['limit'] != -1) {
			$limit = "LIMIT ${args['limit']}";
			if(!empty($args['page']) && $args['page'] > 1) {
				$offset = "OFFSET " . ($args['page'] - 1) * $args['limit'];
			}
		}

		return "$where $order $limit $offset";
	}

	public static function getProducts($args, $withVariations = false) {
		global $wpdb;
		$productsTable = self::$productsTable;
		$querySorting = self::getQuerySorting($args);
		$productsInfoTable = self::$productsInfoTable; $variantsTable = self::$productsVariantsTable;
		$productsQuery = $wpdb->get_results("
				SELECT *
				FROM {$productsTable} as products
				INNER JOIN {$productsInfoTable} as info
				ON products.ID = info.ID $querySorting;", "ARRAY_A");
		$products = [];
		$variableIDs = [];
		foreach ($productsQuery as $productArg) {
			$products[$productArg['ID']] = self::getProductModel($productArg);
			if($productArg['type'] === 'variable') {
				$variableIDs[] = $productArg['ID'];
			}
		}
		if($withVariations) {
			$variableIDs = implode("','", $variableIDs);
			$variationsQuery = $wpdb->get_results("
					SELECT *
					FROM {$variantsTable} AS variants
					INNER JOIN {$productsInfoTable} as info
					ON variants.ID = info.ID
					WHERE variants.parent_id IN ('{$variableIDs}')
					ORDER BY variants.parent_id;
				", "ARRAY_A");
			foreach ($variationsQuery as $variationArgs) {
				$product = $products[$variationArgs['parent_id']];
				if(!is_null($product) && $product->is_type('variable')) {
					$product->addVariation(self::getProductModel($variationArgs));
				}
			}
		}
		return array_values($products);
	}

	public static function getProduct($id, $withVariations = false) {
		global $wpdb;
		$productQuery = $wpdb->get_results("
					SELECT *
					FROM {$wpdb->base_prefix}kb_products as products
					WHERE products.ID = {$id}
					INNER JOIN {$wpdb->base_prefix}kb_products_info
					ON products.ID = {$wpdb->base_prefix}kb_products_info.ID;
				", "ARRAY_A")[0];
		$product = self::getProductModel($productQuery);
		if($withVariations && $product->is_type('variable')) {
			$variationsQuery = $wpdb->get_results("
					SELECT *
					FROM {$wpdb->base_prefix}kb_products_variants AS variants
					WHERE variants.parent_id IN ('{$id}')
					INNER JOIN {$wpdb->base_prefix}kb_products_info
					ON variants.ID = {$wpdb->base_prefix}kb_products_info.ID
					ORDER BY variants.parent_id;
				", "ARRAY_A");
			foreach ($variationsQuery as $variationArgs) {
				$product->addVariation(self::getProductModel($variationArgs));
			}
		}
		return $product;
	}

	private static function getProductModel($args) {
		$product = null;
		switch ($args['type']) {
			case 'variable':
				$product =  new VariableProduct($args['ID']);
				break;
			case 'variation':
				$product = new VariationProduct($args['ID']);
				break;
			case 'simple':
			default:
				$product = new Product($args['ID']);
				break;
		}
		$product->withImageID($args['image_id'])
				->withType($args['type'])
		        ->withName($args['name'])
		        ->withDownloadable($args['downloadable'])
		        ->withVirtual($args['virtual'])
		        ->withOnSale($args['on_sale'])
		        ->withRegularPrice($args['max_price'])
		        ->withStockStatus($args['stock_status'])
		        ->withManagingStock($args['stock_managing'] > 0)
		        ->withBackordersAllowed($args['stock_managing'] > 1)
		        ->withBackordersNotifications($args['stock_managing'] > 2);
		if(!$product->is_type('variation')) {
			if(!empty($args['category_ids'])) {
				$product->withCategoryIDs( (array)json_decode( $args['category_ids'] ) );
			}
			if(!empty($args['cross_sell_ids'])) {
				$product->withCrossSellIDs((array)json_decode($args['cross_sell_ids']));
			}
			if(!empty($args['upsell_ids'])) {
				$product->withUpsellIDs((array)json_decode($args['upsell_ids']));
			}
			if(!empty($args['tag_ids'])) {
				$product->withTagIDs((array)json_decode($args['tag_ids']));
			}
			$product->withSoldIndividually($args['sold_individually'] == 1)
			        ->withDescription($args['description'])
			        ->withShortDescription($args['short_description'])
			        ->withDateCreated($args['date']);
		}
		if($product->is_on_sale()) {
			$product->withSalePrice($args['min_price']);
		}
		if($product->managing_stock()) {
			$product->withQuantity($args['quantity']);
		}
		if($product->is_type('variation') || $product->is_type('variable')) {
			$product->withVariationAttributes((array)json_decode($args['attributes']));
		}
		if($product->is_type('variation')) {
			$product->withParentID($args['parent_id']);
		} elseif(!empty($args['gallery_image_ids'])) {
			$product->withGalleryImageIDs((array)json_decode($args['gallery_image_ids']));
		}
		if($product->is_type('variable')) {
			$product->withVariationRegularPrice('max', $args['max_regular_price']);
			if(!empty($args['min_regular_price'])) {
				$product->withVariationRegularPrice('min', $args['min_regular_price']);
			}
			if(!empty($args['max_sale_price'])) {
				$product->withVariationSalePrice('max', $args['max_sale_price']);
			}
			if(!empty($args['min_sale_price'])) {
				$product->withVariationSalePrice('min', $args['min_sale_price']);
			}
		}
		return $product;
	}
}