<?php if ( ! defined( 'ABSPATH' ) ) exit;   $vcode = $this->_config["vcode"];   ?>
<script type='text/javascript' language='javascript'><?php echo $this->wcpt_js_obj( $this->_config ); ?></script> 
<?php    
$_categories = $this->_config["category_id"];
$_is_rtl_enable = $this->_config["wcpt_enable_rtl"];
$wcpt_enable_post_count = $this->_config["wcpt_enable_post_count"];
$wcpt_hide_empty_category = $this->_config["wcpt_hide_empty_category"];
$wcpt_default_category_open = $this->_config["wcpt_default_category_open"];
$wcpt_short_category_name_by = $this->_config["wcpt_short_category_name_by"];
$wcpt_hide_paging = $this->_config["wcpt_hide_paging"]; 
$wcpt_hide_post_image = $this->_config["wcpt_hide_post_image"]; 
$wcpt_hide_post_short_content = $this->_config["wcpt_hide_post_short_content"]; 
$wcpt_select_paging_type = $this->_config["wcpt_select_paging_type"]; 
$wcpt_hide_post_short_content_length = $this->_config["wcpt_hide_post_short_content_length"]; 
$wcpt_show_all_pane = $this->_config["wcpt_show_all_pane"]; 
$wcpt_read_more_link = $this->_config["wcpt_read_more_link"]; 
$wcpt_order_category_ids = $this->_config["wcpt_order_category_ids"]; 
$wcpt_image_content_width = $this->_config["wcpt_image_content_width"];	
$wcpt_image_height = $this->_config["wcpt_image_height"]; 
$wcpt_shorting_posts_by = $this->_config["wcpt_shorting_posts_by"]; 
$wcpt_post_ordering_type = $this->_config["wcpt_post_ordering_type"]; 
$_wcpt_image_height_class = ""; 
 
if( $wcpt_short_category_name_by != "id" ) 
	$wcpt_order_category_ids = "";
	
$wcpt_space_margin_between_posts = $this->_config["wcpt_space_margin_between_posts"];
$wcpt_posts_grid_alignment = $this->_config["wcpt_posts_grid_alignment"];
$wcpt_posts_loading_effect_on_pagination = $this->_config["wcpt_posts_loading_effect_on_pagination"];
$wcpt_mouse_hover_effect = $this->_config["wcpt_mouse_hover_effect"];
$wcpt_show_author_image_and_name = $this->_config["wcpt_show_author_image_and_name"]; 
$template = $this->_config["template"];

$_u_agent = $_SERVER['HTTP_USER_AGENT'];
$_m_browser = '';  
if(strpos($_u_agent,'MSIE')>-1)
	$_m_browser = 'cls-ie-browser';
	
