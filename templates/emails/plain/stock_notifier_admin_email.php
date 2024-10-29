<?php
/**
 *
 * @author 	
 * @version   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WC_Stock_Notifier;

echo $email_heading . "\n\n";

echo sprintf( __( "Hi there. A customer has subscribed a product on your shop. Product details are shown below for your reference:", 'wc-stock-notifier' ) ) . "\n\n";

echo "\n****************************************************\n\n";

$product_data = wcsn_stock_product_data($product_id);

echo "\n Product Name : " . $product_data['name'];

echo "\n\n Product link : " . $product_data['link'];

echo "\n\n\n****************************************************\n\n";

echo "\n\n Customer Details : ".$customer_email;

echo "\n\n\n****************************************************\n\n";


echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
