<?php
/*
Plugin Name: Soundcloud is Gold
Plugin URI: http://www.mightymess.com/soundcloud-is-gold-wordpress-plugin
Description: <strong><a href="http://www.mightymess.com/soundcloud-is-gold-wordpress-plugin">Soundcloud is gold</a></strong> integrates perfectly into wordpress. Browse through your soundcloud tracks from the 'soundcloud is gold' tab with the post's 'upload media' popup window. Select, set and add track to your post using the soundcloud player. Live Preview, easy, smart and straightforward. You can set default settings in the option page, choose your defaut soundcloud player (Mini, Standard, Artwork), its width, extra classes for you CSS lovers and your favorite colors. You'll still be able to set players to different settings before adding to your post if you fancy a one off change.
Version: 1.0
Author: Thomas Michalak at Mighty Mess
Author URI: http://www.mightymess.com/thomas-michalak
License: A "Slug" license name e.g. GPL2
*/

/*
 Default Sizes
 mini: h = 18, w = 100%
 standard: h = 81, w = 100%
 artwork: h = 300, w = 300
*/

define ('PLUGIN_DIR', WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );
require_once('soundcloud-is-gold-functions.php');

/*** Plugin Init ***/
add_action( 'admin_init', 'soundcloud_is_gold_admin_init' );
function soundcloud_is_gold_admin_init() {
    wp_register_script('soundcloud-is-gold-js', PLUGIN_DIR.'soundcloud-is-gold-js.js', array('jquery', 'farbtastic'));
    wp_register_style('soundcloud-is-gold-css', PLUGIN_DIR.'soundcloud-is-gold-css.css');
    wp_register_style('ChunkFive', PLUGIN_DIR.'ChunkFive-fontfacekit/stylesheet.css');
    wp_register_style('Quicksand', PLUGIN_DIR.'Quicksand-fontfacekit/stylesheet.css');
}
//Plugin option scripts
function soundcloud_is_gold_option_scripts() {
    wp_enqueue_script('farbtastic');
    wp_enqueue_script('soundcloud-is-gold-js');
}
//Plugin option style
function soundcloud_is_gold_option_styles() {
  wp_enqueue_style('soundcloud-is-gold-css');
  wp_enqueue_style('farbtastic');
}
//Plugin Options' Fonts
function soundcloud_is_gold_option_fonts() {
  wp_enqueue_style('ChunkFive');
  wp_enqueue_style('Quicksand');
}
/*** Add Admin Menu ***/
add_action('admin_menu', 'soundcloud_is_gold_menu');
function soundcloud_is_gold_menu() {
    $soundcloudIsGoldPage = add_menu_page('Soundcloud is Gold: Options', 'Soundcloud is Gold', 'activate_plugins', 'soundcloud-is-gold.php', 'soundcloud_is_gold_options', PLUGIN_DIR.'soundcloud-is-gold-icon.png');
    add_action( "admin_print_scripts-$soundcloudIsGoldPage", 'soundcloud_is_gold_option_scripts' ); // Add script
    add_action( "admin_print_styles-$soundcloudIsGoldPage", 'soundcloud_is_gold_option_styles' ); // Add Style
    add_action( "admin_print_styles-$soundcloudIsGoldPage", 'soundcloud_is_gold_option_fonts' ); // Add Fonts
}

