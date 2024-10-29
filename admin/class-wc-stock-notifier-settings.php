<?php

class WC_Stock_Notifier_Settings {
  
   /**
   * Start up
   */
   public function __construct() {
      // Admin menu
      add_action( 'admin_menu', [ $this, 'add_settings_page' ], 100 );
   }

   /**
   * Add options page
   */
   public function add_settings_page() {

      add_menu_page(
         __( 'Stock Notifier', 'wc-stock-notifier' ),
         __( 'Stock Notifier', 'wc-stock-notifier' ),
         'manage_options',
         'wcsn-stock-notifier-setting',
         [ $this, 'create_wc_stock_notifier_settings' ],
         'dashicons-bell', 
         50
      );

      add_submenu_page(
         'wcsn-stock-notifier-setting',                                 // parent slug
         __('Settings', 'wc-stock-notifier'),                           // page title
         __('Settings', 'wc-stock-notifier'),                           // menu title
         'manage_options',                                              // capability
         'wcsn-stock-notifier-setting#&tab=settings&subtab=general',    // callback
         '__return_null'                                                // position
      );

      add_submenu_page( 
         'wcsn-stock-notifier-setting',
         __( 'Subscriber List', 'wc-stock-notifier'), 
         __( 'Subscriber List ', 'wc-stock-notifier' ), 
         'manage_options', 
         'wcsn-stock-notifier-setting#&tab=subscriber-list', 
         '__return_null'
      );

      remove_submenu_page( 'wcsn-stock-notifier-setting', 'wcsn-stock-notifier-setting' );
   }

   /**
   * Options page callback
   */
   public function create_wc_stock_notifier_settings() {
      echo '<div id="wcsn-admin-stocknotifier"></div>';
   }
}