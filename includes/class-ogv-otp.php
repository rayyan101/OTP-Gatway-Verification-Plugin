<?php
/**
 * Send OTP for verification on order Checkout.
 *
 * @package otp-gatway-verification
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ogv_otp' ) ) {
	/**
	 * Class OVG_Mail_OTP_Code
	 */
	class Ogv_Otp {

		/**
		 * Sending otp email to customer for varifications
		 */
		public function __construct() {

		}



		/**
		 * Sending otp email to customer for varifications.
		 *
		 * @return int $rand
		 */
		public function sending_otp_to_email() {
			$to      = isset( $_POST['otp_Verification_email'] ) ? sanitize_email( wp_unslash( $_POST['otp_Verification_email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$subject = 'OTP for Verification';
			$rand    = wp_rand( 100000, 999999 );
			$body    = 'Your order\'s OTP for verification is: ' . $rand;

			wp_mail( $to, $subject, $body );

			return $rand;
		}
	}

}
