<?php

if ( ! function_exists( 'woocommerce_inactive_notice' ) ) {
    function woocommerce_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sProduct Stock Manager & Notifier for WooCommerce is inactive.%s The %sWooCommerce plugin%s must be active for the Product Stock Manager & Notifier for WooCommerce to work. Please %sinstall & activate WooCommerce%s', 'wc-stock-notifier'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugins.php') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( 'get_wc_stock_notifier_form_settings_array' ) ) {
    function get_wc_stock_notifier_form_settings_array() {
        $default_massages = get_wc_stock_notifier_default_massages();
        
        $settings = array(
            'email_placeholder_text'                      => get_wc_stock_notifier_plugin_settings( 'email_placeholder_text', $default_massages['email_placeholder_text'] ),
            'form_description_text'                       => get_wc_stock_notifier_plugin_settings( 'form_description_text', $default_massages['form_description_text'] ),
            'form_description_text_color'                 => get_wc_stock_notifier_plugin_settings( 'form_description_text_color', '' ),
            'subscribe_button_text'                       => get_wc_stock_notifier_plugin_settings( 'subscribe_button_text', $default_massages['subscribe_button_text'] ),
            'unsubscribe_button_text'                     => get_wc_stock_notifier_plugin_settings( 'unsubscribe_button_text', $default_massages['unsubscribe_button_text'] ),
            'subscribe_button_background_color'           => get_wc_stock_notifier_plugin_settings( 'subscribe_button_background_color', '' ),
            'subscribe_button_border_color'               => get_wc_stock_notifier_plugin_settings( 'subscribe_button_border_color', '' ),
            'subscribe_button_text_color'                 => get_wc_stock_notifier_plugin_settings( 'subscribe_button_text_color', '' ),
            'subscribe_button_background_color_onhover'   => get_wc_stock_notifier_plugin_settings( 'subscribe_button_background_color_onhover', '' ),
            'subscribe_button_text_color_onhover'         => get_wc_stock_notifier_plugin_settings( 'subscribe_button_text_color_onhover', '' ),
            'subscribe_button_border_color_onhover'       => get_wc_stock_notifier_plugin_settings( 'subscribe_button_border_color_onhover', '' ),
            'subscription_success'                        => get_wc_stock_notifier_plugin_settings( 'subscription_success', $default_massages['subscription_success'] ),
            'subscription_email_exist'                    => get_wc_stock_notifier_plugin_settings( 'subscription_email_exist', $default_massages['subscription_email_exist'] ),
            'subscription_invalid_email'                  => get_wc_stock_notifier_plugin_settings( 'subscription_invalid_email', $default_massages['subscription_invalid_email'] ),
            'ban_email_domain_text'                       => get_wc_stock_notifier_plugin_settings( 'ban_email_domain_text', $default_massages['ban_email_domain_text'] ),
            'ban_email_address_text'                      => get_wc_stock_notifier_plugin_settings( 'ban_email_address_text', $default_massages['ban_email_domain_text']) ,
            'double_opt_in_success'                       => get_wc_stock_notifier_plugin_settings( 'double_opt_in_success', $default_massages['double_opt_in_success'] ),
            'subscription_unsubscribe'                    => get_wc_stock_notifier_plugin_settings( 'subscription_unsubscribe', $default_massages['subscription_unsubscribe'] ),
            'shown_subscriber_count_text'                 => get_wc_stock_notifier_plugin_settings( 'shown_subscriber_count_text', $default_massages['shown_subscriber_count_text'] ),
            'subscribe_button_font_size'                  => get_wc_stock_notifier_plugin_settings( 'subscribe_button_font_size', '' ),
            'subscribe_button_border_size'                => get_wc_stock_notifier_plugin_settings( 'subscribe_button_border_size', '' ),
            'subscribe_button_border_redious'             => get_wc_stock_notifier_plugin_settings( 'subscribe_button_border_radious', '' ),
        );
        return $settings;
    }
}

