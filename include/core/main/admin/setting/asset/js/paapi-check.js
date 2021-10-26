/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * @name PA-API check
 * @version 1.0.2
 */
(function($){

    /**
     * @var aalPAAPICheck
     */
    $( document ).ready( function() {

        if ( ! _canLoad() ) {
            return;
        }
        _debugLog( 'loaded...', aalPAAPICheck );
        
        // When the user changes the locale, update the Currency and Language
        $( '.action-check-paapi' ).click( function( event ){
            event.preventDefault();
            _debugLog( 'PA-API check button is pressed.' );
            var _oContainerPAAPI  = $( this ).closest( '.paapi' );
            var _oContainerLocale = _oContainerPAAPI.closest( '.locale' );
            var _sLocale          = _oContainerLocale.attr( 'data-locale' );
            var _oThis            = $( this );

            var _oAssociateID     = _oContainerLocale.find( 'input.associate-id' );
            var _sAssociateID     = _oAssociateID.val().trim();
            _oAssociateID.val( _sAssociateID );
            var _oAccessKey       = _oContainerPAAPI.find( 'input.access-key' );
            var _sAccessKey       = _oAccessKey.val().trim();
            _oAccessKey.val( _sAccessKey );
            var _oSecretKey       = _oContainerPAAPI.find( 'input.secret-key' );
            var _sSecretKey       = _oSecretKey.val();
            _oSecretKey.val( _sSecretKey );

            // Validate
            _oContainerLocale.find( 'span.error' ).remove();
            var _aRequired = [];
            if ( ! _sAssociateID ) {
                _aRequired.push( aalPAAPICheck.label.associateID );
                _oAssociateID.after( '<span class="error">* ' + aalPAAPICheck.label.required + '</span>' );
            }
            if ( ! _sAccessKey ) {
                _aRequired.push( aalPAAPICheck.label.accessKey );
                _oAccessKey.after( '<span class="error">* ' + aalPAAPICheck.label.required + '</span>' );
            }
            if ( ! _sSecretKey ) {
                _aRequired.push( aalPAAPICheck.label.secretKey );
                _oSecretKey.after( '<span class="error">* ' + aalPAAPICheck.label.required + '</span>' );
            }
            if ( _aRequired.length ) {
                _showPAAPICheckError( _oThis, aalPAAPICheck.label.optionNotSet + ': ' + _aRequired.join( ', ' ) );
                return;
            }
            var _iRequiredLengthAccessKey = parseInt( _oAccessKey.attr( 'maxlength' ) );
            if ( _iRequiredLengthAccessKey && _iRequiredLengthAccessKey !== _sAccessKey.length ) {
                _oAccessKey.after( '<span class="error">* ' + aalPAAPICheck.label.required + '</span>' );
                _showPAAPICheckError( _oThis, aalPAAPICheck.label.keyLengthAccessKey );
                return;
            }
            var _iRequiredLengthSecretKey = parseInt( _oSecretKey.attr( 'maxlength' ) );
            if ( _iRequiredLengthSecretKey && _iRequiredLengthSecretKey !== _sSecretKey.length ) {
                _oSecretKey.after( '<span class="error">* ' + aalPAAPICheck.label.required + '</span>' );
                _showPAAPICheckError( _oThis, aalPAAPICheck.label.keyLengthSecretKey );
                return;
            }

            $.ajax( {
                type: "post",
                dataType: 'json',
                async: true,
                cache: true,
                url: aalPAAPICheck.ajaxURL,
                data: {
                    action: aalPAAPICheck.actionHookSuffix,   // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalPAAPICheck.nonce,   // the nonce value set in template.php
                    locale: _sLocale,
                    access_key: _sAccessKey,
                    secret_key: _sSecretKey,
                    associate_id: _sAssociateID
                },

                // Custom properties
                spinnerImage: $( '<img class="aal-spinner" src="' + aalPAAPICheck.spinnerURL + '" alt="' + aalPAAPICheck.label.attr.checking + '"/>' ),

                // Callbacks
                beforeSend: function() {
                    _oThis.closest( '.container-button' ).after( this.spinnerImage );
                },
                success: function ( response ) {
                    _debugLog( response );
                    if ( ! response.success ) {
                        _showPAAPICheckResponseError( _oThis, _oContainerPAAPI, response.result );
                        _oContainerPAAPI.find( 'input.status' ).val( 0 );
                        return;
                    }
                    if ( response.result.error ) {
                        _showPAAPICheckResponseError( _oThis, _oContainerPAAPI, response.result.message );
                        _oContainerPAAPI.find( 'input.status' ).val( 0 );
                    } else {
                        _showPAAPICheckResponseText( _oThis, _oContainerPAAPI, response.result.message );
                        _oContainerPAAPI.find( 'input.status' ).val( 1 );
                    }
                    _oContainerPAAPI.find( '.paapi-last-checked' ).html( response.result.last_checked_readable );
                    _oContainerPAAPI.find( 'input.last-checked' ).val( response.result.last_checked );

                },
                error: function( response ) {
                    _debugLog( 'Ajax response error', response );
                    var _aErrorParts = [];
                    if ( 'undefined' !== response.status ) {
                        _aErrorParts.push( response.status );
                    }
                    if ( 'undefined' !== response.statusText ) {
                        _aErrorParts.push( response.statusText );
                    }
                    _aErrorParts.push( response.responseText );
                    _showPAAPICheckResponseError( _oThis, _oContainerPAAPI, _aErrorParts.join( ' ' ) );
                },
                complete: function() {
                    $( '.aal-spinner' ).remove();
                    _debugLog( 'The PA-API check is performed.' );
                }
            } ); // ajax()
            function _showPAAPICheckResponseText( oThis, oContainerPAAPI, sText ) {
                oThis.closest( 'fieldset' ).find( '.response-text-paapi-check' ).html( '<div class="updated container-response-text"><p>' + sText + '</p></div>' );
                oContainerPAAPI.find( '.status-connectivity.connected' ).css({ 'display': 'inline-block' });
                oContainerPAAPI.find( '.status-connectivity.disconnected, .status-connectivity.untested' ).hide();
            }
            function _showPAAPICheckResponseError( oThis, oContainerPAAPI, sText ) {
                oThis.closest( 'fieldset' ).find( '.response-text-paapi-check' ).html( "<div class='error container-response-text'><p>" + sText + "</p></div>" );
                oContainerPAAPI.find( '.status-connectivity.connected, .status-connectivity.untested' ).hide();
                oContainerPAAPI.find( '.status-connectivity.disconnected' ).css({ 'display': 'inline-block' });
            }
            function _showPAAPICheckError( oThis, sText ) {
                oThis.closest( 'fieldset' ).find( '.response-text-paapi-check' ).html( "<div class='error container-response-text'><p>" + sText + "</p></div>" );
            }
            
        } ); // change()

    }); // ready()


    function _debugLog( ...args ) {
        if ( ! parseInt( aalPAAPICheck.debugMode ) ) {
            return;
        }
        console.log( aalPAAPICheck.pluginName, aalPAAPICheck.scriptName, ...args );
    }

    function _canLoad() {
        if ( 'undefined' === typeof aalPAAPICheck ) {
            console.log( 'the PA-API check script is not loaded.' );
            return false;
        }
        return true;
    }


}(jQuery));