<?php 
	 
/** 
 * Abstract class  has been designed to use common functions.
 * This is file is responsible to add custom logic needed by all templates and classes.  
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'richcategoryproducttabLib' ) ) { 
	abstract class richcategoryproducttabLib extends WP_Widget {
		
	   /**
		* Default values can be stored
		*
		* @access    public
		* @since     1.0
		*
		* @var       array
		*/
		public $_config = array();
		public $_plugin_settings = array();
		/**
		 * Constructor method.
		 *
		 * Run the following methods when this class is loaded.
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */ 
		public function __construct() {   
			
			/**
			 * Load text domain
			 */
			add_action( 'plugins_loaded', array( $this, 'richcategoryproducttab_text_domain' ) );
			
			parent::__construct( 'richcategoryproducttab',  __( 'Rich Woocommerce Category and Product Tab', 'richcategoryproducttab' ) ); 	
			
			/**
			 * Widget initialization for tab category and posts
			 */
			add_action( 'widgets_init', array( &$this, 'initrichcategoryproducttab' ) ); 
			
			/**
			 * Load the CSS/JS scripts
			 */
			add_action( 'init',  array( $this, 'richcategoryproducttab_scripts' ) );
			
		}
		
		function init_settings() {
		
			/**
			 * Default values configuration 
			 */
			 $mouse_hover_effect_cls = array(); 
			 for($i = 0; $i <= 41; $i++) {
				$_opt = "ikh-image-style-".$i;
				$_opt_text = "Animation ".$i;
				$mouse_hover_effect_cls[$_opt] = $_opt_text;
			 }
			 
			 $_categories = $this->getCategoryDataByTaxonomy( "product_cat" ) ;
			 $_cat_array = array();
			 $_default_open_category_list = array( "0"=>__( 'None', 'richcategoryproducttab' ), "all"=>__( 'All', 'richcategoryproducttab' ) );
			 if( count( $_categories ) > 0 ) { 
				foreach( $_categories as $_category_items ) {  
						$__chked = "";
						$_default_open_category_list[ $_category_items->id ] = $_cat_array[ $_category_items->id ] =  ($this->get_hierarchy_dash($_category_items->depth)).$_category_items->category; 
				} 
			 }		 
			
		
			$this->_config = array( 			 
					'widget_title' => array( 
						"type" => "text",
						"default" => __( 'Rich Post Tabs', 'richcategoryproducttab' ),
						"field_title" => __( 'Title', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "no",	
						"description" => __( "Please enter the widget/tab title.", "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),    
					'number_of_post_display' => array( 
						"type" => "text",
						"default" => 6,
						"in_js" => "yes",
						"pm" => 1,	
						"field_title" => __( 'Number of post to display', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"description" => __( "Add the integer value to load default number of posts.", "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'category_tab_text_color' => array( 
						"type" => "color",
						"default" => '#000',
						"in_js" => "no",	
						"field_title" => __( 'Category tab text color', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"class" => "richcategoryproducttab-color-field-1", 
						"description" => __( "Add color code or color name for category tab text.", "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),	
					'title_text_color' => array( 
						"type" => "color",
						"default" => '#424242',
						"class" => "richcategoryproducttab-color-field-4", 
						"field_title" => __( 'Post title text color', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "yes",	
						"description" => __( "Add color code or color name for post title color.", "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'category_tab_background_color' => array( 
						"type" => "color",
						"default" => '#ededed',
						"class" => "richcategoryproducttab-color-field-3", 
						"field_title" => __( 'Category tab background color', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "no",	
						"description" => __( "Add color code or color name for category tab background.", "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),	
					'header_text_color' => array( 
						"type" => "color",
						"default" => '#ffffff',
						"class" => "richcategoryproducttab-color-field-4", 
						"field_title" => __( 'Widget title text color', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "no",	
						"description" => __( "Add color code or color name for widget heading title.", "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'header_background_color' => array( 
						"type" => "color",
						"default" => '#0073e0',
						"class" => "richcategoryproducttab-color-field-5", 
						"field_title" => __( 'Widget title background color', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "no",	
						"description" => __( "Add color code or color name for widget heading background.", "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'tp_widget_width' => array( 
						"type" => "text",
						"default" => '100%',
						"field_title" => __( 'Widget Width', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "no",	
						"description" => __( "Add width of widget in pixel or percentage. Default width is 100%", "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					), 
					'hide_widget_title' => array( 
						"type" => "boolean",
						"default" => 'yes',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide widget title?', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "no",	
						"description" => __( 'Select "Yes" to hide widget heading. Default is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'hide_searchbox' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide search textbox?', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide search textbox field. Default is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),	
					'template' => array( 
						"type" => "option",
						"default" => 'no',
						"field_title" => __( 'Templates', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "no",		
						"options" => array(
							 "pane_style_1" =>  __( "Template Style 1", "richcategoryproducttab" ),		
							 "pane_style_2" =>  __( "Template Style 2", "richcategoryproducttab" ),		
							 "pane_style_3" =>  __( "Template Style 3", "richcategoryproducttab" ),
							 "pane_style_4" =>  __( "Template Style 4", "richcategoryproducttab" ),
							 "pane_style_5" =>  __( "Template Style 5", "richcategoryproducttab" ),	
							 "pane_style_6" =>  __( "Template Style 6", "richcategoryproducttab" ),	
							 "pane_style_7" =>  __( "Template Style 7", "richcategoryproducttab" ),
							 "pane_style_8" =>  __( "Template Style 8", "richcategoryproducttab" ),  
							 "pane_style_9" =>  __( "Template Style 9", "richcategoryproducttab" ),
							 "pane_style_10" =>  __( "Template Style 10", "richcategoryproducttab" ), 
							 "pane_style_11" =>  __( "Template Style 11", "richcategoryproducttab" ), 
							 "pane_style_12" =>  __( "Template Style 12", "richcategoryproducttab" ),  
						),
						"description" => __( "Select the template for accordion", "richcategoryproducttab" ),
						"field_group" => __( 'Custom Settings', 'richcategoryproducttab' ),
					),  
					'hide_post_title' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide post title?', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide the post title. Default is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					), 
					'category_type' => array( 
						"type" => "none",
						"default" => 'product_cat', 
					),
					'category_id' => array( 
						"type" => "checkbox",
						"default" => implode(",",array_keys($_cat_array)),
						"field_title" => __( 'Category', 'richcategoryproducttab' ),
						"is_required" => "no",
						"in_js" => "yes",	
						"onchange" => "wcpt_ck_category_check(this)",
						"options" => $_cat_array,
						"description" => __( "Please select the categories.", "richcategoryproducttab" ),
						"field_group" => __( 'Custom Settings', 'richcategoryproducttab' ),
					), 
					'wcpt_short_category_name_by' => array( 
						"type" => "option",
						"default" => 'asc',
						"field_title" => __( 'Short/order category name by', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"options" => array(
							 "asc" =>  __( "Ascending", "richcategoryproducttab" ),	 
							 "desc" =>  __( "Descending", "richcategoryproducttab" ),	 
							 "id" =>  __( "Shorting by category IDs", "richcategoryproducttab" ),	 
						),
						"description" => __( 'Select "Ascending" or "Descending" shorting order of category name. Default is "Ascending" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_enable_rtl' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Enable RTL', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to enable rtl support. Default is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					), 
					'wcpt_enable_rtl' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Enable RTL', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to enable rtl support. Default is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					), 
					'wcpt_enable_post_count' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Enable post count with category name', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "no",	
						"description" => __( 'Select "Yes" to enable post count with category name. Default value is "No" as disabled.', "richcategoryproducttab" ),
						"field_group" => __( 'Custom Settings', 'richcategoryproducttab' ),
					),
					'wcpt_hide_empty_category' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide empty categories', 'richcategoryproducttab' ),
						"is_required" => "no",	
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide empty categories. Default value is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'Custom Settings', 'richcategoryproducttab' ),
					), 
					'wcpt_show_all_pane' => array( 
						"type" => "boolean",
						"default" => 'yes',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Show "All" label tab for all category\'s post', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "no",	
						"description" => __( 'Show/Hide "All" label category tab that will display all posts of all the categories. Default value is "No".', "richcategoryproducttab" ),
						"field_group" => __( 'Custom Settings', 'richcategoryproducttab' ),
					),
					'wcpt_hide_comment_count' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide comments count', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide comments count of the posts. Default is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_hide_posted_date' => array( 
						"type" => "boolean",
						"default" => 'yes',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide posted date', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide posted date of posts. Default is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					), 
					'wcpt_hide_paging' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide ajax paging, load more or next-prev links', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide ajax paging, load more or next-prev links.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_hide_post_image' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide post image', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide the post image.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_hide_post_short_content' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide post short content', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide post short content.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_select_paging_type' => array( 
						"type" => "option",
						"default" => 'load_more_option',
						"options" => array(
							"load_more_option" => __( "Load more option", 'richcategoryproducttab' ),
							"next_and_previous_links" => __( "Next and previous links", 'richcategoryproducttab' ),
							"simple_numeric_pagination" => __( "Simple numeric pagination", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Pagination Type', 'richcategoryproducttab' ),
						"is_required" => "no",
						"pm" => 1,	
						"in_js" => "yes",	
						"description" => __( 'Select the ajax pagination type like load more option, next and previous links or simple numeric pagination.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_hide_post_short_content_length' => array( 
						"type" => "text",
						"default" => '40', 
						"field_title" => __( 'Short content character length', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Add the length of short content if short content has enabled to view. Default content length is 100', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
				   'wcpt_read_more_link' => array( 
						"type" => "boolean",
						"default" => 'yes',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide read more link', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide read more link. Default value is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_hide_product_price' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide product price', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide the product price. Default value is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_add_to_cart_button' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Hide add to cart button', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to hide add to cart button. Default value is "No" to display it.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_image_content_width' => array( 
						"type" => "text",
						"default" => '200', 
						"field_title" => __( 'Maximum image and content block width', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",
						"pm" => 1,	
						"description" => __( 'Set the width of image and content block in pixel. eg. 200 <br /> Note: Do not add "px" after the number', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_order_category_ids' => array( 
						"type" => "text",
						"default" => '', 
						"field_title" => __( 'Categories IDs', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Add comma separated categories IDs in order to short categories tabs. eg. 1,3,8,2', "richcategoryproducttab" ),
						"field_group" => __( 'Custom Settings', 'richcategoryproducttab' ),
					),
					'wcpt_image_height' => array( 
						"type" => "text",
						"default" => '200',
						"pm" => 1,	
						"field_title" => __( 'Maximum image height', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Set the height of image in pixel. eg. 200 <br /> Note: Do not add "px" after the number', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_shorting_posts_by' => array( 
						"type" => "option",
						"default" => 'date',
						"field_title" => __( 'Ordering/ shorting posts by', 'richcategoryproducttab' ),
						"is_required" => "no",
						"in_js" => "yes",		
						"options" => array(
							 "id" => __( "Post ID", "richcategoryproducttab" ),	 
							 "title" => __( "Title", "richcategoryproducttab" ),	 
							 "date" => __( "Posted/Created Date", "richcategoryproducttab" ),	 
						), 
						"description" => __( 'Select the shorting/ordering field like post id, title or posted/created date.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_post_ordering_type' => array( 
						"type" => "option",
						"default" => 'ascending',
						"field_title" => __( 'Select the post ordering type', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"options" => array(
							 "ascending" => __( "Ascending", "richcategoryproducttab" ),	 
							 "descending" => __( "Descending", "richcategoryproducttab" ),	  	 
						), 
						"description" => __( 'Change the post ordering/shorting like ascending, descending.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_space_margin_between_posts' => array( 
						"type" => "text",
						"default" => '15',
						"field_title" => __( 'Space/margin between posts', 'richcategoryproducttab' ),
						"is_required" => "no",	   
						"in_js" => "no",	
						"description" => __( 'Set the space/margin between posts items. eg. 15 <br /> Note: Do not add "px" after the number', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_posts_grid_alignment' => array( 
						"type" => "option",
						"default" => 'fit_to_sides',
						"options" => array(
							 "fit_to_sides" => __( "Auto adjust image width to maximum width", "richcategoryproducttab" ),	 
							 "fixed_width_center" => __( "Fixed/static image width with centered aligned", "richcategoryproducttab" ),	  	 
							 "fixed_width_left" => __( "Fixed/static image width with left aligned", "richcategoryproducttab" ),	  	 
						),
						"field_title" => __( 'Posts grid alignment', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "no",	
						"description" => __( 'Set the space/margin between posts items. eg. 10 <br /> Note: Do not add "px" after the number', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_posts_loading_effect_on_pagination' => array( 
						"type" => "option",
						"default" => 'none',
						"options" => array(
							 "none" => __( "None", "richcategoryproducttab" ),	 
							 "left" => __( "Loads grid posts from left", "richcategoryproducttab" ),	 
							 "right" => __( "Loads grid posts from right", "richcategoryproducttab" ),	  	 
							 "top" => __( "Loads grid posts from top", "richcategoryproducttab" ),	  	 
							 "bottom" => __( "Loads grid posts from bottom", "richcategoryproducttab" ),	  	 
						),
						"field_title" => __( 'Posts loading effect on pagination', 'richcategoryproducttab' ),
						"is_required" => "no",	   
						"in_js" => "no",	
						"description" => __( 'Select posts loading effect or animation style like loads post grid from left, right, top and bottom', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					),
					'wcpt_mouse_hover_effect' => array( 
						"type" => "option",
						"default" => 'ikh-image-style-0',
						"options" =>  $mouse_hover_effect_cls,
						"field_title" => __( 'Mouse hover effect', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select an animation style for the mouse hover of posts item.', "richcategoryproducttab" ),
						"field_group" => __( 'Custom Settings', 'richcategoryproducttab' ),
					),
					'wcpt_show_author_image_and_name' => array( 
						"type" => "boolean",
						"default" => 'no',
						"options" => array(
							"yes" => __( "Yes", 'richcategoryproducttab' ),
							"no" => __( "No", 'richcategoryproducttab' ),
						),
						"field_title" => __( 'Show author image and name', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"in_js" => "yes",	
						"description" => __( 'Select "Yes" to show the image and name of posts author.', "richcategoryproducttab" ),
						"field_group" => __( 'General Settings', 'richcategoryproducttab' ),
					), 
					'wcpt_default_category_open' => array( 
						"type" => "option",
						"default" => 'all',
						"field_title" => __( 'Select category to be opened as default', 'richcategoryproducttab' ),
						"is_required" => "no",	 
						"class" => "wcpt_default_category_open_opt", 
						"inherit_type" => "category_type",
						"in_js" => "no",	
						"options" => $_default_open_category_list,
						"description" => __( 'Select default category to be opened on accordion creation . Default value is "None".', "richcategoryproducttab" ),
						"field_group" => __( 'Custom Settings', 'richcategoryproducttab' ),
					),
					'st' => array(  
						"type" => "none",
						"in_js" => "no",	
						"flag" => get_option('richcategoryproducttab_license_status'),
					),	  
					'richcategoryproducttab_license_url' => array(
						"type" => "none",
						"in_js" => "no",	
						"license_url" => 'https://www.ikhodal.com/activate-license',
					),
					'wcpt_security_key' => array(   
						"type" => "none",
						"in_js" => "yes",	
						"vcode" => $this->getUCode(),
						"security_key" =>  'wcpt_#s@R$@ASI#TA(!@@21M3',
					), 
					'wcpt_media' =>  array( 
						"type" => "none",
						"in_js" => "no",	
						"media_url" => wcpt_media,
					),	
				);
				
				$this->_plugin_settings = $this->_config;
				$this->setPluginValue();  
			
		}
		
		
		/**
		 * Load all the fields from templates
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @return html
		 */
		function loadConfigFields(  $all_fields, $shortcode_config, $type ) {
		    
			$_field_html = array();  
			
			foreach( $all_fields as $kw => $kw_val ) {
			
				if( $kw_val["type"] == "color" ) 
					$_field_html[$all_fields[$kw]["field_group"]][] = $this->createInputColorField($kw, $all_fields, $shortcode_config, $type);
					
				else if( $kw_val["type"] == "text" ) 
					$_field_html[$all_fields[$kw]["field_group"]][] .= $this->createInputTextField($kw, $all_fields, $shortcode_config, $type);	
					
				else if( $kw_val["type"] == "option" ) 
					$_field_html[$all_fields[$kw]["field_group"]][] .= $this->createOptionField($kw, $all_fields, $shortcode_config, $type);	
					
				else if( $kw_val["type"] == "boolean" ) 
					$_field_html[$all_fields[$kw]["field_group"]][] .= $this->createBooleanField($kw, $all_fields, $shortcode_config, $type);		
				
				else if( $kw_val["type"] == "checkbox" ) 
					$_field_html[$all_fields[$kw]["field_group"]][] .= $this->createInputCheckboxField($kw, $all_fields, $shortcode_config, $type);	
				
			} 
			 
			$_field_html = array_reverse($_field_html);
			$_group_html = "";
			foreach( $_field_html as $key_group => $group_fields ) {
				
				$group_title = $key_group;
				$group_field = implode( "", $group_fields );
				
				// Load template according to admin settings
				ob_start();
				require( $this->getrichcategoryproducttabTemplate( 'fields/fld_group.php' ) );	
				$_group_html .= ob_get_clean();	  
				
			}
			
			return $_group_html;
		
		}
		
		/**
		 * Creates the checkbox fields with it's default value
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array  $fields  Contains all the fields for settings
		 * @param   array  $shortcode_config  Array of default/saved values
		 * @param   string $type Specify the type of field
		 */
		function createInputCheckboxField( $key, $fields, $shortcode_config, $type ) { 
		 
			if( isset( $fields[$key] ) ) {
				
				$default_val = $shortcode_config[$key];
				if( trim($shortcode_config[$key]) == "" ) {
					$default_val = $fields[$key]["default"]; 
				}

				if( isset($fields[$key]["inherit_type"]) && trim($fields[$key]["inherit_type"]) != "" ) {
							
					$_categories = $this->getCategoryDataByTaxonomy( $shortcode_config[$fields[$key]["inherit_type"]]  ) ;
					$_cat_array = array();
					$_default_open_category_list = array( "0"=>__( 'None', 'richcategoryproducttab' ), "all"=>__( 'All', 'richcategoryproducttab' ) );
					if( count( $_categories ) > 0 ) { 
						foreach( $_categories as $_category_items ) { 
							  $_cat_array[ $_category_items->id ] =  ($this->get_hierarchy_dash($_category_items->depth)).$_category_items->category; 
						} 
					}
					$fields[$key]["options"] = $_cat_array;
				}	
				
				// Load template according to admin settings
				ob_start();
				require( $this->getrichcategoryproducttabTemplate( 'fields/fld_checkbox.php' ) );	
				return ob_get_clean();	
				
			}
			
		}
		
		/**
		 * Creates the color field with it's default value
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array $fields  Contains all the fields for settings
		 * @param   string $shortcode_config  Array of default/saved values 
		 */
		function createInputColorField( $key, $fields, $shortcode_config, $type ) { 
		 
			if( isset( $fields[$key] ) ) {
			
				$default_val = $shortcode_config[$key];
				if( trim($shortcode_config[$key]) == "" ) {
					$default_val = $fields[$key]["default"]; 
				}  
				
				// Load template according to admin settings
				ob_start();
				require( $this->getrichcategoryproducttabTemplate( 'fields/fld_color.php' ) );	
				return ob_get_clean();	
				
			}
			
		} 
		
		/**
		 * Creates the boolean form field for the admin
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array  $fields  Contains all the fields for settings
		 * @param   array  $shortcode_config  Array of default/saved values
		 * @param   string $type Specify the type of field
		 */
		function createBooleanField( $key, $fields, $shortcode_config, $type ) {

			if( isset( $fields[$key] ) ) {
			
				$default_val = $shortcode_config[$key];
				if( trim($shortcode_config[$key]) == "" ) {
					$default_val = $fields[$key]["default"]; 
				}
				
				// Load template according to admin settings
				ob_start();
				require( $this->getrichcategoryproducttabTemplate( 'fields/fld_boolean.php' ) );	
				return ob_get_clean();	
			}
			 
		}	
		
		/**
		 * Creates the drop down field for the admin settings
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array  $fields  Contains all the fields for settings
		 * @param   array  $shortcode_config  Array of default/saved values
		 * @param   string $type Specify the type of field
		 */
		function createOptionField( $key, $fields, $shortcode_config, $type ) { 
		
			if( isset( $fields[$key] ) ) {
			
				$default_val = $shortcode_config[$key];
				if( trim($shortcode_config[$key]) == "" ) {
					$default_val = $fields[$key]["default"]; 
				}			
				
				if( isset($fields[$key]["inherit_type"]) && trim($fields[$key]["inherit_type"]) != "" ) {
							
					$_categories = $this->getCategoryDataByTaxonomy( $shortcode_config[$fields[$key]["inherit_type"]]  ) ;
					$_default_open_category_list = array( "0"=>__( 'None', 'richcategoryproducttab' ), "all"=>__( 'All', 'richcategoryproducttab' ) );
					if( count( $_categories ) > 0 ) { 
						foreach( $_categories as $_category_items ) {   
							  $_default_open_category_list[ $_category_items->id ] =  ($this->get_hierarchy_dash($_category_items->depth)).$_category_items->category; 
						} 
					}
					$fields[$key]["options"] = $_default_open_category_list;
				}
				
				// Load template according to admin settings
				ob_start();
				require( $this->getrichcategoryproducttabTemplate( 'fields/fld_option.php' ) );	
				return ob_get_clean();	
				
			}
			
		}
		
		/**
		 * Creates the text field for the admin settings
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array  $fields  Contains all the fields for settings
		 * @param   array  $shortcode_config  Array of default/saved values
		 * @param   string $type Specify the type of field
		 */
		function createInputTextField( $key, $fields, $shortcode_config, $type ) { 
		
			if( isset( $fields[$key] ) ) {
			
				$default_val = $shortcode_config[$key];
				if( trim($shortcode_config[$key]) == "" ) {
					$default_val = $fields[$key]["default"]; 
				}
				
				// Load template according to admin settings
				ob_start();
				require( $this->getrichcategoryproducttabTemplate( 'fields/fld_text.php' ) );	
				return ob_get_clean();	
				
			}
			
		}
		
		/**
		 * Load the CSS/JS scripts
		 *
		 * @return  void
		 *
		 * @access  public
		 * @since   1.0
		 */
		function richcategoryproducttab_scripts() {
		
			/**
			 * Default values configuration 
			 */
			$this->init_settings();

			$dependencies = array( 'jquery' );
			 
			/**
			 * Include Rich Woocommerce Category and Product Tab JS/CSS 
			 */
			wp_enqueue_style( 'richcategoryproducttab', $this->_config["wcpt_media"]["media_url"]."css/richcategoryproducttab.css" );
			 
			wp_enqueue_script( 'richcategoryproducttab', $this->_config["wcpt_media"]["media_url"]."js/richcategoryproducttab.js", $dependencies  );
			
			/**
			 * Define global javascript variable
			 */
			wp_localize_script( 'richcategoryproducttab', 'richcategoryproducttab', array(
				'wcpt_ajax_url' => admin_url( 'admin-ajax.php' ),
				'wcpt_security'  =>  wp_create_nonce($this->_config["wcpt_security_key"]["security_key"]),
				'wcpt_media'  => $this->_config["wcpt_media"]["media_url"],
				'wcpt_all'  => __( 'All', 'richcategoryproducttab' ),
				'wcpt_plugin_url' => plugins_url( '/', __FILE__ ),
			)); 
			
			
		}	
		
		
		/**
		 * Loads categories as per taxonomy 
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string  $taxonomy  Type of category
		 * @return  object  Returns categories object
		 */ 
		 public function getCategoryDataByTaxonomy( $taxonomy ) {
				 
			global $wpdb;
			 
			if( !$taxonomy || trim( $taxonomy ) == "" )
				$taxonomy = "category";
					  
			/**
			 * Fetch all the categories from database of the provided type
			 */  
			$_categories = $wpdb->get_results( "SELECT wtt.term_taxonomy_id as id,wtt.term_taxonomy_id as term_id,wtm.meta_value as depth,wtt.parent, wt.name as name, wt.name as category FROM `{$wpdb->prefix}terms` as wt INNER JOIN {$wpdb->prefix}term_taxonomy as wtt on wtt.term_id = wt.term_id and wtt.taxonomy = 'product_cat' INNER JOIN {$wpdb->prefix}termmeta as wtm on wtm.term_id = wt.term_id and wtm.meta_key = 'order' ");			 
			  
			$orderby = 'name';
			$order = 'asc';
			$hide_empty = false ;
			$cat_args = array(
				'orderby'    => $orderby,
				'order'      => $order,
				'hide_empty' => $hide_empty,
			); 
			$_cats = (array)$_categories;  
			return	$_cats;
			
		}
		
		/**
		 * Loads ajax categories as per type selection
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function wcpt_getCategoriesOnTypes() { 
		
			global $wpdb;
			
			/**
			* Check security token from ajax request
			*/
			check_ajax_referer( $this->_config["wcpt_security_key"]["security_key"], 'security' );
			
			$__category_type = "product_cat";
			$_flh = 0;
			
			/**
			 * Fetch all the categories from database of the provided type
			 */  
			$_categories = $this->getCategoryDataByTaxonomy( $__category_type ) ;
			
			if( count( $_categories ) > 0 ) { 
			
				if( isset( $_REQUEST["category_field_name"] ) && !empty( $_REQUEST["category_field_name"] ) ) {
				
					$_category_field_name = sanitize_text_field( $_REQUEST['category_field_name'] );
				
					foreach( $_categories as $_category_items ) { 
						 					 
						?><p><input  class="checkbox-category-ids" type="checkbox" name="<?php echo $_category_field_name; ?>[]" id="ckCategory_<?php echo $_category_items->id; ?>" onchange="ck_category_check(this)" value="<?php echo $_category_items->id; ?>" /><label for ="ckCategory_<?php echo $_category_items->id; ?>" ><?php echo ($this->get_hierarchy_dash($_category_items->depth)).$_category_items->category; ?></label></p><?php 							
						
					}
					
				
				} else {
					
					foreach( $_categories as $_category_items ) { 
						 
						?><p><input  class="checkbox-category-ids" type="checkbox" name="nm_category_id[]" id="ckCategory_<?php echo $_category_items->id; ?>" onchange="ck_category_check(this)" value="<?php echo $_category_items->id; ?>" /><label for ="ckCategory_<?php echo $_category_items->id; ?>" ><?php echo ($this->get_hierarchy_dash($_category_items->depth)).$_category_items->category; ?></label></p><?php 
						
					}
				
				}
				
				$_flh = 1;  
			}  
		 
			
			if( $_flh == 0 )  
					 echo __( 'No category found.', 'richcategoryproducttab' );  
			die();
			 
		}
		
		
		/**
		 * Loads ajax categories radio button as per type selection
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function wcpt_getCategoriesRadioOnTypes() { 

			global $wpdb;
			
			/**
			* Check security token from ajax request
			*/
			check_ajax_referer( $this->_config["wcpt_security_key"]["security_key"], 'security' );
			
			$__category_type = "product_cat";
			$_flh = 0;
		 
			
			/**
			 * Fetch all the categories from database of the provided type
			 */  
			$_categories = $this->getCategoryDataByTaxonomy( $__category_type ) ;
			
			if( count( $_categories ) > 0 ) { 
			
				?><option value="0"><?php echo __( 'None', 'richcategoryproducttab' ); ?></option><?php 
				?><option value="all"><?php echo __( 'All', 'richcategoryproducttab' ); ?></option><?php 
			
				foreach( $_categories as $_category_items ) { 
					
					?><option value="<?php echo $_category_items->id; ?>">
						<?php echo ($this->get_hierarchy_dash($_category_items->depth)).$_category_items->category; ?>
					</option><?php  
					
				} 
				
				$_flh = 1;  
			}  
		 
			
			if( $_flh == 0 )  
					 echo __( 'No category found.', 'richcategoryproducttab' );  
			die();
			 
		}

		
		/**
		 * Loads the text domain
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function richcategoryproducttab_text_domain() {

		  /**
		   * Load text domain
		   */
		   load_plugin_textdomain( 'richcategoryproducttab', false, wcpt_plugin_dir . '/languages' );
			
		}
		 
		/**
		 * Load and register widget settings
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */ 
		public function initrichcategoryproducttab() { 
			
		  /**
		   * Widget registration
		   */
		   register_widget( 'richcategoryproducttabWidget_Admin' );
			
		}     
		
		/**
		 * Get the new image as per width and height from a image source based on new image size calculation
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $img_sc Path of the image 
		 * @param   string $re_width  New width of the image to be displayed on front view
		 * @param   string $re_height  New height of the image to be displayed on front view 
		 * @return  array It returns array of the image size.
		 */  
		function getWPImage($img_sc, $re_width, $re_height) { 
				 
			$quality = 80; 
			if($re_height=="auto")
				$re_height = "180";
			
			$file_parts = explode(".", $img_sc);
			$extention = strtolower( $file_parts[ count( $file_parts ) - 1 ] );
			$_site_urlpath = $directory_cache_root = 'wp-content/uploads'; 
			if(!is_dir($directory_cache_root)) { 
				$directory_cache_root = '../wp-content/uploads';
			} 
			
			$directory_cache = $directory_cache_root.'/pl_cache'; 
			$cache = md5( $img_sc . $re_width . $re_height ).".".strtolower($extention); 
			if(!file_exists($directory_cache)) { 
				mkdir($directory_cache); 
				chmod($directory_cache, 0777);
			}   		
			
			$img_type = array(
				'jpg'  => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'png'  => 'image/png',
				'gif'  => 'image/gif',
				'bmp'  => 'image/bmp',  
			); 
			$imgtype = $img_type[$extention];
			if(!strlen($imgtype)) { $imgtype = 'unknown'; }		
			 
			$image_url = $directory_cache . '/' . $cache;  
			$_site_urlpath = $_site_urlpath . '/pl_cache/' . $cache; 
			 
			if ( !file_exists($image_url)) { 
				if( touch( $image_url ) ) { 
					chmod( $image_url, 0666 ); 
				}  
			}   
			 
			$max_width = $re_width;
			$max_height = $re_height;  
			$image = $img_sc;   
			$size	= GetImageSize( $image );
			$mime	= $size['mime']; 

			$width = $size[0];
			$height = $size[1]; 
			$color		= FALSE; 
			if (!$max_width && $max_height) 
				$max_width	= 99999999999999; 
			elseif ($max_width && !$max_height) 
				$max_height	= 99999999999999; 
			 
			if ( $max_width >= $width && $max_height >= $height ) {
				 $max_width = $width;
				 $max_height = $height;
			}  
			$xRatio		= $max_width / $width;
			$yRatio		= $max_height / $height; 
			if ($xRatio * $height < $max_height) {  
				$img_new_height	= ceil($xRatio * $height);
				$img_new_width	= $max_width;
			} else {
				$img_new_width	= ceil($yRatio * $width);
				$img_new_height	= $max_height;
			}  
			$quality = 90;   
			 
			$img_dest = imagecreatetruecolor($img_new_width, $img_new_height); 
			switch ($size['mime'])
			{
				case 'image/gif': 
					$img_create	= 'ImageCreateFromGif';
					$img_output_function = 'ImagePng';
					$mime = 'image/png';  
					$is_sharpen = FALSE;
					$quality = round(10 - ($quality / 10));  
				break; 
				case 'image/x-png':
				case 'image/png':
					$img_create	= 'ImageCreateFromPng';
					$img_output_function = 'ImagePng';
					$is_sharpen = FALSE;
					$quality = round(10 - ($quality / 10)); 
				break;
				
				default:
					$img_create	= 'ImageCreateFromJpeg';
					$img_output_function = 'ImageJpeg';
					$is_sharpen = TRUE;
				break;
			}
			 
			$img_source	= $img_create( $image); 
			if (in_array($size['mime'], array('image/gif', 'image/png'))) {
				if (!$color) { 
					imagealphablending($img_dest, false);
					imagesavealpha($img_dest, true);
				}
				else {
					 if ($color[0] == '#')
						$color = substr($color, 1);
					
					$background	= FALSE;
					
					if (strlen($color) == 6)
						$background	= imagecolorallocate($img_dest, hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));
					else if (strlen($color) == 3)
						$background	= imagecolorallocate($img_dest, hexdec($color[0].$color[0]), hexdec($color[1].$color[1]), hexdec($color[2].$color[2]));
					if ($background)
						imagefill($img_dest, 0, 0, $background);
				}
			}
			 
			ImageCopyResampled($img_dest, $img_source, 0, 0, 0, 0, $img_new_width, $img_new_height, $width, $height);

			if ($is_sharpen) {
				 
				$img_new_width	= $img_new_width * (750.0 / $width);
				$ik_a		= 52;
				$ik_b		= -0.27810650887573124;
				$ik_c		= .00047337278106508946;
				
				$ik_result = $ik_a + $ik_b * $img_new_width + $ik_c * $img_new_width * $img_new_width; 
				$srp	= max(round($ik_result), 0);
				
				$image_sharpen	= array(
					array(-1, -2, -1),
					array(-2, $srp + 12, -2),
					array(-1, -2, -1)
				);
				$divisor		= $srp;
				$offset			= 0;
				imageconvolution($img_dest, $image_sharpen, $divisor, $offset);
			} 
			$img_output_function($img_dest, $image_url, $quality); 
			ImageDestroy($img_source);
			ImageDestroy($img_dest);  
			 
			return  $_site_urlpath; 
		 
		 }	
		 
		/**
		 * Get post image by given image attachment id and image size
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   int $img  Attachment ID of the image
		 * @param   int $width  Specify the new width of the image
		 * @param   int $height  Specify the new height of the image 
		 * @return  string  Returns the image html from the post attachment
		 */
		 public function getPostImage(  $img, $width = "180", $height = "180") {
		 
			$image_link = wp_get_attachment_url( $img ); 
			  
			if( $image_link ) {				
				$image_title = esc_attr( get_the_title( $img ) ); 
				$_src = site_url()."/".$this->getWPImage($image_link, $width, $height);  
				return "<div style='min-height:".$height."px'><img title='".$image_title."'  alt='".$image_title."'  src='".$_src."' /></div>";
			} else {
				$_defa_media_image = $this->_config["wcpt_media"]["media_url"]."images/no-img.png";
				$_src = site_url()."/".$this->getWPImage( $_defa_media_image, $width, $height);
				return "<div style='min-height:".$height."px'><img src='".$_src."' /></div>";		 
			} 
			
		 }
	
		/**
		 * Get all the categories
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $category_ids Specify the comma separated categories IDs 
		 * @param   string $ordering_ids Order by comma separated IDs
		 * @return  object It contains all the categories by type or IDs
		 */ 
		public function getCategories($category_ids = "", $ordering_ids = "", $wcpt_short_category_name_by = "asc") {

			global $wpdb; 
			$ordering_ids_list = $ordering_ids;
			$wcpt_short_category_name_by = $wcpt_short_category_name_by; 
			$_short_order = "ASC";
			if($wcpt_short_category_name_by=="asc")
				$_short_order = "ASC";
			else	
				$_short_order = "DESC"; 

			$__category_type = "product_cat"; 
			
			$_cats_ordering = array();
			if(trim($ordering_ids) != "") {
				$ordering_ids = explode( ",", $ordering_ids );
				if( count($ordering_ids) > 1 ) {
					foreach( $ordering_ids as $ordering_id ) {
						$_cats_ordering[] = get_term_by('id', $ordering_id , $__category_type);
					}
				}
			}
			
			if(trim($category_ids) != "")
				$_cats = get_terms( $__category_type, array('include'=>$category_ids,'hide_empty'=>false,'order'=>$_short_order) );
			else	
				$_cats = get_terms( $__category_type, array('hide_empty'=>false,'order'=>$_short_order,'exclude'=>$ordering_ids_list) );  
			 
			$__all_categories = array(); 
			if( count($_cats_ordering) > 1 ) {
				
				foreach($_cats_ordering as $_cat_item){
					$__all_categories[] = $_cat_item;
				}
				foreach($_cats as $_cat_item){
				
					if( !in_array( $_cat_item->term_id, $ordering_ids ) ) {
						$__all_categories[] = $_cat_item;
					}
					
				}
				$_cats = $__all_categories; 
				
			}
			
			$_cats = (array)$_cats;
			$this->sort_terms_hierarchy($_cats);  
			return $_cats;
		}
		
		/**
		 * Short terms hierarchy order
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   array $terms terms array to make hierarchy
		 * @return  object It contains all the hierarchy terms for shop
		 */
		function sort_terms_hierarchy(Array &$terms) {
			$result = array();
			$parent = 0;
			$depth = 0;
			$i = 0;
			do {
				$temp = array();
				foreach($terms as $j => $term) {
					if ($term->parent == $parent) {
						$term->depth = $depth;  
						array_push($temp, $term);
						unset($terms[$j]);
					} 
					$term->category = $term->name;
					$term->id = $term->term_taxonomy_id;
				}
				array_splice($result, $i, 0, $temp);
				if(isset($result[$i])){
					$parent = $result[$i]->term_id;
					$depth = $result[$i]->depth + 1;
				}
			} while ($i++ < count($result));
			$terms = $result;
		} 
		
		/**
		 * Get the number of dash string
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   number $depth numberic value that indicates the depth of term
		 * @return  string It returns dash string.
		 */
		function get_hierarchy_dash($depth) {
			$_dash = "";
			for( $i = 0; $i < $depth; $i++ ) {
				$_dash .= "--"; 
			} 
			return $_dash." ";
		}
		

		/**
		* Fetch post data from database by category, search text and item limit
		*
		* @access  public
		* @since   1.0 
		* 
		* @param   int    $category_id  		Category ID 
		* @param   string $post_search_text  Post name or any search keyword to filter posts
		* @param   int    $_limit_start  		Limit to fetch post starting from given position
		* @param   int    $_limit_end  			Limit to fetch post ending to given position
		* @param   int    $category_flg  		Whether to fetch whether posts by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  To check settings of default category If it's value is '1'. Default value is '0'
		* @param   int    $is_count  			Whether to fetch only number of posts from database as count of items 
		* @param   int    $_is_last_updated  	Whether to fetch only last updated post or not
		* @return  object Set of searched post data
		*/
		function getSqlResult( $category_id, $post_search_text, $_limit_start, $_limit_end, $category_flg = 0, $is_default_category_with_hidden = 0, $is_count = 0, $_is_last_updated = 0 ) {
			
			global $wpdb; 
			$_category_filter_query = "";
			$_post_text_filter_query = "";
			$_fetch_fields = "";
			$_limit = "";
			
			$__post_type = "product";
			$category_type = "product_cat"; 
			
			$wcpt_shorting_posts_by = $this->_config["wcpt_shorting_posts_by"];
			$wcpt_post_ordering_type = $this->_config["wcpt_post_ordering_type"];
			if( isset( $_REQUEST['wcpt_shorting_posts_by'] ) && (trim( $_REQUEST['wcpt_shorting_posts_by'] ) == "id" || trim( $_REQUEST['wcpt_shorting_posts_by'] ) == "title" || trim( $_REQUEST['wcpt_shorting_posts_by'] ) == "date" ) ) {			
				$wcpt_shorting_posts_by = sanitize_text_field( $_REQUEST['wcpt_shorting_posts_by'] );	 
			}
			if(trim($wcpt_shorting_posts_by)=="id")
				$wcpt_shorting_posts_by = "ID";
			if(trim($wcpt_shorting_posts_by)=="title")
				$wcpt_shorting_posts_by = "post_title";
			if(trim($wcpt_shorting_posts_by)=="date")
				$wcpt_shorting_posts_by = "post_date";	
				
			if( isset( $_REQUEST['wcpt_post_ordering_type'] ) && ( trim( $_REQUEST['wcpt_post_ordering_type'] ) == "ascending" || trim( $_REQUEST['wcpt_post_ordering_type'] ) == "descending" ) ) {			
				$wcpt_post_ordering_type = sanitize_text_field( $_REQUEST['wcpt_post_ordering_type'] );	 
			}
			if(trim($wcpt_post_ordering_type)=="ascending")
				$wcpt_post_ordering_type = "ASC";	
			if(trim($wcpt_post_ordering_type)=="descending")
				$wcpt_post_ordering_type = "DESC";
	
			
		   /**
			* Prepare safe mysql database query
			*/ 
			
			if( strpos( $category_id, "," ) > 0 ) {
				$arr_category_id = explode( "," , $category_id );
				$category_id = array();
				foreach ($arr_category_id as $__k => $__v) {
					$category_id[] = intval($__v);	
				}
				$category_id  = implode("','", $category_id);
			} else if( trim( $category_id ) == "all" ) {  
					$_category_filter_query .=  $wpdb->prepare( "INNER JOIN {$wpdb->prefix}term_taxonomy as wtt on wtt.taxonomy = %s  INNER JOIN {$wpdb->prefix}term_relationships as wtr on  wtr.term_taxonomy_id = wtt.term_taxonomy_id and wtr.object_id = wp.ID ", $category_type );  
			} else {
				$category_id  = intval( $category_id );
			} 
			
			if( $is_count == 1 ) {
				if( (trim($category_id) != "0" && trim($category_id) != "") && ( $category_flg == 1 || $is_default_category_with_hidden == 1 ) ) {
					$_category_filter_query .=  " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id in ('".$category_id."') and wtr.object_id = wp.ID ";
				} 
				$_fetch_fields = " count(*) as total_val ";
			} else { 
				if( trim($category_id) != "0" && trim($category_id) != "" ) {
					$_category_filter_query .=  " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id in ('".$category_id."') and wtr.object_id = wp.ID ";
				} 
				$_fetch_fields = " wp.post_type, pm_users.display_name, wp.post_content, pm.meta_value as sale_price, pm_image.meta_value as post_image, wp.ID as post_id, wp.post_title as post_title, wp.post_date, wp.post_author ";
				
				if( $_is_last_updated == 1 )
					$_limit = $wpdb->prepare( " group by wp.ID order by wp.".$wcpt_shorting_posts_by." ".$wcpt_post_ordering_type." limit  %d, %d ", 0, 1 );
				else
					$_limit = $wpdb->prepare( " group by wp.ID order by wp.".$wcpt_shorting_posts_by." ".$wcpt_post_ordering_type." limit  %d, %d ", $_limit_start, $_limit_end );
			} 
			
			if( $post_search_text != "" ) { 
				$_post_text_filter_query .= trim( " and wp.post_title like '%".esc_sql( $post_search_text )."%'" );
			}
			if( $__post_type != "" ) {
				$_post_text_filter_query .= $wpdb->prepare( " and wp.post_type = %s ", $__post_type );
			} 	 
			 
		   /**
			* Fetch post data from database
			*/
			$_result_items = $wpdb->get_results( " select $_fetch_fields from {$wpdb->prefix}posts as wp  
				$_category_filter_query LEFT JOIN {$wpdb->prefix}postmeta as pm_image on pm_image.post_id = wp.ID and pm_image.meta_key = '_thumbnail_id'
				LEFT JOIN {$wpdb->base_prefix}users as pm_users on pm_users.ID = wp.post_author
				INNER JOIN {$wpdb->prefix}postmeta as pm on pm.post_id = wp.ID and pm.meta_key = '_price' 
				INNER JOIN {$wpdb->prefix}postmeta as pm_stock on pm_stock.post_id = wp.ID and pm_stock.meta_key = '_stock_status' 
				INNER JOIN {$wpdb->prefix}postmeta as pm_backorders on pm_backorders.post_id = wp.ID and pm_backorders.meta_key = '_backorders' 
				where wp.post_status = 'publish' $_post_text_filter_query $_limit " );			
				
				  
			return $_result_items;

		}
 		
		/**
		 * Get all the post types
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @return  object It contains all the types of posts
		 */
		public function richcategoryproducttab_getPostTypes() {
		
			global $wpdb;
			 
			return $wpdb->get_results( "SELECT post_type FROM {$wpdb->prefix}posts where post_type not in ('product','revision') group by post_type" );
		
		}
		
		 
		/**
		 * Get Unique Block ID
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @return  string 
		 */
		public function getUCode() { 
			
			return 'uid_'.md5( "KASITAJDDRAM@wcpt".time() ); 
		
		} 
		
		/**
		 * Get Rich Woocommerce Category and Product Tab Template
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $file Template file name
		 * @return  string Returns template file path
		 */
		public function getrichcategoryproducttabTemplate( $file ) {
			 
			// Get template file path
			if( locate_template( $file ) != "" ){
				return locate_template( $file );
			}else{
				return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file ;
			}  
	   }
	   
	   	/**
		 * Validate the plugin
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @return    void
		 */
	   function setPluginValue() { 
			
			$license 	= get_option( 'richcategoryproducttab_license_key' );
			$status 	= get_option( 'richcategoryproducttab_license_status' );
			$_valid_key = md5(home_url().$status.$license);
			$ls_reff 	= get_option( 'richcategoryproducttab_license_reff' ); 
			$_st = 'ac';  
			if( $ls_reff != $_valid_key ) {  
			
				$license = trim( get_option( 'richcategoryproducttab_license_key' ) ); 
				$api_params = array( 
					'action'=> 'deactivate_license', 
					'license' 	=> $license, 
					'item_name' => 'wp_richcategoryproducttab', 
					'url'       => home_url()
				);
				
				$response = wp_remote_get( add_query_arg( $api_params, $this->_config["richcategoryproducttab_license_url"]["license_url"] ), array( 'timeout' => 15, 'sslverify' => false ) );
				if ( is_wp_error( $response ) )
					wp_redirect(site_url()."/wp-admin/edit.php?post_type=wcpt_tabs&page=richcategoryproducttab_settings&st=10");
				
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				
				delete_option( 'richcategoryproducttab_license_status' );
				delete_option( 'richcategoryproducttab_license_key' );
				delete_option( 'richcategoryproducttab_license_reff' ); 
				$this->_config['st']['flag'] = $_st."r";
			
			}
			
	   }
	   
	   
	    /**
		 * Replace the specific text into the string
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $wc_string  Content string
		 * @param   string $replace_from  string to be replaced from
		 * @param   string $replace_to  By which string should be replaced 
		 * @return  string Returns replaced string content
		 */ 
		function wcpt_replace( $wc_string, $replace_from=",", $replace_to="-" ) {
						
			$_pattern = array();
			$_pattern[0] = '/'.$replace_from.'/';
			$_replace = array();
			$_replace[0] =  $replace_to;
			
			return preg_replace( $_pattern, $_replace, $wc_string); 
			
		}  
		
		/**
		 * Load the pagination with list of items
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   int $org_page Specify the current page no.
		 * @param   int $total Specify the total number of the pages
		 * @param   int $category_id Category ID
		 * @param   int $_limit_start  Limit to fetch post starting from given position
		 * @param   int $_limit_end    Limit to fetch post ending to given position
		 * @param   string $params_vcode  Specify the plugin view code
		 * @param   int $flg Specify to show only next previous pagination or show the full pagination links 
		 * @return  string Returns ajax pagination links
		 */ 
		function displayPagination(  $org_page, $total, $category_id, $_limit_start, $_limit_end, $params_vcode, $flg = 1 ) {

				$page = ($org_page == 0 ? 1 : $org_page + 1); 
				$start = ($page - 1) * $_limit_end;                              
				$adj = "1"; 
				$prev = (intval($org_page) == 0)?1:$page;             
				$next = (intval($org_page) == 0)?1:$page;
				$pageEnd = ceil($total/$_limit_end);
				$nxtprv = $pageEnd - 1; 
				$setPaginate = "";
				if($pageEnd > 1)
				{  
					$setPaginate .= "<ul class='wcpt-st-paging'>";  
					if($page>1)	
					$setPaginate.= "<li><a  href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( ($prev-1) * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' > &laquo; ".__( 'Previous', 'richcategoryproducttab' )."</a></li>";
					
					$setPaginate1 = ""; 
					
					if ($pageEnd < 7 + ($adj * 2))
					{  
						for ($counter = 1; $counter <= $pageEnd; $counter++)
						{
							if ($counter == $page)
								$setPaginate1.= "<li><a class='current_page'>$counter</a></li>";
							else
								$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($counter * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$counter</a></li>";                 
						}
					}
					elseif($pageEnd > 5 + ($adj * 2))
					{
						if($page < 1 + ($adj * 2))    
						{
							for ($counter = 1; $counter < 4 + ($adj * 2); $counter++)
							{
								if ($counter == $page)
									$setPaginate1.= "<li><a class='current_page'>$counter</a></li>";
								else
									$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($counter * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$counter</a></li>";     
									
							}
							$setPaginate1.= "<li class='dot'>...</li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($nxtprv * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$nxtprv</a></li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($pageEnd * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$pageEnd</a></li>";     
							 
						}
						elseif($pageEnd - ($adj * 2) > $page && $page > ($adj * 2))
						{
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( 1 * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )'>1</a></li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( 2 * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )'>2</a></li>";
							$setPaginate1.= "<li class='dot'>...</li>";
							for ($counter = $page - $adj; $counter <= $page + $adj; $counter++)
							{
								if ($counter == $page)
									$setPaginate1.= "<li><a class='current_page'>$counter</a></li>";
								else if( $counter != 2 )
									$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($counter * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$counter</a></li>";                 
							}
							$setPaginate1.= "<li class='dot'>..</li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($nxtprv * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$nxtprv</a></li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($pageEnd * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$pageEnd</a></li>";     
						}
						else
						{
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( 1 * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >1</a></li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( 2 * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )'>2</a></li>";
							$setPaginate1.= "<li class='dot'>..</li>";
							for ($counter = $pageEnd - (2 + ($adj * 2)); $counter <= $pageEnd; $counter++)
							{
								if ($counter == $page)
									$setPaginate1.= "<li><a class='current_page'>$counter</a></li>";
								else
									$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($counter * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )'>$counter</a></li>";                 
							}
						}
					} 
							
					if( $flg == 1) {
						$setPaginate .= $setPaginate1;
					} else {
						$setPaginate .= "<li class='bet-pages'>$page / $pageEnd</li>";
					}

					if ($page < $counter - 1){
						$setPaginate.= "<li><a  href=\"javascript:void(0)\" onclick = 'wcpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( ($next+1) * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >".__( 'Next', 'richcategoryproducttab' )."  &raquo;</a></li>";						
					} 
					
					$setPaginate.= "<li><a class='wp-load-icon-li'><img width='18px' height='18px' src='".wcpt_media."images/loader.gif' /></a></li>";
		 
					$setPaginate.= "</ul>\n";    
				}
			 
			 
				return $setPaginate;
		}
		
		
		/**
		 * Replace the specific text into the string
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $wc_string  Content string
		 * @param   string $replace_from  string to be replaced from
		 * @param   string $replace_to  By which string should be replaced 
		 * @return  string Returns replaced string content
		 */ 
		function wcpt_js_obj($data_object) { 
		
				$_js_data_ob = array(); 
				foreach( $data_object as $ob_key => $ob_val ) {
					 
					if( (isset($this->_plugin_settings[$ob_key]["in_js"]) && $this->_plugin_settings[$ob_key]["in_js"] == "yes") ||  $ob_key == "vcode" )
						$_js_data_ob[] = $ob_key.":'".esc_js($ob_val)."'";				

				}
				return 'var request_obj_'.$data_object["vcode"].' = { '.implode( ",", $_js_data_ob ).' } ';				
		}  
	    
		
   }
}