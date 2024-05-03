<?php
if ( ! function_exists( 'wcsn_stock_notifier_admin_tabs' ) ) {
    function wcsn_stock_notifier_admin_tabs() {
        $default_massages = get_wc_stock_notifier_default_massages();
        $wcsn_settings_page_endpoint = apply_filters( 'wc_stock_notifier_endpoint_fields_before_value', array(
            'general' => array(
                'tablabel'        => __( 'General', 'wc-stock-notifier' ),
                'apiurl'          => 'save_admin_settings',
                'description'     => __( 'Configure Basic Stock Notifier settings. ', 'wc-stock-notifier' ),
                'icon'            => 'dashicons dashicons-admin-generic',
                'submenu'         => 'settings',
                'modulename'      => [
                	[
                        'key'       => 'is_backorders_enable',
                        'label'     => __("Allow Subscriptions with Backorders", 'wc-stock-notifier'),
                        'class'     => 'wcsn-toggle-checkbox',
                        'type'      => 'checkbox',
                        'options'   => array(
                            array(
                                'key'   => "is_backorders_enable",
                                'label' => __( 'Enabling this setting allow users to subscribe to out-of-stock products with backorders enabled.', 'wc-stock-notifier' ),
                                'value' => "is_backorders_enable"
                            ),
                        ),
                        'database_value' => array(),
                    ],
                    [
                        'key'      => 'is_subscriber_count_enable',
                        'label'    => __( "Display Subscriber Count for Out of Stock Product", 'wc-stock-notifier' ),
                        'class'    => 'wcsn-toggle-checkbox',
                        'type'     => 'checkbox',
                        'options'  => array(
                            array(
                                'key'   => "is_subscriber_count_enable",
                                'label' => __('Enabling this setting shows the subscriber count on the single product page.', 'wc-stock-notifier'),
                                'value' => "is_subscriber_count_enable"
                            ),
                        ),
                        'database_value' => array(),
                    ],
                    [
                        'key'               => 'shown_subscriber_count_text',
                        'type'              => 'textarea',
                        'class'             => 'wcsn-setting-wpeditor-class',
                        'depend_checkbox'   => 'is_subscriber_count_enable',
                        'label'             => __( 'Subscriber Count Message', 'wc-stock-notifier' ),
                        'placeholder'       => $default_massages['shown_subscriber_count_text'],
                        'desc'              => __( 'Customize the notification to inform users about the number of subscribers for the out-of-stock item. Note: Use %no_of_subscribed% as number of subscribed persons.', 'wc-stock-notifier' ),
                        'database_value'    => '',
                    ],
                    [
						'key'     => 'is_admin_email_remove',
						'label'   => __( "Remove Admin Email", 'wc-stock-notifier' ),
						'class'   => 'wcsn-toggle-checkbox',
						'type'    => 'checkbox',
						'options' => array(
                            array(
                                'key'   => "is_admin_email_remove",
                                'label' => __( 'Exclude admin email from the list of recipients for stock notifications.', 'wc-stock-notifier' ),
                                'value' => "is_admin_email_remove"
                            ),
						),
						'database_value' => array(),
					],
                    [
                        'key'               => 'additional_emails',
                        'type'              => 'textarea',
                        'class'             => 'wcsn-setting-wpeditor-class',
                        'desc'              => __( 'Set the email address to receive notifications when a user subscribes to an out-of-stock product. Add multiple emails separated by comma.', 'wc-stock-notifier' ),
                        'label'             => __( 'Additional Email Address for New Subscriber Notification.', 'wc-stock-notifier' ),
                        'database_value'    => '',
                    ],
                ]
            ),
            'form_personalize' => array(
                'tablabel'        => __( 'Personalize Form', 'wc-stock-notifier' ),
                'apiurl'          => 'save_admin_settings',
                'description'     => __( 'Customize Subscription Form.', 'wc-stock-notifier' ),
                'icon'            => 'dashicons dashicons-admin-customizer',
                'submenu'         => 'settings',
                'modulename'      =>  [
                    [
                        'key'       => 'demo_form',
                        'type'      => 'example_form',
                        'class'     => 'wcsn-setting-own-class',
                        'label'     => __( 'Demo Form', 'wc-stock-notifier' )
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'heading',
                        'label'     => __( 'no_label', 'wc-stock-notifier' ),
                        'blocktext' => __( 'Notifier Form Text Customizer', 'wc-stock-notifier' ),
                    ],
                    [
                        'key'               => 'email_placeholder_text',
                        'type'              => 'text',
                        'label'             => __( 'Email Field Placeholder', 'wc-stock-notifier' ),
                        'desc'              => __( 'It will represent email field placeholder text.', 'wc-stock-notifier' ),
                        'placeholder'       => $default_massages['email_placeholder_text'],
                        'database_value'    => '',
                    ],
                    [
                        'key'               => 'form_description_text',
                        'type'              => 'textarea',
                        'label'             => __( 'Form Description Text', 'wc-stock-notifier' ),
                        'desc'              => __( 'Informative message guiding users.', 'wc-stock-notifier' ),
                        'placeholder'       => $default_massages['form_description_text'],
                        'database_value'    => '',
                    ],
                    [
                        'key'               => 'subscribe_button_text',
                        'type'              => 'text',
                        'label'             => __( 'Subscribe Button Text', 'wc-stock-notifier' ),
                        'desc'              => __( 'Modify the subscribe button text. By default - Subscribe.', 'wc-stock-notifier' ),
                        'placeholder'       => $default_massages['subscribe_button_text'],
                        'database_value'    => '',
                    ],
                    [
                        'key'               => 'unsubscribe_button_text',
                        'type'              => 'text',
                        'label'             => __( 'Unsubscribe Button Text', 'wc-stock-notifier' ),
                        'desc'              => __( 'Modify the un-subscribe button text. By default - Unsubscribe.', 'wc-stock-notifier' ),
                        'placeholder'       => $default_massages['unsubscribe_button_text'],
                        'database_value'    => '',
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'heading',
                        'label'     => __( 'no_label', 'wc-stock-notifier' ),
                        'blocktext' => __( 'Notifier Form Customizer', 'wc-stock-notifier' ),
                    ],
                    [
                        'key'       => 'button_color_section',
                        'type'      => 'customize_table',
                        'label'     => __( 'Customization Settings', 'wc-stock-notifier' ),
                        'database_value' => '',
                    ],
                ]
            ),
            'form_submit' => array(
                'tablabel'        => __( 'Submit Messages', 'wc-stock-notifier' ),
                'apiurl'          => 'save_admin_settings',
                'description'     => __( 'Personalize the Confirmation Message Upon Form Submission.', 'wc-stock-notifier' ),
                'icon'            => 'dashicons dashicons-format-status',
                'submenu'         => 'settings',
                'modulename'      => [
                    [
                        'key'               => 'subscription_success',
                        'type'              => 'textarea',
                        'label'             => __( 'Successful Form Submission Massage', 'wc-stock-notifier' ),
                        'class'             => 'wcsn-setting-wpeditor-class',
                        'desc'              => __( 'Incorporate %product_title% for dynamic product titles and %customer_email% for personalized customer email addresses in your messages.', 'wc-stock-notifier' ),
                        'placeholder'       => $default_massages['subscription_success'],
                        'database_value'    => '',
                    ],
                    [
                        'key'               => 'subscription_unsubscribe',
                        'type'              => 'textarea',
                        'label'             => __( 'Unsubscribe Confirmation Massage', 'wc-stock-notifier' ),
                        'class'             => 'wcsn-setting-wpeditor-class',
                        'desc'              => __( 'Modify the text that confirms user that they have successful unsubscribe.', 'wc-stock-notifier' ),
                        'placeholder'       => $default_massages['subscription_unsubscribe'],
                        'database_value'    => '',
                    ],
                    [
                        'key'               => 'subscription_email_exist',
                        'type'              => 'textarea',
                        'label'             => __( 'Repeated Subscription Massage', 'wc-stock-notifier' ),
                        'class'             => 'wcsn-setting-wpeditor-class',
                        'desc'              => __( 'Boost personalization by using %product_title% for dynamic product titles and %customer_email% for individual customer emails.', 'wc-stock-notifier' ),
                        'placeholder'       => $default_massages['subscription_email_exist'],
                        'database_value'    => '',
                    ],
                    [
                        'key'               => 'subscription_invalid_email',
                        'type'              => 'textarea',
                        'label'             => __( 'Email Validation Error Massage', 'wc-stock-notifier' ),
                        'class'             => 'wcsn-setting-wpeditor-class',
                        'desc'              => __( 'Customize the message displayed to users attempting to subscribe with an invalid email address.', 'wc-stock-notifier'),
                        'placeholder'       => $default_massages['subscription_invalid_email'],
                        'database_value'    => '',
                    ],
                    
                ]
            )
        ));

        if ( ! empty( $wcsn_settings_page_endpoint ) ) {
            foreach ( $wcsn_settings_page_endpoint as $settings_key => $settings_value ) {
                if ( isset( $settings_value['modulename'] ) && !empty( $settings_value['modulename'] ) ) {
                    foreach ( $settings_value['modulename'] as $inter_key => $inter_value ) {
                        $change_settings_key = str_replace( "-", "_", $settings_key );
                        $option_name = 'wcsn_'.$change_settings_key.'_tab_settings';
                        $database_value = get_option($option_name) ? get_option($option_name) : array();
                        if ( ! empty( $database_value ) ) {
                            if ( isset( $inter_value['key'] ) && array_key_exists( $inter_value['key'], $database_value ) ) {
                                if ( empty( $inter_value['database_value'] ) ) {
                                   $wcsn_settings_page_endpoint[$settings_key]['modulename'][$inter_key]['database_value'] = $database_value[$inter_value['key']];
                                }
                            }
                        }
                    }
                }
            }
        }

        $wc_stock_notifier_backend_tab_list = apply_filters( 'wc_stock_notifier_admin_tab_list', array(
            'stock_notifier-settings' => $wcsn_settings_page_endpoint,
        ) );
        
        return $wc_stock_notifier_backend_tab_list;
    }
}

