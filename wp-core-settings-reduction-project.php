<?php
/**
 * Plugin Name:         WP Core Settings Reduction Project
 * Plugin URI:          https://core.trac.wordpress.org/ticket/32396
 * Description:         This is not just a plugin, it symbolizes the hope and enthusiasm of an entire core feature-as-a-plugin team summed up in a single plugin programmed most famously by Chris Christoff: Hello, World! When activated you will randomly see settings from ticket 32396 disappearing from your computer screen on every settings page.
 * Author:              Chris Christoff
 * Author URI:          http://www.chriscct7.com
 *
 * Version:             0.1
 * Requires at least:   4.4
 * Tested up to:        4.4
 */

add_action( 'load-options-general.php', 'feature_plugin_options_general' );
function feature_plugin_options_general() {
	require_once WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/replacements/options-general.php';
	die(); // but kill the page before the real options-general.php can continue
}

add_action( 'load-options-discussion.php', 'feature_plugin_options_discussion' );
function feature_plugin_options_discussion() {
	require_once WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/replacements/options-discussion.php';
	die(); // but kill the page before the real options-discussion.php can continue
}

add_action( 'load-options.php', 'feature_plugin_options_reading' );
function feature_plugin_options_reading() {
	require_once WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/replacements/options.php';
	die(); // but kill the page before the real options.php can continue
}

add_action( 'admin_init', 'remove_old_settings_menu_pages', 999 );
function remove_old_settings_menu_pages() {
	remove_submenu_page( 'options-general.php', 'options-reading.php' );
	remove_submenu_page( 'options-general.php', 'options-writing.php' );
}