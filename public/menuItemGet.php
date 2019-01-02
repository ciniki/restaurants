<?php
//
// Description
// ===========
// This method will return all the information about an item.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant the item is attached to.
// item_id:          The ID of the item to get the details for.
//
// Returns
// -------
//
function ciniki_restaurants_menuItemGet($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'),
        'item_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Item'),
        'section_id'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Section'),
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
    $rc = ciniki_restaurants_checkAccess($ciniki, $args['tnid'], 'ciniki.restaurants.menuItemGet');
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
    // Return default for new Item
    //
    if( $args['item_id'] == 0 ) {
        //
        // Get the next sequence number
        //
        $seq = 1;
        if( isset($args['section_id']) ) {
            $strsql = "SELECT MAX(sequence) AS num "
                . "FROM ciniki_restaurant_menu_items "
                . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
                . "AND section_id = '" . ciniki_core_dbQuote($ciniki, $args['section_id']) . "' "
                . "";
            ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQuery');
            $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.restaurants','item');
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            $seq = (isset($rc['item']['num']) ? $rc['item']['num'] + 1 : 1);
        }
        
        $item = array('id'=>0,
            'section_id'=>isset($args['section_id']) ? $args['section_id'] : 0,
            'sequence'=>$seq,
            'code'=>'',
            'name'=>'',
            'permalink'=>'',
            'price'=>'',
            'foodtypes'=>0,
            'primary_image_id'=>0,
            'synopsis'=>'',
        );
    }

    //
    // Get the details for an existing Item
    //
    else {
        $strsql = "SELECT ciniki_restaurant_menu_items.id, "
            . "ciniki_restaurant_menu_items.menu_id, "
            . "ciniki_restaurant_menu_items.section_id, "
            . "ciniki_restaurant_menu_items.sequence, "
            . "ciniki_restaurant_menu_items.code, "
            . "ciniki_restaurant_menu_items.name, "
            . "ciniki_restaurant_menu_items.permalink, "
            . "ciniki_restaurant_menu_items.price, "
            . "ciniki_restaurant_menu_items.foodtypes, "
            . "ciniki_restaurant_menu_items.primary_image_id, "
            . "ciniki_restaurant_menu_items.synopsis "
            . "FROM ciniki_restaurant_menu_items "
            . "WHERE ciniki_restaurant_menu_items.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND ciniki_restaurant_menu_items.id = '" . ciniki_core_dbQuote($ciniki, $args['item_id']) . "' "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
        $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.restaurants', array(
            array('container'=>'items', 'fname'=>'id', 
                'fields'=>array('menu_id', 'section_id', 'sequence', 'code', 'name', 'permalink', 'price', 'foodtypes', 'primary_image_id', 'synopsis'),
                ),
            ));
        if( $rc['stat'] != 'ok' ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.22', 'msg'=>'Item not found', 'err'=>$rc['err']));
        }
        if( !isset($rc['items'][0]) ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.23', 'msg'=>'Unable to find Item'));
        }
        $item = $rc['items'][0];
        $item['price'] = number_format($item['price'], 2);
    }

    $rsp = array('stat'=>'ok', 'item'=>$item);

    //
    // Get the list of sections
    //
    $strsql = "SELECT sections.id, "
        . "sections.sequence, "
        . "sections.name "
        . "FROM ciniki_restaurant_menu_sections AS sections "
        . "WHERE sections.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "ORDER BY sequence "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.restaurants', array(
        array('container'=>'sections', 'fname'=>'id', 
            'fields'=>array('id', 'sequence', 'name')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $rsp['sections'] = isset($rc['sections']) ? $rc['sections'] : array();

    return $rsp;
}
?>
