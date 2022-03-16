/**
 * @name    Button Preview Frame Delay Loader
 * @version 1.0.0
 */
( function( $ ){
  $( '.iframe-button-preview-container iframe' ).on( 'load', function(){
    var _nextFrame = $( '.iframe-button-preview-container iframe:not([src])' ).first();
    _nextFrame.attr( 'src', _nextFrame.data( 'src' ) );
  } );
}( jQuery ) );