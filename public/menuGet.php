<?php
//
// Description
// ===========
// This method will return all the information about an menu.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant the menu is attached to.
// menu_id:          The ID of the menu to get the details for.
//
// Returns
// -------
//
function ciniki_restaurants_menuGet($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'),
        'menu_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Menu'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Make sure this module is activated, and
    // check permission to run this function for this tenant
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'restaurants', 'private', 'checkAccess');
    $rc = ciniki_restaurants_checkAccess($ciniki, $args['tnid'], 'ciniki.restaurants.menuGet');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Load tenant settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'tenants', 'private', 'intlSettings');
    $rc = ciniki_tenants_intlSettings($ciniki, $args['tnid']);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_timezone = $rc['settings']['intl-default-timezone'];
    $intl_currency_fmt = numfmt_create($rc['settings']['intl-default-locale'], NumberFormatter::CURRENCY);
    $intl_currency = $rc['settings']['intl-default-currency'];

    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki, 'php');

    //
    // Return default for new Menu
    //
    if( $args['menu_id'] == 0 ) {
        $menu = array('id'=>0,
            'name'=>'',
            'permalink'=>'',
            'primary_image_id'=>'0',
            'intro'=>'',
            'notes'=>'',
            'sections'=>array(),
        );
    }

    //
    // Get the details for an existing Menu
    //
    else {
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
                . "AND sections.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
                . ") "
            . "LEFT JOIN ciniki_restaurant_menu_items AS items ON ( "
                . "sections.id = items.section_id "
                . "AND items.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
                . ") "
            . "WHERE menus.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND menus.id = '" . ciniki_core_dbQuote($ciniki, $args['menu_id']) . "' "
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
                    'primary_image_id'=>'item_image_id', 'synopsis',
                    ),
                'naprices'=>array('price_display'),
                ),
            ));
        if( $rc['stat'] != 'ok' ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.8', 'msg'=>'Menu not found', 'err'=>$rc['err']));
        }
        if( !isset($rc['menus'][0]) ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.9', 'msg'=>'Unable to find Menu'));
        }
        $menu = $rc['menus'][0];

        $menu['details'] = array(
            array('label'=>'Name', 'value'=>$menu['name']),
            );

    }

    return array('stat'=>'ok', 'menu'=>$menu);
}
?>
