<?php
/*********************************************************************/
/***                                                               ***/
/***                     SOUNDCLOUD UTILITIES                      ***/
/***                                                               ***/
/*********************************************************************/
function get_soundcloud_is_gold_player_types(){
    $m = array('Mini', 'Standard', 'Artwork', 'html5');
    return $m;
}
function get_soundcloud_is_gold_wordpress_sizes(){
    $px = "px";
    $soundcloudIsGoldWordpressSizes = array(
                                                "thumbnail" => array(
                                                                    get_option( 'thumbnail_size_w' ).$px,
                                                                    get_option( 'thumbnail_size_h' ).$px
                                                                    ),
                                                "medium" => array(
                                                                get_option( 'medium_size_w' ).$px,
                                                                get_option( 'medium_size_w' ).$px
                                                                ),
                                                "large" => array(
                                                                get_option( 'large_size_w' ).$px,
                                                                get_option( 'large_size_w' ).$px
                                                                )
                                              );
    return $soundcloudIsGoldWordpressSizes;
}
function get_soundcloud_is_gold_default_width($settings){
    return $settings[$settings['type']];
}
function get_soundcloud_is_gold_default_settings_for_js(){
	echo 'soundcloudIsGoldUser_default = "'.get_option('soundcloud_is_gold_user').'"; ';
	echo 'soundcloudIsGoldPlayerType_default = "'.get_option('soundcloud_is_gold_playerType').'"; ';
        $soundcloudIsGoldSettings = get_option('soundcloud_is_gold_settings');
	echo 'soundcloudIsGoldAutoPlay_default = '.((!isset($soundcloudIsGoldSettings[0]) || $soundcloudIsGoldSettings[0] == '') ? 'false' : 'true') .'; ';
	echo 'soundcloudIsGoldComments_default = '.((!isset($soundcloudIsGoldSettings[1]) || $soundcloudIsGoldSettings[1] == '') ? 'false' : 'true') .'; ';
	echo 'soundcloudIsGoldArtwork_default = '.((!isset($soundcloudIsGoldSettings[2]) || $soundcloudIsGoldSettings[2] == '') ? 'false' : 'true') .'; ';
	echo 'soundcloudIsGoldWidth_default = "'.get_soundcloud_is_gold_default_width(get_option('soundcloud_is_gold_width_settings')).'"; ';
	echo 'soundcloudIsGoldClasses_default = "'.get_option('soundcloud_is_gold_classes').'"; ';
	echo 'soundcloudIsGoldColor_default = "'.get_option('soundcloud_is_gold_color').'"; ';
}
function get_soundcloudIsGoldUserNumber(){
	$soundcloudIsGoldApiCall = 'http://api.soundcloud.com/users/'.get_option('soundcloud_is_gold_user').'.xml?client_id=9rD2GrGrajkmkw5eYFDp2g';
	$soundcloudIsGoldApiResponse = get_soundcloud_is_gold_api_response($soundcloudIsGoldApiCall);
	$result['tracks'] = ($soundcloudIsGoldApiResponse['response']->{'track-count'} == 0) ? '0' : $soundcloudIsGoldApiResponse['response']->{'track-count'};
	$result['sets'] = ($soundcloudIsGoldApiResponse['response']->{'playlist-count'} == 0) ? '0' : $soundcloudIsGoldApiResponse['response']->{'playlist-count'};
	$result['favorites'] = ($soundcloudIsGoldApiResponse['response']->{'public-favorites-count'} == 0) ? '0' : $soundcloudIsGoldApiResponse['response']->{'public-favorites-count'};
	return $result;
	}
