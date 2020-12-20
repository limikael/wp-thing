<?php
/**
 * WP Thing
 *
 * Plugin Name:       WP Thing
 * Plugin URI:        https://github.com/limikael/wp-thing
 * GitHub Plugin URI: https://github.com/limikael/wp-thing
 * Description:       IoT Manager And Data Logger.
 * Version:           1.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Mikael Lindqvist
 * Text Domain:       wp-thing
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) || exit;

define('THING_URL',plugin_dir_url(__FILE__));
define('THING_PATH',plugin_dir_path(__FILE__));

require_once(__DIR__."/ext/CMB2/init.php");
require_once(__DIR__."/ext/cmb2-tabs/plugin.php");
require_once(__DIR__."/src/plugin/ThingPlugin.php");

/**
 * Handle plugin activation.
 *
 * @return void
 */
function thing_activate() {
	thing\ThingPlugin::instance()->activate();
}
register_activation_hook( __FILE__, 'thing_activate' );

/**
 * Handle plugin uninstall.
 *
 * @return void
 */
function thing_uninstall() {
	thing\ThingPlugin::instance()->uninstall();
}
register_uninstall_hook( __FILE__, 'thing_uninstall' );

thing\ThingPlugin::instance();
