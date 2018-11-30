/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */
(function($){
    $( document ).ready( function() {

        $( '.aal_db_update a' ).click( function( event ) {
            event.preventDefault();
            var _oNotice = $( this ).closest( '.aal_db_update' );
            var _oSpinner = $( '<img src="' + aalDBUpdater.spinnerURL + '" />' );
            _oNotice.find( 'p' ).append( _oSpinner );
            jQuery.ajax( {
                type: "post",
                dataType: 'json',
                url: aalDBUpdater.ajaxURL,
                // Data set to $_POSt and $_REQUEST
                data: {
                    action: 'aal_action_update_database_table',   // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalDBUpdater.nonce,   // the nonce value set in template.php
                    versionTo: aalDBUpdater.versionTo,
                    tableName: aalDBUpdater.tableName
                },
                success: function ( response ) {
                    if ( response.success ) {
                        _oNotice.removeClass( 'notice-info' ).addClass( 'updated' );
                    } else {
                        _oNotice.removeClass( 'notice-info' ).addClass( 'error' );
                    }
                    _oNotice.find( 'p' ).html( '<b>' + aalDBUpdater.pluginName + '</b>: ' + response.result );
                },
                error: function( response ) {
                    _oNotice.removeClass( 'notice-info' ).addClass( 'error' );
                    _oNotice.find( 'p' ).html( '<b>' + aalDBUpdater.pluginName + '</b>: ' + aalDBUpdater.requestFailed  );
                },
                complete: function() {
                    _oSpinner.remove();
                    // if ( $( element ).hasClass( 'done' ) ) {
                    //     return;
                    // }
                    // $( element ).removeClass( 'loading' ).addClass( 'gray' );
                }
            } ); // ajax

            return false;
        });

    });
}(jQuery));
