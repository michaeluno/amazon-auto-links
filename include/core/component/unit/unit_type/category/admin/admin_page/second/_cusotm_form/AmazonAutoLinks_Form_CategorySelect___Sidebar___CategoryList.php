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
 * @sicne       3.6.0
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList extends AmazonAutoLinks_Form_CategorySelect__Utility {

    private $___oSimpleDOM = null;

    /**
     *
     * @param   $_oSimpleDOM
     * @since   3.6.0
     */
    public function __construct( $_oSimpleDOM ) {
        $this->___oSimpleDOM = $_oSimpleDOM;
    }

    /**
     * @since   3.6.0
     * @return  string
     */
    public function get() {
        return $this->___getCategoryList( $this->___oSimpleDOM );
    }
        /**
         * Generates the HTML output of the node tree list.
         *
         * @since           2.0.0
         * @since           3.6.0       Moved from `AmazonAutoLinks_Form_CategorySelect`.
         * @return          string
         */
        private function ___getCategoryList( $_oSimpleDOM ) {

            $_oNodeBrowseRoot = $_oSimpleDOM->getElementById( 'zg_browseRoot' );
            $this->___setHrefs( $_oNodeBrowseRoot );
            return $_oNodeBrowseRoot->outertext; // the sidebar html code

        }
            /**
             * Converts href urls io a url with query which contains the original url.
             *
             * e.g. <a href="http://amazon.com/something"> -> <a href="localhost/me.php?href=http://amazon.com/something"
             * and the href value becomes base64 encoded.
             * @since       unknown
             * @since       3.6.0       Renamed from `modifyHref`.
             * @since       3.6.0       Moved from `AmazonAutoLinks_Form_CategorySelect`.
             */
            private function ___setHrefs( $_oSimpleDOMNode, $aQueries=array() ) {

                foreach( $_oSimpleDOMNode->getElementsByTagName( 'a' ) as $nodeA ) {

                    $sHref = $nodeA->getAttribute( 'href' );

                    // sip the sing after 'ref=' in the url
                    // e.g. http://amazon.com/ref=zg_bs_123/324-5242552 -> http://amazon.com
                    $aURL  = explode( "ref=", $sHref, 2 );
                    $sHref = $aURL[0];

                    $nodeA->setAttribute( 'href', $this->_getLinkURLFormatted( $sHref, $aQueries ) );

                }

            }

}