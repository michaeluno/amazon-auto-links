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
 * A base class that extracts product elements from Amazon best seller pages.
 * @since   3.8.12
 */
abstract class AmazonAutoLinks_ScraperDOM_BestsellerProducts_Base extends AmazonAutoLinks_ScraperDOM_Base {

    /**
     * @var DOMNodeList
     */
    protected $_oItemNodes;

    /**
     * @var DOMXPath
     */
    protected $_oXPath;

    /**
     * Sets up properties.
     *
     * @param string $sURLOrFIlePathOrHTML
     * @param string $sCharset
     */
    public function __construct( $sURLOrFIlePathOrHTML, $sCharset='' ) {

        parent::__construct( $sURLOrFIlePathOrHTML, $sCharset );

//            $_oDOM      = new AmazonAutoLinks_DOM;
//            $this->oDoc = $_oDOM->loadDOMFromURL(
//                'https://www.amazon.co.jp/gp/bestsellers/diy/2039681051/ref=zg_bs_nav_diy_1_diy',
//                '',  // mb_lang
//                false, // use file_get_contents()
//                true
//            );
        $this->_oXPath       = new DOMXPath( $this->oDoc );
        $this->_oItemNodes   = $this->___getItemNodes();

    }
        /**
         * @return DOMNodeList
         */
        private function ___getItemNodes() {

            $_oXPath = $this->_oXPath;

            // There are two versions (old and new) of the web page design.

            // Get main container
            $_oContainerNodes = $_oXPath->query( '//ol[@id="zg-ordered-list"] | //div[@id="zg_left_col1"]' );
            if ( ! $_oContainerNodes->length ) {
                return $_aProducts;
            }
            $_oContainerNode = $_oContainerNodes->item( 0 );

            // For the old design, search elements with the `p13n-asin` class attribute
            // and use `zg-item-immersion` for the new design.
            $_oItemNodes = $_oXPath->query(
                './/*[contains(@class, "zg-item-immersion") or contains(@class, "p13n-asin")]',
                $_oContainerNode
            );
            return $_oItemNodes;

        }

    /**
     * @param $oXPath
     * @param $oItemNode
     *
     * @return string
     */
    protected function _getProductLink( DOMXPath $oXPath, $oItemNode ) {
        $_oLinkAttributes = $oXPath->query( './/a[contains(@class, "a-link-normal")]/@href', $oItemNode );
        foreach( $_oLinkAttributes as $_oAttribute ) {
            return $_oAttribute->nodeValue;
        }
        return '';
    }

    
}