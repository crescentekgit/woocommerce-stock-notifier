jQuery( 'input.variation_id' ).change( function(){
    if( '' != jQuery(this).val() ) {
        var var_id = jQuery(this).val();
        var stock_notifier_box = {
            action: 'get_variation_box_ajax',
            product_id: wcsn_sc_data.product_id,
            variation_id : var_id
        };
        for (var i=0; i<wcsn_sc_data.additional_fields.length; i++){
            stock_notifier_box[wcsn_sc_data.additional_fields[i]] = jQuery(this).parent().find('.'+wcsn_sc_data.additional_fields[i]).val();
        }

        jQuery.post( wcsn_sc_data.ajax_url, stock_notifier_box, function(response) {
            jQuery('.stock_notifier-shortcode-subscribe-form').html(response); 
        });
    } else{
        jQuery('.stock_notifier-shortcode-subscribe-form').html('');
    }
});