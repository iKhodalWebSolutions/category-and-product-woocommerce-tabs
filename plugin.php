<?php 
/*
  Plugin Name: Woocommerce product category tabs filter and tabbed category wise product listing
  Description: Beautiful category and product tabs view for the widget and content block
  Author: iKhodal Web Solution
  Plugin URI: https://www.ikhodal.com/woocommerce-product-category-tabs-filter-and-tabbed-category-wise-product-listing
  Author URI: https://www.ikhodal.com
  Version: 2.1 
  Text Domain: richcategoryproducttab
*/ 
  
  
//////////////////////////////////////////////////////
// Defines the constants for use within the plugin. //
////////////////////////////////////////////////////// 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  


/**
*  Assets of the plugin
*/
$wcpt_plugins_url = plugins_url( "/assets/", __FILE__ );

define( 'wcpt_media', $wcpt_plugins_url ); 

/**
*  Plugin DIR
*/
$wcpt_plugin_dir = plugin_basename(dirname(__FILE__));

define( 'wcpt_plugin_dir', $wcpt_plugin_dir );  

 
/**
 * Include abstract class for common methods
 */
require_once 'include/abstract.php';


///////////////////////////////////////////////////////
// Include files for widget and shortcode management //
///////////////////////////////////////////////////////

/**
 * Register custom post type for shortcode
 */ 
require_once 'include/shortcode.php';

/**
 * Admin panel widget configuration
 */ 
require_once 'include/admin.php';

/**
 * Load Rich Woocommerce Category and Product Tab on frontent pages
 */
require_once 'include/richcategoryproducttab.php'; 

/**
 * Clean data on activation / deactivation
 */
require_once 'include/activation_deactivation.php';  
 