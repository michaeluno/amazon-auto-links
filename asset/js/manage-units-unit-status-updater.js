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

        _checkPendingUnitStatus();
        var _refreshIntervalId = setInterval( _checkPendingUnitStatus, 15000 ); // 15 seconds interval
        function _checkPendingUnitStatus() {

            var _oPendingUnits = $( '.unit-status.loading' );

            // No pending items. Nothing to do.
            if ( ! _oPendingUnits.length ) {
                clearInterval( _refreshIntervalId );
                return;
            }

            // var _aPendingUnits = [];
            _oPendingUnits.each( function( index, element ) {

                var _sUnitID = $( element ).attr( 'data-post-id' );

                jQuery.ajax( {
                    type: "post",
                    dataType: 'json',
                    url: aalManageUnits.ajaxURL,
                    // Data set to $_POSt and $_REQUEST
                    data: {
                        action: 'aal_action_update_unit_status',   // WordPress action hook name which follows after `wp_ajax_`
                        aal_nonce: $( '#amazon-auto-links-nonce' ).val(),   // the nonce value set in template.php
                        units: [ _sUnitID ]
                    },
                    success: function ( response ) {
                        if ( response.success ) {
                            $.each( response.result, function( sIndex, value ) {
                                // console.log( sIndex + ": " + value );
                                if ( '' === value ) {
                                    $( '.unit-status[data-post-id=' + sIndex + ']' )
                                        .removeClass( 'loading' )
                                        .addClass( 'green' );
                                    return true;
                                }
                                if ( value ) {
                                    $( '.unit-status[data-post-id=' + sIndex + ']' )
                                        .removeClass( 'loading' )
                                        .addClass( 'red' );
                                    return true;
                                }

                            });
                        } else {
                        }
                    }
                } ); // ajax

            } );

        }

    }); // document ready
}(jQuery));
