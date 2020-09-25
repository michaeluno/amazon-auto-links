/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * @name Plugin Tests
 * @version 1.0.3
 */
(function($){

    /**
     * @var aalTests
     */
    $( document ).ready( function() {

        if ( 'undefined' === typeof aalTests ) {
            console.log( 'Amazon Auto Links', 'The test script failed to load' );
            return;
        }

        ajaxManager.run();

        $( '.copy-to-clipboard' ).click( function( event ) {
            event.preventDefault();
            var _oErrors = $( '.results' ).find( '*[data-success=0]' ).clone();
            if ( ! _oErrors.html() ) {
                alert( 'There is no error.' );
                return;
            }
            var _oToCopy = $( "<div>" ).append( _oErrors );
            _oToCopy.find( 'p:empty, dif:empty' ).remove();   // remove empty elements
            _oToCopy.find( '.result-container, .result-header, .result-body' ).append( '\n' );
            var _bCopied = aalCopyToClipboard( _oToCopy[ 0 ] );
            alert( _bCopied ? 'Copied ' + _oErrors.length + ' error(s).' : 'Failed to copy the errors.' );
            return false;
        } );

        $( '.clear-log' ).click( function( event ) {
            event.preventDefault();
            $( '.results' ).html( '' );
            return false;
        } );

        $( '.aal-tests' ).click( function( event ) {
            event.preventDefault();

            // Remove previous errors.
            $( '.item-select-error' ).remove();

            // Get filter items of tags.
            var _aTags         = $( '.test-tags' ).val().split( /,\s*/ ).filter( item => item );

            // Get checked items.
            var _oCheckedItems = $( 'input.test-categories:checked' );
            if ( ! _oCheckedItems.length ) {
                // $( this ).parent().parent().append( '<span class="item-select-error">* Please select items.</span>' );
                $( this ).closest( 'fieldset' ).after( '<span class="item-select-error">* Please select items.</span>' );
                return false;
            }
            _oCheckedItems.each( function() {

                var _sLabel = $( this ).closest( 'label' ).text();
                if ( 'undefined' === typeof aalTests.files[ _sLabel ] ) {
                    console.error( 'Files for ' + _sLabel + ' could not be found.' );
                    return true;
                }

                var _sResultClass = 'result-' + _sLabel;
                $( '*[class*="' + _sResultClass + '"]' ).remove();    // remove previous result

                var _oSection = $( '<div class="' + _sResultClass + '"></div>' );
                $( '.results' ).append( _oSection ); // create a result area

                var _aFiles = aalTests.files[ _sLabel ];
                $.each( _aFiles, function( index, sFilePath ) {
                     $( '*[class*="' + _sResultClass + '"]' ).html( '' );  // clear previous results
                    ___runFile( _sLabel, sFilePath, _aTags );
                } );

            } );
            return false;

        });

        function ___runFile( sLabel, sFilePath, aTags ) {

            var _oStartButton = $( '.aal-tests' );
            ajaxManager.addRequest( {
                type: "post",
                dataType: 'json',
                async: true,
                url: aalTests.ajaxURL,
                // Data set to $_POSt and $_REQUEST
                data: {
                    action: aalTests.actionHookSuffix,   // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalTests.nonce,   // the nonce value set in template.php
                    file_path: sFilePath,
                    tags: aTags,
                },
                // Custom properties.
                spinnerImage: $( '<img src="' + aalTests.spinnerURL + '" alt="Now loading..." />' ),
                startButton: _oStartButton,
                buttonLabel: _oStartButton.val(),
                resultsArea: $( '*[class*="' + 'result-' + sLabel + '"]' ),

                // Callbacks
                beforeSend: function() {

                    // Spinner and the button label to show processing
                    this.spinnerImage.css( { 'vertical-align': 'middle', 'display': 'inline-block', 'height': 'auto', 'margin-left': '0.5em' } );

                    this.startButton.val( 'Running...' );
                    this.startButton.parent().parent().append( this.spinnerImage );

                },
                success: function ( response ) {
                    ___setResponseOutput( this.resultsArea, response, sFilePath );
                },
                error: function( response ) {
                    this.resultsArea.append(
                        '<div class="response-error">'
                        + '<span class="bold error">ERROR</span> '
                        + response.responseText
                        + '</div>'
                    );
                },
                complete: function( self ) {

                    self.startButton.val( self.buttonLabel );
                    self.spinnerImage.remove();

                    // Accordion
                    $( '*[class*="' + 'result-' + sLabel + '"]' ).find( '.result-header' )
                        .off( 'click' )
                        .click( _accordion );

                }
            } ); // ajax
        }

        function _accordion( event ) {
            event.preventDefault();
            $( this ).closest( '.result-container' ).find( '.result-body' );
            $( this ).next().slideToggle( 'slow' );
            return false;
        }

        function ___setResponseOutput( _oTestTag, response, sFilePath ) {

            if ( ! response.success ) {
                _oTestTag.append( '<p class="test-error">'
                        + '<span class="bold">ERROR</span> '
                        + response.result
                    + '</p>' );
                return;
            }

            $.each( response.result, function( sIndex, eachResult ) {

                var _sHeader      = '';
                var _sDetails     = '';
                var _sPurpose     = '';
                var _sCurrentItem = '<p>File: ' + sFilePath + '</p>';
                var _iLine        = eachResult.line ? eachResult.line : 0;
                if ( _iLine ) {
                    _sCurrentItem = _sCurrentItem + '<p>Line: ' + _iLine + '</p>';
                }
                var _iSucceed     = eachResult.success ? 1 : 0;
                if ( eachResult.success ) {
                    _sHeader  = '<h4 class="result-header">'
                        + '<span class="test-success bold">OK</span> '
                        + eachResult.name
                        + '</h4>';
                    _sPurpose = eachResult.purpose
                        ? '<p class="purpose">' + eachResult.purpose + '</p>'
                        : '';
                    _sDetails = eachResult.raw
                        ? eachResult.message
                        : eachResult.message
                            ? '<p class="">' + eachResult.message + '</p>'
                            : '';
                } else {
                    _sHeader  = '<h4 class="result-header">'
                        + '<span class="test-error bold">Failed</span> '
                        + eachResult.name
                        + '</h4>';
                    _sPurpose = eachResult.purpose
                        ? '<p class="purpose">' + eachResult.purpose + '</p>'
                        : '';
                    _sDetails = eachResult.raw
                        ? eachResult.message
                        : eachResult.message
                            ? '<p class="">' + eachResult.message + '</p>'
                            : '';
                }
                var _sContent = _sPurpose + _sDetails;
                _sContent     = _sContent ? _sContent + '<hr />' : '';
                var _sBody    = "<div class='result-body' style='display:none'>" + _sContent + _sCurrentItem + "</div>";
                var _sResult  = "<div class='result-container' data-success='" + _iSucceed + "'>" + _sHeader + _sBody + "</div>";
                _oTestTag.append( _sResult );

            } );


        } // ___setRequestOutput()

    }); // $( document ).ready()

    /**
     * Disallow the enter key in the form.
     */
    $( document ).on( 'keydown', 'input.test-tags', function( event ) {
        if ( "Enter" !== event.key ) {
            return true;
        }
        $( '.aal-tests' ).trigger( 'click' );
        return false;
    });

    /**
     * Queues Ajax requests.
     * @see https://stackoverflow.com/a/4785886
     * @returns {{stop: stop, removeRequest: removeRequest, addRequest: addRequest, run: run}}
     */
    var ajaxManager = (function() {
        var requests = [];

        return {
            addRequest: function(opt) {
                requests.push(opt);
            },
            removeRequest: function(opt) {
                if( $.inArray(opt, requests) > -1 )
                    requests.splice($.inArray(opt, requests), 1);
            },
            run: function() {
                var self = this,
                    oriSuc;

                if( requests.length ) {
                    oriSuc = requests[0].complete;
                    requests[0].complete = function() {
                         if( typeof( oriSuc ) === 'function' ) {
                             oriSuc( requests[0] );
                         }
                         requests.shift();
                         self.run.apply(self, []);
                    };

                    $.ajax(requests[0]);
                } else {
                  self.tid = setTimeout(function() {
                     self.run.apply(self, []);
                  }, 1000);
                }
            },
            stop:  function() {
                requests = [];
                clearTimeout(this.tid);
            }
         };
    }()); // AjaxManager

}(jQuery));