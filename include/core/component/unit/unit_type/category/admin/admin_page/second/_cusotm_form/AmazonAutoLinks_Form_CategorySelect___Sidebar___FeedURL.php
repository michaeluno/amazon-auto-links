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
 * Provides methods to extract the feed (RSS2) url of the specified page with a Simple DOM object.
 *
 * @sicne       3.5.7
 * @deprecated  3.9.1   Amazon sites no longer provide feeds for best seller items
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___FeedURL {

    private $___oSimpleDOM = null;
    private $___sPageURL   = '';

    public function __construct( $oSimpleDOM, $sPageURL ) {

        // Properties
        $this->___oSimpleDOM = $oSimpleDOM;
        $this->___sPageURL   = $sPageURL;

    }

    /**
     * Extracts the RSS feed URL of the given page.
     * @return  string
     * @since   3.5.7
     */
    public function get() {
        return $this->___getCategoryFeedURL( $this->___oSimpleDOM, $this->___sPageURL );
    }
        /**
         * Extracts the category feed url from the given DOM object.
         *
         * @since       2.0.0
         * @since       3.5.7       Moved from `AmazonAutoLinks_Form_CategorySelect`.
         * @return      string      The category RSS feed URL
         */
        private function ___getCategoryFeedURL( $oSimpleDOM, $sPageURL ) {
            $_sFeedURL = $this->___getCategoryFeedURLExtracted( $oSimpleDOM );
            return $_sFeedURL
                ? $_sFeedURL
                : $this->___getCategoryFeedURLConstructed( $sPageURL );
        }
            /**
             * @param   string    $sPageURL
             * @return  string    The category feed URL
             * @since       3.5.4       Amazon store site (.com) has changed the web design and the new layout does not contain the RSS feed subscription element.                  *
             * So the URL must be constructed using the category node ID. The fact that the feed is no longer displayed in the new desing indicates
             * that the bestseller feeds may be deprecated in the near future.
             * @since       3.5.7       Moved from `AmazonAutoLinks_Form_CategorySelect`.
             */
            private function ___getCategoryFeedURLConstructed( $sPageURL ) {

                // Validate the URL
                if( false === filter_var( $sPageURL, FILTER_VALIDATE_URL ) ){
                    return '';
                }

                $_sDomain         = $this->___getSchemeAndDomainFromURL( $sPageURL );

                // For cases that have a node id in the URL.
                // e.g. https://www.amazon.com/gp/bestsellers/amazon-devices/2496748051/ref=zg_bs_nav_1_amazon-devices
                // -> https://www.amazon.com/gp/bestsellers/2496748051
                $_sCategoryNodeID = $this->___getBrowseNodeIDFromURL( $sPageURL );
                if ( $_sCategoryNodeID ) {
                    return $_sDomain . '/gp/rss/bestsellers/' . $_sCategoryNodeID . '/';
                }

                // For cases that have `/gp/` in the url,
                // e.g. https://www.amazon.com.au/gp/bestsellers/digital-text/2496751051
                // -> https://www.amazon.com.au/gp/rss/bestsellers/digital-text/2496751051
                if ( false !== strpos( $sPageURL, '/gp/' ) ) {
                    return str_replace( '/gp/','/gp/rss/',$sPageURL );
                }

                // For other cases, the first directory after the domain needs to be changed to `gp/rss/bestsellers`.
                // e.g. https://www.amazon.com/Best-Sellers-Baby/zgbs/baby-products
                // -> https://www.amazon.com/gp/rss/bestsellers/zgbs/baby-products/
                $_sURLPath = parse_url( $sPageURL, PHP_URL_PATH );

                /// Remove a substring after `ref=`
                /// e.g. https://www.amazon.com/Best-Sellers/zgbs/ref=zg_bsnr_tab
                /// -> https://www.amazon.com/Best-Sellers/zgbs/
                $_aURLPath = explode( 'ref=', $_sURLPath, 2 );
                $_sURLPath = $_aURLPath[ 0 ];
                $_sURLPath = ltrim( $_sURLPath, '/' );  // remove the preceding forward slash
                $_aURLPath = explode( '/', $_sURLPath );
                unset( $_aURLPath[ 0 ] );   // remove the first occurrence
                $_sURLPath = implode( $_aURLPath, '/' );

                // Passing even an invalid RSS URL so that a breadcrumb is generated anyway.
                return $_sDomain . '/gp/rss/bestsellers/' . $_sURLPath;

            }
                /**
                 * Extracts a browse node ID from a given URL.
                 *
                 * Browse node IDs are positive integers that uniquely identify product sets.
                 * e.g. Literature & Fiction: (17), Medicine: (13996), Mystery & Thrillers: (18), Nonfiction: (53), Outdoors & Nature: (290060).
                 *
                 * e.g. https://www.amazon.com/gp/rss/bestsellers/3400291/ -> 3400291
                 * @since       3.5.4
                 * @since       3.5.7     Moved from `AmazonAutoLinks_Form_CategorySelect`.
                 * @param       string    $sPageURL
                 * @return      string
                 * @see         https://docs.aws.amazon.com/AWSECommerceService/latest/DG/BrowseNodeIDValues.html
                 */
                private function ___getBrowseNodeIDFromURL( $sPageURL ) {
                    $_sNodeID = preg_replace('/.+\/(\d+)\//', '$1', $sPageURL );
                    return is_numeric( $_sNodeID )
                        ? $_sNodeID
                        : '';
                }
                /**
                 * @param   string  $sURL       Without a trailing slash.
                 * @since   3.5.4
                 * @since   3.5.7       Moved from `AmazonAutoLinks_Form_CategorySelect`.
                 * @return  string
                 * @see     https://stackoverflow.com/a/16027164
                 */
                private function ___getSchemeAndDomainFromURL( $sURL ) {
                    $_aPieces = parse_url( $sURL );
                    $_sDomain = isset( $_aPieces[ 'host' ] )
                        ? $_aPieces[ 'host' ]
                        : $_aPieces[ 'path' ];
                    if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $_sDomain, $_aRegs ) ) {
                        return $_aPieces[ 'scheme' ] . '://' . $_aRegs[ 'domain' ];
                    }
                    return '';
                }

            /**
             * Extracts the category feed url from the given DOM object.
             * @since   3.5.4   Moved from `_getCategoryFeedURL()`
             * @since   3.5.7   Moved from `AmazonAutoLinks_Form_CategorySelect`.
             */
            private function ___getCategoryFeedURLExtracted( $oSimpleDOM ) {

                $domRSSLinks = $oSimpleDOM->getElementById( 'zg_rssLinks' );
                if ( ! $domRSSLinks ) {

                    // the root category does not provide a rss link, so return silently
                    echo '<!-- ' . __METHOD__ . ': The zg_rssLinks ID element could not be found. -->';
                    return;

                }

                $nodeA2     = $domRSSLinks->getElementsByTagName( 'a', 1 ); // the second link.
                $sRSSLink   = $nodeA2->getAttribute( 'href' );
                $aURL       = explode( "ref=", $sRSSLink, 2 );
                return $aURL[ 0 ];

            }

}