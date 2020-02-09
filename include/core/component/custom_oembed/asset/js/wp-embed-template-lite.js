/**
 * A light version of wp-embed-template.js which causes clicks not working
 *
 * @see wp-includes/js/wp-embed-template.js
 */
(function ( window, document ) {
	'use strict';

	var supportedBrowser = ( document.querySelector && window.addEventListener ),
		loaded = false,
		secret,
		secretTimeout,
		resizing;

	function sendEmbedMessage( message, value ) {
		window.parent.postMessage( {
			message: message,
			value: value,
			secret: secret
		}, '*' );
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
