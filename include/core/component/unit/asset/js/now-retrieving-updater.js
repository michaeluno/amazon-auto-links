/**
 * @name Now-Retrieving Updater
 * @version 1.0.3
 */
(function($){

    /**
     * @var aalNowRetrieving
     */
    $( document ).ready( function() {

        if ( 'undefined' === typeof( aalNowRetrieving ) ) {
            console.log( 'Amazon Auto Links', 'Now-Retrieving Updater', 'Failed to load.' );
            return;
        }
        processNowRetrieving( this );

    });

    $( 'body' ).on( 'aal_ajax_loaded_unit', function( event ) {
        processNowRetrieving( event.target );
    } );

    function processNowRetrieving( subject ) {

        var _oSpinner = $( '<img class="spinner" src="' + aalNowRetrieving.spinnerURL + '" alt="' + aalNowRetrieving.label.nowLoading + '"/>' );
        _oSpinner.css( { margin: '0 0.5em', 'vertical-align': 'middle', 'display': 'inline-block', 'height': '1em' } );

        var _aItems    = {};
        var _iCount    = 0;
        var _oSubject  = $( subject );
        var _oElements = _oSubject.find( '.now-retrieving' );
        _oElements.find( '.spinner' ).remove();
        _oElements
            .append( _oSpinner )
            .each( function( index, element ) {

            var _aArguments = $( this ).data();
            if ( 'undefined' === typeof _aArguments.asin ) {
                return true;
            }

            $( this ).attr( 'data-attempt', parseInt( $( this ).attr( 'data-attempt' ) ) + 1 );
            _aArguments.attempt = $( this ).attr( 'data-attempt' );

            if ( parseInt( _aArguments.attempt ) > 3 ) {
                $( this ).html( '' ); // remove the now-retrieving output.
                return true;
            }

            var _sKey = _aArguments.asin + '|' + _aArguments.locale + '|' + _aArguments.currency + '|' + _aArguments.language;
            if ( 'undefined' === typeof _aItems[ _sKey ] ) {
                _aItems[ _sKey ] = {};
            }
            _aItems[ _sKey ][ _aArguments.context ] = _aArguments;
            _iCount++;


        } );

        if ( ! _iCount ) {
            return;
        }

        $.ajax( {
            type: "post",
            dataType: 'json',
            url: aalNowRetrieving.ajaxURL,
            data: {
                action: aalNowRetrieving.actionHookSuffix,   // WordPress action hook name which follows after `wp_ajax_`
                aal_nonce: aalNowRetrieving.nonce,   // the nonce value
                items: _aItems
            },
            success: function ( response ) {

                if ( response.success ) {
                    $.each( response.result, function( iIndex, aItem ) {
                        var _oElement = _oSubject.find(
                            '.now-retrieving'
                            + '[data-asin=' + aItem.asin + ']'
                            + '[data-context=' + aItem.context + ']'
                            + '[data-locale=' + aItem.locale + ']'
                            + '[data-currency=' + aItem.currency + ']'
                            + '[data-language=' + aItem.language + ']'
                            + '[data-tag=' + aItem.tag + ']'
                        );
                        if ( 'undefined' === typeof aItem.output || ! aItem.set ) {
                            return true; // continue
                        }
                        var _oOutput  = $( aItem.output );
                        if ( _oOutput.find( '.now-retrieving' ).length ) {
                            return true;
                        }
                        var _oOutput2 = _oOutput.clone();

                        // Attempt to replace the element smoothly.
                        /// 1. Insert invisible element and measure the height.
                        /// 2. Create a wrapper of the now-retrieving element and have the wrapper min-height of the value retrieved in step 12.
                        /// 3. Unwrap and show the output.
                        _oOutput.css( {
                                'position': 'relative', // place inside the original position of the element; otherwise, the height does not match.
                                'left': -99999,
                                'top' : -99999,
                                'float' : 'left',
                                'display': 'block',
                            }).appendTo( _oElement.parent() );
                        _oElement.removeClass( 'now-retrieving' ); // prevents another attempt to resume the element

                        // Wait for 1 seconds for the element to have its height.
                        setTimeout(function() {
                            var _iHeight = 'undefined' !== typeof _oOutput[ 0 ] && 'undefined' !== typeof _oOutput[ 0 ].offsetHeight
                                ? _oOutput[ 0 ].offsetHeight
                                : 0;
                            _oOutput.remove();
                            _oElement.wrap( '<div></div>' );
                            _oElement.parent()
                                .css({
                                    'min-height': _iHeight + 'px',
                                });
                            _oOutput2.css( { 'opacity': '0' } );
                            _oElement.replaceWith( _oOutput2 );
                            _oOutput2.unwrap();
                            _oOutput2.animate( { 'opacity': '1' } );
 
                        }, 1000 );

                    });

                } else {
                }
            },
            error: function( response ) {
            },
            complete: function() {

                _oSpinner.remove();

                // Retry in case some elements are not updated.
                setTimeout(function() {
                    processNowRetrieving( subject );
                }, 3000 );

            }
        } ); // ajax

    }

    /**
     * @see https://gist.github.com/cecilemuller/4049939
     * @param html
     * @returns {*}
     */
	$.fn.replace_html = function(html){
		return this.each(function(){
			var el = $(this);
			el.stop(true, true, false);
			var finish = {width: this.style.width, height: this.style.height};
			var cur = {width: el.width() + "px", height: el.height() + "px"};
			var _oElement = $( html ).hide();
			el.html( _oElement );
			var next = {width: el.width() + "px", height: el.height() + "px"};
			el.css(cur).animate(next, 1000, function(){el.css(finish);});
			_oElement.fadeIn( 'slow' );
		});
	};

}(jQuery));