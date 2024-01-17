<?php

class WC_Stock_Notifier_Display_Form {

    public function __construct() {
        
    }

    /**
     * Display Stock Alert Form
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public static function output( $attr ) {
        global $WC_Stock_Notifier, $product;
        $WC_Stock_Notifier->nocache();
        $frontend_script_path = $WC_Stock_Notifier->plugin_url . 'assets/frontend/js/';
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '';

        if ( empty( $product ) )
            return;

        if ( $product->is_type( 'simple' ) ) {
            echo _e( $WC_Stock_Notifier->frontend->display_subscribe_box( $product ) );
        } else if ( $product->is_type( 'variable' ) ) {
            $stock_interest = $description_text_html = $subscription_button_html = $button_css = '';
            $settings_array = get_wc_stock_notifier_form_settings_array();

            if ( ! empty( $settings_array['form_description_text'] ) ) {
                $description_text_html = '<h5 style="color:' . $settings_array['form_description_text_color'] . '" class="subscribe_for_interest_text">' . $settings_array['form_description_text'] . '</h5>';
            } else {
                $description_text_html = '<h5 class="subscribe_for_interest_text">' . $settings_array['form_description_text'] . '</h5>';
            }

            $border_size = ( ! empty( $settings_array['subscribe_button_border_size'] ) ) ? $settings_array['subscribe_button_border_size'].'px' : '1px';
            
            if ( ! empty( $settings_array['subscribe_button_background_color'] ) )
                $button_css .= "background:" . $settings_array['subscribe_button_background_color'] . ";";
            if ( ! empty( $settings_array['subscribe_button_text_color'] ) )
                $button_css .= "color:" . $settings_array['subscribe_button_text_color'] . ";";
            if ( ! empty( $settings_array['subscribe_button_border_color'] ) )
                $button_css .= "border: " . $border_size . " solid " . $settings_array['subscribe_button_border_color'] . ";";
            if ( ! empty( $settings_array['subscribe_button_font_size'] ) )
                $button_css .= "font-size:" . $settings_array['subscribe_button_font_size'] . "px;";
            if ( ! empty( $settings_array['subscribe_button_border_redious'] ) )
                $button_css .= "border-radius:" . $settings_array['subscribe_button_border_redious'] . "px;";


            if ( ! empty( $button_css ) ) {
                $subscription_button_html = '<button style="' . $button_css .'" class="subscribe_button subscriber_button_hover" name="subscriber_button">' . $settings_array['subscribe_button_text'] . '</button>';
                $unsubscribe_subscription_button_html = '<button class="unsubscribe_button" style="' . $button_css .'">' . $settings_array['unsubscribe_button_text'] . '</button>';
            } else {
                $subscription_button_html = '<button class="subscribe_button" name="subscriber_button">' . $settings_array['subscribe_button_text'] . '</button>';
                $unsubscribe_subscription_button_html = '<button class="unsubscribe_button">' . $settings_array['unsubscribe_button_text'] . '</button>';
            }

            if ( function_exists( 'is_product' ) ) {
                if ( is_product() ) {
                    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                    // Enqueue your frontend javascript from here
                    wp_enqueue_script( 'stock_notifier_shortcode_js', $frontend_script_path . 'shortcode' . $suffix . '.js', array( 'jquery' ), $WC_Stock_Notifier->version, true );
                
                    wp_localize_script( 'stock_notifier_shortcode_js', 'wcsn_sc_data', array(
                        'ajax_url'                  => admin_url( 'admin-ajax.php', 'relative' ),
                        'product_id'                => $product->get_id(),
                        'product_title'             => $product->get_title(),
                        'additional_fields'         => apply_filters( 'wc_stock_notifier_form_additional_fields', [] ),
                        'description_text_html'     => $description_text_html,
                        'unsubscribe_button_html'   => $unsubscribe_subscription_button_html,
                        'subscription_button_html'  => $subscription_button_html,
                    ));
                }
            }
            echo '<div class="stock_notifier-shortcode-subscribe-form"></div>';
        } else {
            echo _e( $WC_Stock_Notifier->frontend->display_subscribe_box( $product ) );
        }

        // remove default stock alert position
        remove_action( 'woocommerce_simple_add_to_cart', [ $WC_Stock_Notifier->frontend, 'display_in_simple_product' ], 31 );
        remove_action( 'woocommerce_bundle_add_to_cart', [ $WC_Stock_Notifier->frontend, 'display_in_simple_product' ], 31 );
        remove_action( 'woocommerce_woosb_add_to_cart', [ $WC_Stock_Notifier->frontend, 'display_in_simple_product' ], 31 );
        remove_action( 'woocommerce_after_variations_form', [ $WC_Stock_Notifier->frontend, 'display_in_no_variation_product' ] );
        remove_action( 'woocommerce_grouped_add_to_cart', [ $WC_Stock_Notifier->frontend, 'display_in_simple_product' ], 32 );
        remove_filter( 'woocommerce_available_variation', [ $WC_Stock_Notifier->frontend, 'display_in_variation' ], 10, 3 );
        // Some theme variation disabled by default if it is out of stock so for that workaround solution.
        remove_filter( 'woocommerce_variation_is_active', [ $WC_Stock_Notifier->frontend, 'enable_disabled_variation_dropdown' ], 100, 2 );
    }

}
