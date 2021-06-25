/**
 * @name Geo-resolver
 * @version 1.0.0
 */
(function($){

    var localStorageKey = 'wpaal';

    /**
     * @var aalGeoResolver
     */
    $( document ).ready( function() {

        if ( 'undefined' === typeof aalGeoResolver ) {
            return;
        }
        debugLog( 'aalGeoResolver', aalGeoResolver );

        convertAmazonLinksByGeolocation( getJSONObjectCloned( aalGeoResolver.apiProviders ) );

    });

    $( 'body' ).on( 'aal_ajax_loaded_unit', function( event ) {
        convertAmazonLinksByGeolocation( getJSONObjectCloned( aalGeoResolver.apiProviders ) );
    } );

    var ajaxAPIHandlers = {
        '__base'    : {
            dataType: 'json',
            success: function ( response ) {
                debugLog( 'response', response );
                var _sAmazonLocale = this.getAmazonLocale( response );
                setLocalStorageTTL( localStorageKey, { locale: _sAmazonLocale, time: getTimestampInSeconds() } );
                convertLinks( _sAmazonLocale );
            },
            countryCodeKey: 'countryCode',  // default
            getAmazonLocale: function( response ) {
                return 'undefined' === typeof response[ this.countryCodeKey ]
                    ? ''
                    : response[ this.countryCodeKey ];
            }
        },
        'cloudflare': {
            dataType: 'text',
            /**
             * The `text` dataType does not have response properties but the passed response parameter just contains a plain text value.
             * @param response
             */
            success: function ( response ) {
                debugLog( 'response', response );

                // Parse response
                var _data  = {};
                var _aText = response.split( '\n' );
                for( var i = 0; i < _aText.length; i++ ){
                    var _aKeyValue = _aText[ i ].split( '=' );
                    if ( _aKeyValue[ 0 ] && ( 'undefined' !== typeof _aKeyValue[ 1 ] ) ) {
                        _data[ _aKeyValue[ 0 ] ] = _aKeyValue[ 1 ];
                    }
                }
                if ( ! _data.loc ) {
                    return;
                }

                setLocalStorageTTL( localStorageKey, { locale: _data.loc, time: getTimestampInSeconds() } );
                convertLinks( _data.loc );

            },
        }
    }

    /**
     *
     * @param aAPIProviders
     */
    function convertAmazonLinksByGeolocation( aAPIProviders ) {

        // Retrieve the cache from storage
        var localCache = getLocalStorageTTL( localStorageKey, 86400 * 7 ); // @todo set it back to 86400 * 7
        debugLog( 'Stored cache:', localCache );
        if ( localCache && 'undefined' !== typeof localCache.locale ) {
            convertLinks( localCache.locale );
            return;
        }
        debugLog( 'Not using cache' );

        // Extract a random provider
        var _sProviderKey = getRandomPropertyKey( aAPIProviders );
        var _aProvider    = aAPIProviders[ _sProviderKey ];
        delete aAPIProviders[ _sProviderKey ];

        if ( ! _aProvider ) {
            debugLog( 'No more APIs' );
            return;
        }

        debugLog( 'API Provider:', _aProvider );

        // Set up an Ajax handler
        var _aAjaxHandler = {
            'url': _aProvider.endpoint,
            'countryCodeKey': _aProvider.countryCodeKey
        };
        var _aAjaxHandlerByProvider = ajaxAPIHandlers.hasOwnProperty( _sProviderKey ) ? ajaxAPIHandlers[ _sProviderKey ] : {};
        _aAjaxHandler = $.extend( {}, ajaxAPIHandlers.__base, _aAjaxHandlerByProvider, _aAjaxHandler );

        // Perform an Ajax request
        $.ajax( _aAjaxHandler )
            .error( function( jqXHR, textStatus, errorThrown ) {
                debugLog( 'response error', jqXHR );
                convertAmazonLinksByGeolocation( aAPIProviders ); // recursive calls
            });

    }

    function getRandomPropertyKey(obj) {
        var keys = Object.keys(obj);
        return keys[ keys.length * Math.random() << 0 ];
    }

    /**
     * Convert Amazon product links to the ones belonging to the store of the visitor's location.
     *
     * @param sLocaleCode      A Amazon locale code
     * @param sSelector        jQuery selector
     */
    function convertLinks( sLocaleCode, sSelector ) {

        // For country codes,
        // @see https://stackoverflow.com/questions/23963979/how-would-i-detect-all-european-countries-with-cloudflare-geolocation
        // @see https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#GB
        var _oLocale = aalGeoResolver.availableLocales.hasOwnProperty( sLocaleCode )
            ? aalGeoResolver.availableLocales[ sLocaleCode ]
            : aalGeoResolver.availableLocales[ aalGeoResolver.defaultLocale ];

        var bNonPluginLinks = aalGeoResolver.non_plugin_links;  // Whether to convert non-plugin links
        var sQueryKey       = aalGeoResolver.queryKey;          // The plugin specific query key that is used for Amazon product links

        debugLog( 'Detected locale', sLocaleCode, _oLocale );

        var _sLocaleDomain  = _oLocale.domain.replace( /^www\./, '' );
        var _sSelector      = sSelector
            ? sSelector
            : bNonPluginLinks ? 'a' : '.amazon-auto-links a';
        debugLog( 'Target Selector', _sSelector );

        var _oHrefs         = $( _sSelector )
          .filter( function() {

            if ( ! this.href ) {
                return false; // drop
            }

            // If the detected locale and the parsing URL is the same locale, drop it
            if ( -1 !== this.href.indexOf( _sLocaleDomain ) ) {
                return false;
            }

            if ( this.href.match( /https?:\/\/(\w+\.)?amazon\./ ) ) {
                return true;
            }
            var _regexPattern = sQueryKey + '\=.+locale\=';
            var _regex = new RegExp( _regexPattern );
            if ( this.href.match( _regex ) ) {
                return true;
            }
            return false;

          });

        var _aUnmodified = [];
        var _aModified = [];
        _oHrefs.each( function( index ) {

            // Generic Amazon links
            var _match = this.href.match( /.+\/([A-Z0-9]{10})/ );
            if ( _match ) {
                this.href = 'undefined' !== typeof _match[ 1 ]
                    ? _oLocale.searchURL + _match[ 1 ]
                    : this.href;
                _aModified.push( this.href );
                return true;
            }
            // The plugin link style
            var _regexPattern = sQueryKey + '\=(.+?)&locale\=(.+)';
            var _regex = new RegExp( _regexPattern );
            _match = this.href.match( _regex );
            if ( _match ) {
                var _sURL = getQueryStringAndHashStrippedFromPath( this.href );
                this.href = _sURL + '?' + sQueryKey + '=' + _match[ 1 ] + '&locale=' + _oLocale.locale + '&tag=' + _oLocale.associateID + '&search=1';
                _aModified.push( this.href );
                return true;
            }
            // Add-to-cart Button links
            if ( -1 !== this.href.indexOf( 'aws/cart/add.html' ) ) {
                this.href = this.href.replace( /^(https?:\/\/)(www\.amazon\..+?)(\W.+)/, '$1' + _oLocale.domain + '$3');
                _aModified.push( this.href );
                return true;
            }

            _aUnmodified.push( this.href );

        } );
        debugLog( 'hrefs found:', _oHrefs.length, 'modified: ', _aModified.length );
        debugLog( _aUnmodified );

    }

    /**
     *
     * @param url
     * @returns {*}
     */
    function getQueryStringAndHashStrippedFromPath(url) {
      return url.split("?")[0].split("#")[0];
    }

    function getLocalStorage( key ) {
        var localCache = localStorage.getItem( localStorageKey );
        if ( localCache ) {
            return JSON.parse( localCache );
        }
        return {};
    }

    /**
     * @deprecated Use setLocalStorageTTL()
     * @param key
     * @param value
     */
    function setLocalStorage( key, value ) {
        debugLog( 'setting local storage:', key, value );
         localStorage.setItem( key, JSON.stringify( value ) );
    }


    function setLocalStorageTTL( key, value ) {
        debugLog( 'setting local storage:', key, { value: value, time: getTimestampInSeconds() } );
        localStorage.setItem( key, JSON.stringify( { value: value, time: getTimestampInSeconds() } ) );
    }

    /**
     * Retrieves local storage data stores wis setLocalStorageTTL().
     * @param sKey
     * @param iLifespan The storage lifespan in seconds
     */
    function getLocalStorageTTL( sKey, iLifespan ) {
        var _value = getLocalStorage( sKey );
        if ( 'undefined' === typeof _value.time || 'undefined' === typeof _value.value ) {
            return {};
        }

        // Check expiry
        var _iStoredTime = parseInt( _value.time );
        _iStoredTime = _iStoredTime ? _iStoredTime : 0;
        if ( _iStoredTime + iLifespan < getTimestampInSeconds() ) {
            debugLog( 'Local storage cache timed out', _iStoredTime, iLifespan, _iStoredTime + iLifespan, getTimestampInSeconds() );
            return {};
        }
        return _value.value;
    }

    /**
     *
     * @returns {number}
     */
    function getTimestampInSeconds() {
        return Math.floor(Date.now() / 1000);
    }

    function debugLog( ...args ) {
        if ( ! aalGeoResolver.debugMode ) {
            return;
        }
        console.log( 'AAL Debug(Geotargeting):', ...args );
    }

    function getJSONObjectCloned( jsonObj ) {
        return JSON.parse( JSON.stringify( jsonObj ) );
    }

}(jQuery));