?> 
<div id="richcategoryproducttab" style="width:<?php echo esc_attr($this->_config["tp_widget_width"]); ?>"  class="<?php echo ((trim($_is_rtl_enable)=="yes")?"wcpt-rtl-enabled":""); ?>   cls-<?php echo $wcpt_posts_grid_alignment; ?> <?php echo $template; ?> ">
	<?php if($this->_config["hide_widget_title"]=="no"){ ?>
		<div class="ik-pst-tab-title-head" style="background-color:<?php echo esc_attr( $this->_config["header_background_color"] ); ?>;color:<?php echo esc_attr( $this->_config["header_text_color"] ); ?>"  >
			<?php echo esc_html( $this->_config["widget_title"] ); ?>   
		</div>
	<?php } ?> 
	<span class='wp-load-icon'>
		<img width="18px" height="18px" src="<?php echo wcpt_media.'images/loader.gif'; ?>" />
	</span>
	<div class="wea_content <?php echo $_m_browser; ?>  lt-tab <?php echo esc_attr($wcpt_select_paging_type); ?>">
		
		<?php
			$_image_width_item = 0;
			if(   intval($wcpt_image_content_width) > 0 ) {
				$_image_width_item = intval($wcpt_image_content_width); 
			}	 
		?>
		<input type="hidden" class="imgwidth" value = "<?php echo $_image_width_item; ?>" />
		
		<?php  
			$_category_res = array();
			$_total_post_count = 0;
			$_category_res_n = array();
			
			if( trim($_categories)=="0" || trim($_categories) == "" )
				$_category_res = $this->getCategories("",$wcpt_order_category_ids);
			else 
				$_category_res = $this->getCategories($_categories,$wcpt_order_category_ids); 
				 
			if( count( $_category_res ) > 0 ) { 
				 
			
				foreach( $_category_res as $_category ) { 
					$_total_post_count = $_total_post_count + $_category->count;
				}
			 
				if( trim($wcpt_show_all_pane) == "yes" ) { 
					
					$arr_category_title = array();
					if( count( $_category_res ) > 0 ) {
						$_category_res_n[] = (object) array( "name" => __( 'All', 'richcategoryproducttab' ), "id" => 'all', "count" => $_total_post_count  );
						$arr_category_title[] = __( 'All', 'richcategoryproducttab' );
						foreach( $_category_res as $_category_item ) {
							$_category_res_n[] = $_category_item;
							$arr_category_title[] = $_category_item->name;
						}
					}
					
					if( $wcpt_short_category_name_by != "id" ) {
						if($wcpt_short_category_name_by=="asc")
							array_multisort($arr_category_title,SORT_ASC,$_category_res_n);
						else
							array_multisort($arr_category_title,SORT_DESC,$_category_res_n);
					}	
					$_category_res = $_category_res_n;
					
				}
				
			
				foreach( $_category_res as $_category ) {  
				
					$_category_name = $_category->name;
					$_category_id = $_category->id; 
					$_post_count = 0;
					
					if( trim( $wcpt_enable_post_count ) == "yes" ||  trim( $wcpt_hide_empty_category ) == "yes" ) {
					
						$_post_count = $_category->count;
						
						if( trim( $wcpt_hide_empty_category ) == "yes"  && intval( $_post_count ) <= 0 )
							continue;
						
					} 
					 
					?>
					<div class="item-pst-list">
						<div class="pst-item <?php echo ((( trim( $wcpt_default_category_open ) != ""  && ( $wcpt_default_category_open ) == $_category_id  ))?"pn-active":""); ?>"  onmouseout="wcpt_cat_tab_ms_out( this )" onmouseover="wcpt_cat_tab_ms_hover( this )" id="<?php echo esc_attr($vcode.'-'.$this->wcpt_replace((($_category_id=="all")?$_categories:esc_js($_category_id )))); ?>" onclick="wcpt_fillPosts( this.id, '<?php echo (($_category_id=="all")?esc_js($_categories):esc_js($_category_id )); ?>', request_obj_<?php echo esc_js( $vcode ); ?>, 1 )"  style="color:<?php echo esc_attr($this->_config["category_tab_text_color"] ); ?>;background-color:<?php echo esc_attr( $this->_config["category_tab_background_color"] ); ?>;" >
							<div class="pst-item-text"  onmouseout="wcpt_cat_tab_ms_out( this.parentNode )" onmouseover="wcpt_cat_tab_ms_hover( this.parentNode )">
								<?php 
							 
									echo esc_html( $_category_name );  
									echo (( trim( $wcpt_enable_post_count ) == "yes" )?" (".$_post_count.")":"");  
									 
								?>								
							</div>
							<div class="ld-pst-item-text"></div>
							<div class="clr"></div>
						</div>		 
						<div class="clr"></div>
					 </div>  
				   <?php
				   
				}
				
			} 
		?>
		<div class="clr"></div>
		<div class="item-posts <?php echo $wcpt_mouse_hover_effect; ?>">
			<input type="hidden" class="ikh_templates" value="<?php echo $wcpt_posts_grid_alignment; ?>" />
			<input type="hidden" class="ikh_posts_loads_from" value="<?php echo $wcpt_posts_loading_effect_on_pagination; ?>" />
			<input type="hidden" class="ikh_border_difference" value="0" />
			<input type="hidden" class="ikh_margin_bottom" value="<?php echo $wcpt_space_margin_between_posts; ?>" />
			<input type="hidden" class="ikh_margin_left" value="<?php echo $wcpt_space_margin_between_posts; ?>" />
			<input type="hidden" class="ikh_image_height" value="<?php echo $wcpt_image_height; ?>" />
			<input type="hidden" class="ikh_item_area_width" value="<?php echo $_image_width_item; ?>" /> 
			<div class="item-posts-wrap">
			<?php  
					// Default category opened category start
					if( trim( $wcpt_default_category_open ) != "" && trim( $wcpt_default_category_open ) != "0"  ) { 
					
						 $post_search_text = ""; 
						 $category_id = $wcpt_default_category_open;
						 $_limit_start = 0;
						 $_limit_end = $this->_config["number_of_post_display"];
						 $is_default_category_with_hidden = 0; 
						
						if(trim($wcpt_default_category_open) != "all"){
							$__current_term = get_term($wcpt_default_category_open);
							$__current_term_count =  $__current_term->count;
						}
						else
						{ 
						   $__current_term_count =  $_total_post_count;
						} 
						
						if( trim($wcpt_default_category_open) == "all" )
							$category_id = $_categories; 
							
						if( $__current_term_count > 0 ) {
							$_category_res = $this->getCategories();
							if( count( $_category_res ) > 0 && !( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes' ) ) { 
								?> 
								<div class="ik-post-category"> 
									<?php if( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'no' ) { ?> 
										 <input type="text" name="txtSearch" placeholder="<?php echo __( 'Search', 'richcategoryproducttab' ); ?>" value="<?php echo esc_html( htmlspecialchars( stripslashes( $post_search_text ) ) ); ?>" class="ik-post-search-text"  /> 
									<?php } ?>   
									<span class="ik-search-button" onclick='wcpt_fillPosts( "<?php echo esc_js( $this->_config["vcode"]."-".$this->wcpt_replace($category_id) ); ?>", "<?php echo esc_js($category_id); ?>", request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?>, 2)'> <img width="18px" alt="Search" height="18px" src="<?php echo wcpt_media.'images/searchicon-3.png'; ?>" />
									</span>
									<div class="clrb"></div>
								</div>
							 <?php
							}
						} else { echo "<input type='hidden' value='".$category_id."' class='ik-drp-post-category' />"; }
						$_total_posts =  $__current_term_count;
						 
						$post_list = $this->getSqlResult( $category_id, $post_search_text, 0, $_limit_end ); 
						if( count($post_list) > 0 ) {
							foreach ( $post_list as $_post ) { 
						
							$image  = $this->getPostImage( $_post->post_image, $wcpt_image_content_width, $this->_config["wcpt_image_height"] ); 
							$_author_name = esc_html($_post->display_name);
							$_author_image = get_avatar($_post->post_author,25);
							?> 
							<div style="<?php echo "width:".esc_attr($wcpt_image_content_width)."px"; ?>" class='ikh-post-item-box pid-<?php echo esc_attr( $_post->post_id ); ?>'> 
								<div class="ikh-post-item ikh-simple"> 
								<?php 
									ob_start();
									if( $wcpt_hide_post_image == "no" ) { ?> 	
										<div  class='ikh-image'  > 
											<a href="<?php echo get_permalink( $_post->post_id ); ?>"> 
												<?php echo $image; ?>
											</a>     
										</div>  
									<?php } 
									$_ob_image = ob_get_clean();  

									ob_start();
									?>   
								 	<div class='ikh-content'> 
									   <div class="ikh-content-data">
										
											<div class='ik-post-name'>												
												<?php if( sanitize_text_field( $this->_config["hide_post_title"] ) =='no'){ ?>  
													<a href="<?php echo get_permalink( $_post->post_id ); ?>" style="color:<?php echo esc_attr( $this->_config["title_text_color"] ); ?>" >
														<?php echo esc_html( $_post->post_title ); ?>
													</a>
												<?php } ?>	  
												
												<?php if( sanitize_text_field( $this->_config["wcpt_hide_posted_date"] ) =='no'){ ?> 
														<div class='ik-post-date'>
															</i><?php echo date(get_option("date_format"),strtotime($_post->post_date)); ?></i>
														</div>
												<?php } ?>	
											
												 <?php if( $wcpt_hide_post_short_content == "no" ) { ?>
													<div class='ik-post-sub-content'>
														<?php																		
														 if( strlen( strip_tags( $_post->post_content ) ) > intval( $wcpt_hide_post_short_content_length ) ) 	
															echo substr( strip_tags( $_post->post_content ), 0, $wcpt_hide_post_short_content_length )."..";  
														 else
															echo trim( strip_tags( $_post->post_content ) );																			
														?> 
													</div>
												<?php } ?> 
											</div>
											
											<?php if( sanitize_text_field( $this->_config["wcpt_hide_comment_count"] ) =='no'){ ?> 
												<div class='ik-post-comment'>
													<?php 
														$_total_comments = (get_comment_count($_post->post_id)); 			
														if($_total_comments["total_comments"] > 0) {
															echo $_total_comments["total_comments"]; 
															?> <?php echo (($_total_comments["total_comments"]>1)?__( 'Comments', 'richcategoryproducttab' ):__( 'Comment', 'richcategoryproducttab' )); 
														}
													?>
												</div>
											<?php } ?>	
											
											<?php if( sanitize_text_field(  $this->_config["wcpt_hide_product_price"] ) =='no'){ ?> 
												<div class='ik-product-sale-price'>
													<?php echo get_woocommerce_currency_symbol().$_post->sale_price; ?>
												</div> 
											<?php } ?> 
												
											<?php if( sanitize_text_field( $this->_config["wcpt_show_author_image_and_name"] ) =='yes') { ?> 
												<div class='ik-post-author'>
													<?php echo (($_author_image!==FALSE)?$_author_image:"<img src='".wcpt_media."images/user-icon.png' width='25' height='25' />"); ?> <?php echo __( 'By', 'richcategoryproducttab' ); ?> <?php echo $_author_name; ?>
												</div>
											<?php } ?>	 		
											
											<?php if( $wcpt_read_more_link == "no" ) { ?>
												<div class="wcpt-read-more-link">
													<a class="lnk-post-content" href="<?php echo get_permalink( $_post->post_id ); ?>" >
														<?php echo __( 'Read More', 'richcategoryproducttab' ); ?>
													</a>
												</div>
											<?php } ?>  
											
											<?php if( sanitize_text_field(  $this->_config["wcpt_add_to_cart_button"] ) =='no'){ ?> 
												<div class='ik-product-sale-btn-price' >
													<?php echo do_shortcode("[add_to_cart show_price='false' style='' id = '".$_post->post_id."']"); ?> 
												</div>
											<?php } ?>
						
											</div> 
									</div>	
								 <?php
								$_ob_content = ob_get_clean(); 
							
								if($wcpt_mouse_hover_effect=='ikh-image-style-40'|| $wcpt_mouse_hover_effect=='ikh-image-style-41' ){
									echo $_ob_content;
									echo $_ob_image;
								} else {
									echo $_ob_image;
									echo $_ob_content;														
								}	
								 ?>
								<div class="clr1"></div>
								</div> 
							</div> 
							<?php 
						} 
						 
						/******PAGING*******/
						if( $wcpt_hide_paging == "no" &&  $wcpt_select_paging_type == "load_more_option" && $_total_posts > sanitize_text_field( $this->_config["number_of_post_display"] ) ) { 
		
									?>
									<div class="clr"></div>
									<div class='ik-post-load-more'  align="center" onclick='wcpt_loadMorePosts( "<?php echo esc_js( $category_id ); ?>", "<?php echo esc_js( $_limit_start+$_limit_end ); ?>", "<?php echo esc_js( $this->_config["vcode"]."-".$this->wcpt_replace($category_id) ); ?>", "<?php echo esc_js( $_total_posts ); ?>", request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?> )'>
										<?php echo __('Load More', 'richcategoryproducttab' ); ?>
									</div>
									<?php   
								 
						} else if( $wcpt_hide_paging == "no" &&  $wcpt_select_paging_type == "next_and_previous_links" ) { 
							
								?><div class="clr"></div>
								<div class="wcpt-simple-paging"><?php
								echo $this->displayPagination(  0, $_total_posts, $category_id, $_limit_start, $_limit_end, $this->_config["vcode"], 2 );
								?></div><div class="clr"></div><?php
							
						} else if( $wcpt_hide_paging == "no" &&  $wcpt_select_paging_type == "simple_numeric_pagination" ) { 
							
								?><div class="clr"></div>
								<div class="wcpt-simple-paging"><?php
								echo $this->displayPagination(  0, $_total_posts, $category_id, $_limit_start, $_limit_end, $this->_config["vcode"], 1 );
								?></div><div class="clr"></div><?php
							
						} else {
								?><div class="clr"></div><?php
						}
						/******PAGING END*********/
					} else {
						?><div class="ik-post-no-items"><?php echo __( 'No posts found.', 'richcategoryproducttab' ); ?></div><?php 										
					}

					$this->_config["category_id"] = $category_id;
					?><script type='text/javascript' language='javascript'><?php echo $this->wcpt_js_obj( $this->_config ); ?></script><?php
					
				} 
				// End Default category opened.
			?> 
			</div>
		</div>
		<div class="clr"></div>
	</div>
</div>