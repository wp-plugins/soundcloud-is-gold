<?php
/*********************************************************************/
/***                                                               ***/
/***                   SOUNDCLOUD MEDIA UPLOAD TAB                 ***/
/***                                                               ***/
/*********************************************************************/
/* Add a new Tab */
function soundcloud_is_gold_media_upload_tab($tabs) {
	$newtab = array('soundcloud_is_gold' => __('Soundcloud is Gold', 'soundcloud_is_gold'));
	return array_merge($tabs, $newtab);
}
add_filter('media_upload_tabs', 'soundcloud_is_gold_media_upload_tab');

/* Add Scripts and Styles to New Tab **/
add_action('admin_print_scripts-media-upload-popup', 'soundcloud_is_gold_option_scripts', 2000);
add_action('admin_print_styles-media-upload-popup', 'soundcloud_is_gold_option_styles', 2000);
	
	
/* Soundcloud is Gold Tab (Iframe) content*/
function media_soundcloud_is_gold_process() {
	media_upload_header();
       // $soundcloudXML = PLUGIN_DIR.'tracks.xml';
	$soundcloudIsGoldXML = 'http://api.soundcloud.com/users/'.get_option(soundcloud_is_gold_user).'/tracks.xml?client_id=9rD2GrGrajkmkw5eYFDp2g';
        get_soundcloud_is_gold_user_tracks($soundcloudIsGoldXML, $_REQUEST['post_id']);
	//testMedia(TRUE);
}
/* load Iframe in the tab page */
function soundcloud_is_gold_media_menu_handle() {
    return wp_iframe( 'media_soundcloud_is_gold_process');
}
add_action('media_upload_soundcloud_is_gold', 'soundcloud_is_gold_media_menu_handle');


/*Add Soundcloud Button to Upload/Insert*/
function plugin_media_button($context) {
	global $post_ID;
	$plugin_media_button = ' %s' . '<a id="add_soundcloud_is_gold" title="Insert Soundcloud Player" href="media-upload.php?post_id='.$post_ID.'&tab=soundcloud_is_gold&TB_iframe=1&width=640&height=584" class="thickbox"><img alt="Insert Soundcloud Player" src="'.PLUGIN_DIR.'soundcloud-is-gold-icon.png"></a>';
	return sprintf($context, $plugin_media_button);
  }
add_filter('media_buttons_context', 'plugin_media_button');
  
