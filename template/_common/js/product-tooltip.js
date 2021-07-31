/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * @name Pointer Tooltip
 * @version 1.0.0
 */
(function($){

    $( document ).ready( function() {
        initialize( '.amazon-disclaimer-tooltip' );
    } );
    $( 'body' ).on( 'aal_ajax_loaded_unit', function( event ) {
        initialize( $( event.target ).find( '.amazon-disclaimer-tooltip' ) );
    } );

    function initialize( target ) {

        var _this = $( target )

        // Disable the CSS default tooltip
        $( _this ).find( '.amazon-disclaimer-tooltip-content-text' ).css( 'display', 'none' );

        var _pointerTooltip = $( _this );
        _pointerTooltip.on( 'click', function() {
            return false; // disable click
        });
        _pointerTooltip.on( 'mouseover', function() {

            // Open the tooltip
            $( this ).aalPointer({
                pointerClass: 'aal-tooltip',
                pointerWidth: 340,
                content: function() {
                    return $( this ).find( '.amazon-disclaimer-tooltip-content-text' ).html();
                },
                // position: 'right',
                position: {
                    edge: 'right',
                    align: 'center',
                    within: $( this ).closest( '.amazon-product-container' ) // <-- this is added
                    // collision: 'flip',
                },
                buttons: function() {},
                close: function() {},
            }).aalPointer( 'open' );

            // Handle toolitip closing
            var _self    = this;
            $( this ).add( '.aal-tooltip' ).on( 'mouseleave', function(){

                var _selfMouseLeave = this;
                // Set a timeout for the tooltip to close, allowing us to clear this trigger if the mouse comes back over
                var _timeoutId = setTimeout(function(){
                    $( _self ).aalPointer( 'close' );
                    $( _self ).off( 'mouseleave' );
                    $( _selfMouseLeave ).off( 'mouseleave' );
                    $( _self ).off( 'mouseenter' );
                    $( _selfMouseLeave ).off( 'mouseenter' );
                }, 650 );
                $( _self ).data( 'timeoutId', _timeoutId );

            } );
            $( this ).add( '.aal-tooltip' ).on( 'mouseenter', function(){
                clearTimeout( $( _self ).data('timeoutId' ) );
            });

        } );

    }

}(jQuery));