/*** Link to Settings from the plugin Page ***/
function soundcloud_is_gold_settings_link($links) { 
  $settings_link = '<a href="admin.php?page=soundcloud-is-gold.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'soundcloud_is_gold_settings_link', 10, 2 );

/*** Options and Utilities***/
function get_soundcloud_is_gold_player_types(){
    $m = array('Mini', 'Standard', 'Artwork');
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
	echo 'soundcloudIsGoldUser_default = "'.get_option(soundcloud_is_gold_user).'"; ';
	echo 'soundcloudIsGoldPlayerType_default = "'.get_option(soundcloud_is_gold_playerType).'"; ';
        $soundcloudIsGoldSettings = get_option(soundcloud_is_gold_settings);
	echo 'soundcloudIsGoldAutoPlay_default = '.(($soundcloudIsGoldSettings[0] == '') ? 'false' : 'true') .'; ';
	echo 'soundcloudIsGoldComments_default = '.(($soundcloudIsGoldSettings[1] == '') ? 'false' : 'true') .'; ';
	echo 'soundcloudIsGoldWidth_default = "'.get_soundcloud_is_gold_default_width(get_option(soundcloud_is_gold_width_settings)).'"; ';
	echo 'soundcloudIsGoldClasses_default = "'.get_option(soundcloud_is_gold_classes).'"; ';
	echo 'soundcloudIsGoldColor_default = "'.get_option(soundcloud_is_gold_color).'"; ';	
}

add_option(soundcloud_is_gold_user, 't-m');
$soundcloudIsGoldDefaultSettings = array(
                                        false,
                                        true
);
add_option(soundcloud_is_gold_settings, $soundcloudIsGoldDefaultSettings);
add_option(soundcloud_is_gold_playerType, 'Standard');
$soundcloudIsGoldWitdhDefaultSettings = array(
                                       "type" => "custom",
                                       "wp" => "medium",
                                       "custom" => "100%"
                                    
);
add_option(soundcloud_is_gold_width_settings, $soundcloudIsGoldWitdhDefaultSettings);
add_option(soundcloud_is_gold_classes, '');
add_option(soundcloud_is_gold_color, 'ff7700');

//delete_option(soundcloud_is_gold_width_settings);

/*** Options Output ***/
function soundcloud_is_gold_options(){
    $soundcloudIsGoldUser = get_option(soundcloud_is_gold_user);
    $soundcloudIsGoldSettings = get_option(soundcloud_is_gold_settings);
    $soundcloudIsGoldPlayerType = get_option(soundcloud_is_gold_playerType);
    $soundcloudIsGoldPlayerTypeDefault = empty($soundcloudIsGoldPlayerType) ? TRUE : FALSE;
    $soundcloudIsGoldWidthSettings = get_option(soundcloud_is_gold_width_settings);
    $soundcloudIsGoldClasses = get_option(soundcloud_is_gold_classes);
    $soundcloudIsGoldColor = get_option(soundcloud_is_gold_color);
    $soundcouldMMapiCall = 'http://api.soundcloud.com/users/'.$soundcloudIsGoldUser.'/tracks.xml?limit=1&client_id=9rD2GrGrajkmkw5eYFDp2g';
    //$soundcouldMMapiCall = PLUGIN_DIR.'tracks.xml';
    $soundcouldMMresp = simplexml_load_file($soundcouldMMapiCall);
    if($soundcouldMMresp){
        foreach($soundcouldMMresp as $soundcloudMMLatestTrack){
            $soundcouldMMId = (string)$soundcloudMMLatestTrack->id;
            $soundcouldMMShortcode = '[soundcloud id='.$soundcouldMMId.']';
        }
    }
?>
    
    <script type="text/javascript">
	//Set default Soundcloud Is Gold Settings
        <?php get_soundcloud_is_gold_default_settings_for_js(); ?>
    </script>
    
    <div class="soundcloudMMWrapper soundcloudMMOptions">
    
        <div id="soundcloudMMTop" class="darkGreyGradient">
            <a id="soundcloudMMLogo" class="orangeGradient" href="http://www.soundcloud.com" title="visit SoundCloud website"><img src="<?php echo PLUGIN_DIR ?>/soundcloud-logo-sc.png" width="107" height="71" alt="Soundcloud Logo"/></a>
            <a id="soundcloudMMHeader" class="mediumGreyGradient textShadow" href="http://www.mightymess.com/soundcloud-is-gold-wordpress-plugin" alt="Visit Mighty Mess for more cool stuff">
                <span class="soundcloudMMTitle">SoundCloud is gold <small>by Thomas Michalak</small></span>
                <span class="soundcloudMMUrl">www.mightymess.com/soundcloud-is-gold-wordpress-plugin</span>
            </a>
        </div>
        
        <div id="soundcloudMMMain" class="lightBlueGradient">
            <form method="post" action="options.php" id="soundcloudMMMainForm" name="soundcloudMMMainForm" class="">
            <p class="hidden soundcloudMMId" id="soundcloudMMId-<?php echo $soundcouldMMId ?>"><?php echo $soundcouldMMId ?></p>
            <?php wp_nonce_field('update-options'); ?>
                <ul id="soundcloudMMSettings">
                    <li class="soundcloudMMBox"><label>User Name</label><input class="soundcloudMMInput <?php echo empty($soundcloudIsGoldUser) ? 'soundcloudMMInputInfo' : '' ?>" type="text" name="soundcloud_is_gold_user" id="soundcloudIsGoldUser" value="<?php echo $soundcloudIsGoldUser ?>"></li>
                    <li class="soundcloudMMBox"><label>Default Settings</label>
                        <ul class="subSettings checkboxes">
                            <li><input type="checkbox" <?php echo (isset($soundcloudIsGoldSettings[0]) && $soundcloudIsGoldSettings[0]) ? 'checked="checked"' : ''?> name="soundcloud_is_gold_settings[0]" value="true" class="soundcloudMMAutoPlay" id="soundcloudMMAutoPlay"/><label for="soundcloudMMAutoPlay">Play Automatically</label></li>
                            <li><input type="checkbox" <?php echo (isset($soundcloudIsGoldSettings[1]) && $soundcloudIsGoldSettings[1]) ? 'checked="checked"' : ''?> name="soundcloud_is_gold_settings[1]" value="true" class="soundcloudMMShowComments" id="soundcloudMMShowComments"/><label for="soundcloudMMShowComments">Show track's comments <small>(only for Default and Artwork versions)</small></label></li>
                        </ul>
                    </li>
                    <li class="soundcloudMMBox"><label>Default Player Type</label>
                        <ul class="subSettings radios">
                            <?php
                            foreach(get_soundcloud_is_gold_player_types() as $type) : ?>
                                <li><input name="soundcloud_is_gold_playerType" id="<?php echo $type ?>" class="soundcloudMMPlayerType" type="radio" value="<?php echo $type ?>" <?php if($soundcloudIsGoldPlayerTypeDefault && $type == 'Standard') echo 'checked="checked"'; else echo ($soundcloudIsGoldPlayerType === $type) ? 'checked="checked"' : '' ?> /><label for="<?php echo $type ?>"><?php echo $type; if($type == 'Artwork') echo ' <small>(not available on free soundcloud account)</small>' ?></label></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="soundcloudMMBox"><label>Default Width</label>
                        <ul id="soundcloudMMWidthSetting" class="subSettings texts">
                            <li>
                                <input name="soundcloud_is_gold_width_settings[type]" <?php echo ($soundcloudIsGoldWidthSettings['type'] == "wp") ? 'checked="checked"' : ''; ?> id="soundcloudMMWpWidth" value="wp" type="radio" class="soundcloudMMWpWidth soundcloudMMWidthType radio"/><label for="soundcloudMMWpWidth">Media Width</label>
                                <select class="soundcloudMMInput soundcloudMMWidth" name="soundcloud_is_gold_width_settings[wp]">
                                <?php foreach(get_soundcloud_is_gold_wordpress_sizes() as $key => $soundcloudIsGoldMediaSize) : ?>
                                    <?php
                                    //First Time, then Other Times
                                    if($soundcloudIsGoldWidthSettings['wp'] == 'medium') $soundcloudIsGoldMediaSelected = ($key == $soundcloudIsGoldWidthSettings['wp']) ? 'selected="selected"' : '';  
                                    else $soundcloudIsGoldMediaSelected = ($soundcloudIsGoldMediaSize[0] == $soundcloudIsGoldWidthSettings['wp']) ? 'selected="selected"' : ''; ?>
                                    <option <?php echo $soundcloudIsGoldMediaSelected ?> value="<?php echo $soundcloudIsGoldMediaSize[0]?>" class="soundcloudMMWpSelectedWidth"><?php echo $key.': '.$soundcloudIsGoldMediaSize[0]?></option>
                                <?php endforeach; ?>
                                </select>
                            </li>
                            <li>
                                <input name="soundcloud_is_gold_width_settings[type]" <?php echo ($soundcloudIsGoldWidthSettings['type'] == "custom") ? 'checked="checked"' : ''; ?> id="soundcloudMMCustomWidth" value="custom" type="radio" class="soundcloudMMCustomWidth soundcloudMMWidthType radio"/><label for="soundcloudMMCustomWidth">Custom Width</label>
                                <input name="soundcloud_is_gold_width_settings[custom]" id="soundcloudMMCustomSelectedWidth" class="soundcloudMMInput soundcloudMMWidth soundcloudMMCustomSelectedWidth" type="text" name="soundcloudMMCustomSelectedWidth" value="<?php echo $soundcloudIsGoldWidthSettings['custom'] ?>" />
                            </li>
                        </ul>
                    </li>
                    <li class="soundcloudMMBox"><label>Extras</label>
                        <ul class="subSettings texts">
                            <li>
                                <label>Color</label>
                                <div class="soundcloudMMColorPickerContainer" id="soundcloudMMColorPickerContainer">
                                    <input type="text" class="soundcloudMMInput soundcloudMMColor" id="soundcloudMMColor" name="soundcloud_is_gold_color" value="<?php echo $soundcloudIsGoldColor ?>" style="background-color:<?php echo $soundcloudIsGoldColor ?>"/><a href="#" class="soundcloudMMBt soundcloudMMBtSmall inline blue soundcloudMMRounder soundcloudMMResetColor">reset to default</a>
                                    <div id="soundcloudMMColorPicker" class="shadow soundcloudMMColorPicker"><div id="soundcloudMMColorPickerSelect" class="soundcloudMMColorPickerSelect"></div><a id="soundcloudMMColorPickerClose" class="blue soundcloudMMBt soundcloudMMColorPickerClose">done</a></div>
                                </div>
                            </li>
                            <li class="clear">
                                <label>Classes <small>(no commas)</small></label><input class="soundcloudMMInput soundcloudMMClasses" type="text" name="soundcloud_is_gold_classes" value="<?php echo $soundcloudIsGoldClasses ?>" />
                            </li>
                        </ul>
                    </li>
                    <li class="soundcloudMMBox"><label>Live Preview <small>(your latest track)</small></label>
                        <?php if($soundcouldMMresp) :?>
                        <p class="soundcloudMMEmbed soundcloudMMEmbedOptions" style="text-align:center;">
			    <!-- Soundcloud Preview here -->
			</p>
                        <p class="soundcloudMMLoading" style="display:none"></p>
                        <?php else : ?>
                        <!-- Error getting XML -->
                        <div class="soundcloudMMXmlError"><p>Oups! There's been a error while getting the tracks from soundcloud. Please reload the page.</p></div>
                        <?php endif; ?>
                    </li>
                </ul>
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="soundcloud_is_gold_user, soundcloud_is_gold_settings, soundcloud_is_gold_playerType, soundcloud_is_gold_width_settings, soundcloud_is_gold_classes, soundcloud_is_gold_color" />
                <p id="soundcloudMMSubmit"><input type="submit" name="Submit" value="Save Your SoundCloud Settings" class="soundcloudMMBt orangeGradient"/></p>
            </form>
        </div>
            <ul id="soundcloudMMExtras" class="lightGreyGradient">
                <li><a href="http://soundcloud.com/t-m" title="TM's music on SoundCloud" class="soundcloudMMBt blue soundcloudMMRounder">TM on SoundCloud</a></li>
                <li><a href="http://www.mightymess.com" title="Thomas Michalak's Website" class="soundcloudMMBt blue soundcloudMMRounder">More Mighty Mess</a></li>
                <li><a href="http://wordpress.org/tags/soundcloud-is-gold?forum_id=10" title="Soundcloud is Gold Forum" class="soundcloudMMBt blue soundcloudMMRounder">Forum</a></li>
                <li>
                <form class="soundcloudMMBtForm" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="hosted_button_id" value="9VGA6PYQWETGY">
                        <input type="submit" name="submit" value="Donate via PayPal" class="soundcloudMMBt darkGrey soundcloudMMRounder" alt="PayPal - The safer, easier way to pay online.">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
                    </form>
                </li>
            </ul>
        <p id="disclaimer">SoundCloud and SoundCloud Logo are trademarks of SoundCloud Ltd.</p>
    </div>
    
    <?php
}


?>