if ( ! function_exists( 'get_wc_stock_notifier_plugin_settings' ) ) {
    function get_wc_stock_notifier_plugin_settings( $key = '', $default = false ) {
        $wcsn_plugin_settings = array();
        $all_options = apply_filters( 'wcsn_stock_notifier_all_admin_options', array(
            'wcsn_form_personalize_tab_settings',
            'wcsn_form_submit_tab_settings',
            'wcsn_general_tab_settings',
            )
        );
        foreach ( $all_options as $option_name ) {
            if ( is_array( get_option( $option_name, array() ) ) ) {
                $wcsn_plugin_settings = array_merge( $wcsn_plugin_settings, get_option( $option_name, array() ) );
            }
        }
        if ( empty( $key ) ) {
            return $default;
        }
        if ( ! isset( $wcsn_plugin_settings[$key] ) || empty( $wcsn_plugin_settings[$key] ) ) {
            return $default;
        }
        return $wcsn_plugin_settings[$key];
    }
}

if ( ! function_exists( 'wcsn_update_product_subscriber' ) ) {
    function wcsn_update_product_subscriber( $subscrption_id, $status ) {
        $args = array(
            'ID'            => $subscrption_id,
            'post_type'     => 'WcStockNotifier',
            'post_status'   => $status,
        );
        $id = wp_update_post( $args );
        return $id;
    }
}

if ( ! function_exists( 'wcsn_update_product_subscriber_count' ) ) {
    function wcsn_update_product_subscriber_count( $product_id ) {
        $get_count = wcsn_get_no_subscribed_persons( $product_id, 'wcsn_subscribed' );
        update_post_meta( $product_id, 'No_of_subscribers', $get_count );
    }
}

if ( ! function_exists( 'wcsn_insert_product_subscriber' ) ) {
    function wcsn_insert_product_subscriber( $subscriber_email, $product_id ) {
        $args = array(
            'post_title'    => $subscriber_email,
            'post_type'     => 'WcStockNotifier',
            'post_status'   => 'wcsn_subscribed',
        );

        $id = wp_insert_post($args);
        if ( ! is_wp_error( $id ) ) {
            $default_data = array(
                'wcsninstock_product_id' => $product_id,
                'wcsninstock_subscriber_email' => $subscriber_email,
            );
            foreach ( $default_data as $key => $value ) {
                update_post_meta( $id, $key, $value );
            }
            wcsn_update_product_subscriber_count( $product_id );
            return $id;
        } else {
            return false;
        }
    }
}

if ( ! function_exists( 'wcsn_insert_product_subscriber_email_trigger' ) ) {
    function wcsn_insert_product_subscriber_email_trigger( $product_id, $customer_email ) {
        $admin_mail = WC()->mailer()->emails['WC_Admin_Email_Stock_Notifier'];
        $cust_mail = WC()->mailer()->emails['WC_Subscriber_Confirmation_Email_Stock_Notifier'];

        $admin_email = '';
        if ( get_wc_stock_notifier_plugin_settings( 'is_admin_email_remove' ) ) {
            $admin_email = '';
        } else {
            $admin_email = get_option( 'admin_email' );
        }

        if ( get_wc_stock_notifier_plugin_settings( 'additional_emails' ) ) {
            $admin_email .= ','.get_wc_stock_notifier_plugin_settings( 'additional_emails' );
        }


        //admin email or vendor email
        if ( ! empty( $admin_email ) )
        $admin_mail->trigger( $admin_email, $product_id, $customer_email );

        //customer email
        $cust_mail->trigger( $customer_email, $product_id );
    }
}

