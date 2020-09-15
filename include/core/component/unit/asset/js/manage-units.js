/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * @name Manage Units
 * @version 1.0.0
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

    });

}(jQuery));
