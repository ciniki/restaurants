<?php
//
// Description
// -----------
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_restaurants_objects($ciniki) {
	
	$objects = array();
	$objects['menu'] = array(
		'name'=>'Menu',
		'sync'=>'yes',
		'table'=>'ciniki_restaurant_menus',
		'fields'=>array(
			'name'=>array(),
			'permalink'=>array(),
			'primary_image_id'=>array('ref'=>'ciniki.images.image'),
			'description'=>array('default'=>'yes'),
			),
		'history_table'=>'ciniki_restaurant_history',
		);
	$objects['category'] = array(
		'name'=>'Category',
		'sync'=>'yes',
		'table'=>'ciniki_restaurant_categories',
		'fields'=>array(
			'name'=>array(),
			'permalink'=>array(),
			'primary_image_id'=>array('ref'=>'ciniki.images.image'),
			'description'=>array('default'=>''),
			),
		'history_table'=>'ciniki_restaurant_history',
		);
	$objects['item'] = array(
		'name'=>'Item',
		'sync'=>'yes',
		'table'=>'ciniki_restaurant_items',
		'fields'=>array(
			'code'=>array(),
			'name'=>array(),
			'price'=>array(),
			'primary_image_id'=>array('ref'=>'ciniki.images.image'),
			'synopsis'=>array(),
			),
		'history_table'=>'ciniki_restaurant_history',
		);
	$objects['item_menu'] = array(
		'name'=>'File',
		'sync'=>'yes',
		'table'=>'ciniki_restaurant_item_menus',
		'fields'=>array(
			'item_id'=>array('ref'=>'ciniki.restaurants.item'),
			'menu_id'=>array('ref'=>'ciniki.restaurants.menu'),
			),
		'history_table'=>'ciniki_restaurant_history',
		);
	$objects['item_category'] = array(
		'name'=>'Price',
		'sync'=>'yes',
		'table'=>'ciniki_restaurant_item_categories',
		'fields'=>array(
			'item_id'=>array('ref'=>'ciniki.restaurants.item'),
			'category_id'=>array('ref'=>'ciniki.restaurants.category'),
			),
		'history_table'=>'ciniki_restaurant_history',
		);
	
	return array('stat'=>'ok', 'objects'=>$objects);
}
?>
