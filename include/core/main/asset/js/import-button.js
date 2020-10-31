/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * @name Import Button
 * @version 1.0.0
 */
(function($){

    $( document ).ready( function() {

        $( '.amazon-auto-links-field-import input[type=submit]' ).click( function( event ) {
            var _iFiles = $( this ).closest( '.amazon-auto-links-field-import' ).find( 'input[type=file]' ).get( 0 ).files.length;
            if ( 0 === _iFiles ) {
                alert( 'No file selected.' );
                return false;
            }
            return true;
        } );

    }); // document ready

}(jQuery));