/** Populate the new Soundcloud is Gold Tab **/
function get_soundcloud_is_gold_user_tracks($soundcloudIsGoldApiCall, $post_id){
	
	$soundcloudIsGoldUser = get_option(soundcloud_is_gold_user);
	$soundcloudIsGoldSettings = get_option(soundcloud_is_gold_settings);
	$soundcloudIsGoldPlayerType = get_option(soundcloud_is_gold_playerType);
	$soundcloudIsGoldPlayerTypeDefault = empty($soundcloudIsGoldPlayerType) ? TRUE : FALSE;
	$soundcloudIsGoldWidthSettings = get_option(soundcloud_is_gold_width_settings);
	$soundcloudIsGoldWidth = get_soundcloud_is_gold_default_width($soundcloudIsGoldWidthSettings);
	$soundcloudIsGoldClasses = get_option(soundcloud_is_gold_classes);
	$soundcloudIsGoldColor = get_option(soundcloud_is_gold_color);
    
	//Sorting Menu
	echo '<form id="library-form" class="media-upload-form validate" action="" method="post" enctype="multipart/form-data"><div id="media-items" class="media-items-'.$post_id.'">';
	?>
	
	<script type="text/javascript">
	<!--
	jQuery(function($){
		
		var preloaded = $(".media-item.preloaded");
		if ( preloaded.length > 0 ) {
			preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
			//updateMediaForm();
		}
	});
	-->
	
	//Set default Soundcloud Is Gold Settings
	<?php get_soundcloud_is_gold_default_settings_for_js(); ?>
		
		
	</script>
	
	<?php // Load the call and capture the document returned by the API
		$soundcloudIsGoldResp = simplexml_load_file($soundcloudIsGoldApiCall);
		//printl($resp);
		//echo htmlentities($resp->asXML());
		// Check to see if the response was loaded, else print an error
		if ($soundcloudIsGoldResp) {
			foreach($soundcloudIsGoldResp as $soundcloudIsGoldtrack): ?>
			
				<div class="media-item preloaded" id="media-item-<?php echo $soundcloudIsGoldtrack->id ?>">
					<a href="#" class="toggle describe-toggle-on soundcloud" id="show-<?php echo $soundcloudIsGoldtrack->id ?>">Show</a>
					<a href="#" class="toggle describe-toggle-off soundcloudMM">Hide</a>
					<div class="filename new"><span class="title soundcloudMMTitle" id="soundcloudMMTitle-<?php echo $soundcloudIsGoldtrack->id ?>"><?php echo $soundcloudIsGoldtrack->title ?></span></div>
					<table class="slidetoggle describe startclosed soundcloudMMWrapper">
						<thead id="media-head-<?php echo $soundcloudIsGoldtrack->id ?>" class="media-item-info">
							<tr valign="top">
								<td id="thumbnail-head-<?php echo $soundcloudIsGoldtrack->id ?>" class="A1B1">
									<p><a href="<?php echo $soundcloudIsGoldtrack->{'permalink-url'}?>" title="Go to the Soundcloud page" target="_blank"><img id="soundcloudMMThumb-<?php echo $soundcloudIsGoldtrack->id ?>" style="margin-top: 3px;" alt="" src="<?php echo $soundcloudIsGoldtrack->{'artwork-url'} ?>" class="thumbnail"></a></p>
								</td>
								<td>
								<p><strong>Title:</strong> <?php echo $soundcloudIsGoldtrack->title ?></p>
								<p id="soundcloudMMId-<?php echo $soundcloudIsGoldtrack->id ?>" class="soundcloudMMId"><strong>id:</strong> <?php echo $soundcloudIsGoldtrack->id ?></p>
								<p><strong>Upload date:</strong> <?php echo $soundcloudIsGoldtrack->{'created-at'} ?></p>
								<p><strong>Duration:</strong> <span id="media-dims-<?php echo $soundcloudIsGoldtrack->id ?>"><?php echo $soundcloudIsGoldtrack->duration ?></span></p>
								<p><strong>Url:</strong> <a id="videoUrl-<?php echo $soundcloudIsGoldtrack->id ?>" href="<?php echo $soundcloudIsGoldtrack->{'permalink-url'} ?>" title="Go to the video page" target="_blank"><?php echo $soundcloudIsGoldtrack->{'permalink-url'}?></a></p>
								</td>
								<td>
								<tbody>
									<tr class="soundcloudMM_description">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Description</span><br class="clear"></label></th>
										<td class="field">
											<p class="text soundcloudMMDescription" id="soundcloudMMDescription-<?php echo $soundcloudIsGoldtrack->id ?>"><?php echo $soundcloudIsGoldtrack->description ?></p>
										</td>
									</tr>
									<tr class="soundcloudMM_settings">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Settings</span><br class="clear"></label></th>
										<td class="field">
											<input type="checkbox" <?php echo ($soundcloudIsGoldSettings[0]) ? 'checked="checked"' : '' ?> id="soundcloudMMAutoPlay-<?php echo $soundcloudIsGoldtrack->id ?>" class="text soundcloudMMAutoPlay">
											<label >Play Automaticly</label>
											<input type="checkbox" <?php echo ($soundcloudIsGoldSettings[1]) ? 'checked="checked"' : '' ?> id="soundcloudMMShowComments-<?php echo $soundcloudIsGoldtrack->id ?>" class="text soundcloudMMShowComments">
											<label >Show track's comments <small>(only for standard version)</small></label>
											<!-- <input type="text" class="soundcloudPlayercolor" value""/> -->
											
										</td>
									</tr>
									<tr class="soundcloudMM_playerType">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Player Type</span><br class="clear"></label></th>
										<td class="field">
											<?php foreach(get_soundcloud_is_gold_player_types() as $type) :?>
											<input type="radio" id="soundcloudMMMiniPlayer-<?php echo $soundcloudIsGoldtrack->id ?>" value="<?php echo $type ?>" name="soundcloudMMPlayerType-<?php echo $soundcloudIsGoldtrack->id ?>" class="text soundcloudMMPlayerType" <?php echo ($soundcloudIsGoldPlayerType === $type) ? 'checked="checked"' : '' ?>>
											<label><?php echo $type; if($type == 'Artwork') echo ' <small>(not available on free soundcloud account)</small>' ?></label>
											<?php endforeach; ?>
										</td>
									</tr>
									<tr class="soundcloudMM_size">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Width</span><br class="clear"></label></th>
										<td class="field">
											<ul id="soundcloudMMWidthSetting" class="subSettings texts soundcloudMMTabWidthSettings">
												<li>
												    <input name="soundcloudMMWidthType-<?php echo $soundcloudIsGoldtrack->id ?>" <?php echo ($soundcloudIsGoldWidthSettings['type'] == "wp") ? 'checked="checked"' : ''; ?> id="soundcloudMMWpWidth-<?php echo $soundcloudIsGoldtrack->id ?>" value="wp" type="radio" class="soundcloudMMWpWidth soundcloudMMWidthType"/><label for="soundcloudMMWpWidth-<?php echo $soundcloudIsGoldtrack->id ?>">Media Width</label>
												    <select class="soundcloudMMInput soundcloudMMWidth" name="soundcloud_is_gold_width_settings[wp]">
												    <?php foreach(get_soundcloud_is_gold_wordpress_sizes() as $key => $soundcloudIsGoldMediaSize) : ?>
													<?php $soundcloudIsGoldMediaSelected = ($soundcloudIsGoldMediaSize[0] == $soundcloudIsGoldWidthSettings['wp']) ? 'selected="selected"' : ''; ?>
													<option <?php echo $soundcloudIsGoldMediaSelected ?> value="<?php echo $soundcloudIsGoldMediaSize[0]?>" class="soundcloudMMWPSelectedWidth soundcloudMMWidth"><?php echo $key.': '.$soundcloudIsGoldMediaSize[0]?></option>
												    <?php endforeach; ?>
												    </select>
												</li>
												<li>
												    <input name="soundcloudMMWidthType-<?php echo $soundcloudIsGoldtrack->id ?>" <?php echo ($soundcloudIsGoldWidthSettings['type'] == "custom") ? 'checked="checked"' : ''; ?> id="soundcloudMMCustomWidth-<?php echo $soundcloudIsGoldtrack->id ?>" value="custom" type="radio" class="soundcloudMMCustomWidth soundcloudMMWidthType"/><label for="soundcloudMMCustomWidth-<?php echo $soundcloudIsGoldtrack->id ?>">Custom Width</label>
												    <input name="soundcloudMMCustomSelectedWidth-<?php echo $soundcloudIsGoldtrack->id ?>" id="soundcloudMMCustomSelectedWidth-<?php echo $soundcloudIsGoldtrack->id ?>" class="soundcloudMMInput soundcloudMMWidth soundcloudMMCustomSelectedWidth" type="text" value="<?php echo $soundcloudIsGoldWidthSettings['custom'] ?>" />
												</li>
											</ul>
										</td>
									</tr>
									<tr class="soundcloudMM_color">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Colour</span><br class="clear"></label></th>
										<td class="field">
											<div class="soundcloudMMColorPickerContainer" id="soundcloudMMColorPickerContainer-<?php echo $soundcloudIsGoldtrack->id ?>">
												<input type="text" id="soundcloudMMColor-<?php echo $soundcloudIsGoldtrack->id ?>" class="soundcloudMMColor" name="soundcloudMMColor-<?php echo $soundcloudIsGoldtrack->id ?>" value="<?php echo $soundcloudIsGoldColor ?>" style="background-color:<?php echo $soundcloudIsGoldColor ?>"/><a href="#" class="soundcloudMMBt soundcloudMMBtSmall inline blue soundcloudMMRounder soundcloudMMResetColor">reset to default</a>
												<div id="soundcloudMMColorPicker-<?php echo $soundcloudIsGoldtrack->id ?>" class="shadow soundcloudMMColorPicker" ><div id="soundcloudMMColorPickerSelect-<?php echo $soundcloudIsGoldtrack->id ?>" class="soundcloudMMColorPickerSelect"></div><a id="soundcloudMMColorPickerClose-<?php echo $soundcloudIsGoldtrack->id ?>" class="blue soundcloudMMBt soundcloudMMColorPickerClose">done</a></div>
											</div>
										</td>
									</tr>
									<tr class="soundcloudMM_classes">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Extra classes</span><br class="clear"></label></th>
										<td class="field">
											<input type="text" class="text soundcloudMMClasses" id="soundcloudMMClasses-<?php echo $soundcloudIsGoldtrack->id ?>" value="<?php echo $soundcloudIsGoldClasses ?>">
											<p class="help">In case you need extra css classes (seperate with a space, no commas!)</p>
										</td>
									</tr>
									<tr class="soundcloudMM_player">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Preview</span><br class="clear"></label></th>
										<td>
											<p id="soundcloudMMEmbed-<?php echo $soundcloudIsGoldtrack->id ?>" class="field soundcloudMMEmbed" style="text-align:center">
												<!-- Soundcloud Preview here -->
											</p>
											<p class="soundcloudMMLoading" style="display:none"></p>
										</td>
									</tr>
									<tr class="soundcloudMM_shortcode">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Shortcode</span><br class="clear"></label></th>
										<td class="field">
											<input id="soundcloudMMShortcode-<?php echo $soundcloudIsGoldtrack->id ?>" type="text" class="text soundcloudMMShortcode" value="[soundcloud <?php echo 'id='.$soundcloudIsGoldtrack->id ?>']">
										</td>
									</tr>
									 <tr class="soundcloudMM_submit">
										<td></td>
										<td class="savesend">
											<a href="#" id="soundcloudMMInsert-<?php echo $soundcloudIsGoldtrack->id ?>" class="button soundcloudMMInsert">Insert Soundcloud Player</a>

											<!-- <input type="submit" value="Insert into Post" name="" class="button"> -->
											<!-- <input type="button" id="soundcloudMMAddToGallery-<?php echo $soundcloudIsGoldtrack->id ?>" value="Add to post's gallery" name="" class="button soundcloudMMAddToGallery">
											<a href="#" id="soundcloudMMFeat-<?php echo $soundcloudIsGoldtrack->id ?>" class="soundcloudMMFeat">Use as featured Soundcloud</a> -->
											
										</td>
									</tr>
								</tbody>
								</td>
							</tr>
						</thead>
					</table>
				</div>
			<?php endforeach;
		}
		//Error getting XML
		else{
			echo '<div class="soundcloudMMXmlError"><p>Oups! There\'s been a error while getting the tracks from soundcloud. Please reload the page.</p></div>';
		}
	echo '<div id="colorpicker"></div>';
	echo '</div></form>';
}





