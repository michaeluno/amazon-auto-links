/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * @name Log Item Filters
 * @version 1.0.3
 */
(function($){

    $( document ).ready( function() {

        if ( ! _canLoad() ) {
            return;
        }
        _debugLog( 'Amazon Auto Links', 'Log Filter Script', aalLog );

        // Clipboard
        $( '.copy-to-clipboard' ).click( function (event) {
            var _oLogClone = $( '.log' ).clone();  // re-retrieve the element as it can be updated
            _oLogClone.find( 'p:empty, dif:empty' ).remove();   // remove empty elements
            _oLogClone.find( '.log-item, .log-item-title, .log-item-message' ).append( '\n' );
            var _bCopied = aalCopyToClipboard( _oLogClone[ 0 ] );
            alert( _bCopied ? aalLog.labels.copied : aalLog.labels.not_copied );
        });


        // Filter input fields.
        var _oLogRaw = $( '.log' ).clone();
        $( 'input.filter-include, input.filter-exclude' ).change( function (event) {
            _filterLog( _oLogRaw.clone(), $( 'input.filter-include' ), $( 'input.filter-exclude' ) );
        }).trigger( 'change' );


        // Fix layout
        $( window ).trigger( 'resize' );
        var observer = new MutationObserver( _fixFormLayout );
        var target = document.querySelector('#side-sortables' );
            observer.observe(target, {
            attributes: true
        });

    });
        function _filterLog( oLogClone, oInputInclude, oInputExclude ) {

            var _sTypedInclude = oInputInclude.val();
            var _sTypedExclude = oInputExclude.val();
            var _aIncludes     = _sTypedInclude.split( /,\s{0,}/ ).filter( item => item );
            var _aExcludes     = _sTypedExclude.split( /,\s{0,}/ ).filter( item => item );

            _debugLog( 'filter include:', _aIncludes, 'exclude: ', _aExcludes );
            oLogClone.find( '.log-item' ).each( function() {
                var _sEntry = $( this ).text();
                if ( '' !== _sTypedExclude && _hasMatch( _sEntry, _aExcludes ) ) {
                    $( this ).remove();
                    return true;  // continue
                }
                if ( '' !== _sTypedInclude && ! _hasMatch( _sEntry, _aIncludes ) ) {
                    $( this ).remove();
                    return true;  // continue
                }
            } );

            $( '.log' ).replaceWith( oLogClone.show() );
            _fixFormLayout();

            // Accordion
            $( '.log-item-head' ).click( function() {
                $( this ).closest( '.log' ).find( '.stack-trace' ).css( {
                    'min-height': '320px',
                    'width': '100%',
                } );
                $( this ).next().slideToggle( 'slow' );

                return false;
            }).next().hide();

        }

    // Fix the form layout
    $( window ).resize( debounce( _fixFormLayout ) );  // window resize
    function _fixFormLayout( event ) {
        var _iSectionWidth = $( '.amazon-auto-links-section' ).width();
        if ( ! _iSectionWidth ) {
            return;
        }
        $( '.log' ).width( _iSectionWidth )
            .show();
        $( '.debug-output' ).width( _iSectionWidth );
    }
    // function _getFormSectionRightPosition() {
    //     var _oSubject = $( '.amazon-auto-links-section > table' ).first();
    //     if ( ! _oSubject.length ) {
    //         return 0;
    //     }
    //     var _iScrollLeft        = $( window ).scrollLeft();
    //     var _aOffsetSubject     = _oSubject.offset();
    //     return _aOffsetSubject.left + _oSubject.width() - _iScrollLeft;
    // }
    // function _getSideMetaBoxLeftPosition() {
    //     var _oSideMetaBox    = $( "#side-sortables" );
    //     if ( ! _oSideMetaBox.length ) {
    //         return 0;
    //     }
    //     var _iScrollLeft            = $( window ).scrollLeft();
    //     var _aOffsetSideMetaBox     = _oSideMetaBox.offset();
    //     var _iSideMetaBoxPosX       = _aOffsetSideMetaBox.left - _iScrollLeft;
    //
    //     // For one column layout,
    //     if ( _iSideMetaBoxPosX < 480 ) {
    //         return 0;
    //     }
    //     return _iSideMetaBoxPosX;
    // }

    /**
     * Disallow the enter key in the form.
     */
    $( document ).on( 'keydown', 'input.filter-include, input.filter-exclude', function( event ) {
        if ( "Enter" !== event.key ) {
            return true;
        }
        $( this ).trigger( 'change' );
        return false;
    });

    function _debugLog( ...args ) {
        if ( ! aalLog.debugMode ) {
            return;
        }
        console.log( ...args );
    }

    function _canLoad() {
        if ( 'undefined' === typeof aalLog ) {
            console.log( 'Amazon Auto Links:', 'the lof filter script is not loaded.' );
            return false;
        }
        if ( 'undefined' === typeof aalCopyToClipboard ) {
            console.log( 'Amazon Auto Links:', 'the utility script is not loaded.' );
            return false;
        }
        return true;
    }

    function _hasMatch( sEntry, aItems ) {
        var _bFound = false;
        $.each( aItems, function( index, _sItem ) {
            if ( '' === _sItem ) {
                return true; // continue
            }
            if ( -1 === sEntry.indexOf( _sItem ) ) {
                return true; // not found, continue
            }
            _bFound = true;
            return false; // found, break
        } );
        return _bFound;
    }



}(jQuery));