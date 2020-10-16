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
 * Generates Amazon product links for oEmbed outputs.
 *
 * @since       4.0.0
 */
class AmazonAutoLinks_UnitOutput_embed extends AmazonAutoLinks_UnitOutput_category3 {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'embed';

    /**
     * Lists the tags (variables) used in the Item Format unit option that require to access the custom database.
     * @var array
     */
    protected $_aItemFormatDatabaseVariables = array(
        '%review%', '%similar%', '%category%', '%rank%', '%prime%',
        '%_discount_rate%', '%_review_rate%', // 3.9.2  - used for advanced filters
    );

    /**
     * Represents the structure of each product array.
     * @var array
     */
    public static $aStructure_Product = array();

    /**
     * Fetches product data and returns the associative array containing the output of product links.
     *
     * @param array $aURLs
     * @return            array            An array contains products.
     */
    public function fetch( $aURLs=array() ) {

        $_aURLs     = preg_split( "/\s+/", trim( ( string ) $this->oUnitOption->get( 'uri' ) ), 0, PREG_SPLIT_NO_EMPTY );
        $_aURLs     = array_merge( $aURLs, $_aURLs );
        $_aProducts = array();
        $_iCount    = ( integer ) $this->oUnitOption->get( 'count' );
        $_sLanguage = $this->oUnitOption->get( 'language' );
        $_sURL      = '';
        foreach( $_aURLs as $_iIndex => $_sURL ) {
            if ( $_iIndex > $_iCount ) {
                break;
            }
            // multiple ASINs can be embedded in a single URL like https://amazon.com/dp/ABCDEFGHIJ?tag=my-associate-21,ABCDEFGHI2,ABCDEFGHI3
            $_aASINs       = $this->getASINs( $_sURL );
            $_sAssociateID = $this->___getAssociateIDFromURL( $_sURL );
            foreach( $_aASINs as $_sASIN ) {
                $_aProduct = $this->___getProduct( $_sURL, $_sASIN, $_sAssociateID, $_sLanguage );
                // @deprecated 4.2.2 A structure array is returned on failure instead of an empty array.
//                if ( empty( $_aProduct ) ) {
//                    continue;
//                }
                $_aProducts[ $_sASIN ] = $_aProduct;
            }

        }
        $_sLocale      = AmazonAutoLinks_Locales::getLocaleFromURL( $_sURL, ( string ) $this->oUnitOption->get( array( 'country' ), 'US' ) );
        $_sAssociateID = $this->___getAssociateIDFromURL( $_sURL );
        $this->oUnitOption->set( 'associate_id', $_sAssociateID ); // some elements are formatted based on this value
        $this->oUnitOption->set( 'country', $_sLocale ); // when no image is found, the alternative thumbnail is based on the default locale

        $_aProductsByPAAPI = $this->___getProductsByPAAPI( array_keys( $_aProducts ) );
        $_aProducts        = $this->___getProductsMerged( $_aProducts, $_aProductsByPAAPI );
        return $this->_getProducts( $_aProducts, $_sLocale, $_sAssociateID, $_iCount );

    }
        /**
         * @param array $aProductsByScraping
         * @param array $aProductsByPAAPI
         * @since 4.2.9
         * @return array
         */
        private function ___getProductsMerged( array $aProductsByScraping, array $aProductsByPAAPI ) {
            foreach( $aProductsByPAAPI as $_aProduct ) {
                $_sASIN = $this->getElement( $_aProduct, array( 'ASIN' ) );
                if ( ! isset( $aProductsByScraping[ $_sASIN ] ) ) {
                    continue;
                }
                $aProductsByScraping[ $_sASIN ] = $_aProduct + $aProductsByScraping[ $_sASIN ];
            }
            return $aProductsByScraping;
        }
        /**
         * @param array $aASINs A numerically indexed array of ASINs.
         * @since 4.2.9
         * @return array
         */
        private function ___getProductsByPAAPI( array $aASINs ) {

            // If the API keys are set, perform an API request
            if ( ! $this->oOption->isAPIKeySet() ) {
                return array();
            }

            $_oUnit      = new AmazonAutoLinks_UnitOutput_item_lookup( array( 'ItemIds' => $aASINs ) + $this->oUnitOption->get() );
            return $_oUnit->fetch();

        }
        /**
         * @param  string $sURL
         * @param  string $sASIN
         * @param  string $sAssociateID
         * @param  string $sLanguage
         * @return array  A product array. Even if it fails to retrieve product data, a structure array will be returned and it's not empty.
         */
        private function ___getProduct( $sURL, $sASIN, $sAssociateID, $sLanguage ) {

            $_sDomain      = parse_url( $sURL, PHP_URL_SCHEME ) . '://' . parse_url( $sURL, PHP_URL_HOST );
            $_sURL         = add_query_arg(
                array(
                    'tag'      => $sAssociateID,
                    'language' => $sLanguage,
                ),
                $_sDomain . '/dp/' . $sASIN . '/'
            );

            $_sHTML    = $this->___getProductPage( $_sURL, $sLanguage );
            $_oScraper = new AmazonAutoLinks_ScraperDOM_Product( $_sHTML, $_sURL );
            $_aProduct = $_oScraper->get( $sAssociateID, $_sDomain );

            // @deprecated 4.2.2 Even if it fails to retrieve product data, a structure array will be returned and it's not empty
//            if ( empty( $_aProduct ) ) {
//                return $_aProduct;
//            }

            // If the thumbnail is not set, it means failure of retrieving the product data.
            if ( ! isset( $_aProduct[ 'thumbnail_url' ] ) ) {
                // @deprecated 4.2.2
//                $_aProduct[ 'thumbnail_url' ] = $this->getThumbnailURLFromASIN(
//                    $_aProduct[ 'ASIN' ],
//                    $this->___getLocaleFromURL( $_aProduct[ 'product_url' ] ),
//                    $this->oUnitOption->get( 'image_size' )
//                );
                unset( $_aProduct[ '_features' ] );
                return $_aProduct;
            }

            $_aProduct[ 'updated_date' ] = $this->getElement( $this->_aModifiedDates, $_sURL );
            $_sDescriptionExtracted      = $this->_getDescriptionSanitized(
                isset( $_aProduct[ 'description' ] ) ? $_aProduct[ 'description' ] : implode( ' ', $_aProduct[ '_features' ] ),
                $this->oUnitOption->get( 'description_length' ),
                $this->_getReadMoreText( $_aProduct[ 'product_url' ] )
            );
            $_aProduct[ 'description' ]  = $_sDescriptionExtracted
                ? "<div class='amazon-product-description'>"
                    . $_sDescriptionExtracted
                . "</div>"
                : '';
            $_aProduct[ 'content' ]      = ! empty( $_aProduct[ 'feature' ] )
                ? "<div class='amazon-product-content'>"
                    . $_aProduct[ 'feature' ]
                . "</div>"
                : '';

            unset( $_aProduct[ '_features' ] );
            return $_aProduct;

        }

