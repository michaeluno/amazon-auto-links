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
        $( 'li[data-has-warning] a' ).on( 'click', function() {
            var _oLi      = $( this ).closest( 'li' );
            var _sContent = _oLi.find( '.warning-tooltip-content' ).html();
            $( this ).pointer({
                content: _sContent,
                position: 'left ',
                close: function() {}
            }).pointer( 'open' );
            return false;   // do not click
        } );


    });

}(jQuery));
