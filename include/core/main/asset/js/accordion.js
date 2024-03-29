/**
 * @name Collapsible Elements
 * @version 1.0.0
 */
(function($){

    $( document ).ready( function(){

        $( '.aal-accordion' ).accordion({
            collapsible: true,
            active: false,
            heightStyle: 'content',
        });
        $( '.aal-accordion > *:header, .aal-accordion > h6' )
            .prepend( '<span class="dashicons dashicons-arrow-up"></span>' );

    } );

}(jQuery));