<?php
//
// Description
// -----------
// This function will return the list of options for the module that can be set for the website.
//
// Arguments
// ---------
// ciniki:
// settings:        The web settings structure.
// tnid:            The ID of the tenant to get ags for.
//
// args:            The possible arguments for posts
//
//
// Returns
// -------
//
function ciniki_restaurants_hooks_webOptions(&$ciniki, $tnid, $args) {

    //
    // Check to make sure the module is enabled
    //
    if( !isset($ciniki['tenant']['modules']['ciniki.restaurants']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.38', 'msg'=>"I'm sorry, the page you requested does not exist."));
    }

    //
    // Get the settings from the database
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDetailsQueryDash');
    $rc = ciniki_core_dbDetailsQueryDash($ciniki, 'ciniki_web_settings', 'tnid', $tnid, 'ciniki.web', 'settings', 'page-restaurants');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['settings']) ) {
        $settings = array();
    } else {
        $settings = $rc['settings'];
    }

    //
    // For specific pages, no options are required currently
    //
    $options = array();
    if( ciniki_core_checkModuleFlags($ciniki, 'ciniki.restaurants', 0x01) ) {
        $strsql = "SELECT menus.name, "
            . "menus.permalink "
            . "FROM ciniki_restaurant_menus AS menus "
            . "WHERE menus.tnid = '" . ciniki_core_dbQuote($ciniki, $tnid) . "' "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.restaurants', 'item');
        if( $rc['stat'] != 'ok' ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.39', 'msg'=>'Unable to load types', 'err'=>$rc['err']));
        }
        foreach($rc['rows'] as $row) {
            $pages['ciniki.restaurants.' . $row['permalink']] = array('name'=>'Menus - ' . $row['name'], 'options'=>$options);
        } 
    }
    
    return array('stat'=>'ok', 'pages'=>$pages);
}
?>