if ( ! function_exists( 'wcsn_is_already_subscribed' ) ) {
    function wcsn_is_already_subscribed( $subscriber_email, $product_id ) {
        $args = array(
            'post_type'         => 'WcStockNotifier',
            'fields'            => 'ids',
            'posts_per_page'    => 1,
            'post_status'       => 'wcsn_subscribed',
        );
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'   => 'wcsninstock_product_id',
                'value' => $product_id,
            ),
            array(
                'key'   => 'wcsninstock_subscriber_email',
                'value' => $subscriber_email,
            ),
        );
        $args['meta_query'] = $meta_query;
        $get_posts = get_posts( $args );
        return $get_posts;
    }
}

if ( ! function_exists( 'wcsn_get_no_subscribed_persons' ) ) {
    function wcsn_get_no_subscribed_persons( $product_id, $status = 'any' ) {
        $args = array(
            'post_type'     => 'WcStockNotifier',
            'post_status'   => $status,
            'meta_query'    => array(
                array(
                    'key'       => 'wcsninstock_product_id',
                    'value'     => array($product_id),
                    'compare'   => 'IN',
                )),
            'numberposts' => -1,
        );
        $query = get_posts( $args );
        return count( $query ); 
    }
}

if ( ! function_exists( 'wcsn_get_product_subscribers_email' ) ) {
    function wcsn_get_product_subscribers_email( $product_id ) {
        $emails = array();
        $args = array(
            'post_type'     => 'WcStockNotifier',
            'fields'        => 'ids',
            'posts_per_page'=> -1,
            'post_status'   => 'wcsn_subscribed',
            'meta_query'    => array(
                array(
                    'key'     => 'wcsninstock_product_id',
                    'value'   => ( $product_id > '0' || $product_id ) ? $product_id : 'no_data_found',
                    'compare' => '='
                )
            )
        );
        $subsciber_post = get_posts( $args );
        if ( $subsciber_post && count( $subsciber_post ) > 0 ) {
            foreach ( $subsciber_post as $subsciber_id ) {
                $email = get_post_meta( $subsciber_id, 'wcsninstock_subscriber_email', true );
                $emails[ $subsciber_id ] = $email ? $email : '';
            }
        }
        return $emails;
    }
}

if ( ! function_exists('wcsn_subscriber_insert' ) ) {

    function wcsn_subscriber_insert( $product_id, $customer_email ) {
        if ( empty( $product_id ) && empty( $customer_email ) ) return;
        $do_complete_additional_task = apply_filters( 'wc_stock_notifier_do_complete_additional_task', false );
        $is_accept_email_address = apply_filters( 'wc_stock_notifier_is_accept_email_address', false );
        
        if ( wcsn_is_already_subscribed( $customer_email, $product_id ) ) {
            return $status = '/*?%already_registered%?*/';
        } else if ( $do_complete_additional_task ) {
            return $status = apply_filters( 'wc_stock_notifier_new_subscriber_added', $status, $customer_email, $product_id );
        } else if ( $is_accept_email_address ) {
            $email_ban_list = get_wc_stock_notifier_plugin_settings( 'ban_email_domains', '' );
            $mail_address_ban_list = get_wc_stock_notifier_plugin_settings( 'ban_email_addresses', '' );
            if ( ! empty( $email_ban_list ) && is_ban_email_domain( $customer_email ) ) {
                return '/*?%ban_email_domain%?*/';
            } else if ( ! empty( $mail_address_ban_list ) && is_ban_email_address( $customer_email ) ) {
                return '/*?%ban_email_address%?*/';
            } else {
                wcsn_insert_product_subscriber( $customer_email, $product_id );
                wcsn_insert_product_subscriber_email_trigger( $product_id, $customer_email );
                return true;
            }
        } else {
            wcsn_insert_product_subscriber( $customer_email, $product_id );
            wcsn_insert_product_subscriber_email_trigger( $product_id, $customer_email );
            return true;
        }
    }
}

