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
 * Provides methods to extract and construct category list of the given page.
 *
 * @sicne       3.5.7
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList extends AmazonAutoLinks_Form_CategorySelect__Utility {

    private $___oSimpleDOM = null;
    private $___sPageURL   = '';    // used in an extended class

    protected $_sSelector = 'zg_browseRoot';

    /**
     *
     * @param   $_oSimpleDOM
     * @since   3.5.7
     */
    public function __construct( $_oSimpleDOM, $sPageURL ) {
        $this->___oSimpleDOM = $_oSimpleDOM;
        $this->___sPageURL   = $sPageURL;
    }

    /**
     * @since   3.5.7
     * @return  string
     */
    public function get() {
        return $this->_getCategoryList( $this->___oSimpleDOM, $this->___sPageURL );
    }
        /**
         * Generates the HTML output of the node tree list.
         *
         * @since           2.0.0
         * @since           3.5.7       Moved from `AmazonAutoLinks_Form_CategorySelect`.
         * @return          string
         */
        protected function _getCategoryList( $oSimpleDOM, $sPageURL ) {

            $_oNodeBrowseRoot = $oSimpleDOM->getElementById( $this->_sSelector );
            $this->_setHrefs( $_oNodeBrowseRoot, $sPageURL );
            return $_oNodeBrowseRoot->outertext; // the sidebar html code

        }
            /**
             * Converts href urls io a url with query which contains the original url.
             *
             * e.g. <a href="http://amazon.com/something"> -> <a href="localhost/me.php?href=http://amazon.com/something"
             * and the href value becomes base64 encoded.
             * @since       unknown
             * @since       3.5.7       Renamed from `modifyHref`.
             * @since       3.5.7       Moved from `AmazonAutoLinks_Form_CategorySelect`.
             */
            protected function _setHrefs( $oSimpleDOMNode, $sPageURL ) {

                $_aURLParts = parse_url( $sPageURL );
                $_sDomain   = $_aURLParts[ 'scheme' ] . '://' . $_aURLParts[ 'host' ];

                foreach( $oSimpleDOMNode->getElementsByTagName( 'a' ) as $_nodeA ) {

                    $_sHref = $_nodeA->getAttribute( 'href' );
                    $_sHref = $this->___getHrefSanitized( $_sHref, $_sDomain );
                    $_nodeA->setAttribute( 'href', $_sHref );

                }

            }
                /**
                 * @remark
                 * @since       3.5.7
                 * @return      string
                 */
                private function ___getHrefSanitized( $sHref, $sDomain ) {

                    // remove the substring after 'ref=' in the url
                    // e.g. http://amazon.com/ref=zg_bs_123/324-5242552 -> http://amazon.com
                    // e.g. amazon.com/ref=zg_bs_123/324-5242552 -> amazon.com
                    $_aURL  = explode( "ref=", $sHref, 2 );
                    $sHref  = $_aURL[ 0 ];

                    // There are cases that the href value is relative and absolute URL.
                    // relative: gp/top-sellers/digital-text/,
                    // absolute: https://amazon.com/gp/top-sellers/digital-text/
                    $_aURLParts = parse_url( $sHref );
                    $sHref      = isset( $_aURLParts[ 'scheme' ] ) && $_aURLParts[ 'scheme' ]
                        ? $sHref
                        : $sDomain . '/' . $sHref;   // add the domain

                    // Encrypt
                    return $this->_getLinkURLFormatted( $sHref, array() );

                }


}