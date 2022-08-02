<?php
	/**
	 * Main Loader File.
	 *
	 * @package otp-gatway-verification
	 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'OGV_Loader' ) ) {

	/**
	 * Class OGV_Loader
	 */
	class OGV_Loader {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->includes();
			add_action( 'wp_enqueue_scripts', array( $this, 'ogv_enqueue_scripts' ) );
		}

		/**
		 * Function for Enqueue Scripts and Style.
		 */
		public function ogv_enqueue_scripts() {
			wp_enqueue_script( 'ogv_script_js', plugin_dir_url( __DIR__ ) . '/assets/js/script.js', array( 'jquery' ), wp_rand() );
			wp_localize_script( 'ogv_script_js', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_style( 'ogv_plugin_style', plugin_dir_url( __DIR__ ) . '/assets/css/scriptstyle.css', array(), '1.0' );
		}

		/**
		 * Function for Including Files and Classes.
		 */
		public function includes() {
			include_once 'class-ogv-gatway.php'; 
			include_once 'class-ogv-otp-sending.php';
			include_once 'class-ogv-otp-checking.php';
			include_once 'class-ogv-otp-checkout-redirection.php';
		}
	}
}
new OGV_Loader();

