<?php

/**
 * @version		1.0
 * @package		woocommerce-stock-notifier
 */

class WC_Stock_Notifier_Ajax {

	public function __construct() {
		
		// Save customer email in database
		add_action( 'wp_ajax_subscribe_ajax', [$this, 'subscribe_product_stock' ] );
		add_action( 'wp_ajax_nopriv_subscribe_ajax', [ $this, 'subscribe_product_stock' ] );

		// Delete unsubscribed users
		add_action( 'wp_ajax_unsubscribe_button', [ $this, 'unsubscribe_product_stock' ] );
		add_action( 'wp_ajax_nopriv_unsubscribe_button', [ $this, 'unsubscribe_product_stock' ] );

		//add fields for variation product shortcode
		add_action( 'wp_ajax_nopriv_get_variation_box_ajax', [ $this, 'get_variation_box_ajax' ] );
		add_action( 'wp_ajax_get_variation_box_ajax', [ $this, 'get_variation_box_ajax' ] );

		//recaptcha version3 validate
		add_action( 'wp_ajax_recaptcha_validate_ajax', [ $this, 'recaptcha_validation_ajax' ] );
		add_action( 'wp_ajax_nopriv_recaptcha_validate_ajax', [ $this, 'recaptcha_validation_ajax'] );
	}

	public function recaptcha_validation_ajax() {
        $recaptcha_secret = isset( $_POST[ 'captcha_secret' ] ) ? $_POST[ 'captcha_secret' ] : '';
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_response = isset( $_POST[ 'captcha_response' ] ) ? $_POST[ 'captcha_response' ] : '';

        $recaptcha = file_get_contents( $recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response );
        $recaptcha = json_decode( $recaptcha );
        if ( ! $recaptcha->success || $recaptcha->score < 0.5 ) {
            echo 0;
        } else {
        	echo 1;
        }
        die();
	}
	
	public function unsubscribe_product_stock() {
		$customer_email = isset( $_POST['customer_email'] ) ? sanitize_email( $_POST['customer_email'] ) : '';
		$product_id = isset( $_POST['product_id'] ) ? (int)$_POST['product_id'] : '';
		$variation_id = isset( $_POST['var_id'] ) ? (int)$_POST['var_id'] : 0;
		$current_subscriber = array();
		$success = 'false';
		if ( $product_id && !empty( $product_id ) && !empty( $customer_email ) ) {
			$product = wc_get_product( $product_id );
			if ( $product && $product->is_type( 'variable' ) && $variation_id > 0 ) {
				$success = wcsn_subscriber_unsubscribe( $variation_id, $customer_email );
			} else {
				$success = wcsn_subscriber_unsubscribe( $product_id, $customer_email );
			}
		}
		echo $success;
		die();
	}
	
	public function subscribe_product_stock() {
		$customer_email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		$product_id = isset( $_POST['product_id'] ) ? (int)$_POST['product_id'] : '';
		$variation_id = isset( $_POST['variation_id'] ) ? (int)$_POST['variation_id'] : 0;
		$status = '';
		if ( $product_id && !empty( $product_id ) && ! empty( $customer_email ) ) {
			$product = wc_get_product($product_id);
			if ( $product && $product->is_type( 'variable' ) && $variation_id > 0 ) {
				$status = wcsn_subscriber_insert( $variation_id, $customer_email );
			} else {
				$status = wcsn_subscriber_insert( $product_id, $customer_email );
			}
		}
		echo $status;
		die();
	}

	public function get_variation_box_ajax(){
		global $WC_Stock_Notifier;
		$product_id = isset( $_POST['product_id'] ) ? (int)$_POST['product_id'] : '';
		$child_id = isset( $_POST['variation_id'] ) ? (int)$_POST['variation_id'] : '';
		$product = wc_get_product( $product_id );
		$display_stock_notifier_form = false;
		
		if ( $child_id && !empty( $child_id ) ) {
			$child_obj = new WC_Product_Variation( $child_id );
			$stock_quantity = $child_obj->get_stock_quantity();
			$managing_stock = $child_obj->managing_stock();
			$is_in_stock = $child_obj->is_in_stock();
			$is_on_backorder = $child_obj->is_on_backorder( 1 );

			if ( ! $is_in_stock ) {
					$display_stock_notifier_form = true;
			} elseif ( $managing_stock && $is_on_backorder && get_wc_stock_notifier_plugin_settings( 'is_backorders_enable' ) ) {
					$display_stock_notifier_form = true;
			} elseif ( $managing_stock ) {
				if ( get_option( 'woocommerce_notify_no_stock_amount' ) ) {
					if ( $stock_quantity <= (int) get_option( 'woocommerce_notify_no_stock_amount' ) && get_wc_stock_notifier_plugin_settings( 'is_backorders_enable' ) ) {
						$display_stock_notifier_form = true;
					}
				}
			}

			if ( $display_stock_notifier_form ) {
				echo $WC_Stock_Notifier->frontend->html_subscribe_form( $product, $child_obj );
			}
		}
		die();
	}
}