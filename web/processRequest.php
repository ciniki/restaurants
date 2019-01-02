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
function ciniki_restaurants_web_processRequest(&$ciniki, $settings, $tnid, $args) {

    //
    // Check to make sure the module is enabled
    //
    if( !isset($ciniki['tenant']['modules']['ciniki.restaurants']) ) {
        return array('stat'=>'404', 'err'=>array('code'=>'ciniki.restaurants.27', 'msg'=>"I'm sorry, the page you requested does not exist."));
    }

    if( preg_match('/ciniki.restaurants.(.*)/', $args['module_page'], $m) ) {
        $args['menu_permalink'] = $m[1];
        ciniki_core_loadMethod($ciniki, 'ciniki', 'restaurants', 'web', 'processRequestMenu');
        return ciniki_restaurants_web_processRequestMenu($ciniki, $settings, $tnid, $args); 
    } 

    return array('stat'=>'404', 'err'=>array('code'=>'ciniki.restaurants.28', 'msg'=>"I'm sorry, the page you requested does not exist."));
}
?>
