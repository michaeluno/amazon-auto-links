/**
 * @name Now-Retrieving Updater
 * @version 1.0.0
 */
(function($){

    $( document ).ready( function() {

// console.log( aalNowRetrieving );

        processNowRetrieving( this );


    });

    $( 'body' ).on( 'aal_ajax_loaded_unit', function( event ) {

        processNowRetrieving( event.target );


    } );

    function processNowRetrieving( subject ) {

        // Spinner
        var _oSpinner = $( '<img src="' + aalNowRetrieving.spinnerURL + '" />' );
        _oSpinner.css( { margin: '0 0.5em', 'vertical-align': 'middle', 'display': 'inline-block' } );


        var _aItems = {};
        var _iCount = 0;

        $( subject ).find( '.now-retrieving' ).each( function( index, element ) {
            var _aArguments = $( this ).data();
            if ( 'undefined' === typeof _aArguments.asin ) {
                return true;
            }
            var _sKey = _aArguments.asin + '|' + _aArguments.locale + '|' + _aArguments.currency + '|' + _aArguments.language;
            if ( 'undefined' === typeof _aItems[ _sKey ] ) {
                _aItems[ _sKey ] = {};
            }
            _aItems[ _sKey ][ _aArguments.context ] = _aArguments;
            _iCount++;

            $( this ).append( _oSpinner );
        } );

        if ( ! _iCount ) {
            return;
        }

        // Ajax
        $.ajax( {
            type: "post",
            dataType: 'json',
            url: aalNowRetrieving.ajaxURL,
            // Data set to $_POST and $_REQUEST
            data: {
                action: aalNowRetrieving.actionHookSuffix,   // WordPress action hook name which follows after `wp_ajax_`
                aal_nonce: aalNowRetrieving.nonce,   // the nonce value
                items: _aItems
            },
            success: function ( response ) {
                if ( response.success ) {
                    // @todo update the 'Now retrieving...' elements
                    $.each( response.result, function( iIndex, aItem ) {
                        if ( 'undefined' === typeof aItem.output ) {
                            return true; // continue;
                        }
                        $( subject ).find(
                            '.now-retrieving'
                            + '[data-asin=' + aItem.asin + ']'
                            + '[data-context=' + aItem.context + ']'
                            + '[data-locale=' + aItem.locale + ']'
                            + '[data-currency=' + aItem.currency + ']'
                            + '[data-language=' + aItem.language + ']'
                            + '[data-tag=' + aItem.tag + ']'
                        ).replaceWith( $( aItem.output ) );
                    });
                } else {
                }
            },
            complete: function() {
            }
        } ); // ajax

    }

}(jQuery));