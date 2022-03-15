/**
 * @name:    Button Preview Framed Page
 * @version: 1.1.0
 */
window.addEventListener( 'DOMContentLoaded', function ( e ) {
  setParentButtonPreviewIframeStyle();
} );

function setParentButtonPreviewIframeStyle() {
  var _oBodies   = document.getElementsByTagName( 'body' );
  var _oBody     = _oBodies.length ? _oBodies[ 0 ] : null;
  var _oButton   = document.getElementById( 'preview-button' );
  var _iWidth    = 0;
  var _iHeight   = 0;
  var _iButtonID = 0;
  if ( 'undefined' !== typeof _oBody && null !== _oBody ) {
    _iHeight   = _oBody.offsetHeight;
  }
  if ( 'undefined' !== typeof _oButton && null !== _oButton ) {
    _iWidth    = _oButton.offsetWidth;
    _iButtonID = _oButton.getAttribute( 'data-button-id' );
  }
  if ( 'undefined' !== typeof parent.aalSetButtonPreviewIframeStyle ) {
    window.parent.aalSetButtonPreviewIframeStyle( _iWidth, _iHeight, _iButtonID );
  }
}