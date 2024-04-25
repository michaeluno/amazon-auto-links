<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
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

        $_aBreadcrumbs       = array();
        $_aPageURLs          = $this->___getURLs( $this->oUnitOutput->oUnitOption->get( array( 'categories' ), array() ), $_aBreadcrumbs );
        $_aExcludingPageURLs = $this->___getURLs( $this->oUnitOutput->oUnitOption->get( array( 'categories_exclude' ), array() ) );
        $_iCountUserSet      = ( integer ) $this->oUnitOutput->oUnitOption->get( 'count' );
        $_iCount             = $_iCountUserSet < 10 ? 10 : $_iCountUserSet;     // 4.6.14 Fetch at least 10 to reduce http requests and database queries
        return array_merge( $aProducts, $this->___getFoundProducts( $_aPageURLs, $_aExcludingPageURLs, $_iCount, array(), $_aBreadcrumbs ) );

    }

        /**
         * Returns the subject urls for this unit.
         * @param   array  $aItems
         * @param   array &$aBreadcrumbs An array to store breadcrumbs corresponding to the returning URLs.
         * @since   3.9.0
         * @since   5.0.0 Moved from `AmazonAutoLinks_UnitOutput_category`.
         * @since   5.2.6 Added the `$aBreadcrumbs` parameter
         * @return  array
         */
        private function ___getURLs( array $aItems, array &$aBreadcrumbs=array() ) {

            $_aAllURLs     = array();
            foreach( $aItems as $_aItem ) {

                $_sURL        = $this->getElement( $_aItem, array( 'page_url' ), '' );
                $_sBreadcrumb = $this->getElement( $_aItem, array( 'breadcrumb' ), '' );
                if ( empty( $_sURL ) ) {
                    continue;
                }
                foreach ( $this->oUnitOutput->oUnitOption->get( 'feed_type' ) as $_sSlug => $_bEnabled ) {

                    if ( ! $_bEnabled ) {
                        continue;
                    }

                    if ( 'bestsellers' === $_sSlug ) {
                        $_aAllURLs[ $_sURL ]    = $_sURL;
                        $aBreadcrumbs[ $_sURL ] = $_sBreadcrumb;
                        continue;
                    }

                    // At this point, it is not the best seller page.
                    $_sReplaced = str_replace(
                        array( '/gp/bestsellers/', '/gp/top-sellers/' ),
                        "/gp/{$_sSlug}/",
                        $_sURL
                    );
                    if ( $_sURL !== $_sReplaced ) {
                        $_aAllURLs[ $_sReplaced ] = $_sReplaced;
                        $aBreadcrumbs[ $_sReplaced ] = $_sBreadcrumb;
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
                        $_aAllURLs[ $_sReplaced ] = $_sReplaced;
                        $aBreadcrumbs[ $_sReplaced ] = $_sBreadcrumb;
                        continue;
                    }

                }

            }

            return array_values( $_aAllURLs );

        }

        /**
         * Fetches product data.
         * @remark Called recursively. @todo investigate whether this is no longer the case.
         * @since  ?
         * @since  5.0.0    Moved from `AmazonAutoLinks_UnitOutput_category`.
         * @since  5.2.6    Added the `$aBreadcrumbs` parameter.
         * @param  array    $aURLs
         * @param  array    $aExcludeURLs
         * @param  integer  $iItemCount
         * @param  array    $aExcludeASINs
         * @param  array    $aBreadcrumbs
         * @return array
         */
        private function ___getFoundProducts( array $aURLs, array $aExcludeURLs, $iItemCount, array $aExcludeASINs=array(), array $aBreadcrumbs=array() ) {

            // Find products
            $_aProducts = $this->___getProductsFromURLs( $aURLs, $aBreadcrumbs );

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
                $_aThisFound     = $this->___getProductsFromURLs( $_aURLs, $aBreadcrumbs );
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
             * @param  array $aBreadcrumbs
             * @return array
             * @since  4.3.4
             * @since  5.2.6 Added the `$aBreadcrumbs` parameter.
             */
            private function ___getProductsFromURLs( array $aURLs, array $aBreadcrumbs ) {
                add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 1 );
                add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10, 2 );
                $_aHTMLs          = $this->___getHTTPBodies( $aURLs );
                remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );
                remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10 );
                $_sAssociateID    = $this->oUnitOutput->oUnitOption->get( 'associate_id' );
                $_oLocale         = new AmazonAutoLinks_Locale( $this->oUnitOutput->oUnitOption->get( 'country' ) );
                $_sMarketPlaceURL = $_oLocale->getMarketPlaceURL();
                return $this->___getProductsScraped( $_aHTMLs, $_sAssociateID, $_sMarketPlaceURL, $aBreadcrumbs );
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
             * @param  array  $aBreadcrumbs
             * @return array
             * @since  ?
             * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_category`.
             * @since  5.2.6 Added the `$aBreadcrumbs` parameter.
             */
            private function ___getProductsScraped( array $aHTMLs, $sAssociateID, $sSiteDomain, array $aBreadcrumbs ) {

                $_aLocaleCodes = AmazonAutoLinks_Locales::getLocales();
                $_aProducts    = array();
                foreach( $aHTMLs as $_sURL => $_sHTML ) {
                    // [5.4.3] There is a report of a bug saying "Uncaught ValueError: DOMDocument::loadHTML(): Argument #1 ($source) must not be empty",
                    // caused by instantiating the below `AmazonAutoLinks_ScraperDOM_BestsellerProducts` class.
                    if ( empty( $_sHTML ) ) {
                        continue;
                    }
                    $_sModifiedDate   = $this->getElement( $this->oUnitOutput->aModifiedDates, $_sURL );
                    $_oProductScraper = new AmazonAutoLinks_ScraperDOM_BestsellerProducts( $_sHTML );
                    $_aFoundProducts  = $_oProductScraper->get( $sAssociateID, $sSiteDomain );
                    foreach( $_aFoundProducts as $_sASIN => $_aFoundProduct ) {
                        $_aFoundProducts[ $_sASIN ][ 'updated_date' ]      = $_sModifiedDate;

                        // [5.2.6+]
                        $_aFoundProducts[ $_sASIN ][ 'rating' ]            = $_aFoundProducts[ $_sASIN ][ 'rating_point' ]; // [5.2.6] to be consistent with other unit type data structure
                        $_aFoundProducts[ $_sASIN ][ 'number_of_reviews' ] = $_aFoundProducts[ $_sASIN ][ 'review_count' ]; // [5.2.6] to be consistent with other unit type data structure
                        $_aFoundProducts[ $_sASIN ][ '_categories' ]       = array( $this->___getCategoryListFromBreadcrumb( $this->getElement( $aBreadcrumbs, array( $_sURL ) ), $_aLocaleCodes ) );   // enclosing it in an array because there are products with multiple categories
                        $_aFoundProducts[ $_sASIN ][ 'category' ]          = $this->oUnitOutput->getCategoriesFormatted( $_aFoundProducts[ $_sASIN ][ '_categories' ] );
                    }
                    $_aProducts       = $_aProducts + $_aFoundProducts;
                }
                return $_aProducts;

            }
                /**
                 * @since  5.2.6
                 * @param  string   $sBreadcrumb
                 * @param  array    $aLocaleCodes
                 * @return string[]
                 */
                private function ___getCategoryListFromBreadcrumb( $sBreadcrumb, array $aLocaleCodes ) {
                    $_aBreadcrumb = explode( ' > ', $sBreadcrumb );
                    $_sFirstItem  = reset( $_aBreadcrumb );
                    if ( in_array( $_sFirstItem, $aLocaleCodes, true ) ) {
                        unset( $_aBreadcrumb[ 0 ] );
                        $_aBreadcrumb = array_values( $_aBreadcrumb );  // re-index to start from 0
                    }
                    return $_aBreadcrumb;
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