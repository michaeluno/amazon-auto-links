/**
 * A light version of wp-embed-template.js which causes clicks not working
 *
 * @see wp-includes/js/wp-embed-template.js
 * @name WP Embed Template Lite
 * @version 1.2.1
 */
(function ( window, document ) {
	'use strict';

	var supportedBrowser = ( document.querySelector && window.addEventListener ),
		loaded = false,
		secret,
		secretTimeout,
		resizing;

	function sendEmbedMessage( message, value ) {

		var _window = window;
		// Climb up the iframe tree and send the message if the parent frame is either of the plugin's embedded iframe or the Gutenberg's block iframe.
		// As in the Gutenberg editor, embedded contents are displayed in nested iframes.
		while ( isWindow( _window ) && isWithinIframe( _window ) ) {
			var _className = _window.frameElement.getAttribute( 'class' );
			if ( ! hasSubstring( _className, [ 'aal-embed', 'components-sandbox', 'aal-unit-preview-frame' ] ) ) {
				continue;
			}
			_window.parent.postMessage( {
				message: message,
				value: value,
				secret: secret
			}, '*' );
			_window = _window.parent;
		}

	}

	function isWindow( thing ) {
		return thing && thing.document && thing.location && thing.alert && thing.setInterval;
	}

	function isWithinIframe( _window ) {
		return _window.frameElement && 'IFRAME' === _window.frameElement.nodeName;
	}

	function hasSubstring( string, substrings ) {
		return substrings.some( function(v) { return string.indexOf(v) >= 0; } );
	}

	function onLoad() {
		if ( loaded ) {
			return;
		}
		loaded = true;

		var thumbnail = document.querySelector( '.amazon-auto-links img' ),
			images = document.querySelector( '.sub-images img' );

		if ( window.self === window.top ) {
			return;
		}

		/**
		 * Send this document's height to the parent (embedding) site.
		 */
		sendEmbedMessage( 'height', Math.ceil( document.body.getBoundingClientRect().height ) );

		// Send the document's height again after thumbnails and image sets have been loaded.
		if ( thumbnail ) {
			thumbnail.addEventListener( 'load', function() {
				sendEmbedMessage( 'height', Math.ceil( document.body.getBoundingClientRect().height ) );
			} );
		}

		if ( images ) {
			images.addEventListener( 'load', function() {
				sendEmbedMessage( 'height', Math.ceil( document.body.getBoundingClientRect().height ) );
			} );
		}

	}

	/**
	 * Iframe resize handler.
	 * @remark	Sending the height more than 1000px gets converted to 1000px.
	 * @see wp-includes/js/wp-embed.js window.wp.receiveEmbedMessage()
	 */
	function onResize() {
		if ( window.self === window.top ) {
			return;
		}

		clearTimeout( resizing );

		resizing = setTimeout( function () {
			sendEmbedMessage( 'height', Math.ceil( document.body.getBoundingClientRect().height ) );
		}, 100 );
	}

	/**
	 * Re-get the secret when it was added later on.
	 */
	function getSecret() {
		if ( window.self === window.top || !!secret ) {
			return;
		}

		secret = window.location.hash.replace( /.*secret=([\d\w]{10}).*/, '$1' );

		clearTimeout( secretTimeout );

		secretTimeout = setTimeout( function () {
			getSecret();
		}, 100 );
	}

	if ( supportedBrowser ) {
		getSecret();
		document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/, '' ) + ' js';
		document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/, '' ) + ' js';
		document.addEventListener( 'DOMContentLoaded', onLoad, false );
		window.addEventListener( 'load', onLoad, false );
		window.addEventListener( 'resize', onResize, false );
	}
})( window, document );
