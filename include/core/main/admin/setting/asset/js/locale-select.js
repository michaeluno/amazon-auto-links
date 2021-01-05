/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * @name Locale Select
 * @version 1.0.0
 */
(function($){

    /**
     * @var aalLocaleSelect
     */
    $( document ).ready( function() {

        if ( ! _canLoad() ) {
            return;
        }
        _debugLog( 'loaded...', aalLocaleSelect );
        
        // When the user changes the locale, update the Currency and Language
        $( 'select[name=unit_default\\[country\\]]' ).change( function( event ){

            _debugLog( 'locale selected', aalLocaleSelect );

            $.ajax( {
                type: "post",
                dataType: 'json',
                async: true,
                cache: true,
                url: aalLocaleSelect.ajaxURL,
                // Data set to $_POST and $_REQUEST
                data: {
                    action: aalLocaleSelect.actionHookSuffix,   // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalLocaleSelect.nonce,   // the nonce value set in template.php
                    locale: $( this ).val(),
                },

                // Custom properties
                spinnerImage: $( '<img class="aal-spinner" src="' + aalLocaleSelect.spinnerURL + '" />' ),

                // Callbacks
                beforeSend: function() {
                    this.spinnerImage.css( { 'vertical-align': 'middle', 'display': 'inline-block', 'height': 'auto', 'margin-left': '0.5em' } );
                    $( 'select[name=unit_default\\[language\\]], select[name=unit_default\\[preferred_currency\\]]' )
                        .closest( '.amazon-auto-links-fieldrow' )
                        .find( '.amazon-auto-links-field-title' )
                        .append( this.spinnerImage );
                },
                success: function ( response ) {
                    _debugLog( response );
                    if ( response.success ) {
                        $( 'select[name=unit_default\\[language\\]]' ).html( response.result.language );
                        $( 'select[name=unit_default\\[preferred_currency\\]]' ).html( response.result.currency );
                    } else {
                        _debugLog( 'Ajax response error', response );
                    }
                },
                error: function( response ) {
                    _debugLog( 'Ajax response error', response );
                },
                complete: function() {
                    // this.spinnerImage.remove();
                    $( '.aal-spinner' ).remove();
                    _debugLog( 'The Language and Currency fields are updated.' );
                }
            } ); // ajax()
            
        } ); // change()

    }); // ready()


    function _debugLog( ...args ) {
        if ( ! aalLocaleSelect.debugMode ) {
            return;
        }
        console.log( aalLocaleSelect.pluginName, aalLocaleSelect.scriptName, ...args );
    }

    function _canLoad() {
        if ( 'undefined' === typeof aalLocaleSelect ) {
            console.log( 'Amazon Auto Links:', 'the locale selection script is not loaded.' );
            return false;
        }
        return true;
    }


}(jQuery));