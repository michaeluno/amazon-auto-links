<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A base class that extracts product elements from Amazon best seller pages.
 * @since   3.8.12
 */
abstract class AmazonAutoLinks_ScraperDOM_BestsellerProducts_Base extends AmazonAutoLinks_ScraperDOM_Base {

    /**
     * @var DOMNodeList|array
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

        $this->_oXPath       = new DOMXPath( $this->oDoc );
        $this->_oItemNodes   = $this->___getItemNodes();

    }
    /**
     * @return DOMNodeList|array
     */
    private function ___getItemNodes() {

        // Going to parse three types of the web page design structure (old and new, and search).
        try {

            $_oItemNodes = $this->___getItemNodeListType20211209( $this->_oXPath );
            if ( ! empty( $_oItemNodes ) && $_oItemNodes->length ) {
                return $_oItemNodes;
            }

            $_oItemNodes = $this->___getItemNodeListTypeOld2( $this->_oXPath );
            if ( ! empty( $_oItemNodes ) && $_oItemNodes->length ) {
                return $_oItemNodes;
            }
            $_oItemNodes = $this->___getItemNodeListTypeOld1( $this->_oXPath );
            if ( ! empty( $_oItemNodes ) && $_oItemNodes->length ) {
                return $_oItemNodes;
            }
            $_oItemNodes = $this->___getItemNodeListTypeSearchResult( $this->_oXPath );
            if ( ! empty( $_oItemNodes ) && $_oItemNodes->length ) {
                return $_oItemNodes;
            }

        } catch ( Exception $_oException ) {
            // to check error message, $_oException->getMessage();
            return array();
        }

        // Not found
        return array();

    }

        /**
         * @param  $oXPath
         * @throws Exception
         * @return DOMNodeList|array
         * @since  5.0.4
         */
        private function ___getItemNodeListType20211209( $oXPath ) {
            $_oContainerNodes = $oXPath->query( '//div[@id="zg-right-col"]//div[contains(@id, "CardInstance") and contains(@data-card-metrics-id, "p13n-zg-list")]' );
            if ( ! $_oContainerNodes->length ) {
                return array();
            }
            $_oContainerNode  = $_oContainerNodes->item( 0 );
            $_oItemNodes      = $oXPath->query(
                './/*[contains(@class, "zg-grid-general-faceout")]',
                $_oContainerNode
            );
            if ( ! $_oItemNodes->length ) {
                throw new Exception( 'the container found (current design) but the items not found' );
            }
            return $_oItemNodes;
        }
        /**
         * @throws  Exception
         * @return  DOMNodeList|array
         * @since   3.8.13
         */
        private function ___getItemNodeListTypeOld2( $oXPath ) {
            $_oContainerNodes = $oXPath->query( '//ol[@id="zg-ordered-list"]' );
            if ( ! $_oContainerNodes->length ) {
                return array();
            }
            $_oContainerNode  = $_oContainerNodes->item( 0 );
            $_oItemNodes      = $oXPath->query(
                './/*[contains(@class, "zg-item-immersion")]',
                $_oContainerNode
            );
            if ( ! $_oItemNodes->length ) {
                throw new Exception( 'the container found (current design) but the items not found' );
            }
            return $_oItemNodes;
        }
        /**
         * @throws  Exception
         * @return  DOMNodeList|array
         * @since   3.8.13
         */
        private function ___getItemNodeListTypeOld1( $oXPath ) {
            $_oContainerNodes = $oXPath->query( '//div[@id="zg_left_col1"]' );
            if ( ! $_oContainerNodes->length ) {
                return array();
            }
            $_oContainerNode = $_oContainerNodes->item( 0 );
            $_oItemNodes      = $oXPath->query(
                './/*[contains(@class, "p13n-asin") or contains(@class, "a-carousel-card")]',  // a-carousel-card is added 2021/08/01. Possibly, a new design.
                $_oContainerNode
            );

            if ( ! $_oItemNodes->length ) {
                throw new Exception( 'the container found (old design) but the items not found' );
            }
            return $_oItemNodes;
        }
        /**
         * @throws  Exception
         * @return  DOMNodeList|array
         * @since   3.8.13
         */
        private function ___getItemNodeListTypeSearchResult( $oXPath ) {
            $_oContainerNodes = $oXPath->query( '//div[contains(@class, "s-result-list")]' );
            if ( ! $_oContainerNodes->length ) {
                return array();
            }
            $_oContainerNode = $_oContainerNodes->item( 0 );
            $_oItemNodes      = $oXPath->query(
                './/*[contains(@class, "s-result-item")]',
                $_oContainerNode
            );
            if ( ! $_oItemNodes->length ) {
                throw new Exception( 'the container found (search result) but the items not found' );
            }
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