/**
 * @name    Button Preview Frame Delay Loader
 * @version 1.0.1
 */
( function( $ ){

  if ( 'undefined' === typeof aalButtonPreviewFrameDelayLoader ) {
    return;
  }

  var _spinner = $( '<img>', {
    'alt': 'Loading...',
    'src': aalButtonPreviewFrameDelayLoader.spinnerURL,
    'style': 'vertical-align: middle; margin: 0 0.6em; display: inline-block;',
    'class': 'iframe-load-spinner'
  } );

  var jqFrames = $( '.iframe-button-preview-container iframe' );
  jqFrames.before( _spinner );
  jqFrames.hide();
  jqFrames.on( 'load', function(){

    $( this ).parent().find( '.iframe-load-spinner' ).remove(); // remove the spinner
    $( this ).fadeIn();

    // Adjust the size of the frame to center it
    var _frameContents = $( this ).contents();
    var _textContainer = _frameContents.find( '.amazon-auto-links-button .button-label' );
    var _width  = Math.ceil( _frameContents.find( '#preview-button' ).width() );
    var _height = Math.ceil( _frameContents.find( 'body' ).height() );
    $( this ).width( _width ).height( _height )
      .css( {
        'width':      _width + 'px',
        'height':     _height + 'px',
        'visibility': 'visible',
        'min-width':  '116px',
      } );

    // Load the next frame
    var _nextFrame = $( '.iframe-button-preview-container iframe:not([src])' ).first();
    _nextFrame.attr( 'src', _nextFrame.data( 'src' ) );
  } );
}( jQuery ) );