if ( ! function_exists( 'get_wc_stock_notifier_default_massages' ) ) {
    function get_wc_stock_notifier_default_massages() {
        $default_massages = array(
            'email_placeholder_text'            => __( 'Enter your email', 'wc-stock-notifier' ),
            'form_description_text'             => __( 'Get notified when this product is back in stock.', 'wc-stock-notifier' ),
            'subscribe_button_text'             => __( 'Subscribe', 'wc-stock-notifier' ),
            'unsubscribe_button_text'           => __( 'Unsubscribe', 'wc-stock-notifier' ),
            'subscription_success'              => __( 'Thanks for your interest in %product_title%. We will notify you by email when it is back in stock.', 'wc-stock-notifier' ),
            'subscription_email_exist'          => __( '%customer_email% is already registered for %product_title%. Please try using a different email address.', 'wc-stock-notifier' ),
            'subscription_invalid_email'        => __( 'Kindly provide a valid email address and try again.', 'wc-stock-notifier' ),
            'subscription_unsubscribe'          => __( '%customer_email% is successfully unsubscribe.', 'wc-stock-notifier' ),
            'shown_subscriber_count_text'       => __( '%no_of_subscribed% individuals have already subscribed.', 'wc-stock-notifier' ),
            'ban_email_domain_text'             => __( 'This email domain is ban in our site, kindly use another email domain.', 'wc-stock-notifier' ),
            'ban_email_address_text'            => __( 'This email address is ban in our site, kindly use another email address.', 'wc-stock-notifier' ),
            'double_opt_in_success'             => __( 'Kindly check your inbox to confirm the subscription.', 'wc-stock-notifier' ),
        );

        return $default_massages;
    }
}