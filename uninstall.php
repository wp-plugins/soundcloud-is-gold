<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) exit();
 
delete_option("soundcloud_is_gold_user");
delete_option("soundcloud_is_gold_settings");
delete_option("soundcloud_is_gold_playerType");
delete_option("soundcloud_is_gold_width_settings");
delete_option("soundcloud_is_gold_classes");
delete_option("soundcloud_is_gold_color");

?>