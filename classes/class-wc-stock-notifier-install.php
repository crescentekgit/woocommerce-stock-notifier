<?php

/**
 * @version     1.0
 * @package     woocommerce-stock-notifier
 */

class WC_Stock_Notifier_Install {
    
    public function __construct() {    
        if ( ! get_option( 'wc_stock_notifier_activate' ) ) {
            $this->wc_stock_notifier_activate();
        }
        
        if ( ! get_option( 'wc_stock_notifier_cron_start' ) ) {
            $this->start_cron_job();
        }
    }

    /*
     * This function will start the cron job
     */
    function start_cron_job() {
        wp_clear_scheduled_hook( 'wc_stock_notifier_start_notification_cron_job' );
        wp_schedule_event( time(), 'hourly', 'wc_stock_notifier_start_notification_cron_job' );
        update_option( 'wc_stock_notifier_cron_start', 1 );
    }

    function wc_stock_notifier_activate() {
        $default_massages = get_wc_stock_notifier_default_massages();
        $stock_notifier_general_settings = array(
            'double_opt_in_success'             => $default_massages['double_opt_in_success'],
            'shown_subscriber_count_text'       => $default_massages['shown_subscriber_count_text'],
        );

        if ( ! get_option( 'wcsn_general_tab_settings' ) ) {
            update_option( 'wcsn_general_tab_settings', $stock_notifier_general_settings );
        }

        $stock_notifier_form_submission_settings = array(
            'subscription_success'              => $default_massages['subscription_success'],
            'subscription_email_exist'          => $default_massages['subscription_email_exist'],
            'subscription_invalid_email'        => $default_massages['subscription_invalid_email'],
            'alert_unsubscribe_message'         => $default_massages['alert_unsubscribe_message'],
        );

        if ( ! get_option( 'wcsn_form_submit_tab_settings' ) ) {
            update_option( 'wcsn_form_submit_tab_settings', $stock_notifier_form_submission_settings );
        }
        update_option( 'wc_stock_notifier_activate', 1 );
    }
}