            /**
             * @param  string $sURL
             * @param  string $sLanguage
             * @return string
             * @since  4.3.4
             */
            private function ___getProductPage( $sURL, $sLanguage ) {

                add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 4 );
                add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10, 5 );
                add_filter( 'aal_filter_http_request_result', array( $this, 'replyToCaptureError' ), 20, 2 );

                $_oHTTP   = new AmazonAutoLinks_HTTPClient(
                    $sURL,
                    86400,
                    array(
                        'timeout'     => 20,    // 20 seconds as the default is 5 seconds and it often times out
                        'redirection' => 20,
                    ),
                    $this->sUnitType . '_unit_type' // request type
                );

                $_sHTTPBody = $_oHTTP->getBody();
                remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );
                remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10 );
                remove_filter( 'aal_filter_http_request_result', array( $this, 'replyToCaptureError' ), 20 );

                return $_sHTTPBody;

            }
            /**
             * @param string $sURL
             *
             * @return  string
             */
            private function ___getAssociateIDFromURL( $sURL ) {

                $_bOverrideAssociatesIDOfURL = ( boolean ) $this->oOption->get( 'custom_oembed', 'override_associates_id_of_url' );
                if ( ! $_bOverrideAssociatesIDOfURL ) {
                    $_sQuery = parse_url( $sURL, PHP_URL_QUERY );
                    parse_str( $_sQuery, $_aQuery );
                    if ( isset( $_aQuery[ 'tag' ] ) ) {
                        return $_aQuery[ 'tag' ];
                    }
                }

                $_sHost   = parse_url( $sURL, PHP_URL_HOST ); // without https://
                $_sLocale = AmazonAutoLinks_Locales::getLocaleByDomain( $_sHost );
                $_sSetAssociatesID = $this->oOption->get( 'custom_oembed', 'associates_ids', $_sLocale );
                return $_sSetAssociatesID
                    ? $_sSetAssociatesID
                    : ( string ) $this->oUnitOption->get( 'associate_id' );

            }

    /**
     * Overrides parent method to return errors specific to this embed unit type.
     * @param array $aProducts
     *
     * @return string
     * @since   4.2.2
     */
    protected function _getError( $aProducts ) {
        /**
         * For users not using PA-API keys, errors should be displayed.
         * Otherwise, even failing to access to the store page, API requests can be preformed with ASIN.
         */
        if ( ! $this->oOption->isAPIConnected() && ! empty( $this->___aErrors ) ) {
            $_sErrors = implode( ' ', $this->___aErrors );
            $this->___aErrors = array();
            return $_sErrors;
        }
        return parent::_getError( $aProducts );
    }

    /**
     * Stores captured HTTP errors.
     * @var array
     */
    private $___aErrors = array();

    /**
     * @param  WP_Error|array $aoResponse
     * @param  string $sURL
     * @return array
     * @since  4.2.2
     * @since  4.3.4 Changed the parameters to be singular.
     */
    public function replyToCaptureError( $aoResponse, $sURL ) {
        remove_filter( 'aal_filter_http_request_result', array( $this, 'replyToCaptureError' ), 20 );
        if ( ! is_wp_error( $aoResponse ) ) {
            return $aoResponse;
        }
        $this->___aErrors[ $sURL ] = $aoResponse->get_error_message();
        return $aoResponse;
    }

    /**
     * Called when the unit has access to the plugin custom database table.
     *
     * Sets the 'content' and 'description' elements in the product (item) array which require plugin custom database table.
     *
     * @since       4.2.10
     * @return      array
     * @callback    add_filter      aal_filter_unit_each_product_with_database_row
     * @param   array $aProduct
     * @param   array $aDBRow
     * @param   array $aScheduleIdentifier
     */
    public function replyToFormatProductWithDBRow( $aProduct, $aDBRow, $aScheduleIdentifier=array() ) {

        remove_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFormatProductWithDBRow' ), 10 );

        if ( empty( $aProduct ) ) {
            return array();
        }

        $aProduct[ 'content' ]          = $this->_getContents( $aProduct, $aDBRow, $aScheduleIdentifier );
        $_sDescriptionExtracted         = $this->_getDescriptionSanitized(
            $aProduct[ 'content' ],
            $this->oUnitOption->get( 'description_length' ),
            $this->_getReadMoreText( $aProduct[ 'product_url' ] )
        );
        $aProduct[ 'description' ]      = $_sDescriptionExtracted
            ? "<div class='amazon-product-description'>"
                    . $_sDescriptionExtracted
                . "</div>"
            : '';

        $aProduct[ 'text_description' ] = strip_tags( $aProduct[ 'description' ] );
        if ( $this->isDescriptionBlocked( $aProduct[ 'text_description' ] ) ) {
            $this->aBlockedASINs[ $aProduct[ 'ASIN' ] ] = $aProduct[ 'ASIN' ];
            return array(); // will be dropped
        }
        return $aProduct;

    }

}