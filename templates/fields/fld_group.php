<?php if ( ! defined( 'ABSPATH' ) ) exit;  ?> 

<div class="meta-box-sortables ui-sortable">
	<div class="postbox">
		<button type="button" class="handlediv button-link" <?php echo (($type=="widget")?"onclick='richcategoryproducttab_show_accordion(this)'":""); ?>  aria-expanded="true">
			<span class="screen-reader-text">Settings</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		<h2 class="hndle ui-sortable-handle"><span><?php echo $group_title; ?></span></h2>
		<div class="inside"> 
			<table class="richcategoryproducttab-admin <?php echo (($type=="widget")?"richcategoryproducttab-admin-widget":""); ?>" cellspacing="0" cellpadding="0">
				<?php echo $group_field; ?>
			</table>
		</div>
	</div> 
</div>