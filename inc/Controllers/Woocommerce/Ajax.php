<?php

use Inc\DAO\Products\ProductsDAO;
/**
 * @package starterWordpressTheme
 */
function kbCreateProductsTables() {
	ProductsDAO::createTables();
	die();
}

add_action('wp_ajax_nopriv_kbCreateProductsTables', 'kbCreateProductsTables');
add_action('wp_ajax_kbCreateProductsTables', 'kbCreateProductsTables');
