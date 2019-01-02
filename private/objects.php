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
        'table' => 'ciniki_restaurant_menus',
        'fields' => array(
            'name' => array('name'=>'Name'),
            'permalink' => array('name'=>'Permalink', 'default'=>''),
            'primary_image_id' => array('name'=>'Image', 'ref'=>'ciniki.images.image', 'default'=>'0'),
            'intro' => array('name'=>'Introduction', 'default'=>''),
            'notes' => array('name'=>'Notes', 'default'=>''),
            ),
        'history_table' => 'ciniki_restaurant_history',
        );
    $objects['menusection'] = array(
        'name' => 'Section',
        'sync' => 'yes',
        'o_name' => 'section',
        'o_container' => 'sections',
        'table' => 'ciniki_restaurant_menu_sections',
        'fields' => array(
            'menu_id' => array('name'=>'Menu', 'ref'=>'ciniki.restaurants.menu'),
            'sequence' => array('name'=>'Order', 'default'=>'1'),
            'name' => array('name'=>'Name'),
            'permalink' => array('name'=>'Permalink', 'default'=>''),
            'primary_image_id' => array('name'=>'Image', 'ref'=>'ciniki.image.image', 'default'=>'0'),
            'intro' => array('name'=>'Introduction', 'default'=>''),
            'notes' => array('name'=>'Notes', 'default'=>''),
            ),
        'history_table' => 'ciniki_restaurant_history',
        );
    $objects['menuitem'] = array(
        'name' => 'Item',
        'sync' => 'yes',
        'o_name' => 'item',
        'o_container' => 'items',
        'table' => 'ciniki_restaurant_menu_items',
        'fields' => array(
            'menu_id' => array('name'=>'Menu', 'ref'=>'ciniki.restaurants.menu'),
            'section_id' => array('name'=>'Section', 'ref'=>'ciniki.restaurants.menusection'),
            'sequence' => array('name'=>'Order', 'default'=>'1'),
            'code' => array('name'=>'Code', 'default'=>''),
            'name' => array('name'=>'Name'),
            'permalink' => array('name'=>'Permalink'),
            'price' => array('name'=>'Price', 'default'=>''),
            'foodtypes' => array('name'=>'Food Types', 'default'=>'0'),
            'primary_image_id' => array('name'=>'Image', 'ref'=>'ciniki.images.image', 'default'=>'0'),
            'synopsis' => array('name'=>'Synopsis', 'default'=>''),
            ),
        'history_table' => 'ciniki_restaurant_history',
        );
    
    return array('stat'=>'ok', 'objects'=>$objects);
}
?>
