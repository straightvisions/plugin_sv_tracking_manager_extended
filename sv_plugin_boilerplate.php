<?php
/*
Version: 1.4.00
Plugin Name: SV Plugin Boilerplate
Text Domain: sv_plugin_boilerplate
Description: Customer specific features which are too small and specific to worth an own plugin.
Plugin URI: https://straightvisions.com/
Author: straightvisions GmbH
Author URI: https://straightvisions.com
Domain Path: /languages
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0-standalone.html
*/

namespace sv_plugin_boilerplate;

if(!class_exists('\sv_dependencies\init')){
	require_once( 'lib/core_plugin/dependencies/sv_dependencies.php' );
}

if ( $GLOBALS['sv_dependencies']->set_instance_name( 'SV Plugin Boilerplate' )->check_php_version() ) {
	require_once( dirname(__FILE__) . '/init.php' );
} else {
	$GLOBALS['sv_dependencies']->php_update_notification()->prevent_plugin_activation();
}