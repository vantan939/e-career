<?php
/*
Plugin Name: Career
Description: Management Career Infomartion
Version: 1.0
Author: Tan Nguyen
Text Domain: e-career
*/

if(!defined('ABSPATH')) exit;

define( 'CAREER_VERSION', '1.0' );
define( 'CAREER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAREER_PLUGIN_URL', plugins_url(basename(dirname(__FILE__))) );
define( 'CAREER_PLUGIN_ASSETS_URL', CAREER_PLUGIN_URL.'/assets' );

require_once( CAREER_PLUGIN_DIR . '/libs/PhpSpreadsheet/vendor/autoload.php' );
require_once( CAREER_PLUGIN_DIR . '/main.php' );