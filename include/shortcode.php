<?php 
/** 
 * Register custom post type to manage shortcode
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'richcategoryproducttabShortcode_Admin' ) ) {
	class richcategoryproducttabShortcode_Admin extends richcategoryproducttabLib {
	
		public $_shortcode_config = array();
		 
		/**
		 * constructor method.
		 *
		 * Register post type for tab for category and posts shortcode
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */
		public function __construct() {
			
			parent::__construct();
			
	       /**
		    * Register hooks to manage custom post type for tab for category and posts
		    */
			add_action( 'init', array( &$this, 'wcpt_registerPostType' ) );   
			add_action( 'add_meta_boxes', array( &$this, 'add_richcategoryproducttab_metaboxes' ) );
			add_action( 'save_post', array(&$this, 'wp_save_richcategoryproducttab_meta' ), 1, 2 ); 
			add_action( 'admin_enqueue_scripts', array( $this, 'wcpt_admin_enqueue' ) ); 
			
		   /* Register hooks for displaying shortcode column. */ 
			if( isset( $_REQUEST["post_type"] ) && !empty( $_REQUEST["post_type"] ) && trim($_REQUEST["post_type"]) == "wcpt_tabs" ) {
				add_action( "manage_posts_custom_column", array( $this, 'richcategoryproducttabShortcodeColumns' ), 10, 2 );
				add_filter( 'manage_posts_columns', array( $this, 'wcpt_shortcodeNewColumn' ) );
			}
			
			add_action( 'wp_ajax_wcpt_getCategoriesOnTypes',array( &$this, 'wcpt_getCategoriesOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_wcpt_getCategoriesOnTypes', array( &$this, 'wcpt_getCategoriesOnTypes' ) );
			add_action( 'wp_ajax_wcpt_getCategoriesRadioOnTypes',array( &$this, 'wcpt_getCategoriesRadioOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_wcpt_getCategoriesRadioOnTypes', array( &$this, 'wcpt_getCategoriesRadioOnTypes' ) ); 
			 
		}    
		
 	   /**
		* Register and load JS/CSS for admin widget configuration 
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool|void It returns false if not valid page or display HTML for JS/CSS
		*/  
		public function wcpt_admin_enqueue() {

			if ( ! $this->validate_page() )
				return FALSE;
			
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'admin-richcategoryproducttab.css', wcpt_media."css/admin-richcategoryproducttab.css" );
			wp_enqueue_script( 'admin-richcategoryproducttab.js', wcpt_media."js/admin-richcategoryproducttab.js" ); 
			
		}		
		 
	   /**
		* Add meta boxes to display shortcode
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/ 
		public function add_richcategoryproducttab_metaboxes() {
			
			/**
			 * Add custom fields for shortcode settings
		     */
			add_meta_box( 'wp_richcategoryproducttab_fields', __( 'Rich Woocommerce Category and Product Tab', 'richcategoryproducttab' ),
				array( &$this, 'wp_richcategoryproducttab_fields' ), 'wcpt_tabs', 'normal', 'high' );
			
			/**
			 * Display shortcode of tab for category and posts
		     */
			add_meta_box( 'wp_richcategoryproducttab_shortcode', __( 'Shortcode', 'richcategoryproducttab' ),
				array( &$this, 'shortcode_meta_box' ), 'wcpt_tabs', 'side' );	
		
		}  
		
	   /**
		* Validate widget or shortcode post type page
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool It returns true if page is post.php or widget otherwise returns false
		*/ 
		private function validate_page() {

			if ( ( isset( $_GET['post_type'] )  && $_GET['post_type'] == 'wcpt_tabs' ) || strpos($_SERVER["REQUEST_URI"],"widgets.php") > 0  || strpos($_SERVER["REQUEST_URI"],"post.php" ) > 0 || strpos($_SERVER["REQUEST_URI"], "richcategoryproducttab_settings" ) > 0  )
				return TRUE;
		
		} 			
 
	   /**
		* Display richcategoryproducttab block configuration fields
		*
		* @access  private
		* @since   1.0
		*
		* @return  void Returns HTML for configuration fields 
		*/  
		public function wp_richcategoryproducttab_fields() {
			
			global $post; 
			 
			foreach( $this->_config as $kw => $kw_val ) {
				$this->_shortcode_config[$kw] = get_post_meta( $post->ID, $kw, true ); 
			}
			 
			foreach ( $this->_shortcode_config as $sc_key => $sc_val ) {
				if( trim( $sc_val ) == "" )
					unset( $this->_shortcode_config[ $sc_key ] );
				else {
					if(!is_array($sc_val) && trim($sc_val) != "" ) 
						$this->_shortcode_config[ $sc_key ] = htmlspecialchars( $sc_val, ENT_QUOTES );
					else 
						$this->_shortcode_config[ $sc_key ] = $sc_val;
				}	
			}
			
			foreach( $this->_config as $kw => $kw_val ) {
				if( !is_array($this->_shortcode_config[$kw]) && trim($this->_shortcode_config[$kw]) == "" ) {
					$this->_shortcode_config[$kw] = $this->_config[$kw]["default"];
				} 
			}
			
			$this->_shortcode_config["vcode"] = get_post_meta( $post->ID, 'vcode', true );    
			 
			require( $this->getrichcategoryproducttabTemplate( "admin/admin_shortcode_post_type.php" ) );
			
		}
		
	   /**
		* Display shortcode in edit mode
		*
		* @access  private
		* @since   1.0
		*
		* @param   object  $post Set of configuration data.
		* @return  void	   Displays HTML of shortcode
		*/
		public function shortcode_meta_box( $post ) {

			$richcategoryproducttab_id = $post->ID;

			if ( get_post_status( $richcategoryproducttab_id ) !== 'publish' ) {

				echo '<p>'.__( 'Please make the publish status to get the shortcode', 'richcategoryproducttab' ).'</p>';

				return;

			}

			$richcategoryproducttab_title = get_the_title( $richcategoryproducttab_id );

			$shortcode = sprintf( "[%s id='%s']", 'richcategoryproducttab', $richcategoryproducttab_id );
			
			echo "<p class='tpp-code'>".$shortcode."</p>";
		}
				  
	   /**
		* Save tab for category and posts shortcode fields
		*
		* @access  private
		* @since   1.0 
		*
		* @param   int    	$post_id post id
		* @param   object   $post    post data object
		* @return  void
		*/ 
		function wp_save_richcategoryproducttab_meta( $post_id, $post ) {
			
		/*	if( !isset($_POST['richcategoryproducttab_nonce']) ) {
				return $post->ID;
			} 
			if( !wp_verify_nonce( $_POST['richcategoryproducttab_nonce'], plugin_basename(__FILE__) ) ) {
				return $post->ID;
			}
			*/
			
		   /**
			* Check current user permission to edit post
			*/
			if(!current_user_can( 'edit_post', $post->ID ))
				return $post->ID;
				
			 /**
			* sanitize text fields 
			*/
			$wcpt_meta = array(); 
			
			foreach( $this->_config as $kw => $kw_val ) { 
				$_save_value =  $_POST["nm_".$kw];
				if($kw_val["type"]=="boolean"){
					$_save_value = $_POST["nm_".$kw][0];
				}
				if( $kw_val["type"]=="checkbox" && count($_POST["nm_".$kw]) > 0 ) {
					$_save_value = implode( ",", $_POST["nm_".$kw] );
				}
				$wcpt_meta[$kw] =  sanitize_text_field( $_save_value );
			}     
			 
			foreach ( $wcpt_meta as $key => $value ) {
			
			   if( $post->post_type == 'revision' ) return;
				$value = implode( ',', (array)$value );
				
				if( trim($value) == "Array" || is_array($value) )
					$value = "";
					
			   /**
				* Add or update posted data 
				*/
				if( get_post_meta( $post->ID, $key, FALSE ) ) { 
					update_post_meta( $post->ID, $key, $value );
				} else { 
					add_post_meta( $post->ID, $key, $value );
				} 
			
			}		
			
		  
		}
		
			 
	   /**
		* Register post type tab for category and posts shortcode
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		function wcpt_registerPostType() { 
			
		   /**
			* Post type and menu labels 
			*/
			$labels = array(
				'name' => __('Category & Products Tab View Shortcode', 'richcategoryproducttab' ),
				'singular_name' => __( 'Category & Products Tab View Shortcode', 'richcategoryproducttab' ),
				'add_new' => __( 'Add New Shortcode', 'richcategoryproducttab' ),
				'add_new_item' => __( 'Add New Shortcode', 'richcategoryproducttab' ),
				'edit_item' => __( 'Edit', 'richcategoryproducttab'  ),
				'new_item' => __( 'New', 'richcategoryproducttab'  ),
				'all_items' => __( 'All', 'richcategoryproducttab'  ),
				'view_item' => __( 'View', 'richcategoryproducttab'  ),
				'search_items' => __( 'Search', 'richcategoryproducttab'  ),
				'not_found' =>  __( 'No item found', 'richcategoryproducttab'  ),
				'not_found_in_trash' => __( 'No item found in Trash', 'richcategoryproducttab'  ),
				'parent_item_colon' => '',
				'menu_name' => __( 'WCPT', 'richcategoryproducttab'  ) 
			);
			
		   /**
			* Rich Woocommerce Category and Product Tab post type registration options
			*/
			$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => false,
				'rewrite' => false,
				'capability_type' => 'post',
				'menu_icon' => 'dashicons-list-view',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title' )
			);
			 
		   /**
			* Register new post type
			*/
			// if(  $this->_config["st"]["flag"] == "valid")
			register_post_type( 'wcpt_tabs', $args );
			 

		}
		
	   /**
		* Display shortcode column in tab for category and posts list
		*
		* @access  private
		* @since   1.0
		*
		* @param   string  $column  Column name
		* @param   int     $post_id Post ID
		* @return  void	   Display shortcode in column	
		*/
		public function richcategoryproducttabShortcodeColumns( $column, $post_id ) { 
		
			if( $column == "shortcode" ) {
				 echo sprintf( "[%s id='%s']", 'richcategoryproducttab', $post_id ); 
			}  
		
		}
		
	   /**
		* Register shortcode column
		*
		* @access  private
		* @since   1.0
		*
		* @param   array  $columns  Column list 
		* @return  array  Returns column list
		*/
		public function wcpt_shortcodeNewColumn( $columns ) {
			
			$_edit_column_list = array();	
			$_i = 0;
			
			foreach( $columns as $__key => $__value) {
					
					if($_i==2){
						$_edit_column_list['shortcode'] = __( 'Shortcode', 'richcategoryproducttab' );
					}
					$_edit_column_list[$__key] = $__value;
					
					$_i++;
			}
			
			return $_edit_column_list;
		
		}
		
	} 

}

new richcategoryproducttabShortcode_Admin();
 
?>