if ( ! function_exists( 'is_ban_email_address' ) ) {
    function is_ban_email_domain( $customer_email ) {
        $email_ban_list = get_wc_stock_notifier_plugin_settings( 'ban_email_domains', '' );
        if ( ! empty( $email_ban_list ) ) {
            $black_list = explode( ',', $email_ban_list );
            $emailParts = explode( '@', $customer_email );
            if ( in_array( end( $emailParts ), $black_list ) ) {
                return true;
            }
        }
        return false;
    }
}

if ( ! function_exists( 'is_ban_email_address' ) ) {
    function is_ban_email_address( $customer_email ) {
        $mail_address_ban_list = get_wc_stock_notifier_plugin_settings( 'ban_email_addresses', '' );
        if ( ! empty( $mail_address_ban_list ) ) {
            $blacklistAddress = explode( ',', $mail_address_ban_list );
            if ( in_array( $customer_email, $blacklistAddress ) ) {
                return true;
            }
        }
        return false;
    }
}

if ( ! function_exists( 'wcsn_subscriber_unsubscribe' ) ) {
    function wcsn_subscriber_unsubscribe( $product_id, $customer_email ) {
        $unsubscribe_post = wcsn_is_already_subscribed( $customer_email, $product_id );
        if ( $unsubscribe_post ) {
            foreach ( $unsubscribe_post as $post ) {
                wcsn_update_product_subscriber( $post, 'wcsn_unsubscribed' );
            }
            wcsn_update_product_subscriber_count( $product_id );
            return true;
        }
        return false;
    }
}

if ( ! function_exists( 'wcsn_is_product_outofstock') ) {
    function wcsn_is_product_outofstock( $product_id, $type = '' ) {
        $is_outof_stock = false;
        if ( ! $product_id ) return $is_outof_stock;
        
        if ( $type == 'variation' ) {
            $child_obj = new WC_Product_Variation( $product_id );
            $manage_stock = $child_obj->managing_stock();
            $stock_quantity = intval($child_obj->get_stock_quantity());
            $stock_status = $child_obj->get_stock_status();
        } else {
            $product = wc_get_product($product_id);
            $manage_stock = $product->get_manage_stock();
            $stock_quantity = $product->get_stock_quantity();
            $stock_status = $product->get_stock_status();
        }

        $is_backorders_enable = get_wc_stock_notifier_plugin_settings( 'is_backorders_enable' );
        if ( $manage_stock ) {
            if ($stock_quantity <= (int) get_option( 'woocommerce_notify_no_stock_amount' ) ) {
                $is_outof_stock = true;
            } elseif ( $stock_quantity <= 0 ) {
                $is_outof_stock = true;
            }
        } else {
            if ( $stock_status == 'onbackorder' && $is_backorders_enable ) {
                $is_outof_stock = true;
            } elseif ( $stock_status == 'outofstock' ) {
                $is_outof_stock = true;
            }
        }
        return $is_outof_stock;
    }
}

if ( ! function_exists( 'wcsn_is_activate_double_opt_in' ) ) {
    function wcsn_is_activate_double_opt_in() {
        $wcsn_plugin_settings = array();
        $wcsn_plugin_settings = get_option( 'wcsn_general_tab_settings', array() );
        if ( ! isset( $wcsn_plugin_settings['is_double_optin'] ) || empty( $wcsn_plugin_settings['is_double_optin'] ) ) {
            return false;
        }
        return $wcsn_plugin_settings['is_double_optin'];
    }
}

if( ! function_exists( 'wcsn_stock_product_data' ) ) {
    function wcsn_stock_product_data( $product_id ) {
        $product_data = array();
        $parent_product_id = wp_get_post_parent_id( $product_id );
        if( $parent_product_id ) {
            $product_obj = wc_get_product( $parent_product_id );
            $parent_id = $parent_product_id ? $parent_product_id : 0;
            $product_data['link'] = $product_obj->get_permalink();
            $product_data['name'] = $product_obj && $product_obj->get_formatted_name() ? $product_obj->get_formatted_name() : '';
            $product_data['price'] = $product_obj && $product_obj->get_price_html() ? $product_obj->get_price_html() : '';
        } else {
            $product_obj = wc_get_product( $product_id );
            $product_data['link'] = $product_obj->get_permalink();
            $product_data['name'] = $product_obj && $product_obj->get_formatted_name() ? $product_obj->get_formatted_name() : '';
            $product_data['price'] = $product_obj && $product_obj->get_price_html() ? $product_obj->get_price_html() : '';
        }
        return apply_filters( 'wc_stock_notifier_product_data', $product_data, $product_id );
    }
}

