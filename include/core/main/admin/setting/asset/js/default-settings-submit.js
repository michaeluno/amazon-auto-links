/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * @name Default Settings Submit
 * @version 1.0.2
 */
(function($){

    $( document ).ready( function() {

        if ( ! _canLoad() ) {
            return;
        }
        _debugLog( 'Submit Script', aalSubmit );

        $( '.reset-defaults' ).click( function( event ) {

            $( '.submit-warning-reset' ).remove();

            if ( ! $( 'input.reset-confirm[type=checkbox]' ).prop( 'checked' ) ) {
                event.preventDefault();
                $( this ).closest( '.amazon-auto-links-fieldset' )
                    .closest( '.amazon-auto-links-fields' )
                    .append( '<span class="warning submit-warning-reset">* ' + aalSubmit.label.please_confirm + '</span>' )
                return false;
            }
            return true;

        } );



    }); // document ready


    function _debugLog( ...args ) {
        if ( ! parseInt( aalSubmit.debugMode ) ) {
            return;
        }
        console.log( aalSubmit.pluginName, ...args );
    }

    function _canLoad() {
        if ( 'undefined' === typeof aalSubmit ) {
            console.log( 'The submit script is not loaded.' );
            return false;
        }
        return true;
    }


}(jQuery));