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
        'name' => 'Menu',
        'sync' => 'yes',
        'o_name' => 'menu',
        'o_container' => 'menus',
        'table' => 'ciniki_restaurants_menus',
        'fields' => array(
            'name' => array('name'=>'Name'),
            'permalink' => array('name'=>'Permalink', 'default'=>''),
            'primary_image_id' => array('name'=>'Image', 'ref'=>'ciniki.images.image'),
            'description' => array('name'=>'Description', 'default'=>''),
            ),
        'history_table' => 'ciniki_restaurants_history',
        );
    $objects['menucategory'] = array(
        'name' => 'Category',
        'sync' => 'yes',
        'o_name' => 'category',
        'o_container' => 'categories',
        'table' => 'ciniki_restaurants_menu_categories',
        'fields' => array(
            'menu_id' => array('name'=>'', 'ref'=>'ciniki.restaurants.menu'),
            'name' => array('name'=>'Name'),
            'permalink' => array('name'=>'Permalink', 'default'=>''),
            'primary_image_id' => array('name'=>'Image', 'ref'=>'ciniki.image.image'),
            'description' => array('name'=>'Description', 'default'=>''),
            ),
        'history_table' => 'ciniki_restaurants_history',
        );
    $objects['menuitem'] = array(
        'name' => 'Item',
        'sync' => 'yes',
        'o_name' => 'item',
        'o_container' => 'items',
        'table' => 'ciniki_restaurants_menu_items',
        'fields' => array(
            'category_id' => array('name'=>'', 'ref'=>'ciniki.restaurants.menucategory'),
            'code' => array('name'=>'Code', 'default'=>''),
            'name' => array('name'=>'Name'),
            'price' => array('name'=>'Price', 'default'=>''),
            'primary_image_id' => array('name'=>'Image', 'ref'=>'ciniki.images.image'),
            'synopsis' => array('name'=>'Synopsis', 'default'=>''),
            ),
        'history_table' => 'ciniki_restaurants_history',
        );
    
    return array('stat'=>'ok', 'objects'=>$objects);
}
?>
