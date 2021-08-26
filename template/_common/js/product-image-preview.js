/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * @name Image Preview Tooltip
 * @version 1.0.2
 */
(function($){

    var imageSize = 420;

    $( document ).ready( function() {
        setMainThumbnailDataAttributes( $( 'body' ) );
        initialize( '.amazon-auto-links-product-image img[data-large-src]' );
    } );

    $( 'body' ).on( 'aal_ajax_loaded_unit', function( event ) {
        setMainThumbnailDataAttributes( event.target );
        initialize( $( event.target ).find( '.amazon-auto-links-product-image img[data-large-src]' ) );
    } );

    function setMainThumbnailDataAttributes( target ) {
        $( target ).find( '.amazon-product-thumbnail-container[data-large-src]' ).each( function(){
            $( this ).find( '.amazon-product-thumbnail img' ).attr( 'data-large-src', $( this ).attr( 'data-large-src' ) )
                .attr( 'data-href', $( this ).attr( 'data-href' ) );
        } );
    }

    // @deprecated
    // function setImageData( target ) {
    //     $( target ).each( function() {
    //         $( this ).data( 'href', $( this ).closest( 'a' ).attr( 'href' ) );
    //             $( this ).data( 'largeSrc', $( this ).attr( 'src' ).replace( /(?<=\.*[_A-Z,\.]+)(\d+)(?=[,_\.])/g, imageSize ) );
    //     } );
    // }

    function initialize( target ) {

        var _imagePreviewPopup = $( target );
        _imagePreviewPopup.on( 'click', function() {
            return false; // disable click
        });
        _imagePreviewPopup.on( 'mouseover', function() {

            var _srcImg = $( this ).data( 'largeSrc' );
            if ( ! _srcImg ) {
                return;
            }

            var _container = $( this ).closest( '.amazon-product-container, .amazon-auto-links' ),
            // var _container = $( this ).closest( '.amazon-product-container' ),
                _body      = $( 'body' );
            var _maxSize = Math.min( _body.width(), _body.height() );

            _maxSize = _maxSize < imageSize
                ? ( _maxSize * 0.8 )    // this occurs in the embedded view. This is to prevent the image to be cut off
                : imageSize;

            // Open the tooltip
            $( this ).aalPointer({
                pointerClass: 'aal-image-preview-tooltip',
                pointerWidth: parseInt( _maxSize ),
                pointerHeight: parseInt( _maxSize ),
                content: function() {
                    return "<a href='" + $( this ).data( 'href' ) + "' target='_blank'><img src='" + $( this ).data( 'largeSrc' ) + "' /></a>";
                },
                position: {
                    edge: 'bottom',
                    align: 'center',
                    within: _container,
                    collision: 'fit',   // don't exceed the container set with the `within` argument
                },
                fadeIn: 300,
                buttons: function() {},
                close: function() {},
                show: function( event, t ) {
                    t.pointer.fadeIn( 300 );
                    t.opened();
                },
                hide: function( event, t ) {
                    t.pointer.fadeOut( 300 );
                    t.closed();
                },
            });

            var _self    = this;

            // Wait for the image to load to check its dimension
            var img = new Image();
            img.onload = function() {
                var _pointer = $( _self ).aalPointer( 'get' );
                // Check portrait or landscape
                // if landscape
                if ( this.width > this.height ) {
                    _pointer.css( 'height', '' );
                } else {
                    var _heightBody = _body.height();
                    if ( _heightBody < this.height + 24 ) { // padding top: 12px + padding bottom: 12px
                        _pointer.css( 'height', ( _heightBody * 0.80 ) + 'px' );
                    }
                    _pointer.css( 'width', '' );
                }
                $( _self ).aalPointer( 'open' );
            };
            img.src = _srcImg;  // set the image

            // var _oPointer = $( this ).aalPointer( 'get' );


            // Handle toolitip closing
            $( this ).add( '.aal-image-preview-tooltip' ).on( 'mouseleave', function(){

                var _selfMouseLeave = this;
                // Set a timeout for the tooltip to close, allowing us to clear this trigger if the mouse comes back over
                var _timeoutId = setTimeout(function(){
                    $( _self ).aalPointer( 'close' );
                    $( _self ).off( 'mouseleave' );
                    $( _selfMouseLeave ).off( 'mouseleave' );
                    $( _self ).off( 'mouseenter' );
                    $( _selfMouseLeave ).off( 'mouseenter' );
                }, 200 );
                $( _self ).data( 'timeoutId', _timeoutId );

            } );
            $( this ).add( '.aal-image-preview-tooltip' ).on( 'mouseenter', function(){
                clearTimeout( $( _self ).data('timeoutId' ) );
            });

        });
    }

}(jQuery));