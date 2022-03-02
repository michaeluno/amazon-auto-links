<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Fetches product data from outside source.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_Category_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFetcher_Base {

    public $sUnitType = 'category';

    /**
     * @var AmazonAutoLinks_UnitOutput_category
     */
    public $oUnitOutput;

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {

        $_aPageURLs          = wp_list_pluck( $this->oUnitOutput->oUnitOption->get( array( 'categories' ), array() ), 'page_url' );
        $_aPageURLs          = $this->___getURLs( $_aPageURLs );
        $_aExcludingPageURLs = wp_list_pluck( $this->oUnitOutput->oUnitOption->get( array( 'categories_exclude' ), array() ), 'page_url' );
        $_aExcludingPageURLs = $this->___getURLs( $_aExcludingPageURLs );

        $_iCountUserSet      = ( integer ) $this->oUnitOutput->oUnitOption->get( 'count' );
        $_iCount             = $_iCountUserSet < 10 ? 10 : $_iCountUserSet;     // 4.6.14 Fetch at least 10 to reduce http requests and database queries

        return array_merge( $aProducts, $this->___getFoundProducts( $_aPageURLs, $_aExcludingPageURLs, $_iCount ) );

    }

        /**
         * Returns the subject urls for this unit.
         * @param   array $aURLs
         * @since   3.9.0
         * @since   5.0.0 Moved from `AmazonAutoLinks_UnitOutput_category`.
         * @return  array
         */
        private function ___getURLs( array $aURLs ) {

            $_aAllURLs = array();
            foreach( $aURLs as $_sURL ) {

                foreach ( $this->oUnitOutput->oUnitOption->get( 'feed_type' ) as $_sSlug => $_bEnabled ) {

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
                    $_sURL = preg_replace( '/ref=.+$/', '', $_sURL );  // remove the ending part `ref=...`.
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
         * Fetches product data.
         * @remark Called recursively.
         * @param  array    $aURLs
         * @param  array    $aExcludeURLs
         * @param  integer  $iItemCount
         * @param  array    $aExcludeASINs
         * @return array
         * @since  ?
         * @since  5.0.0    Moved from `AmazonAutoLinks_UnitOutput_category`.
         */
        private function ___getFoundProducts( array $aURLs, array $aExcludeURLs, $iItemCount, array $aExcludeASINs=array() ) {

            // Find products
            $_aProducts = $this->___getProductsFromURLs( $aURLs );

            // If no items found, no need to continue.
            if ( empty( $_aProducts ) ) {
                return $_aProducts;
            }

            // Excluding Items - $aExcludeASINs will be updated.
            $_aProducts = $this->___getExcludesApplied( $_aProducts, $aExcludeURLs, $aExcludeASINs );

            // Make sure to fill the set number of item count
            $_iPage     = 2;    // starts from 2
            $_iMaxPage  = ( integer ) apply_filters( 'aal_filter_amazon_bestseller_category_max_number_of_page', 2 );   // 4.7.8
            while( ( count( $_aProducts ) < $iItemCount ) && ( $_iPage <= $_iMaxPage ) ) {
                $_aURLs          = $this->___getURLsPageIncremented( $aURLs, $_iPage );
                $_aExcludeURLs   = $this->___getURLsPageIncremented( $aExcludeURLs, $_iPage );
                $_aThisFound     = $this->___getProductsFromURLs( $_aURLs );
                $_aThisFound     = empty( $_aThisFound )
                    ? $_aThisFound
                    : $this->___getExcludesApplied( $_aThisFound, $_aExcludeURLs, $aExcludeASINs );
                if ( empty( $_aThisFound ) ) {
                    break;
                }
                $_aNewFoundItems = array_diff( array_keys( $_aThisFound ), array_keys( $_aProducts ) );
                if ( empty( $_aNewFoundItems ) ) {
                    break;  // if the previous found items cover the newly found items, then it could be a page that does not support pagination.
                }
                $_aProducts      = $_aProducts + $_aThisFound;
                $_iPage++;
            }
            return $_aProducts;

        }
            /**
             * @param  array $aURLs
             * @return array
             * @since  4.3.4
             */
            private function ___getProductsFromURLs( array $aURLs ) {
                add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 1 );
                add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10, 2 );
                $_aHTMLs          = $this->___getHTTPBodies( $aURLs );
                remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );
                remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10 );
                $_sAssociateID    = $this->oUnitOutput->oUnitOption->get( 'associate_id' );
                $_oLocale         = new AmazonAutoLinks_Locale( $this->oUnitOutput->oUnitOption->get( 'country' ) );
                $_sMarketPlaceURL = $_oLocale->getMarketPlaceURL();
                return $this->___getProductsScraped( $_aHTMLs, $_sAssociateID, $_sMarketPlaceURL );
            }
            /**
             * @param  array $aProducts
             * @param  array $aExcludeURLs
             * @param  array $aExcludeASINs
             * @return array
             * @since  4.3.4
             */
            private function ___getExcludesApplied( array $aProducts, array $aExcludeURLs, array &$aExcludeASINs ) {
                $_aExcludeHTMLs    = $this->___getHTTPBodies( $aExcludeURLs );
                $_aExcludeASINs    = array_unique( array_merge( $aExcludeASINs, $this->___getASINsExtracted( $_aExcludeHTMLs ) ) );
                $_aExcludeASINKeys = array_flip( $_aExcludeASINs );
                return array_diff_key( $aProducts, $_aExcludeASINKeys );
            }
            /**
             * @param  array   $aURLs
             * @param  integer $iPage
             * @return array
             */
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
             * @param array $aURLs
             * @return array
             */
            private function ___getHTTPBodies( array $aURLs ) {
                // URLs for exclusion items may be empty
                if ( empty( $aURLs ) ) {
                    return array();
                }
                $_oHTTP = new AmazonAutoLinks_HTTPClient_Multiple(
                    $aURLs,
                    $this->oUnitOutput->oUnitOption->get( 'cache_duration' ),
                    array(  // http arguments
                        'timeout'     => 20,
                        'redirection' => 20,
                    ),
                    $this->sUnitType . '_unit_type' // request type
                );
                return $_oHTTP->get();
            }

            /**
             * @param  array  $aHTMLs
             * @param  string $sAssociateID
             * @param  string $sSiteDomain
             * @return array
             * @since  ?
             * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_category`.
             */
            private function ___getProductsScraped( array $aHTMLs, $sAssociateID, $sSiteDomain ) {
                $_aProducts = array();
                foreach( $aHTMLs as $_sURL => $_sHTML ) {
                    $_sModifiedDate   = $this->getElement( $this->oUnitOutput->aModifiedDates, $_sURL );
                    $_oProductScraper = new AmazonAutoLinks_ScraperDOM_BestsellerProducts( $_sHTML );
                    $_oProductScraper->setLastUpdated( is_numeric( $_sModifiedDate ) ? ( integer ) $_sModifiedDate : strtotime( $_sModifiedDate ) );
                    $_aFoundProducts  = $_oProductScraper->get( $sAssociateID, $sSiteDomain );
                    foreach( $_aFoundProducts as $_sASIN => $_aFoundProduct ) {
                        $_aFoundProducts[ $_sASIN ][ 'updated_date' ] = $_sModifiedDate;
                    }
                    $_aProducts       = $_aProducts + $_aFoundProducts;
                }
                return $_aProducts;
            }

            /**
             * @param  array $aHTMLs
             * @return array A linear array holding found ASINs
             */
            private function ___getASINsExtracted( array $aHTMLs ) {
                $_aASINs = array();
                foreach( $aHTMLs as $_sHTML ) {
                    $_oASINScraper = new AmazonAutoLinks_ScraperDOM_BestsellerProducts_ASIN( $_sHTML );
                    $_aASINs = array_merge( $_aASINs, $_oASINScraper->get() );
                }
                return array_unique( $_aASINs );
            }


    /**
     * @param    array  $aCache
     * @callback add_filter() aal_filter_http_response_cache
     * @since    ?
     * @since    5.0.0  Moved from `AmazonAutoLinks_UnitOutput_category`.
     * @return   array
     */
    public function replyToCaptureUpdatedDate( $aCache ) {
        $this->oUnitOutput->aModifiedDates[ $aCache[ 'request_uri' ] ] = $this->getLastModified( $aCache[ 'data' ], $aCache[ '_modified_timestamp' ] );
        return $aCache;
    }

    /**
     * @param    array|WP_error $aoResponse
     * @param    string         $sURL
     * @callback add_filter()   aal_filter_http_request_response
     * @since    4.2.2
     * @since    5.0.0  Moved from `AmazonAutoLinks_UnitOutput_category`.
     * @return   array|WP_Error
     */
    public function replyToCaptureUpdatedDateForNewRequest( $aoResponse, $sURL ) {
        $this->oUnitOutput->aModifiedDates[ $sURL ] = $this->getLastModified( $aoResponse, time() );
        return $aoResponse;
    }

}