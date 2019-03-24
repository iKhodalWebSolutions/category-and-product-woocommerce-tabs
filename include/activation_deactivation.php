<?php

/**
 * Clean data on activation / deactivation
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
 
register_activation_hook( __FILE__, 'richcategoryproducttab_activation');

function richcategoryproducttab_activation() {

	if( ! current_user_can ( 'activate_plugins' ) ) {
		return;
	} 
	add_option( 'richcategoryproducttab_license_status', 'invalid' );
	add_option( 'richcategoryproducttab_license_key', '' ); 

}

register_uninstall_hook( __FILE__, 'richcategoryproducttab_uninstall');

function richcategoryproducttab_uninstall() {

	delete_option( 'richcategoryproducttab_license_status' );
	delete_option( 'richcategoryproducttab_license_key' ); 
	
}