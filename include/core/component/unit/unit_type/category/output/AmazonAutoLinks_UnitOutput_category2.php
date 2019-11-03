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
 * Creates Amazon product links by category.
 *
 * As Amazon deprecated bestseller feeds, this is an alternative method to retrieve best seller products by category.
 *
 * @package     Amazon Auto Links
 * @sicne  3.8.1
 */
class AmazonAutoLinks_UnitOutput_category2 extends AmazonAutoLinks_UnitOutput_url {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'category';

    /**
     * The alternative for the `$_aRSSURLs` property.
     * @since   3.8.1
     * @var array
     */
    protected $_aPageURLs = array();
    
    /**
     * The alternative for the `$_aExcludingRSSURLs` property.
     * @var array
     * @since   3.8.1
     */
    protected $_aExcludingPageURLs = array();

    /**
     * @var array
     * @since   3.8.12
     */
    protected $_aScraped = array();


    /**
     * @param $aResponse
     *
     * @return array
     */
    protected function getProducts( $aResponse ) {
        add_filter( 'aal_filter_unit_each_product', array( $this, 'replyToSetScrapedProductData' ), 10, 3 );
        $_aProducts = parent::getProducts( $aResponse );    // refers to item_lookup's method
        remove_filter( 'aal_filter_unit_each_product', array( $this, 'replyToSetScrapedProductData' ), 10 );
        return $_aProducts;
    }
        /**
         * Adds scraped product data to reduce background tasks.
         * @since   3.8.12
         * @return  array
         */
        public function replyToSetScrapedProductData( $aProduct, $aArguments, $oUnitOutput ) {
            $_sASIN = $aArguments[ 'asin' ];
             if ( ! isset( $this->_aScraped[ $_sASIN ] ) ) {
                 return $aProduct;
             }

             $aProduct[ 'rating' ] = $this->_aScraped[ $_sASIN ][ 'rating' ];
             $aProduct[ 'price' ]  = $aProduct[ 'price' ]
                ? $aProduct[ 'price' ]
                : $this->_aScraped[ $_sASIN ][ 'price' ];
             return $aProduct;
        } 

    /**
     * Returns the found ASINs from HTML documents.
     * @remark      Excludes ASINs of exclusion categories.
     * @since       3.8.2
     * @since       3.8.12      sets a property that stores product data scraped directly from the parsed HTML documents
     * @return      array
     */
    protected function _getFoundItems( $aHTMLs ) {

        $_sAssociateID    = $this->oUnitOption->get( 'associate_id' );
        $_sSiteDomain     = AmazonAutoLinks_PAAPI50___Locales::getMarketPlaceByLocale( $this->oUnitOption->get( 'country' ) );

        $this->_aScraped  = $this->_aScraped + $this->___getProductsScraped( $aHTMLs, $_sAssociateID, $_sSiteDomain );
        $_aASINs          = array_keys( $this->_aScraped );
        $_aExcludeASINs   = $this->___getASINs( $this->_aExcludingPageURLs, $_sAssociateID, $_sSiteDomain );
        $_aASINsDiff      = array_diff( $_aASINs, $_aExcludeASINs ); // the return value

        // Cover cases with insufficient number of products by checking the paged pages.
        $_iPage         = 2;    // starts from 2
        $_iSetCount     = $this->oUnitOption->get( 'count' );
        $_aURLs         = array_keys( $aHTMLs );
        while ( count( $_aASINsDiff ) < $_iSetCount ) {
            $_aURLs             = $this->___getURLsPageIncremented( $_aURLs, $_iPage );
            $_aHTMLs            = $this->_getHTMLBodies( $_aURLs );
            $_aProducts         = $this->___getProductsScraped( $_aHTMLs, $_sAssociateID, $_sSiteDomain );
            $_aThisFoundASINs   = array_keys( $_aProducts );
            if ( empty( $_aThisFoundASINs ) ) {
                break;
            }
            $this->_aScraped   = $this->_aScraped + $_aProducts;
            $_aASINs            = array_unique( array_merge( $_aASINs, $_aThisFoundASINs ) );
            $_aExcludeURLs      = $this->___getURLsPageIncremented( $this->_aExcludingPageURLs, $_iPage );
            $_aThisExcludeASINs = $this->___getASINs( $_aExcludeURLs, $_sAssociateID, $_sSiteDomain );
            $_aExcludeASINs     = array_unique( array_merge( $_aExcludeASINs, $_aThisExcludeASINs ) );
            $_aASINsDiff        = array_diff( $_aASINs, $_aExcludeASINs ); // the return value
            if ( $_iPage > 10 ) {
                break;
            }
            $_iPage++;
        }

        return $_aASINsDiff;

    }
        private function ___getURLsPageIncremented( array $aURLs, $iPage ) {
            $_aURLs = array();
            foreach( $aURLs as $_iIndex => $_sURL ) {
                $_aURLs[ $_iIndex ] = add_query_arg(
                    array(
                       'pg' => $iPage,
                    ),
                    $_sURL
                );
            }
            return $_aURLs;
        }
        /**
         * @param array $aHTMLs
         * @param $sAssociateID
         * @param $sSiteDomain
         *
         * @return array
         */
        private function ___getProductsScraped( array $aHTMLs, $sAssociateID, $sSiteDomain ) {
            $_aProducts = array();
            foreach( $aHTMLs as $_sHTML ) {
                $_oProductScraper = new AmazonAutoLinks_ScraperDOM_BestsellerProducts_Minimal( $_sHTML );
                $_aProducts       = $_aProducts + $_oProductScraper->get( $sAssociateID, $sSiteDomain );
            }
            return $_aProducts;
        }
        /**
         * @return array
         * @since   3.8.2
         * @since   3.8.12  Added the `$sAssociateID` and `$sSiteDomain` parameters.
         */
        private function ___getASINs( array $aURLs, $sAssociateID, $sSiteDomain ) {
            if ( empty( $aURLs ) ) {
                return array();
            }

            $_aASINs = array();
            $_aHTMLs = $this->_getHTMLBodies( $aURLs );
            foreach( $_aHTMLs as $_sHTML ) {
                $_oASINScraper = new AmazonAutoLinks_ScraperDOM_BestsellerProducts_ASIN( $_sHTML );
                $_aASINs = array_merge( $_aASINs, $_oASINScraper->get( $sAssociateID, $sSiteDomain ) );
            }
            return array_unique( $_aASINs );

        }


