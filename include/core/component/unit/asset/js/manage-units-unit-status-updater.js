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

        $( '.unit-status.gray' ).data( 'countTrial', 0 );

        _checkPendingUnitStatus();
        var _refreshIntervalId = setInterval( _checkPendingUnitStatus, 7500 ); // 7.5cseconds interval
        function _checkPendingUnitStatus() {

            var _oPendingUnits = $( '.unit-status.gray' );

            // No pending items. Nothing to do.
            if ( ! _oPendingUnits.length ) {
                clearInterval( _refreshIntervalId );
                return;
            }

            _oPendingUnits.each( function( index, element ) {

                if ( $( element ).hasClass( 'done' ) ) {
                    return true;
                }

                $( element ).data().countTrial++;
                if ( $( element ).data( 'countTrial' ) > 3 ) {
                    $( element ).addClass( 'gray done' );
                    return true;
                }

                var _sUnitID = $( element ).attr( 'data-post-id' );
                $( element ).removeClass( 'gray' ).addClass( 'loading' );
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
                                if ( 'normal' === value ) {
                                    $( '.unit-status[data-post-id=' + sIndex + ']' )
                                        .removeClass( 'loading' )
                                        .addClass( 'green done' );
                                    return true;
                                }
                                if ( value ) {
                                    $( '.unit-status[data-post-id=' + sIndex + ']' )
                                        .removeClass( 'loading' )
                                        .addClass( 'red done' );
                                    return true;
                                }
                            });
                        } else {
                        }
                    },
                    complete: function() {
                        if ( $( element ).hasClass( 'done' ) ) {
                            return;
                        }
                        $( element ).removeClass( 'loading' ).addClass( 'gray' );
                    }
                } ); // ajax

            } );

        }

    }); // document ready
}(jQuery));