if ( ! function_exists( 'wcsn_form_fileds' ) ) {
    function wcsn_form_fileds() {
        $notifier_fields_array = array();
        $notifier_field = $user_email = '';
        $separator = apply_filters( 'wc_fileds_separator', '<br>' );
        $settings_array = get_wc_stock_notifier_form_settings_array();
        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            $user_email = $current_user->data->user_email;
        }
        $placeholder = $settings_array['email_placeholder_text'];
        $notifiers_field_list = apply_filters('wcsn_form_fileds_array', array(
            'email_address'       => array(
                'type'          => 'text',
                'class'         => 'stock_notifier_email wcsn-fields',
                'value'         => $user_email,
                'placeholder'   => $placeholder
            )
        ), $settings_array );
        if ( $notifiers_field_list ) {
            foreach ( $notifiers_field_list as $key => $fvalue ) {
                $type = in_array( $fvalue['type'], ['recaptcha-v3', 'text', 'number', 'email']) ? esc_attr($fvalue['type']) : 'text';
                $class = isset( $fvalue['class'] ) ? esc_attr($fvalue['class'] ) : 'stock_notifier_' . $key;
                $value = isset( $fvalue['value'] ) ? esc_attr($fvalue['value'] ) : '';
                $placeholder = isset( $fvalue['placeholder'] ) ? esc_attr( $fvalue['placeholder'] ) : '';
                switch ( $fvalue['type'] ) {
                    case 'recaptcha-v3':
                        $recaptcha_type = isset( $fvalue['version'] ) ? esc_attr( $fvalue['version'] ) : 'v3';
                        $sitekey = isset( $fvalue['sitekey'] ) ? esc_attr( $fvalue['sitekey'] ) : '';
                        $secretkey = isset( $fvalue['secretkey'] ) ? esc_attr( $fvalue['secretkey'] ) : '';

                        $recaptchaScript = '
                        <script>
                            grecaptcha.ready(function () {
                                grecaptcha.execute("' . $sitekey . '").then(function (token) {
                                    var recaptchaResponse = document.getElementById("recaptchav3_response");
                                    recaptchaResponse.value = token;
                                });
                            });
                        </script>';
                        
                        $recaptchaResponseInput = '<input type="hidden" id="recaptchav3_response" name="recaptchav3_response" value="" />';
                        $recaptchaSiteKeyInput = '<input type="hidden" id="recaptchav3_sitekey" name="recaptchav3_sitekey" value="' . esc_html($sitekey) . '" />';
                        $recaptchaSecretKeyInput = '<input type="hidden" id="recaptchav3_secretkey" name="recaptchav3_secretkey" value="' . esc_html($secretkey) . '" />';

                        $notifier_fields_array[] = $recaptchaScript . $recaptchaResponseInput . $recaptchaSiteKeyInput . $recaptchaSecretKeyInput;
                        break;
                    default:
                        $notifier_fields_array[] = '<input type="' . $type . '" name="' . $key . '" class="' . $class . '" value="' . $value . '" placeholder="' . $placeholder . '" >';
                        break;
                }
            }
        }
        if ( $notifier_fields_array ) {
            $notifier_field = implode( $separator, $notifier_fields_array );
        }
        return $notifier_field;    
    }
}