function get_soundcloud_is_gold_api_response($soundcloudIsGoldApiCall){
	//Set Error default message && default XML state
	$soundcloudIsGoldRespError = false;
	$soundcloudIsGoldResp = false;
	//Check is cURL extension is loaded
	if(extension_loaded("curl")){
		// create a new cURL resource
		$soundcloudIsGoldCURL = curl_init();
		//Set cURL Options
		curl_setopt($soundcloudIsGoldCURL, CURLOPT_URL, $soundcloudIsGoldApiCall);
		curl_setopt($soundcloudIsGoldCURL, CURLOPT_RETURNTRANSFER, true);//return a string
		curl_setopt($soundcloudIsGoldCURL, CURLOPT_USERAGENT, "user_agent : FOOBAR");
		// Get XML as a string
		$soundcloudIsGoldXmlString = curl_exec($soundcloudIsGoldCURL);
		//Check for cURL errors
		if($soundcloudIsGoldXmlString === false) $soundcloudIsGoldRespError = 'Curl error: ' . curl_error($soundcloudIsGoldCURL);
		//No cURL Errors: Load the call and captured xml returned by the API
		else $soundcloudIsGoldResp = simplexml_load_string($soundcloudIsGoldXmlString);
		// close cURL resource, and free up system resources
		curl_close($soundcloudIsGoldCURL);
	}
	//No cURL: Try loading the XML directly with simple_xml_load_file
	else $soundcloudIsGoldResp = simplexml_load_file($soundcloudIsGoldApiCall);

	//Add response and error to array
	$soundCloudIsGoldResponseArray = array('response' => $soundcloudIsGoldResp, 'error' => $soundcloudIsGoldRespError);
	return $soundCloudIsGoldResponseArray;
}
/*Pagination
soundcloud_is_gold_pagination($totalItems, $currentPage, $perPage)
*/
function soundcloud_is_gold_pagination($totalItems, $currentPage, $perPage, $post_ID){
	
	// The items on the current page.
	$offset = ($currentPage - 1) * $perPage;
	$firstItem = $offset + 1;
	$lastItem = $offset + $perPage < $totalItems ? $offset + $perPage : $totalItems;
	
	// Some useful variables for making links.
	$firstPage = 1;
	$lastPage = ceil($totalItems / $perPage);
	$prevPage = $currentPage - 1 > 0 ? $currentPage - 1 : 1;
	$nextPage = $currentPage + 1 > $lastPage ? $lastPage : $currentPage + 1;
	
	$disableFirst = ($currentPage == $firstPage) ? ' disabled' : '';
	$disableLast = ($currentPage == $lastPage) ? ' disabled' : '';
	
	$output = '<div class="tablenav-pages"><span class="displaying-num">'.$totalItems.' tracks</span>';
	$output .= '<span class="pagination-links"><a href="?post_id='.$post_ID.'&tab=soundcloud_is_gold&paged='.$firstPage.'&TB_iframe=1&width=640&height=584" title="Go to the first page" class="first-page'.$disableFirst.'">&laquo;</a>';
	$output .= '<a href="?post_id='.$post_ID.'&tab=soundcloud_is_gold&paged='.$prevPage.'&TB_iframe=1&width=640&height=584" title="Go to the previous page" class="prev-page'.$disableFirst.'">&lsaquo;</a>';
	$output .= '<span class="paging-input">page '.$currentPage.' of <span class="total-pages">'.$lastPage.'</span></span>';
	$output .= '<a href="?post_id='.$post_ID.'&tab=soundcloud_is_gold&paged='.$nextPage.'&TB_iframe=1&width=640&height=584" title="Go to the next page" class="next-page'.$disableLast.'">&rsaquo;</a>';
	$output .= '<a href="?post_id='.$post_ID.'&tab=soundcloud_is_gold&paged='.$lastPage.'&TB_iframe=1&width=640&height=584" title="Go to the last page" class="last-page'.$disableLast.'">&raquo;</a></span></div>';
	
	return $output;
}
/*Select Tracks / Favorites / Sets
*/
function soundcloud_is_gold_select_tracks_favs_sets($selectedFormat, $soundcloudIsGoldNumbers, $post_ID){
	$formats = array('tracks', 'sets', 'favorites');
	$output = '<ul id="soundcloudMMSelectTracksFavsSets" class="subsubsub">';
	foreach($formats as $key => $format){
		$current = ($format == $selectedFormat) ? 'current' : '';
		$seperator = ($key != 0) ? ' | ' : ' ';
		$output .= $seperator.'<li><a href="?post_id='.$post_ID.'&tab=soundcloud_is_gold&selectFormat='.$format.'&paged=1" class="'.$current.'">'.$format.' <span class="count">('.$soundcloudIsGoldNumbers[$format].')</span></a></li>';
	}
	$output .= '</ul>';
	return $output;
}

/*Add Soundcloud is Gold Plugin to TinyMce*/
function soundcloud_is_gold_mce_plugin($plugin_array) {
    $plugin_array['soundcloudIsGold']  =  SIG_PLUGIN_DIR.'tinymce-plugin/soundcloud-is-gold-editor_plugin.js';
    return $plugin_array;
}
function soundcloud_is_gold_mce_button( $buttons ) {
	// add a separation before our button, here our button's id is "mygallery_button"
	array_push( $buttons, '|', 'soundcloudisgoldbtns' );
	return $buttons;
}
function soundcloud_is_gold_mce_css($mce_css) {
  if (! empty($mce_css)) $mce_css .= ',';
  $mce_css .= SIG_PLUGIN_DIR.'/tinymce-plugin/soundcloud-is-gold-editor_plugin.css';
  return $mce_css; 
}

