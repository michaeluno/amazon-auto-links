<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Provides methods to extract the feed (RSS2) url of the specified page with a Simple DOM object.
 *
 * @sicne       3.6.0
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
     * @since   3.6.0
     */
    public function get() {
        return $this->___getCategoryFeedURL( $this->___oSimpleDOM, $this->___sPageURL );
    }
        /**
         * Extracts the category feed url from the given DOM object.
         *
         * @since       2.0.0
         * @since       3.6.0       Moved from `AmazonAutoLinks_Form_CategorySelect`.
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
             * @since       3.6.0       Moved from `AmazonAutoLinks_Form_CategorySelect`.
             */
            private function ___getCategoryFeedURLConstructed( $sPageURL ) {

                // Validate the URL
                if( false === filter_var( $sPageURL, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) ){
                    return '';
                }

                // At this point, it is a URL.
                $_sDomain         = $this->___getSchemeAndDomainFromURL( $sPageURL );
                $_sCategoryNodeID = $this->___getBrowseNodeIDFromURL( $sPageURL );
                return $_sDomain . '/gp/rss/bestsellers/' . $_sCategoryNodeID . '/';

            }
                /**
                 * Extracts a browse node ID from a given URL.
                 *
                 * Browse node IDs are positive integers that uniquely identify product sets.
                 * e.g. Literature & Fiction: (17), Medicine: (13996), Mystery & Thrillers: (18), Nonfiction: (53), Outdoors & Nature: (290060).
                 *
                 * e.g. https://www.amazon.com/gp/rss/bestsellers/3400291/ -> 3400291
                 * @since       3.5.4
                 * @since       3.6.0     Moved from `AmazonAutoLinks_Form_CategorySelect`.
                 * @param       string    $sPageURL
                 * @return      string
                 * @see         https://docs.aws.amazon.com/AWSECommerceService/latest/DG/BrowseNodeIDValues.html
                 */
                private function ___getBrowseNodeIDFromURL( $sPageURL ) {
                    return preg_replace('/.+\/(\d+)\//', '$1', $sPageURL );
                }
                /**
                 * @param   string  $sURL
                 * @since   3.5.4
                 * @since   3.6.0       Moved from `AmazonAutoLinks_Form_CategorySelect`.
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
             * @since   3.6.0   Moved from `AmazonAutoLinks_Form_CategorySelect`.
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