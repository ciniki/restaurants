<?php
//
// Description
// ===========
// This method will return all the information about an section.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant the section is attached to.
// section_id:          The ID of the section to get the details for.
//
// Returns
// -------
//
function ciniki_restaurants_menuSectionGet($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'),
        'section_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Section'),
        'menu_id'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Menu'),
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
    $rc = ciniki_restaurants_checkAccess($ciniki, $args['tnid'], 'ciniki.restaurants.menuSectionGet');
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

    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki, 'php');

    //
    // Return default for new Section
    //
    if( $args['section_id'] == 0 ) {
        //
        // Get the next sequence number
        //
        $seq = 1;
        if( isset($args['menu_id']) ) {
            $strsql = "SELECT MAX(sequence) AS num "
                . "FROM ciniki_restaurant_menu_sections "
                . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
                . "AND menu_id = '" . ciniki_core_dbQuote($ciniki, $args['menu_id']) . "' "
                . "";
            ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQuery');
            $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.restaurants','item');
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            $seq = (isset($rc['item']['num']) ? $rc['item']['num'] + 1 : 1);
        }
        
        $section = array('id'=>0,
            'menu_id'=>'',
            'sequence'=>$seq,
            'name'=>'',
            'permalink'=>'',
            'primary_image_id'=>'0',
            'intro'=>'',
            'notes'=>'',
        );
    }

    //
    // Get the details for an existing Section
    //
    else {
        $strsql = "SELECT ciniki_restaurant_menu_sections.id, "
            . "ciniki_restaurant_menu_sections.menu_id, "
            . "ciniki_restaurant_menu_sections.sequence, "
            . "ciniki_restaurant_menu_sections.name, "
            . "ciniki_restaurant_menu_sections.permalink, "
            . "ciniki_restaurant_menu_sections.primary_image_id, "
            . "ciniki_restaurant_menu_sections.intro, "
            . "ciniki_restaurant_menu_sections.notes "
            . "FROM ciniki_restaurant_menu_sections "
            . "WHERE ciniki_restaurant_menu_sections.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND ciniki_restaurant_menu_sections.id = '" . ciniki_core_dbQuote($ciniki, $args['section_id']) . "' "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
        $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.restaurants', array(
            array('container'=>'sections', 'fname'=>'id', 
                'fields'=>array('menu_id', 'sequence', 'name', 'permalink', 'primary_image_id', 'intro', 'notes'),
                ),
            ));
        if( $rc['stat'] != 'ok' ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.15', 'msg'=>'Section not found', 'err'=>$rc['err']));
        }
        if( !isset($rc['sections'][0]) ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.restaurants.16', 'msg'=>'Unable to find Section'));
        }
        $section = $rc['sections'][0];
    }

    return array('stat'=>'ok', 'section'=>$section);
}
?>
