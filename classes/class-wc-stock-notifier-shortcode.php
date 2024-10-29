<?php
if (!defined('ABSPATH')) exit;

/**
 * @version		1.0
 * @package		woocommerce-stock-notifier
 */

class WC_Stock_Notifier_Shortcode {

	public function __construct() {
		// Stock Notifier Form Shortcode
		add_shortcode( 'display_notifier_form', [ $this, 'display_notifier_form' ] );
	}
	function display_notifier_form( $attr ) {
		global $WC_Stock_Notifier;
		$this->load_class( 'display-notifier-form' );
		return $this->shortcode_wrapper( array( 'WC_Stock_Notifier_Display_Form', 'output' ), $attr );
	}

	/**
	 * Shortcode Wrapper
	 */
	public function shortcode_wrapper( $function, $atts = array() ) {
		ob_start();
		call_user_func( $function, $atts );
		return ob_get_clean();
	}

	/**
	 * Shortcode Class Loader
	 */
	
	public function load_class( $class_name = '' ) {
		global $WC_Stock_Notifier;
		if ( '' != $class_name && '' != $WC_Stock_Notifier->token ) {
			require_once ( 'shortcode/class-' . esc_attr( $WC_Stock_Notifier->token ) . '-shortcode-' . esc_attr( $class_name ) . '.php' );
		}
	}
}