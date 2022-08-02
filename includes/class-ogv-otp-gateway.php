<?php
/**
 * Add OTP Verification Gateway to WC payment gateways.
 *
 * @package otp-gateway-verification
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add OGV_OTP_Gateway class to Gateway classes.
 *
 * @param Array $gateways Payment Gateway classes.
 * @return Array $gateways Payment Gateway classes.
 */
function wc_otp_gateway_class_adding( $gateways ) {
	$gateways[] = 'OGV_OTP_Gateway';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'wc_otp_gateway_class_adding' );


add_action( 'plugins_loaded', 'init_gatway_class', 11 );

	/**
	 * Define OGV_OTP_Gateway Class.
	 */
function init_gatway_class() {

	if ( ! class_exists( 'OGV_OTP_Gateway' ) ) {
		/**
		 * Class OGV_OTP_Gateway.
		 */
		class OGV_OTP_Gateway extends WC_Payment_Gateway {

			/**
			 * Constructor.
			 */
			public function __construct() {
				$this->id                 = 'otp_gatway';
				$this->method_title       = 'OTP Gateway Verification';
				$this->method_description = 'Description of OTP Verification';
				$this->has_fields         = false;

				// Method with all the options fields.
				$this->init_form_fields();

				// Load the settings.
				$this->title       = $this->get_option( 'title' );
				$this->description = $this->get_option( 'description' );
				$this->enabled     = $this->get_option( 'enabled' );
				$this->shop_id     = $this->get_option( 'shop_id' );

				// saving settings.
				add_action( 'woocommerce_checkout_process', array( $this, 'verification_email_field_validation' ) );

			}

			/**
			 * Adding fields in OTP gatway.
			 */
			public function init_form_fields() {
				$this->form_fields = apply_filters(
					'woo_otp_pay_fields',
					array(
						'enabled'      => array(
							'title'   => __( 'Enable/Disable', 'otp-gateway-verification' ),
							'type'    => 'checkbox',
							'label'   => __( 'Enable or Disable OTP Payments', 'otp-gateway-verification' ),
							'default' => 'no',
						),
						'title'        => array(
							'title'       => __( 'OTP Payments Gateway', 'otp-gateway-verification' ),
							'type'        => 'text',
							'default'     => __( 'OTP Payments Gateway', 'otp-gateway-verification' ),
							'desc_tip'    => true,
							'description' => __( 'Add a new title for the OTP Payments Gateway that customers will see when they are in the checkout page.', 'otp-gateway-verification' ),
						),
						'description'  => array(
							'title'       => __( 'OTP Payments Gateway Description', 'otp-gateway-verification' ),
							'type'        => 'textarea',
							'default'     => __( 'Please remit your payment to the shop to allow for the delivery to be made', 'otp-gateway-verification' ),
							'desc_tip'    => true,
							'description' => __( 'Add a new title for the OTP Payments Gateway that customers will see when they are in the checkout page.', 'otp-gateway-verification' ),
						),
						'instructions' => array(
							'title'       => __( 'Instructions', 'otp-gateway-verification' ),
							'type'        => 'textarea',
							'default'     => __( 'Default instructions', 'otp-gateway-verification' ),
							'desc_tip'    => true,
							'description' => __( 'Instructions that will be added to the thank you page and odrer email', 'otp-gateway-verification' ),
						),
					)
				);
			}

			/**
			 * Email field after choosing OTP Gatway in front-end.
			 */
			public function payment_fields() {
				woocommerce_form_field(
					'otp_Verification_email',
					array(
						'type'  => 'email',
						'class' => array( 'transaction_type form-row-wide' ),
						'label' => __( 'Enter your email', 'otp-verification' ),
					),
					''
				);
			}

			/**
			 * Validates OTP email value on OTP Checkout.
			 *
			 * @return boolean
			 */
			public function validate_fields() {
				global $woocommerce;

				if ( ! isset( $_POST['otp_Verification_email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wc_add_notice( __( 'Email for OTP verification is a required field.', 'otp-gateway-verification' ), 'error' );
					return false;
				} else {
					if ( ! filter_var( wp_unslash( $_POST['otp_Verification_email'] ), FILTER_VALIDATE_EMAIL ) ) {  // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						wc_add_notice( __( 'Invalid email address for OTP Verification.', 'otp-gateway-verification' ), 'error' );
						return false;
					}
				}

				return true;
			}

			/**
			 * Sets order on on-hold status, updates Order Stockes code & email on OTP Gateway Checkout.
			 *
			 * @param String $order_id Order's ID.
			 */
			public function process_payment( $order_id ) {

				$order = wc_get_order( $order_id );

				$order->update_status( 'on-hold', __( 'Awaiting for OTP Verification,', 'otp-gateway-verification' ) );

				$order->reduce_order_stock();

				WC()->cart->empty_cart();

				// Calling Ogv_otp class For Sending OTP.
				$otp_class_obj = new Ogv_otp();

				$db_otp = $otp_class_obj->sending_otp_to_email();

				// Updating orders meta with Send OTP email.
				update_post_meta( $order_id, 'OTP (Sent)', $db_otp );

				// Updating orders meta with customer's OTP email.
				update_post_meta( $order_id, 'Email_for_OTP', sanitize_email( wp_unslash( $_POST['otp_Verification_email'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}

		}

	}
}


