/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * @name Manage Units
 * @version 1.1.0
 */
(function($){

    $( document ).ready( function() {

        if ( 'undefined' === typeof aalManageUnits ) {
            console.log( 'Amazon Auto Links', 'Translation items are not loaded.' );
            return;
        }

        $( '.row-actions .copy a' ).click( function() {
            aalCopyToClipboard( document.getElementById( $( this ).attr( 'data-target' ) ) );
            alert( aalManageUnits.labels.copied );
        });


        // Warning tooltip
        var _oTooltipWarnings = $( 'li[data-has-warning] a' );
        _oTooltipWarnings.on( 'click', function() {
            return false; // disable click
        });
        _oTooltipWarnings.on( 'mouseover', function() {

            var _oLi      = $( this ).closest( 'li' );
            var _sContent = _oLi.find( '.warning-tooltip-content' ).html();

            // Open the tooltip
            $( this ).pointer({
                pointerClass: 'aal-pointer',
                content: _sContent,
                position: 'top',
                buttons: function() {},
                close: function() {}
            }).pointer( 'open' );

            // Handle toolitip closing
            var _this    = this;
            $( this ).add( '.wp-pointer' ).on( 'mouseleave', function(){

                var _thisMouseLeave = this;
                // Set a timeout for the tooltip to close, allowing us to clear this trigger if the mouse comes back over
                var _timeoutId = setTimeout(function(){
                    $( _this ).pointer( 'close' );
                    $( _this ).off( 'mouseleave' );
                    $( _thisMouseLeave ).off( 'mouseleave' );
                    $( _this ).off( 'mouseenter' );
                    $( _thisMouseLeave ).off( 'mouseenter' );
                }, 650 );
                $( _this ).data( 'timeoutId', _timeoutId );

            } );
            $( this ).add( '.wp-pointer' ).on( 'mouseenter', function(){
                clearTimeout( $( _this ).data('timeoutId' ) );
            });




            return false;   // do not click

        } );


    });

}(jQuery));
