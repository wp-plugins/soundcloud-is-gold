<?php
$soundcloudIsGoldAdvancedPlayerDefault = array(
    'theme_color' => array('label' => 'Theme Color', 'slug' => 'ThemeColor', 'value' => '', 'type' => 'texts'),
    'text_buy_track' => array('label' => 'Text buy track', 'slug' => 'TextBuyTrack', 'value' => '', 'type' => 'texts'),
    'text_buy_set' => array('label' => 'Text buy set', 'slug' => 'TextBuySet', 'value' => '', 'type' => 'texts'),
    'text_download_track' => array('label' => 'Text download track', 'slug' => 'TextDownloadTrack', 'value' => '', 'type' => 'texts'),
    'start_track' => array('label' => 'Start Track', 'slug' => 'StartTrack', 'value' => '', 'type' => 'texts'),
    'height' => array('label' => 'Height', 'slug' => 'Height', 'value' => '', 'type' => 'texts'),
    'font' => array('label' => 'Font', 'slug' => 'Font', 'value' => '', 'type' => 'texts'),
    
    'buying' => array('label' => 'Show/Hide Buying', 'slug' => 'Buying', 'value' => TRUE, 'type' => 'checkboxes'),
    'sharing' => array('label' => 'Show/Hide Sharing', 'slug' => 'Sharing', 'value' => TRUE, 'type' => 'checkboxes'),
    'show_bpm' => array('label' => 'Show/Hide Bpm', 'slug' => 'bpm', 'value' => TRUE, 'type' => 'checkboxes'),
    'show_playcount' => array('label' => 'Show/Hide Playcount', 'slug' => 'Playcount', 'value' => TRUE, 'type' => 'checkboxes'),
    'enable_api' => array('label' => 'Enable API', 'slug' => 'Api', 'value' => FALSE, 'type' => 'checkboxes'),
    'single_active' => array('label' => 'Single Active', 'slug' => 'SingleActive', 'value' => FALSE, 'type' => 'checkboxes'),
    'show_user' => array('label' => 'Show/Hide User', 'slug' => 'User', 'value' => TRUE, 'type' => 'checkboxes')
    
);
add_option('soundcloud_is_gold_advanced_player', $soundcloudIsGoldAdvancedPlayerDefault);
//delete_option("soundcloud_is_gold_advanced_player");

/* Default Settings */
$soundcloudIsGoldAdvancedPlayer = get_option('soundcloud_is_gold_advanced_player');
?>

<a href="#" title="bring it on!" id="soundcloudMMAdvancedSettingsShowHide">I'm a grown up, show me those Advanced Options!</a>
<ul class="subSettings texts">
    <?php foreach($soundcloudIsGoldAdvancedPlayer as $key => $advancePlayerOption) : ?>
        <?php if(isset($advancePlayerOption['type']) && $advancePlayerOption['type'] == 'texts') :?>
        <li class=""><label for="soundcloudMM<?php echo $advancePlayerOption['slug'] ?>"><?php echo $advancePlayerOption['label'] ?></label><input type="text" value="<?php echo $advancePlayerOption['value'] ?>" name="soundcloud_is_gold_advanced_player[<?php echo $key ?>]" class="soundcloudMMInput soundcloudMM<?php echo $advancePlayerOption['slug'] ?>" id="soundcloudMM<?php echo $advancePlayerOption['slug'] ?>" /></li>
        <?php endif; ?>
            
        <?php if(isset($advancePlayerOption['type']) && $advancePlayerOption['type'] == 'checkboxes') :?>
        <li><input type="checkbox" <?php echo (isset($advancePlayerOption['value']) && $advancePlayerOption['value']) ? 'checked="checked"' : ''?> name="soundcloud_is_gold_advanced_player[<?php echo $key ?>]" value="true" class="soundcloudMM<?php echo $advancePlayerOption['slug'] ?>" id="soundcloudMM<?php echo $advancePlayerOption['slug'] ?>"/><label for="soundcloudMM<?php echo $advancePlayerOption['slug'] ?>"><?php echo $advancePlayerOption['label'] ?></label></li>
        <?php endif; ?>
       
    <?php endforeach; ?>
</ul>