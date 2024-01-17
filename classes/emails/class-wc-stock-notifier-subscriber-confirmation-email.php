<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Subscriber_Confirmation_Email_Stock_Notifier' ) ) :

/**
 *
 * An confirmation email will be sent to the customer when they subscribe product.
 *
 * @class 		WC_Subscriber_Confirmation_Email_Stock_Notifier
 * @extends 	WC_Email
 */
class WC_Subscriber_Confirmation_Email_Stock_Notifier extends WC_Email {
	
	public $product_id;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		global $WC_Stock_Notifier;
		
		$this->id 				= 'stock_notifier_subscriber_confirmation';
		$this->title 			= __( 'Confirm subscriber', 'wc-stock-notifier' );
		$this->description		= __( 'Notify customer when they subscribe a product', 'wc-stock-notifier' );
		$this->template_html 	= 'emails/stock_notifier_subscriber_confirmation.php';
		$this->template_plain 	= 'emails/plain/stock_notifier_subscriber_confirmation.php';
		$this->template_base 	= $WC_Stock_Notifier->plugin_path . 'templates/';
		
		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $recipient, $product_id ) {
		
		$this->recipient = $recipient;
		$this->product_id = $product_id;
		
		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}
			
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get email subject.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_default_subject() {
		return apply_filters( 'stock_notifier_subscriber_confirmation_email_subject', __( 'You have subscribed a product on {site_title}', 'wc-stock-notifier' ), $this->object );
	}

	/**
	 * Get email heading.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_default_heading() {
		return apply_filters( 'stock_notifier_subscriber_confirmation_email_heading', __( 'Welcome to {site_title}', 'wc-stock-notifier' ), $this->object );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'email_heading' 	=> $this->get_heading(),
			'product_id' 		=> $this->product_id,
			'customer_email' 	=> $this->recipient,
			'sent_to_admin' 	=> false,
			'plain_text' 		=> false,
			'email' 			=> $this,
		), '', $this->template_base );
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'email_heading' 	=> $this->get_heading(),
			'product_id' 		=> $this->product_id,
			'customer_email' 	=> $this->recipient,
			'sent_to_admin' 	=> false,
			'plain_text' 		=> true
		) ,'', $this->template_base );
		return ob_get_clean();
	}
}
endif;