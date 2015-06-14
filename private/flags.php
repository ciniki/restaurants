<?php
//
// Description
// -----------
// The module flags
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_restaurants_flags($ciniki, $modules) {
	$flags = array(
		// 0x01
		array('flag'=>array('bit'=>'1', 'name'=>'Menus')),
		array('flag'=>array('bit'=>'2', 'name'=>'Categories')),
		array('flag'=>array('bit'=>'3', 'name'=>'Codes')),
//		array('flag'=>array('bit'=>'4', 'name'=>'')),
		// 0x10
//		array('flag'=>array('bit'=>'5', 'name'=>'')),
//		array('flag'=>array('bit'=>'6', 'name'=>'')),
//		array('flag'=>array('bit'=>'7', 'name'=>'')),
//		array('flag'=>array('bit'=>'8', 'name'=>'')),
		);

	return array('stat'=>'ok', 'flags'=>$flags);
}
?>
