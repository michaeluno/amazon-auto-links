<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Performs PA-API 5 requests with the `SearchItems` operation.
 *
 * @since   5.0.0
 */
class AmazonAutoLinks_Unit_PAAPI5_Request_SearchItems extends AmazonAutoLinks_Unit_PAAPI5_Request_Base {

    /**
     * @var   string
     * @since 5.0.0
     */
    protected $_sResponseItemsParentKey = 'SearchResult';

    /**
     * Performs paged API requests.
     *
     * This enables to retrieve more than 10 items. However, for it, it performs multiple requests, thus, it will be slow.
     *
     * @since  2.0.1
     * @since  4.3.1   Changed the scope to public from protected. This is for the background routine to fetch products.
     * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`. Renamed from `getRequest()`.
     * @return array
     * @param  integer $iCount
     */
    public function getPAAPIResponse( $iCount ) {

        $_sAssociateID = $this->oUnitOption->get( 'associate_id' );
        if ( ! $_sAssociateID ) {
            return array(
                'Error' => array(
                    'Code'    => $this->oUnitOption->sUnitType,
                    'Message' => 'An Associate tag is not set.',
                )
            );
        }

        $_oAPI    = new AmazonAutoLinks_PAAPI50( $this->oUnitOption->get( 'country' ), $this->sPublicKey, $this->sSecretKey, $_sAssociateID );

        // First, perform the search for the first page regardless the specified count (number of items).
        // Keys with an empty value will be filtered out when performing the request.
        $_aResponse = $_oAPI->request(
            $this->_getAPIParameters( $this->oUnitOption->get( 'Operation' ) ),
            $this->oUnitOption->get( 'cache_duration' ),
            $this->oUnitOption->get( '_force_cache_renewal' )
        );

        if ( $iCount <= 10 ) {
            return $_aResponse;
        }

        // Check if it has an item
        if ( ! $this->___hasItem( $_aResponse ) ) {
            return $_aResponse;
        }

        /**
         * As PA-API 5.0 does not return reliable total page count,
         * perform search from the lowest page number and when it hits an error stop.
         */
        $_iMaxPage = ( integer ) ceil( $iCount / 10 );

        $_aResponseTrunk = $_aResponse;

        // Start from the second page since the first page has been already done.
        for ( $_i = 2; $_i <= $_iMaxPage; $_i++ ) {

            $_aResponse = $_oAPI->request(
                $this->_getAPIParameters(
                    $this->oUnitOption->get( 'Operation' ),
                    $_i // page number
                ),
                $this->oUnitOption->get( 'cache_duration' ),
                $this->oUnitOption->get( '_force_cache_renewal' )
            );
            if ( isset( $_aResponse[ 'Error' ] ) ) {
                break;
            }
            if ( $this->___hasItem( $_aResponse ) ) {
                $_aResponseTrunk[ $this->_sResponseItemsParentKey ][ 'Items' ] = $this->___addItems(
                    $_aResponseTrunk[ $this->_sResponseItemsParentKey ][ 'Items' ],
                    $_aResponse[ $this->_sResponseItemsParentKey ][ 'Items' ]
                );
            }

        }

        return $_aResponseTrunk;

    }
        /**
         * @param  array $aResponse
         * @return boolean
         * @since  ?
         * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
         */
        private function ___hasItem( array $aResponse ) {
            $_aItems = $this->getElement( $aResponse, array( $this->_sResponseItemsParentKey, 'Items' ) );
            if ( ! is_array( $_aItems ) ) {
                return false;
            }
            return ( boolean ) count( $_aItems );
        }

        /**
         * Adds product item elements in a response array if the same ASIN is not already in there
         *
         * @since  2.0.4.1
         * @since  3.9.0   Changed the scope to private.
         * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
         * @param  array   $aMain
         * @param  array   $aItems
         * @return array
         */
        private function ___addItems( $aMain, $aItems ) {

            // Extract all ASINs from the main array.
            $_aASINs = array();
            foreach( $aMain as $_aItem ) {
                if ( ! isset( $_aItem[ 'ASIN' ] ) ) {
                    continue;
                }
                $_aASINs[ $_aItem[ 'ASIN' ] ] = $_aItem[ 'ASIN' ];
            }

            // Add the items if not already there.
            foreach ( $aItems as $_aItem ) {
                if ( ! isset( $_aItem[ 'ASIN' ] ) ) {
                    continue;
                }
                if ( in_array( $_aItem[ 'ASIN' ], $_aASINs ) ) {
                    continue;
                }
                $aMain[] = $_aItem;    // At last, add the item
            }

            return $aMain;

        }

