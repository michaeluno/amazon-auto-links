/**
 * Overwrite/bypass <iframe></iframe> height limit imposed by Wordpress
 * Original idea from bypass-iframe-height-limit plugin by Justin Carboneau
 * Adapted from original /wp-includes/js/wp-embed.js
 * @see     https://medium.com/@wlarch/overwrite-and-bypass-wordpress-iframe-height-dimension-limit-using-javascript-9d5035c89e37
 * @name    iframe Height Adjuster
 * @version 1.3.2
 * @remark  Modified by Michael Uno
 */
(function ( window, document ) {
  'use strict';

  var supportedBrowser = false;

  // Verify if browser is supported
  if ( document.querySelector ) {
    if ( window.addEventListener ) {
      supportedBrowser = true;
    }
  }

  /** @namespace aalEmbed */
  window.aalEmbed = window.aalEmbed || {};

  if ( !! window.aalEmbed.OverwriteIframeHeightLimit ) {
    return;
  }

  window.aalEmbed.OverwriteIframeHeightLimit = function ( e ) {
    var data = e.data;
    // Check if all needed data is provided
    if ( ! (data.secret || data.message || data.value) ) {
      return;
    }

    // Check if data secret is alphanumeric
    if ( /[^a-zA-Z0-9]/.test( data.secret ) ) {
      return;
    }

    if ( 'height' !== data.message ) {
      return;
    }

    // Select all iframes
    // var iframes = document.querySelectorAll( 'iframe[data-secret="' + data.secret + '"]' ),
    var iframes = document.querySelectorAll( 'iframe.components-sandbox, iframe[data-secret="' + data.secret + '"], iframe.aal-unit-preview-frame' ),
      i, _thisFrame;

    for ( i = 0; i < iframes.length; i++ ) {

      _thisFrame = iframes[ i ];
      if (
        e.source !== _thisFrame.contentWindow           // whether the message origin frame and the parsing iframe is the same
        && e.source.parent !== _thisFrame.contentWindow // whether the message origin frame's parent frame and the parsing iframe is the same (for Gutenberg blocks)
      ) {
        continue;
      }

      var _iHeight = parseInt( data.value, 10 );
      var _attrID = _thisFrame.getAttribute( 'id' );
      _attrID = _attrID ? _attrID : 'iframe-' + data.secret + '-' + i;

      var _eventAdjustIFrameHeight = new CustomEvent( 'receivedIframeMessage', {
        'detail': {
          'number': i,
          'height': _iHeight,
          'id': _attrID,
          'source': _thisFrame,
          'data': data,
          'isSandbox': e.source.parent === _thisFrame.contentWindow  // for Gutenberg blocks
        }
      } );
      window.dispatchEvent( _eventAdjustIFrameHeight );

    } // end for()

  };

  if ( supportedBrowser ) {
    window.addEventListener( 'message', window.aalEmbed.OverwriteIframeHeightLimit, false );
    window.addEventListener( 'receivedIframeMessage', debounceCallbackForEvent( adjustFrameHeight ) );
  } else {
    console.log( 'Wordpress <iframe> limit is still present because the browser is not supported.' );
    console.log( 'For more information : https://medium.com/@wlarch/overwrite-and-bypass-wordpress-iframe-height-dimension-limit-using-javascript-9d5035c89e37' );
  }

  function adjustFrameHeight( event ) {

    var _attrID  = event.detail.id;
    var _iHeight = event.detail.height;
    event.detail.source.setAttribute( 'id', _attrID );
    event.detail.source.setAttribute( 'height', _iHeight.toString() );
    event.detail.source.classList.add( 'aal-adjusted-height' );

    var _secret = event.detail.data.secret;
    var _css = '#' + _attrID + ' { height: ' + _iHeight + 'px; overflow: hidden;  } ';

    // For Gutenberg oEmbed blocks, set the height to the inner iframe
    if ( event.detail.isSandbox ) {
      event.detail.source.setAttribute( 'scrolling', 'no' );
      var _innerIframe = event.detail.source.contentWindow.document.querySelector( 'iframe[data-secret="' + _secret + '"]' );
      if ( null !== _innerIframe ) {
        _innerIframe.setAttribute( 'height', _iHeight.toString() );
      }
    }

    // Now add stylesheet
    // wp-embed.js clears any added inline styles, that's why we need to create a style element
    var _styleElem = document.getElementById( 'aal-embed-style-' + _secret + '-' + event.detail.number );
    if ( _styleElem ) {
      _styleElem.innerHTML = _css;
      return;
    }
    var head = document.head || document.getElementsByTagName( 'head' )[ 0 ],
      style = document.createElement( 'style' );

    style.type = 'text/css';
    style.id = 'aal-embed-style-' + _secret + '-' + event.detail.number;
    style.appendChild( document.createTextNode( _css ) );
    head.appendChild( style );

  }

  /**
   * Cancels previous callback within the set time span per individual frame basis.
   * @param func
   * @param timeout
   * @returns {function(): void}
   * @remark the first parameter of the callback function must be an event object
   */
  function debounceCallbackForEvent( func, timeout ) {
    var timeout = timeout || 200;
    var _timeoutIDs = {};    // key by frame number
    var _heightsByIDs = {};  // key by frame number
    return function () {
      var scope = this, args = arguments;
      var _event = arguments[ 0 ];

      // If the height isn't changing, do not call as it causes flickering
      if ( _event.detail.height === _heightsByIDs[ _event.detail.number ] ) {
        return;
      }

      // Cancel the previous call
      clearTimeout( _timeoutIDs[ _event.detail.number ] );

      // Schedule a call and store this height and this timeout ID.
      _heightsByIDs[ _event.detail.number ] = _event.detail.height;
      _timeoutIDs[ _event.detail.number ] = setTimeout( function () {
        func.apply( scope, Array.prototype.slice.call( args ) );
      }, timeout );
    }
  }

})( window, document );