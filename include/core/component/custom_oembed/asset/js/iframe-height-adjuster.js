/**
 * Overwrite/bypass <iframe></iframe> height limit imposed by Wordpress
 * Original idea from bypass-iframe-height-limit plugin by Justin Carboneau
 * Adapted from original /wp-includes/js/wp-embed.js
 * @see https://medium.com/@wlarch/overwrite-and-bypass-wordpress-iframe-height-dimension-limit-using-javascript-9d5035c89e37
 */
(function(window, document) {
    'use strict';

    var supportedBrowser = false;

    // Verify if browser is supported
    if (document.querySelector) {
        if (window.addEventListener) {
            supportedBrowser = true;
        }
    }

    /** @namespace aalEmbed */
    window.aalEmbed = window.aalEmbed || {};

    if (!!window.aalEmbed.OverwriteIframeHeightLimit) {
        return;
    }

    window.aalEmbed.OverwriteIframeHeightLimit = function(e) {
		var data = e.data;

		// Check if all needed data is provided
        if (!(data.secret || data.message || data.value)) {
            return;
        }

		// Check if data secret is alphanumeric
        if (/[^a-zA-Z0-9]/.test(data.secret)) {
            return;
        }

		// Select all iframes
        var iframes = document.querySelectorAll('iframe[data-secret="' + data.secret + '"]'),
            i, source;

        for (i = 0; i < iframes.length; i++) {
            source = iframes[i];

            if (e.source !== source.contentWindow) {
                continue;
            }

            if ('height' === data.message) {
                // wp-embed.js clears any added inline styls, that's why we need to create a style element
                var styleElem = document.getElementById('aalEmbed-style-' + source.getAttribute('data-secret'));
                var css = 'iframe.wp-embedded-content.aal-embed[data-secret="' + source.getAttribute('data-secret') +
                    '"] { height: ' + parseInt(data.value, 10) + 'px; }';

                if (styleElem) {
                    styleElem.innerHTML = css;
                } else {
                    var head = document.head || document.getElementsByTagName('head')[0],
                        style = document.createElement('style');

                    style.type = 'text/css';
                    style.id = 'aalEmbed-style-' + source.getAttribute('data-secret');
                    style.appendChild(document.createTextNode(css));

                    head.appendChild(style);
                }
            }

        }
    };

    if (supportedBrowser) {
        window.addEventListener('message', window.aalEmbed.OverwriteIframeHeightLimit, false);
    } else {
        console.log('Wordpress <iframe> limit is still present because the browser is not supported.');
    	console.log('For more information : https://medium.com/@wlarch/overwrite-and-bypass-wordpress-iframe-height-dimension-limit-using-javascript-9d5035c89e37');
    }
})(window, document);