<?php
//
// Description
// -----------
// This method will return the list of Menus for a tenant.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:        The ID of the tenant to get Menu for.
//
// Returns
// -------
//
function ciniki_restaurants_menuList($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Check access to tnid as owner, or sys admin.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'restaurants', 'private', 'checkAccess');
    $rc = ciniki_restaurants_checkAccess($ciniki, $args['tnid'], 'ciniki.restaurants.menuList');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Get the list of menus
    //
    $strsql = "SELECT ciniki_restaurant_menus.id, "
        . "ciniki_restaurant_menus.name, "
        . "ciniki_restaurant_menus.permalink "
        . "FROM ciniki_restaurant_menus "
        . "WHERE ciniki_restaurant_menus.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.restaurants', array(
        array('container'=>'menus', 'fname'=>'id', 
            'fields'=>array('id', 'name', 'permalink')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['menus']) ) {
        $menus = $rc['menus'];
    } else {
        $menus = array();
    }

    return array('stat'=>'ok', 'menus'=>$menus);
}
?>
