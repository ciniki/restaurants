<?php
//
// Description
// -----------
//
// Arguments
// ---------
// ciniki:
// settings:        The web settings structure, similar to ciniki variable but only web specific information.
//
// Returns
// -------
//
function ciniki_restaurants_web_processRequestMenu(&$ciniki, $settings, $tnid, $args) {

    ciniki_core_loadMethod($ciniki, 'ciniki', 'web', 'private', 'processContent');

    //
    // Check to make sure the module is enabled
    //
    if( !isset($ciniki['tenant']['modules']['ciniki.restaurants']) ) {
        return array('stat'=>'404', 'err'=>array('code'=>'ciniki.restaurants.31', 'msg'=>"I'm sorry, the page you requested does not exist."));
    }
    $page = array(
        'title'=>$args['page_title'],
        'breadcrumbs'=>$args['breadcrumbs'],
        'blocks'=>array(),
        'submenu'=>array(),
        'container-class'=>'ciniki-restaurants',
        );
    $menu_permalink = $args['menu_permalink'];

    //
    // Load the menu
    //
    $strsql = "SELECT menus.id, "
        . "menus.name, "
        . "menus.permalink, "
        . "menus.primary_image_id, "
        . "menus.intro, "
        . "menus.notes, "
        . "sections.id AS section_id, "
        . "sections.sequence AS section_sequence, "
        . "sections.name AS section_name, "
        . "sections.permalink AS section_permalink, "
        . "sections.primary_image_id AS section_image_id,"
        . "sections.intro AS section_intro,"
        . "sections.notes AS section_notes,"
        . "items.id AS item_id, "
        . "items.sequence AS item_sequence, "
        . "items.code AS item_code, "
        . "items.name AS item_name, "
        . "items.permalink AS item_permalink, "
        . "items.primary_image_id AS item_image_id,"
        . "items.price, "
        . "items.foodtypes, "
        . "items.synopsis "
        . "FROM ciniki_restaurant_menus AS menus "
        . "LEFT JOIN ciniki_restaurant_menu_sections AS sections ON ( "
            . "menus.id = sections.menu_id "
            . "AND sections.tnid = '" . ciniki_core_dbQuote($ciniki, $tnid) . "' "
            . ") "
        . "LEFT JOIN ciniki_restaurant_menu_items AS items ON ( "
            . "sections.id = items.section_id "
            . "AND items.tnid = '" . ciniki_core_dbQuote($ciniki, $tnid) . "' "
            . ") "
        . "WHERE menus.tnid = '" . ciniki_core_dbQuote($ciniki, $tnid) . "' "
        . "AND menus.permalink = '" . ciniki_core_dbQuote($ciniki, $args['menu_permalink']) . "' "
        . "ORDER BY sections.sequence, sections.name, items.sequence, items.name "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.restaurants', array(
        array('container'=>'menus', 'fname'=>'id', 
            'fields'=>array('name', 'permalink', 'primary_image_id', 'intro', 'notes'),
            ),
        array('container'=>'sections', 'fname'=>'section_id', 
            'fields'=>array('id'=>'section_id', 'name'=>'section_name', 'permalink'=>'section_permalink', 
                'sequence'=>'section_sequence',
                'primary_image_id'=>'section_image_id', 'intro'=>'section_intro', 'notes'=>'section_notes'),
            ),
        array('container'=>'items', 'fname'=>'item_id', 
            'fields'=>array('id'=>'item_id', 
                'sequence'=>'item_sequence',
                'code'=>'item_code', 'name'=>'item_name', 'permalink'=>'item_permalink', 
                'price', 'price_display'=>'price', 'foodtypes',
                'image_id'=>'item_image_id', 'synopsis',
                ),
//            'naprices'=>array('price_display'),
            ),
        ));
    if( $rc['stat'] != 'ok' ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.34', 'msg'=>'Menu not found', 'err'=>$rc['err']));
    }
    if( !isset($rc['menus'][0]) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.35', 'msg'=>'Unable to find Menu'));
    }
    $menu = $rc['menus'][0];

  
    //
    // Display the menu
    //
    if( isset($menu['intro']) && $menu['intro'] != '' ) {
        $page['blocks'][] = array('type'=>'content', 'wide'=>'yes', 'content'=>$menu['intro']);
    }
    if( isset($menu['sections']) ) {
        foreach($menu['sections'] as $section) {
            $title = $section['name'];
            if( isset($section['intro']) && $section['intro'] != '' ) {
                $page['blocks'][] = array('type'=>'content', 'wide'=>'yes', 'title'=>$title, 'content'=>$section['intro']);
                $title = '';
            }

            if( isset($section['items']) ) {
                $page['blocks'][] = array('type'=>'priceditems', 'wide'=>'yes', 'title'=>$title, 'list'=>$section['items']);
            }

            if( isset($section['notes']) && $section['notes'] != '' ) {
                $page['blocks'][] = array('type'=>'content', 'wide'=>'yes', 'content'=>$section['notes']);
            }
        }
    }
    if( isset($menu['notes']) && $menu['notes'] != '' ) {
        $page['blocks'][] = array('type'=>'content', 'wide'=>'yes', 'content'=>$menu['notes']);
    }

    return array('stat'=>'ok', 'page'=>$page);
}
?>
