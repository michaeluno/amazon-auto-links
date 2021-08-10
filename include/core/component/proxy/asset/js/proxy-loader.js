/**
 * @name Proxy Loader
 * @version 1.0.0
 */
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

            var _oSpinner   = $( '<img src="' + aalProxyLoader.spinnerURL + '" alt="Spinner" />' );
            _oSpinner.addClass( 'ajax-spinner' );
            $( this ).parent().append( _oSpinner );

            event.preventDefault();
            jQuery.ajax( {
                type: 'post',
                dataType: 'json',
                url: aalProxyLoader.ajaxURL,
                data: {
                    // Required
                    action: aalProxyLoader.action_hook_suffix,  // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalProxyLoader.nonce,   // the nonce value set in template.php
                },
                success: function ( response ) {
                    if ( response.success ) {
                        var _oProxyListTextArea = $( 'textarea.proxy-list' );
                        if ( _oProxyListTextArea.length ) {
                            var _sCurrentValue =  _oProxyListTextArea.val();
                            _sCurrentValue = _sCurrentValue.replace( /^\s+|\s+$/g, "" ); // trim line feeds
                            _sCurrentValue = $.trim( _sCurrentValue ); // trim white spaces
                            _sCurrentValue = _sCurrentValue
                                ? _sCurrentValue + '\r\n'
                                : '';
                            _oProxyListTextArea.val( _sCurrentValue + $.trim( response.result ) );
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