if ( ! function_exists( 'wcsn_get_product_subscribers_array' ) ) {
    function wcsn_get_product_subscribers_array( $args = array() ) {
        $all_product_ids = $get_subscribed_user = array();
        $default_args = array(
            'post_type'     => 'product',
            'post_status'   => 'publish',
            'numberposts'   => -1
        );
        $args = wp_parse_args( $args, $default_args );
        $products = get_posts( $args );
        if ( $products ) {
            foreach ( $products as $product ) {
                $product_obj = wc_get_product( $product->ID );
                if ( $product_obj->is_type( 'variable' ) && $product_obj->has_child() ) {
                    $child_ids = $product_obj->get_children();
                    $all_product_ids = array_merge( $all_product_ids, $child_ids );
                } else {
                    $all_product_ids[] = $product->ID;
                }
            }

            if ( ! empty( $all_product_ids ) && is_array( $all_product_ids ) ) {
                foreach ($all_product_ids as $product_id) {
                    $subscribers = wcsn_get_product_subscribers_email( $product_id );
                    if ( $subscribers && !empty( $subscribers ) ) {
                        $get_subscribed_user[$product_id] = $subscribers; 
                    }
                }
            }
        }
        return $get_subscribed_user;
    }
}



if ( ! function_exists( 'wc_stock_notify_to_subscribed_user' ) ){
    function wc_stock_notify_to_subscribed_user() {
        global $WC;
        $get_subscribed_user = wcsn_get_product_subscribers_array();
        if ( ! empty( $get_subscribed_user ) && is_array( $get_subscribed_user ) ) {
            foreach ( $get_subscribed_user as $p_id => $subscriber ) {
                $product = wc_get_product($p_id);
                $product_availability_stock = $product->get_stock_quantity();
                $manage_stock = $product->get_manage_stock();
                $managing_stock = $product->managing_stock();
                $stock_status = $product->get_stock_status();
                if ( $managing_stock ) {
                    if ( $product->backorders_allowed() && get_wc_stock_notifier_plugin_settings( 'is_backorders_enable' ) ) {
                        $email = WC()->mailer()->emails['WC_Email_Stock_Notifier'];
                        foreach ( $subscriber as $post_id => $to ) {
                            $email->trigger( $to, $p_id );
                            wcsn_update_product_subscriber( $post_id, 'wcsn_mailsent' );
                            delete_post_meta( $p_id, 'No_of_subscribers' );
                        }        
                    } else {
                        if ( $product_availability_stock > (int) get_option( 'woocommerce_notify_no_stock_amount' ) ) {
                            $email = WC()->mailer()->emails['WC_Email_Stock_Notifier'];
                            foreach ( $subscriber as $post_id => $to ) {
                                $email->trigger( $to, $p_id );
                                wcsn_update_product_subscriber( $post_id, 'wcsn_mailsent' );
                                delete_post_meta( $p_id, 'No_of_subscribers' );
                            }
                        }
                    }
                } else {
                    if ( $stock_status == 'onbackorder' && get_wc_stock_notifier_plugin_settings( 'is_backorders_enable' ) ) {
                        if ( $stock_status != 'outofstock' || $product_availability_stock > (int) get_option( 'woocommerce_notify_no_stock_amount' ) ) {
                            $email = WC()->mailer()->emails['WC_Email_Stock_Notifier'];
                            foreach ($subscriber as $post_id => $to) {
                                $email->trigger($to, $p_id);
                                wcsn_update_product_subscriber( $post_id, 'wcsn_mailsent' );
                                delete_post_meta( $p_id, 'No_of_subscribers' );
                            }
                        }
                    } elseif ( $stock_status == 'instock' ) {
                        $email = WC()->mailer()->emails['WC_Email_Stock_Notifier'];
                        foreach ( $subscriber as $post_id => $to ) { 
                            $email->trigger($to, $p_id);
                            wcsn_update_product_subscriber( $post_id, 'wcsn_mailsent' );
                            delete_post_meta( $p_id, 'No_of_subscribers' );
                        }
                    }
                }
            }
        }
    }
}