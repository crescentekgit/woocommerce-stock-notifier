"use strict";
jQuery(function ($) {
    var instock_notifier = {
        init: function () {
            $(document).on('click', '.subscribe_button', this.subscribe_form);
            $(document).on('click', '.unsubscribe_button', this.unsubscribe_form);
            $(".single_variation_wrap").on("show_variation", this.perform_upon_show_variation);
        },
        perform_upon_show_variation: function (event, variation) {
            var vid = variation.variation_id;
            $('.stock_notifier-subscribe-form').hide(); //remove existing form
            $('.stock_notifier-subscribe-form-' + vid).show(); //add subscribe form to show
        },
        is_email: function (email) {
            var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (!regex.test(email)) {
                return false;
            } else {
                return true;
            }
        },

        unsubscribe_form: function (e) {
            e.preventDefault();
            $(this).text(wcsn_data.processing);   
            $(this).addClass("stk_disabled");   
            var form = $(this).closest('.stock_notifier-subscribe-form');
            var customer_data = {
                action: 'unsubscribe_button',
                customer_email: form.find('.subscribed_email').val(),
                product_id: form.find('.product_id').val(),
                var_id : form.find('.variation_id').val(),
            };
            
            var unsubscribe_successful_messsage = wcsn_data.subscription_unsubscribe;
            unsubscribe_successful_messsage = unsubscribe_successful_messsage.replace( '%customer_email%', customer_data.customer_email );
            
            $.post(wcsn_data.ajax_url, customer_data, function(response) {
                $(this).removeClass("stk_disabled");    
                if(response == true) {
                    $('.stock_notifier-subscribe-form').html('<div class="registered_message">' + unsubscribe_successful_messsage + '</div>');
                } else {
                    $('.stock_notifier-subscribe-form').html('<div class="registered_message">' + wcsn_data.error_occurs + '<a href="' + window.location + '"> '+ wcsn_data.try_again + '</a></div>');
                }
            });
        },
        subscribe_form: function (e) {
            e.preventDefault();
            $(this).text(wcsn_data.processing);
            $(this).addClass("stk_disabled");
            var recaptcha_enabled = wcsn_data.recaptcha_enabled;
            var form = $(this).closest('.stock_notifier-subscribe-form');

            if (recaptcha_enabled) {
                var recaptcha_secret = form.find('#recaptchav3_secretkey').val();
                var recaptcha_response = form.find('#recaptchav3_response').val();
                var recaptcha = {
                    action: 'recaptcha_validate_ajax',
                    captcha_secret : recaptcha_secret,
                    captcha_response : recaptcha_response
                }

                $.post(wcsn_data.ajax_url, recaptcha, function(response) {
                    if (response == 1) {
                        instock_notifier.process_form(form.find('.stock_notifier_email').val(), form.find('.current_product_id').val(), form.find('.current_variation_id').val(), form.find('.current_product_name').val());
                    } else {
                        alert('Oops, recaptcha not varified!');
                        $(this).removeClass("stk_disabled");
                    }
                });
            } else {
                instock_notifier.process_form(form.find('.stock_notifier_email').val(), form.find('.current_product_id').val(), form.find('.current_variation_id').val(), form.find('.current_product_name').val());
            }
        },

        process_form: function(cus_email, product_id, var_id, pro_title) {
            var description_text_html                   = wcsn_data.description_text_html;
            var subscription_button_html                = wcsn_data.subscription_button_html;
            var subscription_success                    = wcsn_data.subscription_success;
            var subscription_email_exist                = wcsn_data.subscription_email_exist;
            var subscription_invalid_email              = wcsn_data.subscription_invalid_email;
            var ban_email_domin                         = wcsn_data.ban_email_domain_text;
            var ban_email_address                       = wcsn_data.ban_email_address_text;
            var double_opt_in_text                      = wcsn_data.double_opt_in_success;
            var unsubscribe_button_html                 = wcsn_data.unsubscribe_button_html;
            var subscribe_form_field                    = wcsn_data.subscribe_form_field;
            
            var subscription_success = subscription_success.replace( '%product_title%', pro_title );
            var subscription_success = subscription_success.replace( '%customer_email%', cus_email );
            
            var subscription_email_exist = subscription_email_exist.replace( '%product_title%', pro_title );
            var subscription_email_exist = subscription_email_exist.replace( '%customer_email%', cus_email );

            if( cus_email && instock_notifier.is_email(cus_email) ) {
                $(this).toggleClass('alert_loader').blur(); 
                var wc_stock_notifier = {
                    action: 'subscribe_ajax',
                    email: cus_email,
                    product_id: product_id,
                    variation_id : var_id
                }

                for ( var i = 0; i < wcsn_data.additional_fields.length; i++ ){
                    wc_stock_notifier[ wcsn_data.additional_fields[ i ] ] = $( this ).parent().find( '.'+wcsn_data.additional_fields[i] ).val();
                }

                $.post( wcsn_data.ajax_url, wc_stock_notifier, function( response ) {   
                    
                    if ( response == '0' ) {
                        $( '.stock_notifier-subscribe-form' ).html( '<div class="registered_message">'+wcsn_data.error_occurs+'<a href="'+window.location+'"> '+wcsn_data.try_again+'</a></div>');
                    } else if ( response == '/*?%already_registered%?*/' ) {
                        $( '.stock_notifier-subscribe-form' ).html('<div class="registered_message">'+subscription_email_exist+'</div>'+unsubscribe_button_html+'<input type="hidden" class="subscribed_email" value="'+cus_email+'" /><input type="hidden" class="product_id" value="'+product_id+'" /><input type="hidden" class="variation_id" value="'+var_id+'" />');
                    } else if ( response == '/*?%ban_email_address%?*/' ) {
                        $( '.stock_notifier-subscribe-form' ).html(description_text_html+'<div class="wcsn_fields_wrap">'+subscribe_form_field+''+subscription_button_html+'</div><p class="wc_stock_notifier_error_message ban_email_address">'+ban_email_address+'</p><input type="hidden" class="current_product_id" value="'+product_id+'" /> <input type="hidden" class="current_variation_id" value="'+var_id+'" /><input type="hidden" class="current_product_name" value="'+pro_title+'" />' );
                    } else if ( response == '/*?%ban_email_domain%?*/' ) {
                        $( '.stock_notifier-subscribe-form' ).html(description_text_html+'<div class="wcsn_fields_wrap">'+subscribe_form_field+''+subscription_button_html+'</div><p class="wc_stock_notifier_error_message ban_email_domin">'+ban_email_domin+'</p><input type="hidden" class="current_product_id" value="'+product_id+'" /> <input type="hidden" class="current_variation_id" value="'+var_id+'" /><input type="hidden" class="current_product_name" value="'+pro_title+'" />' );
                    } else if ( response == '/*?%double_opt_in%?*/' ) {
                        $( '.stock_notifier-subscribe-form' ).html( '<div class="registered_message">'+double_opt_in_text+'</div>' );
                    } else {
                        $( '.stock_notifier-subscribe-form' ).html( '<div class="registered_message">'+subscription_success+'</div>' );
                    }
                });
            } else {
                $( '.stock_notifier-subscribe-form' ).html( description_text_html+'<div class="wcsn_fields_wrap">'+subscribe_form_field+''+subscription_button_html+'</div><p style="color:#e2401c;" class="wc_stock_notifier_error_message">'+subscription_invalid_email+'</p><input type="hidden" class="current_product_id" value="'+product_id+'" /> <input type="hidden" class="current_variation_id" value="'+var_id+'" /><input type="hidden" class="current_product_name" value="'+pro_title+'" />' );
            }
            $( this ).removeClass( "stk_disabled" );
        }
    };
    instock_notifier.init();
});