/**
 * @name Web Page Dumper Tester
 * @version 1.0.0
 */
(function($){

    $( document ).on( 'keypress', '#web-page-dumper-input-url', function( event ) {
        if ( "Enter" !== event.key ) {
            return true;
        }
        $( this ).attr( 'value', $( this ).val() ); // without this, the click event of 'Add to List' button somehow does not get triggered.
        $( '#web-page-dumper-action-test' ).trigger( 'click' );
        return false;
    });

    $( document ).ready( function() {

        if ( 'undefined' === typeof aalWebPageDumperTester ) {
            return;
        }

        $( '#web-page-dumper-input-url' ).change( function() {
            var _addToList = $( this ).parent().find( '#web-page-dumper-action-add-to-list' )
            if( ! _addToList.is( ':disabled' ) ){
                _addToList.attr( 'disabled', 'disabled' );
            }
        } );

        $( '#web-page-dumper-action-add-to-list' ).click( function( event ){
            event.preventDefault();
            $( '.list-web-page-dumper' ).val(function(i, text) {

                var _url = $( '#web-page-dumper-input-url' ).val().trim();
                var lines = text.split('\n');
                if ( -1 !== lines.indexOf( _url ) ) {
                    $( '.test-result' ).html( '<div class="error"><p>' + aalWebPageDumperTester.label.alradyAdded + '</p></div>' );
                    return text;
                }
                return ( text ? text + '\n' : '' ) + _url;

            });
            return false;
        } );

        $( '#web-page-dumper-action-test' ).click( function( event ) {
            event.preventDefault();

            var _url = $( this ).parent().find( '#web-page-dumper-input-url' ).val();
            if ( ! _url ) {
                alert( aalWebPageDumperTester.label.enterURL )
                return;
            }

            var _addToList = $( this ).parent().find( '#web-page-dumper-action-add-to-list' );
            _addToList.attr( 'disabled', 'disabled' );

            var _oSpinner   = $( '<img class="ajax-spinner test-web-page-dumper-spinner" src="' + aalWebPageDumperTester.spinnerURL + '" alt="Spinner" />' );
            var _oLabelContainer = $( this ).closest( '.amazon-auto-links-input-label-container' );
            _oLabelContainer.find( '.test-result' ).remove();
            var _oTestResult = $( '<div class="test-result"><p>' + aalWebPageDumperTester.label.testing + '</p></div>' );
            _oLabelContainer.append( _oTestResult );
            _oLabelContainer.find( '.test-result p' ).append( _oSpinner );

            jQuery.ajax( {
                type: 'post',
                dataType: 'json',
                url: aalWebPageDumperTester.ajaxURL,
                // Data set to $_POSt and $_REQUEST
                data: {
                    // Required
                    action: aalWebPageDumperTester.actionHookSuffix,  // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalWebPageDumperTester.nonce,   // the nonce value set in template.php
                    url: _url
                },
                success: function ( response ) {
                    if ( response.success ) {
                        _oTestResult.html( '<div class="updated"><p>' + response.result + '</p></div>' );
                        _addToList.removeAttr( 'disabled' );
                        return;
                    }
                    _oTestResult.html( '<div class="error"><p>' + response.result + '</p></div>' );
                },
                error: function( response ) {
                    _oTestResult.html( '<p>' + response.responseText + '</p>' );
                },
                complete: function() {
                    _oSpinner.remove();

                }
            } ); // ajax

            return false; // do not click
        });

    });
}(jQuery));
