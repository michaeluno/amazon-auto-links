/**
 * @name:    Button Preview Framed Page
 * @version: 1.1.0
 */
window.addEventListener( 'DOMContentLoaded', function ( e ) {
  setParentButtonPreviewIframeStyle();
} );

function setParentButtonPreviewIframeStyle() {
  var _oButton   = document.getElementById( "preview-button" );
  var _iWidth    = 0;
  var _iHeight   = 0;
  var _iButtonID = 0;
  if ( 'undefined' !== typeof _oButton && null !== _oButton ) {
    _iWidth    = _oButton.offsetWidth;
    _iHeight   = _oButton.offsetHeight;
    _iButtonID = _oButton.getAttribute( 'data-button-id' );
  }
  if ( 'undefined' !== typeof parent.aalSetButtonPreviewIframeStyle ) {
    window.parent.aalSetButtonPreviewIframeStyle( _iWidth, _iHeight, _iButtonID );
  }
}