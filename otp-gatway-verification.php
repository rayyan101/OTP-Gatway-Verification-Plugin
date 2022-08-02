<?php
/**
 * Plugin Name:       OTP Getway Verifcation
 * Description:       Sending Emails For OTP Verifcation
 *  Plugin URI:       https://codup.co
 * Version:           1.1.1.0
 * Author:            Codup
 * Author URI:        https://codup.co
 * License:           GPL v2 or later
 * Text Domain:       otp-verification

 * @package otp-getway-verification
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'OGV_PLUGIN_DIR' ) ) {
	define( 'OGV_PLUGIN_DIR', __DIR__ );
}

if ( ! defined( 'OGV_PLUGIN_DIR_URL' ) ) {
	define( 'OGV_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'OGV_ABSPATH' ) ) {
	define( 'OGV_ABSPATH', dirname( __FILE__ ) );
}
	require_once OGV_ABSPATH . '/includes/class-ogv-loader.php';

