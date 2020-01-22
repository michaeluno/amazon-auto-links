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
 * Provides methods to extract and construct category list of the given page.
 *
 * @sicne       3.5.7
 * @since       3.9.1   No longer uses PHP Simple DOM Parser.
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList extends AmazonAutoLinks_Form_CategorySelect__Utility {

    /**
     * @var string
     */
    private $___sPageURL   = '';    // used in an extended class
    /**
     * @var DOMDocument
     */
    private $___oDoc;

    protected $_sSelector = 'zg_browseRoot';

    /**
     *
     * @param   DOMDocument $oDoc
     * @since   3.5.7
     * @since   3.9.1   No longer uses PHP Simple DOM Parser
     */
    public function __construct( DOMDocument $oDoc, $sPageURL ) {

        $this->___sPageURL   = $sPageURL;
        $this->___oDoc       = $oDoc;
    }

    /**
     * @since   3.5.7
     * @return  string
     */
    public function get() {
        return $this->_getCategoryList( $this->___oDoc, $this->___sPageURL );
    }
        /**
         * Generates the HTML fragment output of the node tree list.
         *
         * @since           2.0.0
         * @since           3.5.7       Moved from `AmazonAutoLinks_Form_CategorySelect`.
         * @since           3.9.1       No longer uses PHP Simple DOM Parser
         * @return          string
         */
        protected function _getCategoryList( DOMDocument $oDoc, $sPageURL ) {

            $_oNodeBrowseRoot = $oDoc->getElementById( $this->_sSelector );
            if ( null === $_oNodeBrowseRoot ) {
                return '';
            }
            $this->_setHrefs( $_oNodeBrowseRoot, $sPageURL );
            $_sHTMLFragment = $oDoc->saveXml( $_oNodeBrowseRoot, LIBXML_NOEMPTYTAG );
            $_sHTMLFragment = preg_replace( '/(?<=>)\s+|\s+(?=<)/', '', $_sHTMLFragment  );
            return $_sHTMLFragment; // the sidebar html code

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
            protected function _setHrefs( DOMElement $oNode, $sPageURL ) {

                $_aURLParts = parse_url( $sPageURL );
                $_sDomain   = $_aURLParts[ 'scheme' ] . '://' . $_aURLParts[ 'host' ];
                foreach( $oNode->getElementsByTagName( 'a' ) as $_nodeA ) {
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