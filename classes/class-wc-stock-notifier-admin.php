<?php

/**
 * @version     1.0
 * @package     woocommerce-stock-notifier
 */

class WC_Stock_Notifier_Admin {

    public $settings;

    public function __construct() {
        // load menu
        $this->load_class('settings');
        $this->settings = new WC_Stock_Notifier_Settings();
        //load Script
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_script' ] );

        // show number of subscribers for individual product
        add_action( 'woocommerce_product_options_inventory_product_data', [ $this, 'product_subscriber_details' ] );
        add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'manage_variation_product_subscriber' ], 10, 3 );
        
        // add product_subscriber column
        add_action( 'manage_edit-product_columns', [ $this, 'add_product_subscriber_column' ] );
        // manage product_subscriber column
        add_action( 'manage_product_posts_custom_column', [ $this, 'manage_product_subscriber_column' ], 10, 2 );

        // check product stock status and update the user
        add_action( 'save_post', [ $this, 'product_stock_status_update' ], 5, 2 );

        // bulk action to remove subscribers
        add_filter( 'bulk_actions-edit-product', [ $this, 'register_product_subscriber_bulk_actions' ] );
        add_filter( 'handle_bulk_actions-edit-product', [ $this, 'subscribers_bulk_action_handler' ], 10, 3 );

        add_action( 'admin_notices', [ $this, 'subscribers_bulk_action_admin_notice' ] );
    }

    public function load_class( $class_name = '' ) {
        global $WC_Stock_Notifier;
        if ( '' != $class_name ) {
            require_once( $WC_Stock_Notifier->plugin_path . '/admin/class-' . esc_attr( $WC_Stock_Notifier->token ) . '-' . esc_attr( $class_name ) . '.php' );
        }
    }

    public function register_product_subscriber_bulk_actions( $bulk_actions ) {
        $bulk_actions['remove_product_subscribers'] = __( 'Remove Subscribers', 'wc-stock-notifier' );
        return $bulk_actions;
    }

    public function subscribers_bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
        if ( $doaction !== 'remove_product_subscribers' ) {
            return $redirect_to;
        }
        foreach ( $post_ids as $post_id ) {
            $product = wc_get_product( $post_id );
            if ( $product && $product->is_type( 'variable' ) ) {
                if ( $product->has_child() ) {
                    $child_ids = $product->get_children();
                    if ( isset( $child_ids ) && ! empty( $child_ids ) ) {
                        foreach ( $child_ids as $child_id ) {
                            $subscribers_email = wcsn_get_product_subscribers_email( $child_id );
                            if ( $subscribers_email && !empty( $subscribers_email ) ) {
                                foreach ( $subscribers_email as $alert_id => $to ) {
                                    wcsn_update_product_subscriber( $alert_id, 'wcsn_unsubscribed' );
                                }
                                delete_post_meta( $child_id, 'no_of_subscribers' );
                            }
                        }
                    }
                }
			} else {
                $subscribers_email = wcsn_get_product_subscribers_email( $post_id );
                if ( $subscribers_email && ! empty( $subscribers_email ) ) {
                    foreach ( $subscribers_email as $alert_id => $to ) {
                        wcsn_update_product_subscriber( $alert_id, 'wcsn_unsubscribed' );
                    }
                    delete_post_meta( $post_id, 'No_of_subscribers' );
                }
            }
        }
        $redirect_to = add_query_arg( 'bulk_remove_subscribers', count( $post_ids ), $redirect_to );
        return $redirect_to;
    }

    public function subscribers_bulk_action_admin_notice() {
        if ( ! empty( $_REQUEST['bulk_remove_subscribers'] ) ) {
            $bulk_remove_count = intval( $_REQUEST['bulk_remove_subscribers'] );
            printf( '<div id="message" class="updated fade"><p>' .
                    _n( 'Removed subscribers from %s product.', 'Removed subscribers from %s products.', $bulk_remove_count, 'wc-stock-notifier'
                    ) . '</p></div>', $bulk_remove_count );
        }
    }

    /**
     * Admin Scripts
     */
    public function enqueue_admin_script() {
        global $WC_Stock_Notifier;
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $columns_subscriber = apply_filters( 'wc_stock_notifier_subscribers_list_headers', array(
            array(
                'name'      =>  __( 'Email', 'wc-stock-notifier' ),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "email",
            ),
            array(
                'name'      =>  __( 'Status', 'wc-stock-notifier' ),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "status",
            ),
            array(
                'name'      =>  __( 'Product', 'wc-stock-notifier' ),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "product",
            ),
            array(
                'name'      =>  __( 'Registered User', 'wc-stock-notifier' ),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "registered",
            ),
            array(
                'name'      =>  __( 'Date', 'wc-stock-notifier' ),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "date",
            ),
        ));

        $subscription_page_string     =   array(
            'all'           =>  __( 'All', 'wc-stock-notifier' ),
            'subscribe'     =>  __( 'Subscribe', 'wc-stock-notifier' ),
            'unsubscribe'   =>  __( 'Unsubscribe', 'wc-stock-notifier' ),
            'mail_sent'     =>  __( 'Mail Sent', 'wc-stock-notifier' ),
            'search'        =>  __( 'Search by Email', 'wc-stock-notifier' ),
            'show_product'  =>  __( 'Search by Product Name', 'wc-stock-notifier' ),
            'daterenge'     =>  __( 'DD-MM-YYYY ~ DD-MM-YYYY', 'wc-stock-notifier' ),
        );

        $setting_string     =   array(
            'form_dec'              =>  __( 'Form Description', 'wc-stock-notifier' ),
            'submit_button_text'    =>  __( 'Button Text', 'wc-stock-notifier' ),
            'background'            =>  __( 'Button Background', 'wc-stock-notifier' ),
            'border'                =>  __( 'Button Border', 'wc-stock-notifier' ),
            'hover_background'      =>  __( 'Hover Button Background', 'wc-stock-notifier' ),
            'hover_border'          =>  __( 'Hover Button Border', 'wc-stock-notifier' ),
            'hover_text'            =>  __( 'Hover Button Text', 'wc-stock-notifier' ),
            'font_size'             =>  __( 'Font Size', 'wc-stock-notifier' ),
            'border_radius'         =>  __( 'Border Radius', 'wc-stock-notifier' ),
            'border_size'           =>  __( 'Border Size', 'wc-stock-notifier' ),
        );
        
        if ( get_current_screen()->id == 'toplevel_page_wcsn-stock-notifier-setting' ) {
            $default_massages = get_wc_stock_notifier_default_massages();
            wp_enqueue_script( 'wc-stock-notifier-script', $WC_Stock_Notifier->plugin_url . 'build/index.js', array( 'wp-element' ), $WC_Stock_Notifier->version, true );
            wp_localize_script( 'wc-stock-notifier-script', 'wcsnLocalizer', apply_filters('wc_stock_notifier_admin_default_text', [
                'apiUrl'                            => home_url('/wp-json'),
                'nonce'                             => wp_create_nonce('wp_rest'),
                'default_form_description_text'     => $default_massages['form_description_text'],
                'default_email_place'               => $default_massages['email_placeholder_text'],
                'default_subscribe_button_text'     => $default_massages['subscribe_button_text'],
                'columns_subscriber'                => $columns_subscriber,
                'subscription_page_string'          => $subscription_page_string,
                'download_csv'                      => __( 'Download CSV', 'wc-stock-notifier' ),
                'setting_string'                    => $setting_string,
              ]));
            wp_enqueue_style( 'wc-stock-notifier-style', $WC_Stock_Notifier->plugin_url . 'build/index.css' );
            wp_enqueue_style( 'wc-stock-notifier-rsuite-css', $WC_Stock_Notifier->plugin_url . 'assets/admin/css/rsuite-default' . '.min' . '.css', array(), $WC_Stock_Notifier->version );
        } 
        wp_enqueue_style( 'wc-stock-notifier-product-admin-css', $WC_Stock_Notifier->plugin_url . 'assets/admin/css/admin'. $suffix .'.css' );
    }

    /**
     * Custom column addition
     */
    public function add_product_subscriber_column( $columns ) {
        return array_merge( $columns, array( 'product_subscriber' => __( 'Interested Person(s)', 'wc-stock-notifier' ) ) );
    }

    /**
     * Manage custom column for Stock Alert
     */
    public function manage_product_subscriber_column( $column_name, $post_id ) {
        $no_of_subscriber = 0;
        $product_subscriber = $child_ids = $product_obj = array();
        switch ( $column_name ) {
            case 'product_subscriber' :
                $product_obj = wc_get_product($post_id);
                if (!$product_obj->is_type('grouped')) {
                    if ($product_obj->is_type('variable')) {
                        $child_ids = $product_obj->get_children();
                        if ( isset( $child_ids ) && !empty( $child_ids ) ) {
                            foreach ( $child_ids as $child_id ) {
                                if (wcsn_is_product_outofstock( $child_id, 'variation' ) ) {
                                    $no_of_subscriber += wcsn_get_no_subscribed_persons( $child_id, 'wcsn_subscribed' );
                                }
                            }
                        }
                        echo '<div class="product-subscribtion-column">' . $no_of_subscriber . '</div>';
                    } else {
                        if ( wcsn_is_product_outofstock($product_obj->get_id() ) ) {
                            $no_of_subscriber = wcsn_get_no_subscribed_persons( $product_obj->get_id(), 'wcsn_subscribed' );
                        }
                        echo '<div class="product-subscribtion-column">' . $no_of_subscriber . '</div>';
                    }
                }
        }
    }

    /**
     * Stock Alert news on Product edit page (simple)
     */
    public function product_subscriber_details() {
        global $post, $WC_Stock_Notifier;
        $no_of_subscriber = 0;
        $product_obj = wc_get_product($post->ID);
        if ( ! $product_obj->is_type( 'variable' ) ) {
            if ( wcsn_is_product_outofstock( $post->ID ) ) {
                $no_of_subscriber = wcsn_get_no_subscribed_persons( $post->ID, 'wcsn_subscribed' );
                if ( ! empty( $no_of_subscriber ) && $no_of_subscriber > 0 ) {
                    ?>
                    <p class="form-field _stock_field">
                        <label class=""><?php _e( 'Number of Interested Person(s)', 'wc-stock-notifier' ); ?></label>
                        <span class="no_subscriber"><?php echo $no_of_subscriber; ?></span>
                    </p>
                    <?php
                }
            }
        }
    }

    /**
     * Show person on variable product edit page
     */
    public function manage_variation_product_subscriber( $loop, $variation_data, $variation ) {
        global $WC_Stock_Notifier;
        if ( wcsn_is_product_outofstock( $variation->ID, 'variation' ) ) {
            $product_subscriber = wcsn_get_no_subscribed_persons( $variation->ID, 'wcsn_subscribed' );
            if ( ! empty( $product_subscriber ) && $product_subscriber > 0 ) {
                ?>
                <p class="form-row form-row-full interested_person">
                    <label class="stock_label"><?php echo _e( 'Number of Interested Person(s) : ', 'wc-stock-notifier' ); ?></label>
                <div class="variation_no_subscriber"><?php echo $product_subscriber; ?></div>
                </p>
                <?php
            }
        }
    }

    /**
     * Send mail on product stock update
     */
    public function product_stock_status_update( $post_id, $post ) {
        if ( $post->post_type == 'product' ) {
            $product_subscriber = array();
            $product_obj = array();
            $product_obj = wc_get_product( $post_id );
            if ( $product_obj && $product_obj->is_type( 'variable' ) ) {
                if ( $product_obj->has_child() ) {
                    $child_ids = $product_obj->get_children();
                    if ( isset( $child_ids ) && !empty( $child_ids ) ) {
                        foreach ( $child_ids as $child_id ) {
                            $child_obj = new WC_Product_Variation( $child_id );
                            $product_subscriber = wcsn_get_product_subscribers_email( $child_id ); 
                            if ( isset( $product_subscriber ) && ! empty( $product_subscriber ) ) {
                                $product_availability_stock = $child_obj->get_stock_quantity();
                                $manage_stock = $child_obj->get_manage_stock();
                                $stock_status = $child_obj->get_stock_status();
                                if ( isset( $product_availability_stock ) && $manage_stock ) {
                                    if ( $product_availability_stock > (int) get_option( 'woocommerce_notify_no_stock_amount' ) ) {
                                        $email = WC()->mailer()->emails['WC_Email_Stock_Notifier'];
                                        foreach ($product_subscriber as $subscribe_id => $to ) {
                                            $email->trigger($to, $child_id);
                                            wcsn_update_product_subscriber( $subscribe_id, 'wcsn_mailsent' );
                                            delete_post_meta( $child_id, 'No_of_subscribers' );
                                        }
                                    }
                                } elseif ($stock_status == 'instock' ) {
                                    $email = WC()->mailer()->emails['WC_Email_Stock_Notifier'];
                                    foreach ( $product_subscriber as $subscribe_id => $to ) {
                                        $email->trigger( $to, $child_id );
                                        wcsn_update_product_subscriber( $subscribe_id, 'wcsn_mailsent' );
                                        delete_post_meta( $child_id, 'No_of_subscribers' );
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $product_subscriber = wcsn_get_product_subscribers_email( $post_id );
                if ( isset( $product_subscriber ) && ! empty( $product_subscriber ) ) {
                    $product_availability_stock = $product_obj->get_stock_quantity();
                    $manage_stock = $product_obj->get_manage_stock();
                    $stock_status = $product_obj->get_stock_status();
                    if (isset( $product_availability_stock ) && $manage_stock ) {
                        if ( $product_availability_stock > (int) get_option('woocommerce_notify_no_stock_amount' ) ) {
                            $email = WC()->mailer()->emails['WC_Email_Stock_Notifier'];
                            foreach ( $product_subscriber as $subscribe_id => $to ) {
                                $email->trigger( $to, $post_id );
                                wcsn_update_product_subscriber( $subscribe_id, 'wcsn_mailsent' );
                                delete_post_meta( $post_id, 'No_of_subscribers' );
                            }
                        }
                    } elseif ( $stock_status == 'instock' ) {
                        $email = WC()->mailer()->emails['WC_Email_Stock_Notifier'];
                        foreach ( $product_subscriber as $subscribe_id => $to ) {
                            $email->trigger( $to, $post_id );
                            wcsn_update_product_subscriber( $subscribe_id, 'wcsn_mailsent' );
                            delete_post_meta( $post_id, 'No_of_subscribers' );
                        }
                    }
                }
            }
        }
    }
}