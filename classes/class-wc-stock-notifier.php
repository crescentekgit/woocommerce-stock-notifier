<?php

/**
 * @version     1.0
 * @package     woocommerce-stock-notifier
 */

class WC_Stock_Notifier {
    public $token;
    public $plugin_url;
    public $plugin_path;
    public $version;
    public $template;
    public $admin;
    public $shortcode;
    public $frontend;
    public $ajax;
    private $file;

    public function __construct( $file ) {
        $this->file = $file;
        $this->plugin_url = trailingslashit( plugins_url( '', $plugin = $file ) );
        $this->plugin_path = trailingslashit( dirname( $file ) );
        $this->token = WC_STOCK_NOTIFIER_PLUGIN_TOKEN;
        $this->version = WC_STOCK_NOTIFIER_PLUGIN_VERSION;

        add_action( 'init', [ &$this, 'init' ], 0 );
        // Woocommerce Email structure
        add_filter( 'woocommerce_email_classes', [ &$this, 'wc_stock_notifier_mail' ] );
        add_action( 'wc_stock_notifier_start_notification_cron_job', 'wc_stock_notify_to_subscribed_user' );
    }

    /**
     * initilize plugin on init
     */
    function init() {
        $this->load_plugin_textdomain();
 
        if ( defined( 'DOING_AJAX' ) ) {
            $this->load_class( 'ajax' );
            $this->ajax = new WC_Stock_Notifier_Ajax();
        }

        if ( is_admin() ) {
            $this->load_class( 'admin' );
            $this->admin = new WC_Stock_Notifier_Admin();
        }

        if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
            $this->load_class( 'frontend' );
            $this->frontend = new WC_Stock_Notifier_Frontend();

            $this->load_class( 'shortcode' );
            $this->shortcode = new WC_Stock_Notifier_Shortcode();
        }
        $this->load_class( 'template' );
        $this->template = new WC_Stock_Notifier_Template();


        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'rest_api_init', [ $this, 'wc_stock_notifier_rest_routes' ] );
        }

        register_post_status( 'wcsn_mailsent', [
            'label' => _x( 'Mail Sent', 'wcstocknotifier', 'wc-stock-notifier' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop( 'Mail Sent <span class="count">(%s)</span>', 'Mail Sent <span class="count">(%s)</span>', 'wc-stock-notifier' ),
        ]);

        register_post_status( 'wcsn_subscribed', [
            'label' => _x( 'Subscribed', 'wcstocknotifier', 'wc-stock-notifier' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop( 'Subscribed <span class="count">(%s)</span>', 'Subscribed <span class="count">(%s)</span>' ),
        ]);

        register_post_status( 'wcsn_unsubscribed', [
            'label' => _x( 'Unsubscribed', 'wcstocknotifier', 'wc-stock-notifier' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop( 'Unsubscribed <span class="count">(%s)</span>', 'Unsubscribed <span class="count">(%s)</span>' ),
        ]);
    }


    /**
     * Load Localisation files.
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters( 'plugin_locale', $locale, 'wc-stock-notifier' );
        load_textdomain( 'wc-stock-notifier', WP_LANG_DIR . '/woocommerce-stock-notifier/woocommerce-stock-notifier-' . $locale . '.mo' );
        load_plugin_textdomain( 'wc-stock-notifier', false, plugin_basename(dirname(dirname(__FILE__))) . '/languages' );
    }

    public function load_class( $class_name = '' ) {
        if ( '' != $class_name && '' != $this->token ) {
            require_once ( 'class-' . esc_attr( $this->token ) . '-' . esc_attr( $class_name ) . '.php' );
        } // End If Statement
    }

    /**
     * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
     */
    public function nocache() {
        if ( ! defined( 'DONOTCACHEPAGE' ) )
            define( "DONOTCACHEPAGE", "true" );
            // WP Super Cache constant
    }

    /**
     * Install upon activation
     */
    public static function activate_wc_stock_notifier() {
        global $WC_Stock_Notifier;
        update_option( 'wc_stock_notifier_installed', 1 );
        // Init install
        $WC_Stock_Notifier->load_class( 'install' );
        new WC_Stock_Notifier_Install();
    }

    /**
     * Install upon deactivation
     *
     */
    public static function deactivate_wc_stock_notifier() {
        if ( get_option( 'wc_stock_notifier_cron_start' ) ) {
            wp_clear_scheduled_hook( 'wc_stock_notifier_start_notification_cron_job' );
            delete_option( 'wc_stock_notifier_cron_start' );
        }
         delete_option( 'wc_stock_notifier_installed' );
    }

    public function wc_stock_notifier_rest_routes() {
        register_rest_route( 'wc_stocknotifier/v1', '/fetch_admin_tabs', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => array( $this, 'wc_stock_notifier_fetch_admin_tabs' ),
            'permission_callback' => array( $this, 'wc_stock_notifier_permission' ),
        ] );
        register_rest_route( 'wc_stocknotifier/v1', '/save_admin_settings', [
            'methods'   => WP_REST_Server::EDITABLE,
            'callback'  => array( $this, 'wc_stock_notifier_save_admin_settings' ),
            'permission_callback' => array( $this, 'wc_stock_notifier_permission' ),
        ] );
        register_rest_route( 'wc_stocknotifier/v1', '/get_button_data', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => array( $this, 'wc_stock_notifier_get_button_data' ),
            'permission_callback' => array( $this, 'wc_stock_notifier_permission' ),
        ] );
        register_rest_route( 'wc_stocknotifier/v1', '/no_of_subscribe_list', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => array( $this, 'wc_stock_notifier_fetch_no_of_subscribe_list' ),
            'permission_callback' => array( $this, 'wc_stock_notifier_permission' )
        ] );
        register_rest_route( 'wc_stocknotifier/v1', '/show_subscribe_from_status_list', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => array( $this, 'wc_stock_notifier_show_subscribe_from_status_list' ),
            'permission_callback' => array( $this, 'wc_stock_notifier_permission' )
        ] );
        register_rest_route( 'wc_stocknotifier/v1', '/search_specific_subscribe', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => array( $this, 'wc_stock_notifier_search_specific_subscribe' ),
            'permission_callback' => array( $this, 'wc_stock_notifier_permission' )
        ] );
        register_rest_route( 'wc_stocknotifier/v1', '/search_subscribe_by_product', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => array( $this, 'wc_stock_notifier_search_subscribe_by_product' ),
            'permission_callback' => array( $this, 'wc_stock_notifier_permission' )
        ] );
    }

    public function wc_stock_notifier_show_subscribe_from_status_list( $request ) {
        $params = $request->get_params();
        $status = isset( $params['subscription_status'] ) ? $params['subscription_status'] : '';
        $subscribtions = $this->get_all_subscription_post( $request, $status );
        return rest_ensure_response( $subscribtions );
    }

    public function wc_stock_notifier_search_specific_subscribe( $request ) {;
        $params = $request->get_params();
        $email = isset( $params['email_id'] ) ? $params['email_id'] : '';
        $subscribtions = $this->get_all_subscription_post( $request,'', '', $email );
        return rest_ensure_response( $subscribtions );
    }

    public function wc_stock_notifier_search_subscribe_by_product( $request ){
        $params = $request->get_params();
        $status = isset( $params['subscription_status'] ) ? $params['subscription_status'] : '';
        $product = isset( $params['product'] ) ? $params['product'] : '';
        $product_ids = array();
        if ( $product ) {
            $args = array(
                's'      => $product,
                'return' => 'ids',
            );
            $products_obj_ids = wc_get_products( $args );
            foreach ( $products_obj_ids as $id ) {
                $product_obj = wc_get_product( $id );
                if ( $product_obj->is_type('variable') ) {
                    $product_ids = $product_obj->get_children(); 
                } else {
                    $product_ids[] = $id;
                }
            }
        }
        
        $subscribtions = $this->get_all_subscription_post( $request, $status, $product_ids );
        return rest_ensure_response($subscribtions );
    }

    public function wc_stock_notifier_fetch_no_of_subscribe_list( $request ) {
        $params = $request->get_params();
        $status = isset( $params['subscribtion_status'] ) ? $params['subscribtion_status'] : '';
        $subscribe_list = 0;
        $subscribtions = $this->get_all_subscription_post( $request, $status );
        $subscribe_list = $subscribtions;
        return rest_ensure_response( count( $subscribe_list ) );
    }

    public function get_all_subscription_post( $request = '', $status = '', $product_ids = array(), $email = '' ) {
        $reg_user = '';
        $date_range = $request && $request->get_param('date_range') ? $request->get_param('date_range') : '';
        $start_date    = strtotime( '-20 years', strtotime( 'midnight', current_time( 'timestamp' ) ) );
        $end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
        if ( $date_range ) {
            $initial_start = $date_range ? $date_range[0] : '';
            $initial_end = $date_range ? $date_range[1] : '';
            $start_date = max( strtotime( '-20 years' ), strtotime( sanitize_text_field( $initial_start ) ) );
            $end_date = strtotime( 'midnight', strtotime( sanitize_text_field( $initial_end ) ) );
        }
        $subscribtions = $subscribe_list = array();
        $args = array(
            'post_type'         => 'WcStockNotifier',
            'posts_per_page'    => -1,
            'post_status'       => $status ? $status : 'any',
            'date_query'        => array(
                'inclusive' => true,
                'after'     => array(
                    'year'  => date('Y', $start_date),
                    'month' => date('n', $start_date),
                    'day'   => date('j', $start_date), //date('1'),
                ),
                'before' => array(
                    'year'  => date('Y', $end_date),
                    'month' => date('n', $end_date),
                    'day'   => date('j', $end_date),
                ),
            ),
        );
        if ( $product_ids ) {
            $args['meta_query'] = array(
                array(
                    'key'       => 'wcsninstock_product_id',
                    'value'     => $product_ids,
                    'compare'   => 'IN'
                )
            );
        }

        if ( $email && $email != '' ) {
            $args['meta_query'] = array(
                array(
                    'key'       => 'wcsninstock_subscriber_email',
                    'value'     => wc_clean( $email ),
                    'compare'   => 'LIKE'
                )
            );
        }

        $subscribtions = new WP_Query($args);
        if ( $subscribtions->get_posts() && ! empty( $subscribtions->get_posts() ) ) {
            foreach ( $subscribtions->get_posts() as $post ) {
                $product_id = get_post_meta( $post->ID, 'wcsninstock_product_id', true ) ? get_post_meta( $post->ID, 'wcsninstock_product_id', true ) : 0;
                $email_id = get_post_meta( $post->ID, 'wcsninstock_subscriber_email', true ) ? get_post_meta( $post->ID, 'wcsninstock_subscriber_email', true ) : '';
                if ( $email_id && ! empty( $email_id ) )
                    $reg_user = email_exists( $email_id ) ? __( 'Yes', 'wc-stock-notifier' ) : __( 'No', 'wc-stock-notifier' );
                $product = get_post( $product_id );
                $status_raw = $post->post_status;
                switch ( $status_raw ) {
                    case 'wcsn_mailsent':
                        $alert_status = __( 'Mail Sent', 'wc-stock-notifier' );
                        break;
                    case 'wcsn_subscribed':
                        $alert_status = __( 'Subscribed', 'wc-stock-notifier' );
                        break;
                    case 'wcsn_unsubscribed':
                        $alert_status = __( 'Unsubscribed', 'wc-stock-notifier' );
                        break;
                    default:
                        $alert_status = __( '-', 'wc-stock-notifier' );
                        break;
                }

                $subscribe_list[] = apply_filters('wc_stock_notifier_subscriber_list_data', array(
                    'email'         => $email_id,
                    'status'        => $alert_status,
                    'product'       => $product->post_title ? $product->post_title : '-',
                    'registered'    => $reg_user ? $reg_user : __( 'No', 'wc-stock-notifier' ),
                    'date'          => get_the_time( 'd M Y', $post->ID )  
                ), $post);
            }
        }
        return $subscribe_list;
    }

    public function wc_stock_notifier_permission() {
        return true;
    }
    
    public function wc_stock_notifier_fetch_admin_tabs() {
		$wcsn_admin_tabs_data = wcsn_stockalert_admin_tabs() ? wcsn_stockalert_admin_tabs() : [];
        return rest_ensure_response( $wcsn_admin_tabs_data );
	}

    public function wc_stock_notifier_get_button_data() {
        $button_data = array(
            'form_description_text_color'                 => get_wc_stock_notifier_plugin_settings( 'form_description_text_color', '' ),
            'subscribe_button_background_color'           => get_wc_stock_notifier_plugin_settings( 'subscribe_button_background_color', '' ),
            'subscribe_button_border_color'               => get_wc_stock_notifier_plugin_settings( 'subscribe_button_border_color', '' ),
            'subscribe_button_text_color'                 => get_wc_stock_notifier_plugin_settings( 'subscribe_button_text_color', '' ),
            'subscribe_button_background_color_onhover'   => get_wc_stock_notifier_plugin_settings( 'subscribe_button_background_color_onhover', '' ),
            'subscribe_button_text_color_onhover'         => get_wc_stock_notifier_plugin_settings( 'subscribe_button_text_color_onhover', '' ),
            'subscribe_button_border_color_onhover'       => get_wc_stock_notifier_plugin_settings( 'subscribe_button_border_color_onhover', '' ),
            'subscribe_button_font_size'                  => get_wc_stock_notifier_plugin_settings( 'subscribe_button_font_size', '' ),
            'subscribe_button_border_radious'             => get_wc_stock_notifier_plugin_settings( 'subscribe_button_border_radious', '' ),
            'subscribe_button_border_size'                => get_wc_stock_notifier_plugin_settings( 'subscribe_button_border_size', '' ),
        );
        return rest_ensure_response( $button_data );
    }

    public function wc_stock_notifier_save_admin_settings( $request ) {
        $all_details = [];
        $modulename = $request->get_param( 'modulename' );
        $modulename = str_replace( "-", "_", $modulename );
        $get_managements_data = $request->get_param( 'model' );
        $optionname = 'wcsn_'.$modulename.'_tab_settings';
        update_option( $optionname, $get_managements_data );
        do_action( 'wcsn_settings_after_save', $modulename, $get_managements_data );
        $all_details['error'] = __( 'Settings Saved', 'wc-stock-notifier' );
        return $all_details;
        die;
    }

    public function wc_stock_notifier_mail( $emails ) {
        require_once( 'emails/class-wc-stock-notifier-admin-email.php' );
        require_once( 'emails/class-wc-stock-notifier-subscriber-confirmation-email.php' );
        require_once( 'emails/class-wc-stock-notifier-alert-email.php' );

        $emails['WC_Admin_Email_Stock_Notifier'] = new WC_Admin_Email_Stock_Notifier();
        $emails['WC_Subscriber_Confirmation_Email_Stock_Notifier'] = new WC_Subscriber_Confirmation_Email_Stock_Notifier();        
        $emails['WC_Email_Stock_Notifier'] = new WC_Email_Stock_Notifier();

        return $emails;
    }
}