/******************************************************/
/**                                                  **/
/**                     SHORTCODE                    **/
/**                                                  **/
/******************************************************/
add_shortcode('soundcloud', 'soundcloud_is_gold_shortcode');
function soundcloud_is_gold_shortcode($attr){
	//Ajax
	?>
	<!-- 
	<div id="soundcloud-<?php echo $attr['id'] ?>" style="width:<?php echo $attr['width'] ?>" class="soundcloud">
		<script type="text/javascript">
			jQuery(document).ready(function(){
				loadSoundcloud(<?php //echo implode(',', $attr)?>);	
			});
		</script>
	</div>
	-->
	<?php
	//no js
	echo soundcloud_is_gold_player($attr['id'], $attr['autoPlay'], $attr['comments'], $attr['width'], $attr['classes'], $attr['type'], $attr['color']);
}



/******************************************************/
/**                                                  **/
/**                     OUTPUT                       **/
/**                                                  **/
/******************************************************/


/** Preview **/
add_action('wp_ajax_soundcloud_is_gold_player_preview', 'soundcloud_is_gold_player_preview');
function soundcloud_is_gold_player_preview(){
	if(isset($_POST['request'])) echo soundcloud_is_gold_player($_POST['ID'], $_POST['autoPlay'], $_POST['comments'], $_POST['width'], $_POST['classes'], $_POST['playerType'], $_POST['color']);
	die;
}


/** The Player **/
function soundcloud_is_gold_player($id, $autoPlay, $comments, $width, $classes, $playerTypes, $color){
	

	$soundcloudIsGoldSettings = get_option(soundcloud_is_gold_settings);
	if($autoPlay == NULL) $autoPlay = $soundcloudIsGoldSettings[0];
	if($comments == NULL) $comments = $soundcloudIsGoldSettings[1];
	if($width == NULL) $width = get_soundcloud_is_gold_default_width(get_option(soundcloud_is_gold_width_settings));
	if($classes == NULL) $classes = get_option(soundcloud_is_gold_classes);
	if($playerTypes == NULL) $playerTypes = get_option(soundcloud_is_gold_playerType);
	if($color == NULL) $color = get_option(soundcloud_is_gold_color);
	$color = str_replace('#', '', $color);
	
	switch($playerTypes){
		case 'Standard':
			$height = '81px';
			$playerType = 'standard';
			break;
		case 'Artwork':
			$height = $width;
			$playerType = 'artwork';
			break;
		case 'Mini':
			$playerType = 'tiny';
			$height = '18px';
			break;
	}

	$player = '<div class="soundcloudIsGold '.$classes.'" id="soundcloud-'.$id.'">';
	$player .= '<object height="'.$height.'" width="'.$width.'">';
	$player .= '<param name="movie" value="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F'.$id.'&amp;auto_play='.$autoPlay.'&amp;player_type='.$playerType.'&amp;show_comments='.$comments.'&amp;color='.$color.'"></param>';
	$player .= '<param name="allowscriptaccess" value="always"></param>';
	$player .= '<param name="wmode" value="window"></param>';
	$player .= '<embed wmode="window" allowscriptaccess="always" height="'.$height.'" src="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F'.$id.'&amp;auto_play='.$autoPlay.'&amp;player_type='.$playerType.'&amp;show_comments='.$comments.'&amp;color='.$color.'" type="application/x-shockwave-flash" width="'.$width.'"></embed>';
	$player .= '</object>';
	$player .= '</div>';
        
	
	return $player;

}


/** viewer Ajax **/
add_action('wp_ajax_get_soundcloud_player', 'get_soundcloud_player');
add_action('wp_ajax_nopriv_get_soundcloud_player', 'get_soundcloud_player');
function get_soundcloud_player(){
	echo soundcloud_is_gold_player($_POST['id'], $_POST['width'], $_POST['comments'], $_POST['autoPlay'], $_POST['type'], $_POST['color']);
	die();
	
}
?>