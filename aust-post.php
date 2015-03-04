<?php
/*
Plugin Name: MarketPress Australia Post
Plugin URI: http://buykodo.com
Description: Automatic Shipping Calculation using the Australia Post Shipping API for MarketPress.
Version: 1.2
Author: cybergeekshop
Author URI: http://www.cybergeekshop.net
License: GPL2s
*/

###################################################
####### plugin Code ###############
###################################################

add_action('mp_load_shipping_plugins', 'load_test_shipping_plugin');
function load_test_shipping_plugin() {
 include(dirname(__file__).'/api.php');
}
?>