    /**
     * Returns the subject urls for this unit.
     * @scope   protected   the category2 unit output class extends this method to set own URLs.
     * @param   $asURLs
     * @since   3.8.1
     * @return  array
     */
    protected function _getURLs( $asURLs ) {

        $_aURLs    = parent::_getURLs( $asURLs );
        $_aURLs    = array_merge( $this->_aPageURLs, $_aURLs );

        $_aAllURLs = array();
        foreach( $_aURLs  as $_sURL ) {

            foreach ( $this->oUnitOption->get( 'feed_type' ) as $_sSlug => $_bEnabled ) {

                if ( ! $_bEnabled ) {
                    continue;
                }

                if ( 'bestsellers' === $_sSlug ) {
                    $_aAllURLs[] = $_sURL;
                    continue;
                }

                // At this point, it is not the best seller page.
                $_sReplaced = str_replace(
                    array( '/gp/bestsellers/', '/gp/top-sellers/' ),
                    "/gp/{$_sSlug}/",
                    $_sURL
                );
                if ( $_sURL !== $_sReplaced ) {
                    $_aAllURLs[] = $_sReplaced;
                    continue;
                }

                /**
                 * For a case of the US locale, the bestseller URLs of some categories have changed.
                 * @since   3.8.13
                 * For example, Best Sellers in Laptop Accessories
                 * ### The original URL structure
                 * https://www.amazon.com/bestsellers/pc/3011391011/ref=zg_bs_nav_pc_1_pc
                 * https://www.amazon.com/bestsellers/pc/ref=zg_bs_nav_pc_1_pc
                 * ### Current structure
                 * https://www.amazon.com/Best-Sellers-Computers-Accessories-Laptop/zgbs/pc/3011391011/
                 * If the feed type slug is `new-releases`, it should be changed to
                 * https://www.amazon.com/gp/new-releases/pc/3011391011
                 * ### Test Cases
                 * https://www.amazon.com/Best-Sellers-Sports-Collectibles/zgbs/sports-collectibles/
                 * https://www.amazon.com/gp/new-releases/pc/ref=zg_bs_nav_pc_1_pc
                 * https://www.amazon.com/Best-Sellers-Grocery-Gourmet-Food-Beverage-Gifts/zgbs/grocery/2255571011
                 */
                $_sURL = preg_replace( '/ref\=.+$/', '', $_sURL );  // remove the ending part `ref=...`.
                $_sURL = rtrim( $_sURL, '/\\' ) . '/';  // trailingslashit()
                preg_match( '/\/[^\/]+\/(\d+\/)?(?=$)/', $_sURL, $_aMatches );
                if ( isset( $_aMatches[ 0 ] ) ) {
                    $_aURLParts = parse_url( $_sURL );
                    $_sScheme   = isset( $_aURLParts[ 'scheme' ] ) ? $_aURLParts[ 'scheme' ] : '';
                    $_sDomain   = isset( $_aURLParts[ 'host' ] ) ? $_aURLParts[ 'host' ] : '';
                    $_sReplaced = $_sScheme . '://' . $_sDomain . '/gp/' . $_sSlug . $_aMatches[ 0 ];
                    $_aAllURLs[] = $_sReplaced;
                    continue;
                }


            }
        }

        return array_unique( $_aAllURLs );

    }

    /**
     * Sets up properties. Called at the end of the constructor.
     *
     * @remark      The 'tag' unit type will override this method.
     */
    protected function _setProperties() {

        $this->_aPageURLs          = wp_list_pluck( $this->oUnitOption->get( array( 'categories' ), array() ), 'page_url' );
        $this->_aExcludingPageURLs = wp_list_pluck( $this->oUnitOption->get( array( 'categories_exclude' ), array() ), 'page_url' );

        // The `url` unit type (which this `category` unit type extends) uses this key, `_sort`, not `sort`.
        $this->oUnitOption->set( '_sort', $this->oUnitOption->get( 'sort' ) );

        parent::_setProperties();


    }

}