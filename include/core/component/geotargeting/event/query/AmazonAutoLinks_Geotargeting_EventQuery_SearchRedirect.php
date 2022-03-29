<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Redirects the visitor to the store that matches the visitor's location.
 * @since   4.6.0
 */
class AmazonAutoLinks_Geotargeting_EventQuery_SearchRedirect {

    /**
     * Sets up properties and hooks.
     * @since 4.6.0
     */
    public function __construct() {
        add_filter( 'aal_filter_store_redirect_url', array( $this, 'replyToGetSearchResultURL' ), 10, 3 );
    }

    /**
     * @param  string $sURL
     * @param  string $sASIN
     * @param  array  $aGET
     * @return string
     * @since 4.6.0
     */
    public function replyToGetSearchResultURL( $sURL, $sASIN, $aGET ) {
        $aGET = $aGET + array( 'locale' => null, 'tag' => null );
        if ( isset( $aGET[ 'search' ] ) && $aGET[ 'search' ]  ) {
            $_oLocale = new AmazonAutoLinks_Locale( $aGET[ 'locale' ] );
            return $_oLocale->getMarketPlaceURL( '/s?tag=' . $aGET[ 'tag' ] . '&k=' . $sASIN );
        }
        return $sURL;
    }

}