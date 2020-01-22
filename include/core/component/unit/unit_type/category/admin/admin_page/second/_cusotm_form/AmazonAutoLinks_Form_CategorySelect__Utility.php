<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Provides shared methods for the category select form classes.
 *
 */
class AmazonAutoLinks_Form_CategorySelect__Utility extends AmazonAutoLinks_WPUtility {

    /**
     * Gets the current self-url. needs to exclude the query part
     * e.g. http://localhost/me.php?href=http://....  -> http://localhost/me.php
     * @return      string
     * @since       unknown
     * @since       3.5.7       Moved from `AmazonAutoLinks_Form_CategorySelect`.
     * @since       3.5.7       Renamed from from `_formatLinkURL()`.
     */
    protected function _getLinkURLFormatted( $sURL, $aQueries=array() ) {
        $_oEncrypt = new AmazonAutoLinks_Encrypt;
        return add_query_arg(
            array(
                'href' => $_oEncrypt->encode( $sURL ),
            ) + $aQueries + $_GET
            , admin_url( $GLOBALS[ 'pagenow' ] )
        );
    }

    // Deperecated methods
    /**
     *
     * @return  string
     * @deprecated  Not used at the moment
     */
    protected function _getRedirectDestination( $sURL ) {

        $k = curl_init( $sURL );
        curl_setopt( $k, CURLOPT_FOLLOWLOCATION, true ); // follow redirects
        curl_setopt( $k, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.7 ' .
            '(KHTML, like Gecko) Chrome/7.0.517.41 Safari/534.7'
        ); // imitate chrome
        curl_setopt( $k, CURLOPT_NOBODY, true ); // HEAD request only (faster)
        curl_setopt( $k, CURLOPT_RETURNTRANSFER, true ); // don't echo results
        curl_exec($k);
        $_sFinalURL = curl_getinfo( $k, CURLINFO_EFFECTIVE_URL ); // get last URL followed
        curl_close($k);
        return $_sFinalURL;

    }

    /**
     * @deprecated
     */
    protected function _removeLineFeeds( $sOutput ) {

        $sOutput    = str_replace( array( "\r\n", "\r" ), "\n", $sOutput );

        $aLines     = explode( "\n", $sOutput );
        $aNewLines  = array();
        foreach( $aLines as $i => $sLine ) {
            if( ! $this->isEmpty( $sLine ) ) {
                $aNewLines[] = trim( $sLine, '\t\n\r\0\x0B' );
            }
        }

        return implode( $aNewLines );

    }

    /**
     * @deprecated
     */
    protected function _modifyHref( $oDOM, $aQueries=array() ) {

        $aQueries   = ( array ) $aQueries;
        $oXpath     = new DOMXPath( $oDOM );     // since getElementByID constantly returned false for unknown reason, use xpath
        $domleftCol = $oXpath->query( "//*[@id='zg_browseRoot']" )->item( 0 );        // $domleftCol = $oDOM->getElementById('zg_browseRoot');
        if ( !$domleftCol ) {
            echo '<!-- ' . __( 'Categories not found. Please consult the plugin developer.', 'amazon-auto-links' ) . ' -->' . PHP_EOL;
            return false;
        }
        foreach( $oDOM->getElementsByTagName( 'a' ) as $nodeA ) {

            $sHref = $nodeA->getAttribute( 'href' );
            $nodeA->removeAttribute( 'href' );

            // sip the sing after 'ref=' in the url
            // e.g. http://amazon.com/ref=zg_bs_123/324-5242552 -> http://amazon.com
            $aURL  = explode( "ref=", $sHref, 2 );
            $sHref = $aURL[0];

            @$nodeA->setAttribute( 'href', $this->_getLinkURLFormatted( $sHref, $aQueries ) );

        }
        return true;

    }

    /**
     * Returns the language code of the specified Amazon store locale.
     *
     * Either ja, en, or uni is returned.
     *
     * @deprecated
     */
    protected function _getMBLanguage( $sLocale='US' ) {
        return isset( AmazonAutoLinks_Property::$aCategoryPageMBLanguages[ $sLocale ] )
                ? AmazonAutoLinks_Property::$aCategoryPageMBLanguages[ $sLocale ]
                : 'uni';
    }


}