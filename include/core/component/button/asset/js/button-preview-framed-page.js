window.addEventListener( 'DOMContentLoaded', function ( e ) {
  var _oButton = document.getElementById( "preview-button" );
  var _iWidth  = 0;
  var _iHeight = 0;
  if ( 'undefined' !== typeof _oButton && null !== _oButton ) {
    _iWidth    = _oButton.offsetWidth;
    _iHeight   = _oButton.offsetHeight;
  }
  if ( 'undefined' !== typeof parent.aalSetButtonPreviewIframeStyle ) {
    window.parent.aalSetButtonPreviewIframeStyle( _iWidth, _iHeight );
  }
} );