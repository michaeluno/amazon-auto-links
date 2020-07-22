(function($){
    $( document ).ready( function() {

        // Clear unusable proxies.
        $( '.button_clear_unusable' ).click( function( event ) {
            event.preventDefault();
            var _oUnusableListTextArea = $( 'textarea.unusable-list' );
            if ( _oUnusableListTextArea.length ) {
                _oUnusableListTextArea.val( '' );
            }
            return false;
        } );

        // Load a proxy list.
        $( '.button_load_proxies' ).click( function( event ) {

            var _oSpinner   = $( '<img src="' + aalProxyLoader.spinnerURL + '" />' );
            _oSpinner.addClass( 'ajax-spinner' );
            $( this ).parent().append( _oSpinner );

            event.preventDefault();
            jQuery.ajax( {
                type: 'post',
                dataType: 'json',
                url: aalProxyLoader.ajaxURL,
                // Data set to $_POSt and $_REQUEST
                data: {
                    // Required
                    action: aalProxyLoader.action_hook_suffix,  // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalProxyLoader.nonce,   // the nonce value set in template.php
                },
                success: function ( response ) {
                    if ( response.success ) {
                        var _oProxyListTextArea = $( 'textarea.proxy-list' );
                        if ( _oProxyListTextArea.length ) {
                            var _sCurrentValue = $.trim( _oProxyListTextArea.val() );
                            _oProxyListTextArea.val( _sCurrentValue + '\r\n' + $.trim( response.result ) );
                        }

                    } else {
                    }
                },
                error: function( response ) {
                    // should display a warning
                },
                complete: function() {
                    _oSpinner.remove();
                }
            } ); // ajax            

            return false; // do not click
        });

    });
}(jQuery));
