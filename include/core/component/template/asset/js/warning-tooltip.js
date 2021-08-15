/**
 * @name Warning Tooltips
 * @version 1.1.0
 */
(function($){
    $( document ).ready( function() {

        $( 'div[data-has-warning]' ).on( 'mouseover', function() {

            // Open the tooltip
            $( this ).pointer({
                pointerClass: 'aal-pointer',
                content: function() {
                    return $( this ).find( '.tooltip-content' ).html();
                },
                position: 'top',
                buttons: function() {},
                close: function() {}
            }).pointer( 'open' );

            // Handle toolitip closing
            var _self    = this;
            $( this ).add( '.aal-pointer' ).on( 'mouseleave', function(){

                var _selfMouseLeave = this;
                // Set a timeout for the tooltip to close, allowing us to clear this trigger if the mouse comes back over
                var _timeoutId = setTimeout(function(){
                    $( _self ).pointer( 'close' );
                    $( _self ).off( 'mouseleave' );
                    $( _selfMouseLeave ).off( 'mouseleave' );
                    $( _self ).off( 'mouseenter' );
                    $( _selfMouseLeave ).off( 'mouseenter' );
                }, 650 );
                $( _self ).data( 'timeoutId', _timeoutId );

            } );
            $( this ).add( '.aal-pointer' ).on( 'mouseenter', function(){
                clearTimeout( $( _self ).data('timeoutId' ) );
            });

            return false;

        } );

    });
}(jQuery));