    /**
     * @since  2.0.2
     * @since  3.9.0   The parameter format has been changed in PA-API 5
     * @since  4.3.1   Renamed from `getAPIParameterArray()`. Made it public.
     * @since  5.0.0   Changed the visibility scope to protected from public as this is not used from outside. Moved from `AmazonAutoLinks_UnitOutput_search`.
     * @see    http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
     * @see    http://docs.aws.amazon.com/AWSECommerceService/latest/DG/PowerSearchSyntax.html
     * @param  string  $sOperation
     * @param  integer $iItemPage
     * @return array
     */
    protected function _getAPIParameters( $sOperation='SearchItems', $iItemPage=0 ) {

        $_sTitle                 = $this->getEachDelimitedElementTrimmed( $this->oUnitOption->get( 'Title' ), ',', false );
        $_sKeywords              = $this->getEachDelimitedElementTrimmed( $this->oUnitOption->get( 'Keywords' ), ',', false );
        $_aPayload               = array(
            'Keywords'              => strlen( $_sKeywords ) ? $_sKeywords : null,
            'Title'                 => $_sTitle ? $_sTitle : null,
            'Operation'             => $this->_getOperation( $sOperation ),
            'SearchIndex'           => $this->oUnitOption->get( 'SearchIndex' ),
            $this->oUnitOption->get( 'search_by' ) => $this->oUnitOption->get( 'additional_attribute' )
                ? $this->oUnitOption->get( 'additional_attribute' )
                : null,
            'SortBy'                => $this->___getParameterSortBy( $this->oUnitOption->get( 'Sort' ) ),
            'BrowseNodeId'          => $this->oUnitOption->get( 'BrowseNode' )
                ? $this->oUnitOption->get( 'BrowseNode' )
                : null,
            'Availability'          => $this->oUnitOption->get( 'Availability' )
                ? 'Available'
                : 'IncludeOutOfStock',
            'Condition'             => $this->oUnitOption->get( 'Condition' ),
            'MaxPrice'              => $this->oUnitOption->get( 'MaximumPrice' )
                ? ( integer ) $this->oUnitOption->get( 'MaximumPrice' )
                : null,
            'MinPrice'              => $this->oUnitOption->get( 'MinimumPrice' )
                ? ( integer ) $this->oUnitOption->get( 'MinimumPrice' )
                : null,
            'MinReviewsRating'      => $this->oUnitOption->get( 'MinReviewsRating' )
                ? ( integer ) $this->oUnitOption->get( 'MinReviewsRating' )
                : null,
            'MinSavingPercent'      => $this->oUnitOption->get( 'MinPercentageOff' )
                ? ( integer ) $this->oUnitOption->get( 'MinPercentageOff' )
                : null,
            'Merchant'            => 'Amazon' === $this->oUnitOption->get( 'MerchantId' )
                ? 'Amazon'
                : null,

            'DeliveryFlags'         => array_keys( array_filter( ( array ) $this->oUnitOption->get( 'DeliveryFlags' ) ) ),
            'CurrencyOfPreference'  => $this->oUnitOption->get( 'preferred_currency' )
                ? $this->oUnitOption->get( 'preferred_currency' )
                : null,
            'LanguagesOfPreference' => $this->oUnitOption->get( 'language' )
                ? $this->getAsArray( $this->oUnitOption->get( 'language' ) )
                : null,
            'Resources'             => AmazonAutoLinks_PAAPI50___Payload::$aResources,
        );
        $_aPayload = $iItemPage
            ? $_aPayload + array( 'ItemPage' => $iItemPage )
            : $_aPayload;

        // 'search_by' option might cause an empty element
        unset( $_aPayload[ '' ] );
        $_aPayload = array_filter( $_aPayload, array( $this, 'isNotNull' ) ); // drop null elements.
        ksort( $_aPayload ); // important to generate identical caches.
        return $_aPayload;

    }

        /**
         * For backward compatibility for PA-API 4
         *
         * @param  string $sOperation
         * @return string
         * @since  3.9.0
         * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`.
         */
        protected function _getOperation( $sOperation ) {
            // ItemSearch, ItemLookup, SimilarityLookup
            if ( 'ItemSearch' === $sOperation ) {
                return 'SearchItems';
            }
            if ( 'ItemLookup' === $sOperation ) {
                return 'GetItems';
            }
            return $sOperation;
        }

        /**
         *
         * Accepted parameter values:
         *  - AvgCustomerReviews     Sorts results according to average customer reviews
         *  - Featured               Sorts results with featured items having higher rank
         *  - NewestArrivals         Sorts results with according to newest arrivals
         *  - Price:HighToLow        Sorts results according to most expensive to least expensive
         *  - Price:LowToHigh        Sorts results according to least expensive to most expensive
         *  - Relevance              Sorts results with relevant items having higher rank
         * @see https://webservices.amazon.com/paapi5/documentation/search-items.html#sortby-parameter
         * @return string
         * @since  3.9.0
         * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_search`. Removed the `$oUnitOption` parameter.
         */
        private function ___getParameterSortBy( $sSort ) {
                        
            // First, check if it is a valid values. If so, just return it.
            if ( in_array( $sSort, array( 'AvgCustomerReviews', 'Featured', 'NewestArrivals', 'Relevance', 'Price:HighToLow', 'Price:LowToHigh' ), true ) ) {
                return $sSort;
            }

            // Check backward compatible values.
            if ( in_array( $sSort, array( 'price', 'pricerank' ), true ) ) {
                return 'Price:LowToHigh';
            }
            if ( in_array( $sSort, array( '-price', 'inversepricerank' ), true ) ) {
                return 'Price:HighToLow';
            }
            if ( in_array( $sSort, array( 'reviewrank' ), true ) ) {
                return 'AvgCustomerReviews';
            }

            // Other values will be treated as Relevance as default.
            return 'Relevance';

        }

}