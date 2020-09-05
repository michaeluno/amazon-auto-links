/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */
(function($){

    $( document ).ready( function() {

        if ( ! _canLoad() ) {
            return;
        }
        _debugLog( 'Amazon Auto Links', 'Log Filter Script', aalLog );

        // Log container
        var _oLog = $( '.log' );
        _oLog.css( {
            'display': 'block',
            'width' : _oLog.parent().width() + 'px',
        } );

        // Clipboard
        $( '.copy-to-clipboard' ).click( function (event) {
            var _oLogClone = $( '.log' ).clone();  // re-retrieve the element as it can be updated
            _oLogClone.find( 'p:empty, dif:empty' ).remove();   // remove empty elements
            _oLogClone.find( '.log-item-title, .log-item-message' ).append( '\n' );
            var _bCopied = aalCopyToClipboard( _oLogClone[ 0 ] );
            alert( _bCopied ? aalLog.labels.copied : aalLog.labels.not_copied );
        });

        var _oLogRaw = _oLog.clone();

        $( 'input.filter-include, input.filter-exclude' ).change( function (event) {
            _filterLog( _oLogRaw.clone(), $( 'input.filter-include' ), $( 'input.filter-exclude' ) );
        }).trigger( 'change' );

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

            $( '.log' ).replaceWith( oLogClone );

            // Accordion
            $( '.log-item-head' ).click( function() {
                $( this ).closest( '.log' ).find( '.stack-trace' ).css( {
                    'min-height': '320px',
                    'width': '100%',
                } );
                $( this ).next().toggle( 'slow' );

                return false;
            }).next().hide();

        }

    });

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
