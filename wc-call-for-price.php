<?php

/*
Plugin Name: WC Call For Price
Plugin URI: http://www.wordpress.org/wc-call-for-price
Version: 1.0
Author Name: Aftabul Islam
Author Email: toaihimel@gmail.com
Description: This plugin shows "call for price" text/HTML or image on empty price fields. It depends on woocommerce.
License: GPLv2 or later

Copyright 2015  Aftabul Islam  (email : toaihimel@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Restricting Direct Access
defined('ABSPATH') or die("<h1>404</h1> Page not found.");

// Cheacking for PHP Version Match
if(!defined('PHP_VERSION_ID')){

	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ( $version[0] * 10000 + $version[1] * 100 + $version) );

}

if(file_exists('../wp-admin/includes/plugin.php')) require_once('../wp-admin/includes/plugin.php');

define('WC_CALL_FOR_PRICE_VERSION', '1.0');
define('WC_CALL_FOR_PRICE_PATH', plugin_basename(__FILE__));

if(PHP_VERSION_ID < 50200){

	wp_die('<div class=\'error\'><p>'.__('The WC Call For Price plugin requires at least PHP version of 5.2').'</p></div>');

	// Deactivating the plugin
	if(is_plugin_active(WC_CALL_FOR_PRICE_PATH)) deactivate_plugins(WC_CALL_FOR_PRICE_PATH);

}

// Activation and Deactivation Hooks
register_activation_hook(__FILE__, 'wc_call_for_price__activate');
register_deactivation_hook(__FILE__, 'wc_call_for_price__deactivate');

// Loading FrontEnd Assets
add_action('wp_enqueue_scripts', 'wc_call_for_price__frontend_assets');
// WooCommerce Filter
add_filter('woocommerce_empty_price_html', 'wc_call_for_price__call_for_price', 11);

// Activating the plugin
if(!function_exists('wc_call_for_price__activate')){function wc_call_for_price__activate(){

	// Cheacking if the woocommerce plugin is available
	if(!class_exists('WooCommerce')){

		deactivate_plugins(WC_CALL_FOR_PRICE_PATH);

		// Adding an action to show notice at admin panel
		add_action('admin_notices', 'wc_call_for_price__wc_missing_notice');

		return;
	}

	// Creating Options
	$prefix = 'wc_call_for_price__'; // Prefix for every options
	add_option($prefix.'text', 'Call For Price', $deprecated = '', $atuoload = 'yes');
	add_option($prefix.'show_image', 'off', $deprecated = '', $atuoload = 'yes');
	add_option($prefix.'image', 'cfp_1', $deprecated = '', $atuoload = 'yes');
	add_option($prefix.'show_uploaded_image', 'off', $deprecated = '', $atuoload = 'yes');
	add_option($prefix.'upload_image', '', $deprecated = '', $atuoload = 'yes');

	// Successful Activation Admin Notice
	add_action('admin_notices', 'wc_call_for_price__successful_activation_notice');
}}

// Deactivating the plugin
if(!function_exists('wc_call_for_price__deactivate')){ function wc_call_for_price__deactivate(){

	// Removing Options
	$prefix = 'wc_call_for_price__';
	delete_option($prefix.'text');
	delete_option($prefix.'show_image');
	delete_option($prefix.'image');
	delete_option($prefix.'show_uploaded_image');
	delete_option($prefix.'upload_image');

}}

// Plugin Successful Activation Notice
if(!function_exists('wc_call_for_price__successful_activation_notice')){function wc_call_for_price__successful_activation_notice(){
	printf('<div class="success"><p>'.__('WC Call For Price plugin has been successfully activated. Thank you for choosing this plugin.').'</p></div>');
}}

// WooCommerce Plugin Missing Notice Function
if(!function_exists('wc_call_for_price__wc_missing_notice')){function wc_call_for_price__wc_missing_notice(){

	printf('<div class="error"><p>'.__("The WC Call for Price plugin will not work without WooCommerce plugin.").'</p></div>');

    printf('<div class="updated"><p>'.__('Please install <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a>').'</p></div>');

}}

// Loading frontend assets
if(!function_exists('wc_call_for_price__frontend_assets')){function wc_call_for_price__frontend_assets(){

	wp_enqueue_script('jquery');

}}

// WC Call For Price Function
if(!function_exists('wc_call_for_price__call_for_price')){function wc_call_for_price__call_for_price($price){
	$prefix = 'wc_call_for_price__';

	if(get_option($prefix.'show_uploaded_image') == 'on') return '<img src="'.get_option($prefix.'upload_image').'" />';

	if(get_option($prefix.'show_image') == 'on') return '<img src="'.plugins_url('/wc-call-for-price/images/'.get_option($prefix.'image')).'.png" />';
		else {
			$temp = get_option($prefix.'text');
			if(!empty($temp)) return get_option($prefix.'text');
				else return 'Call For Price';
		}
}}



/*--------------------------------------------- ADMIN OPTIONS -----------------------------------------------------*/
add_action('admin_enqueue_scripts', 'wc_call_for_price__admin_assets');
add_action('admin_menu', 'wc_call_for_price__menu');

// Loading Admin Assets
if(!function_exists('wc_call_for_price__admin_assets')){function wc_call_for_price__admin_assets(){

	wp_enqueue_media();
	wp_enqueue_script('jquery');
	wp_enqueue_style('wc_call_for_price__bootstrap_css', plugins_url('/wc-call-for-price/bootstrap-3.3.5/css/bootstrap.min.css'), '3.3.5');
	wp_enqueue_script('wc_call_for_price__bootstrap_js', plugins_url('/wc-call-for-price/bootstrap-3.3.5/js/bootstrap.min.js'), '3.3.5');

}}

// Admin menu Options
if(!function_exists('wc_call_for_price__menu')){function wc_call_for_price__menu(){

	add_submenu_page('woocommerce', 'WooCommerce Call For Price', 'Call For Price', 'manage_options', 'wc-call-for-price', 'wc_call_for_price__menu_options');

}}

// Admin menu Options Page
if(!function_exists('wc_call_for_price__menu_options')){function wc_call_for_price__menu_options(){

	if(!current_user_can('manage_options')) wp_die(__('You don\'t have permission to access this page'));
		else require_once('wp_call_for_price__manage_options_form.php');

}}
?>
