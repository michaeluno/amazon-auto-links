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


        if ( 'undefined' === typeof aalLog ) {
            return;
        }

        _debugLog( 'Amazon Auto Links', 'Log Filter Script', aalLog );

        var _oTextArea  = $( 'textarea.log' );
        var _sLogRaw    = _oTextArea.val();


        $( 'input.filter-include, input.filter-exclude' ).change(function (event) {
            _filterLog( _sLogRaw, _oTextArea, $( 'input.filter-include' ), $( 'input.filter-exclude' ) );
        }).trigger( 'change' );

        function _filterLog( sLogRaw, oTextArea, oInputInclude, oInputExclude ) {

            var _sTypedInclude = oInputInclude.val();
            var _sTypedExclude = oInputExclude.val();
            var _aIncludes     = _sTypedInclude.split( /,\s{0,}/ ).filter( item => item );
            var _aExcludes     = _sTypedExclude.split( /,\s{0,}/ ).filter( item => item );

            _debugLog( 'filter include:', _aIncludes, 'exclude: ', _aExcludes );

            var _aLog          = sLogRaw.split( /\n(?=\d{4}-\d{2}-\d{2})/ );
            var _aCurrent      = [];
            $.each( _aLog, function( index, sEntry ) {

                if ( '' !== _sTypedExclude && _hasMatch( sEntry, _aExcludes ) ) {
                    return true;  // continue
                }
                if ( '' !== _sTypedInclude && ! _hasMatch( sEntry, _aIncludes ) ) {
                    return true;  // continue
                }
                _aCurrent.push( sEntry );

            } );

            // Write to the text area.
            oTextArea.val( _aCurrent.join( '\n' ) );

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

}(jQuery));
