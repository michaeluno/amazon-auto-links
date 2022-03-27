/**
 * @name:    Button Preview Framed Page
 * @version: 1.1.3
 */
(function () {

  'use strict';

  // Verify if browser is supported
  if ( document.querySelector ) {
    if ( ! window.addEventListener ) {
      return;
    }
  }

  var lastHeight;
  var lastWidth;

  // Add an event listener for a custom event
  document.addEventListener( 'ReloadButtonPreview', function( e ) {

    // console.log( 'Framed event:', e );

    // Tell the parent frame about the dimensions of the document.
    var _previewInfo = getPreviewInformation();
    if ( lastHeight === _previewInfo.height && lastWidth === _previewInfo.width ) {
      return;
    }
    lastHeight = _previewInfo.height;
    lastWidth  = _previewInfo.width;
    parent.postMessage( {
			message: 'height',
			height: _previewInfo.height,
      width: _previewInfo.width,
      id: _previewInfo.id,
			nonce: getParameterByName( 'nonce' )
    }, location.protocol + '//' + location.host );

  });

  /**
   * Listen fo post-message from parent windows
   * For parent frames, send a message with data with `event` key with a value of a event name to trigger on this framed page, such as `ReloadButtonPreview`.
   */
  window.addEventListener('message', function(event) {

    if ( event.origin !== location.protocol + '//' + location.host ) {
      return;
    }
    if ( getParameterByName( 'nonce' ) !== event.data.nonce ) {
      return;
    }
    if ( 'undefined' === typeof event.data.event ) {
      return;
    }
    dispatchEvent( event.data.event );

  }, false );

  /**
   * Fires an event in an old fashioned way to support older b. 
   * @see   https://stackoverflow.com/a/2490876 
   * @param eventName
   */
  function dispatchEvent( eventName ) {
    var _element = document;
    var _e; // The custom _e that will be created
    if ( document.createEvent ) {
        _e = document.createEvent( 'HTMLEvents' );
        _e.initEvent( eventName , true, true );
        _e._eName = eventName;
        _element.dispatchEvent( _e );
    } else {
        _e = document.createEventObject();
        _e._eName = eventName;
        _e._eType = eventName;
        _element.fireEvent( 'on' + _e._eType, _e );
    }
  }

  window.addEventListener( 'DOMContentLoaded', function ( e ) {
    setParentButtonPreviewIframeStyle();
  } );

  /**
   * If the parent window has the aalSetButtonPreviewIframeStyle() global function, call it.
   * Also, send a window message with the proportional information of the current document.
   */
  function setParentButtonPreviewIframeStyle() {
    if ( 'undefined' === typeof parent.aalSetButtonPreviewIframeStyle ) {
      return;
    }
    var _data = getPreviewInformation();
    window.parent.aalSetButtonPreviewIframeStyle( _data.width, _data.height, _data.id );
  }

  function getPreviewInformation() {
    var _oBodies   = document.getElementsByTagName( 'body' );
    var _oBody     = _oBodies.length ? _oBodies[ 0 ] : null;
    var _oButton   = document.getElementById( 'preview-button' );
    var _iWidth    = 0, _iHeight   = 0, _iButtonID = 0;
    if ( 'undefined' !== typeof _oBody && null !== _oBody ) {
      // There is a case that document height gets too long viewed in iframe (rare case), so using body.
      // document.body.getBoundingClientRect().height
      _iHeight   = _oBody.offsetHeight;
    }
    if ( 'undefined' !== typeof _oButton && null !== _oButton ) {
      _iWidth    = _oButton.offsetWidth;
      _iButtonID = _oButton.getAttribute( 'data-button-id' );
    }
    return {
      height: Math.ceil( _iHeight ),
      width: Math.ceil( _iWidth ),
      id: _iButtonID
    };
  }

  /**
   * @see     https://stackoverflow.com/a/901144
   * @param   name
   * @param   url
   * @returns {string|null}
   */
  function getParameterByName( name, url = window.location.href ) {
    name = name.replace( /[\[\]]/g, '\\$&' );
    var regex = new RegExp( '[?&]' + name + '(=([^&#]*)|&|#|$)' ),
      results = regex.exec( url );
    if ( ! results ) return null;
    if ( ! results[ 2 ] ) return '';
    return decodeURIComponent( results[ 2 ].replace( /\+/g, ' ' ) );
  }

})();