/* Debug */
if(!function_exists('printl')){
	function printl($val){
		printf("<pre>%s</pre>", print_r($val, true));
	}
}

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
	get_soundcloud_is_gold_user_tracks();
}
/* load Iframe in the tab page */
function soundcloud_is_gold_media_menu_handle() {
    return wp_iframe( 'media_soundcloud_is_gold_process');
}
add_action('media_upload_soundcloud_is_gold', 'soundcloud_is_gold_media_menu_handle');


/*Add Soundcloud Button to Upload/Insert*/
function plugin_media_button($context) {
	global $post_ID;
	$plugin_media_button = ' %s' . '<a id="add_soundcloud_is_gold" title="Insert Soundcloud Player" href="media-upload.php?post_id='.$post_ID.'&tab=soundcloud_is_gold&selectFormat=tracks&paged=1&TB_iframe=1&width=640&height=584" class="thickbox"><img alt="Insert Soundcloud Player" src="'.SIG_PLUGIN_DIR.'soundcloud-is-gold-icon.png"></a>';
	return sprintf($context, $plugin_media_button);
  }
add_filter('media_buttons_context', 'plugin_media_button');
  
/** Populate the new Soundcloud is Gold Tab **/
function get_soundcloud_is_gold_user_tracks(){
	//Default Settings
	$soundcloudIsGoldUser = get_option('soundcloud_is_gold_user');
	$soundcloudIsGoldSettings = get_option('soundcloud_is_gold_settings');
	$soundcloudIsGoldPlayerType = get_option('soundcloud_is_gold_playerType');
	$soundcloudIsGoldPlayerTypeDefault = empty($soundcloudIsGoldPlayerType) ? TRUE : FALSE;
	$soundcloudIsGoldWidthSettings = get_option('soundcloud_is_gold_width_settings');
	$soundcloudIsGoldWidth = get_soundcloud_is_gold_default_width($soundcloudIsGoldWidthSettings);
	$soundcloudIsGoldClasses = get_option('soundcloud_is_gold_classes');
	$soundcloudIsGoldColor = get_option('soundcloud_is_gold_color');
	
	//Default Pagination Settings
	$soundcloudIsGoldTracksPerPage = 25;
	$soundcloudIsGoldPage = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : '1';
	$post_id = $_REQUEST['post_id'];
	$soundcloudIsGoldApiOffset = $soundcloudIsGoldTracksPerPage*($soundcloudIsGoldPage-1);
	
	//API Call
	$soundcloudIsGoldSelectedFormat = isset($_REQUEST['selectFormat']) ? $_REQUEST['selectFormat'] : 'tracks';
	if($soundcloudIsGoldSelectedFormat == 'tracks') $soundcloudIsGoldApiCall = 'http://api.soundcloud.com/users/'.get_option('soundcloud_is_gold_user').'/tracks.xml?limit='.$soundcloudIsGoldTracksPerPage.'&offset='.$soundcloudIsGoldApiOffset.'&client_id=9rD2GrGrajkmkw5eYFDp2g';
	if($soundcloudIsGoldSelectedFormat == 'sets') $soundcloudIsGoldApiCall = 'http://api.soundcloud.com/users/'.get_option('soundcloud_is_gold_user').'/playlists.xml?limit='.$soundcloudIsGoldTracksPerPage.'&offset='.$soundcloudIsGoldApiOffset.'&client_id=9rD2GrGrajkmkw5eYFDp2g';
	if($soundcloudIsGoldSelectedFormat == 'favorites') $soundcloudIsGoldApiCall = 'http://api.soundcloud.com/users/'.get_option('soundcloud_is_gold_user').'/favorites.xml?limit='.$soundcloudIsGoldTracksPerPage.'&offset='.$soundcloudIsGoldApiOffset.'&client_id=9rD2GrGrajkmkw5eYFDp2g';
	$soundcloudIsGoldApiResponse = get_soundcloud_is_gold_api_response($soundcloudIsGoldApiCall);
	
	//Pagination and Actions
	$soundcloudIsGoldNumbers = get_soundcloudIsGoldUserNumber($soundcloudIsGoldSelectedFormat);
	$soundcloudIsGoldPagination = soundcloud_is_gold_pagination($soundcloudIsGoldNumbers[$soundcloudIsGoldSelectedFormat], $soundcloudIsGoldPage, $soundcloudIsGoldTracksPerPage, $post_id);
	$soundcloudIsGoldSelectTracksFavsSets = soundcloud_is_gold_select_tracks_favs_sets($soundcloudIsGoldSelectedFormat, $soundcloudIsGoldNumbers, $post_id);
	
	echo '<div id="soundcloudMMTabActions" class="tablenav">';
		//Select Tracks / Sets / Favs
		echo (isset($soundcloudIsGoldSelectTracksFavsSets)) ? $soundcloudIsGoldSelectTracksFavsSets : '';
		//Pagination
		echo (isset($soundcloudIsGoldPagination)) ? $soundcloudIsGoldPagination : '';
	echo '</div>';
	
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
	
	<?php
	if (isset($soundcloudIsGoldApiResponse['response']) && $soundcloudIsGoldApiResponse['response']) {
			foreach($soundcloudIsGoldApiResponse['response'] as $soundcloudIsGoldtrack): ?>
			
				<div class="media-item preloaded" id="media-item-<?php echo $soundcloudIsGoldtrack->id ?>">
					<a href="#" class="toggle describe-toggle-on soundcloud" id="show-<?php echo $soundcloudIsGoldtrack->id ?>">Show</a>
					<a href="#" class="toggle describe-toggle-off soundcloudMM">Hide</a>
					<div class="filename new"><span class="title soundcloudMMTitle" id="soundcloudMMTitle-<?php echo $soundcloudIsGoldtrack->id ?>"><?php echo $soundcloudIsGoldtrack->title ?></span></div>
					<table class="slidetoggle describe startclosed soundcloudMMWrapper <?php echo $soundcloudIsGoldSelectedFormat ?>">
						<thead id="media-head-<?php echo $soundcloudIsGoldtrack->id ?>" class="media-item-info">
							<tr valign="top">
								<td id="thumbnail-head-<?php echo $soundcloudIsGoldtrack->id ?>" class="A1B1">
									<p><a href="<?php echo $soundcloudIsGoldtrack->{'permalink-url'}?>" title="Go to the Soundcloud page" target="_blank"><img id="soundcloudMMThumb-<?php echo $soundcloudIsGoldtrack->id ?>" style="margin-top: 3px;" alt="" src="<?php echo ($soundcloudIsGoldtrack->{'artwork-url'} != '') ? $soundcloudIsGoldtrack->{'artwork-url'} : SIG_PLUGIN_DIR."/noThumbnail.gif" ?>" class="thumbnail"></a></p>
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
											<label >Show comments <small>(standard and artwork player)</small></label>
											<input type="checkbox" <?php echo ($soundcloudIsGoldSettings[2]) ? 'checked="checked"' : '' ?> id="soundcloudMMShowArtwork-<?php echo $soundcloudIsGoldtrack->id ?>" class="text soundcloudMMShowArtwork">
											<label >Show artwork <small>(html5 player)</small></label>
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
			if($soundcloudIsGoldApiResponse['error'] === false) $soundcloudIsGoldApiResponse['error'] = 'XML error';
			echo '<div class="soundcloudMMXmlError"><p>Oups! There\'s been a error while getting the tracks from soundcloud. Please reload the page.</p><p class="error">'.$soundcloudIsGoldApiResponse['error'].'</p></div>';
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
function soundcloud_is_gold_shortcode($atts){
	$soundcloudIsGoldSettings = get_option('soundcloud_is_gold_settings');
	//Only use lowercase as atts!
	extract( shortcode_atts( array(
					'id' => '1',
					'autoplay' => ((!isset($soundcloudIsGoldSettings[0]) || $soundcloudIsGoldSettings[0] == '') ? 'false' : 'true'),
					'comments' => ((!isset($soundcloudIsGoldSettings[1]) || $soundcloudIsGoldSettings[1] == '') ? 'false' : 'true'),
					'artwork' => ((!isset($soundcloudIsGoldSettings[2]) || $soundcloudIsGoldSettings[2] == '') ? 'false' : 'true'),
					'width' => get_soundcloud_is_gold_default_width(get_option('soundcloud_is_gold_width_settings')),
					'classes' => get_option('soundcloud_is_gold_classes'),
					'playertype' => get_option('soundcloud_is_gold_playerType'),
					'color' => get_option('soundcloud_is_gold_color'),
					'format' => 'tracks'
				), $atts )
		);
	return soundcloud_is_gold_player($id, $autoplay, $comments, $width, $classes, $playertype, $color, $artwork, $format);
}



/******************************************************/
/**                                                  **/
/**                     OUTPUT                       **/
/**                                                  **/
/******************************************************/


/** Preview **/
add_action('wp_ajax_soundcloud_is_gold_player_preview', 'soundcloud_is_gold_player_preview');
function soundcloud_is_gold_player_preview(){
	if(isset($_POST['request'])) echo soundcloud_is_gold_player($_POST['ID'], $_POST['autoPlay'], $_POST['comments'], $_POST['width'], $_POST['classes'], $_POST['playerType'], $_POST['color'], $_POST['artwork'], $_POST['format']);
	die;
}


/** The Player **/
function soundcloud_is_gold_player($id, $autoPlay, $comments, $width, $classes, $playerTypes, $color, $artwork, $format){
	
	//Default values: Needed when not called trough shortode (like in the ajax preview)
	$soundcloudIsGoldSettings = get_option('soundcloud_is_gold_settings');
	if(!isset($autoPlay)) $autoPlay = ((!isset($soundcloudIsGoldSettings[0]) || $soundcloudIsGoldSettings[0] == '') ? 'false' : 'true');
	if(!isset($comments)) $comments = ((!isset($soundcloudIsGoldSettings[1]) || $soundcloudIsGoldSettings[1] == '') ? 'false' : 'true');
	if(!isset($artwork)) $artwork = ((!isset($soundcloudIsGoldSettings[2]) || $soundcloudIsGoldSettings[2] == '') ? 'false' : 'true');
	if(!isset($width)) $width = get_soundcloud_is_gold_default_width(get_option('soundcloud_is_gold_width_settings'));
	if(!isset($classes)) $classes = get_option('soundcloud_is_gold_classes');
	if(!isset($playerTypes)) $playerTypes = get_option('soundcloud_is_gold_playerType');
	if(!isset($color)) $color = get_option('soundcloud_is_gold_color');
	if(!isset($format)) $format = 'tracks';
	elseif($format == 'sets' || $format == 'set') $format = 'playlists';
	$html5Player = false;
	
	$color = str_replace('#', '', $color);
	
	switch($playerTypes){
		case 'Standard':
			$height = ($format == 'tracks') ? '81px' : '165px';
			$playerType = 'standard';
			break;
		case 'Artwork':
			$height = $width;
			$playerType = 'artwork';
			break;
		case 'Mini':
			$height = '18px';
			$playerType = 'tiny';
			break;
		case 'html5':
			$height = ($format == 'tracks') ? '166px' : '450px';
			$html5Player = true;
			break;
	}

	$player = '<div class="soundcloudIsGold '.$classes.'" id="soundcloud-'.$id.'">';
	
	//Flash Player
	if(!$html5Player){
		$player .= '<object height="'.$height.'" width="'.$width.'">';
		$player .= '<param name="movie" value="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2F'.$format.'%2F'.$id.'&amp;auto_play='.$autoPlay.'&amp;player_type='.$playerType.'&amp;show_comments='.$comments.'&amp;color='.$color.'"></param>';
		$player .= '<param name="allowscriptaccess" value="always"></param>';
		$player .= '<param name="wmode" value="transparent"></param>';
		$player .= '<embed wmode="transparent" allowscriptaccess="always" height="'.$height.'" src="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2F'.$format.'%2F'.$id.'&amp;auto_play='.$autoPlay.'&amp;player_type='.$playerType.'&amp;show_comments='.$comments.'&amp;color='.$color.'" type="application/x-shockwave-flash" width="'.$width.'"></embed>';
		$player .= '</object>';	
	}
	//Html5 Player
	else{
		$player .= '<iframe width="'.$width.'" height="'.$height.'" scrolling="no" frameborder="no" src="http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2F'.$format.'%2F'.$id.'&amp;auto_play='.$autoPlay.'&amp;show_artwork='.$artwork.'&amp;color='.$color.'"></iframe>';
	}
	$player .= '</div>';
        
	
	return $player;

}


/** viewer Ajax **/
add_action('wp_ajax_get_soundcloud_player', 'get_soundcloud_player');
add_action('wp_ajax_nopriv_get_soundcloud_player', 'get_soundcloud_player');
function get_soundcloud_player(){
	echo soundcloud_is_gold_player($_POST['id'], $_POST['width'], $_POST['comments'], $_POST['autoPlay'], $_POST['type'], $_POST['color'], $_POST['format']);
	die();
	
}
?>