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
 * Creates Amazon product links by category, supporting direct scraping.
 * 
 * @package     Amazon Auto Links
 * @since       3.9.0           Changed the name from `AmazonAutoLinks_UnitOutput_Category`.
 */
class AmazonAutoLinks_UnitOutput_category3 extends AmazonAutoLinks_UnitOutput_category {

    /**
     * Stores modified dates for HTTP requests so these can be applied to the product updated date.
     * @since   3.9.0
     * @since   4.0.0   Changed the scope to protected as the Embed unit type extends this class and uses this property.
     * @var array
     */
    protected $_aModifiedDates = array();

    /**
     * Override the parent method that accesses the duplicate properties.
     * @since   4.2.0
     */
    protected function _setProperties() {
        // do nothing.
    }

    /**
     * Fetches and returns the associative array containing the output of product links.
     * 
     * If the first parameter is not given, 
     * it will determine the RSS urls by the post IDs from the given arguments set in the constructor.
     *
     * @param  array $aURLs
     * @return array The array contains product information.
     */
    public function fetch( $aURLs=array() ) {

        $_aPageURLs          = wp_list_pluck( $this->oUnitOption->get( array( 'categories' ), array() ), 'page_url' );
        $_aPageURLs          = array_merge( $aURLs, $this->___getURLs( $_aPageURLs ) );
        $_aExcludingPageURLs = wp_list_pluck( $this->oUnitOption->get( array( 'categories_exclude' ), array() ), 'page_url' );
        $_aExcludingPageURLs = $this->___getURLs( $_aExcludingPageURLs );

        $_sLocale            = ( string ) $this->oUnitOption->get( 'country' );
        $_sAssociateID       = ( string ) $this->oUnitOption->get( 'associate_id' );
        $_iCount             = ( integer ) $this->oUnitOption->get( 'count' );

        $_aProducts          = $this->___getFoundProducts( $_aPageURLs, $_aExcludingPageURLs, $_iCount );

        $_aProducts          = $this->_getProductsSorted( $_aProducts );

        return $this->_getProducts( $_aProducts, $_sLocale, $_sAssociateID, $_iCount );

    }
        /**
         * Returns the subject urls for this unit.
         * @scope   protected   the category2 unit output class extends this method to set own URLs.
         * @param   $aURLs
         * @since   3.9.0
         * @return  array
         */
        private function ___getURLs( array $aURLs ) {

            $_aAllURLs = array();
            foreach( $aURLs as $_sURL ) {

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
         * Fetches product data.
         *
         * @param  array    $aURLs
         * @param  array    $aExcludeURLs
         * @param  integer  $iItemCount
         * @param  array    $aExcludeASINs
         * @return array
         */
        private function ___getFoundProducts( array $aURLs, array $aExcludeURLs, $iItemCount, array $aExcludeASINs=array() ) {

            // Find products
            add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 4 );
            add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10, 5 );
            $_aHTMLs          = $this->___getHTTPBodies( $aURLs );

            $_sAssociateID    = $this->oUnitOption->get( 'associate_id' );
            $_oLocale         = new AmazonAutoLinks_Locale( $this->oUnitOption->get( 'country' ) );
            $_sMarketPlaceURL = $_oLocale->getMarketPlaceURL();
            $_aProducts       = $this->___getProductsScraped( $_aHTMLs, $_sAssociateID, $_sMarketPlaceURL );

            // If no items found, no need to continue the rest.
            if ( empty( $_aProducts ) ) {
                return $_aProducts;
            }

            // Excluding Items
            $_aExcludeHTMLs    = $this->___getHTTPBodies( $aExcludeURLs );
            $_aExcludeASINs    = array_unique( array_merge( $aExcludeASINs, $this->___getASINsExtracted( $_aExcludeHTMLs ) ) );
            $_aExcludeASINKeys = array_flip( $_aExcludeASINs );
            $_aProducts        = array_diff_key( $_aProducts, $_aExcludeASINKeys );

            // Make sure to fill the set number of item count
            $_iPage         = 2;    // starts from 2
            $_iFoundCount   = count( $_aProducts );
            while( $_iFoundCount < $iItemCount ) {
                $_aURLs          = $this->___getURLsPageIncremented( $aURLs, $_iPage );
                $_aExcludeURLs   = $this->___getURLsPageIncremented( $aExcludeURLs, $_iPage );
                $_aThisFound     = $this->___getFoundProducts( $_aURLs, $_aExcludeURLs, $_iFoundCount - $iItemCount, $_aExcludeASINs );  // recursive call
                if ( empty( $_aThisFound ) ) {
                    break;
                }
                $_aNewFoundItems = array_diff( array_keys( $_aThisFound ), array_keys( $_aProducts ) );
                if ( empty( $_aNewFoundItems ) ) {
                    break;  // if the previous found items cover the the newly found items, then it could be a page that does not support pagination.
                }
                $_aProducts     = $_aProducts + $_aThisFound;
                if ( $_iPage > 10 ) {
                    break;
                }
                $_iPage++;
            }
            return $_aProducts;

        }
            /**
             * @callback    filter      aal_filter_http_response_cache
             * @return      array
             */
            public function replyToCaptureUpdatedDate( $aCache, $iCacheDuration, $aArguments, $sRequestType ) {
                remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );
                $this->_aModifiedDates[ $aCache[ 'request_uri' ] ] = $this->___getLastModified( $aCache[ 'data' ], $aCache[ '_modified_timestamp' ] );
                return $aCache;
            }
            /**
             * @callback filter
             * @since    4.2.2
             * @return   array|WP_Error
             */
            public function replyToCaptureUpdatedDateForNewRequest( $oaResult, $sURL, $aArguments, $sRequestType, $iCacheDuration ) {
                remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10 );
                $this->_aModifiedDates[ $sURL  ] = $this->___getLastModified( $oaResult, time() );
                return $oaResult;
            }
                /**
                 * Extracts the last-modified header item and convert it to the unix timestamp.
                 * @since   4.2.10
                 * @param   WP_Error|array  $aoResponse
                 * @param   integer         $iDefault
                 * @return  integer
                 */
                private function ___getLastModified( $aoResponse, $iDefault=0 ) {
                    $_sResponseDate = wp_remote_retrieve_header( $aoResponse, 'last-modified' );
                    $_sResponseDate = $_sResponseDate
                        ? $_sResponseDate
                        : wp_remote_retrieve_header( $aoResponse, 'date' );
                    return $_sResponseDate
                        ? strtotime( $_sResponseDate )
                        : $iDefault;
                }

            /**
             * @param array $aURLs
             * @param $iPage
             *
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
                $_oHTTP = new AmazonAutoLinks_HTTPClient_Multiple(
                    $aURLs,
                    $this->oUnitOption->get( 'cache_duration' ),
                    array(  // http arguments
                        'timeout'     => 20,
                        'redirection' => 20,
                    ),
                    $this->sUnitType . '_unit_type' // request type
                );
                return $_oHTTP->get();
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
                foreach( $aHTMLs as $_sURL => $_sHTML ) {
                    $_oProductScraper = new AmazonAutoLinks_ScraperDOM_BestsellerProducts( $_sHTML );
                    $_aFoundProducts  = $_oProductScraper->get( $sAssociateID, $sSiteDomain );
                    $_sModifiedDate   = $this->getElement( $this->_aModifiedDates, $_sURL );
                    foreach( $_aFoundProducts as $_sASIN => $_aFoundProduct ) {
                        $_aFoundProducts[ $_sASIN ][ 'updated_date' ] = $_sModifiedDate;
                    }
                    $_aProducts       = $_aProducts + $_aFoundProducts;
                }
                return $_aProducts;
            }

            /**
             * @param array $aHTMLs
             *
             * @return array    a liner array holding found ASINs
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
             * @param array   $aItems
             * @param string  $sLocale       The `country` unit argument value
             * @param string  $sAssociateID
             * @param integer $iCount
             *
             * @return      array
             * @since       3.9.0
             * @since       4.0.0   Changed the scope to protected for the Embed unit type to extend this class.
             */
            protected function _getProducts( array $aItems, $sLocale, $sAssociateID, $iCount ) {

                // First Iteration - Extract displaying ASINs.
                $_aASINLocaleCurLangs = array();  // stores added product ASINs, locales, currencies and languages for performing a custom database query.
                $_aProducts           = array();

                $_sLocale             = $sLocale ? strtoupper( $sLocale ) : strtoupper( $this->oUnitOption->get( array( 'country' ), 'US' ) );
                $_sCurrency           = $this->oUnitOption->get( array( 'preferred_currency' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale ) );
                $_sLanguage           = $this->oUnitOption->get( array( 'language' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale ) );

                foreach ( $aItems as $_iIndex => $_aItem ) {

                    $_sASIN = $this->getElement( $_aItem, array( 'ASIN' ), '' );

                    // This parsed item is no longer needed and must be removed once it is parsed
                    // as this method is called recursively.
                    unset( $aItems[ $_sASIN ] );

                    try {

                        $_aProduct = $this->___getProduct( $_aItem, $sLocale, $sAssociateID );

                    } catch ( Exception $_oException ) {
                        // When the items is filtered, this is reached
                        // AmazonAutoLinks_Debug::log( $_oException->getMessage() );
                        if ( false !== strpos( $_oException->getMessage(), '(product filter)' ) ) {
                            $this->aBlockedASINs[ $_sASIN ] = $_sASIN;
                        }
                        continue;   // skip
                    }


                    $_aASINLocaleCurLang    = "{$_aProduct[ 'ASIN' ]}|{$_sLocale}|{$_sCurrency}|{$_sLanguage}";
                    $_aASINLocaleCurLangs[ $_aASINLocaleCurLang ] = array(
                        'asin'      => $_aProduct[ 'ASIN' ],
                        'locale'    => $_sLocale,
                        'currency'  => $_sCurrency,
                        'language'  => $_sLanguage,
                    );

                    // Store the product
                    $_aProducts[]           = $_aProduct;

                    // Max Number of Items
                    if ( count( $_aProducts ) >= $iCount ) {
                        break;
                    }

                }

                // Second iteration
                return $this->___getProductsFormatted( $aItems, $_aProducts, $_aASINLocaleCurLangs, $sLocale, $sAssociateID, $iCount );

            }

                /**
                 * Second iteration
                 * @param  array   $aItems
                 * @param  array   $_aProducts
                 * @param  array   $aASINLocaleCurLangs     Holding the information of ASIN, locale, currency and language for a database query.
                 * @param  string  $_sLocale
                 * @param  string  $_sAssociateID
                 * @param  integer $_iCount
                 * @return array
                 * @since  3.9.0
                 */
                private function ___getProductsFormatted( $aItems, $_aProducts, $aASINLocaleCurLangs, $_sLocale, $_sAssociateID, $_iCount ) {

                    add_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFormatProductWithDBRow' ), 10, 3 );
                    add_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFilterProducts' ), 100, 1 );
                    $_iResultCount          = count( $_aProducts );
                    try {

                        $_aProducts             = $this->_getProductsFormatted( $_aProducts, $aASINLocaleCurLangs, $_sLocale, $_sAssociateID );
                        $_iCountAfterFormatting = count( $_aProducts );
                        if ( $_iResultCount > $_iCountAfterFormatting ) {
                            throw new Exception( $_iCount - $_iCountAfterFormatting ); // passing a count for another call
                        }

                    } catch ( Exception $_oException ) {

                        // Recursive call
                        $_aAdditionalProducts = $this->_getProducts(
                            $aItems,
                            $_sLocale,
                            $_sAssociateID,
                            ( integer ) $_oException->getMessage() // the number of items to retrieve
                        );
                        $_aProducts = array_merge( $_aProducts, $_aAdditionalProducts );

                    }

                    // These removal are necessary as the hooks might not be called so the remove_filter() inside the callback method does not get triggered.
                    remove_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFormatProductWithDBRow' ), 10 );
                    remove_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFilterProducts' ), 100 );

                    return $_aProducts;

                }

                /**
                 *
                 * @param  array  $_aItem
                 * @param  string $_sLocale
                 * @param  string $_sAssociateID
                 * @return array
                 * @throws Exception
                 * @since  3.9.0
                 */
                private function ___getProduct( $_aItem, $_sLocale, $_sAssociateID ) {

                    $_aProduct = $_aItem + self::$aStructure_Product;

                    // ASIN - required to detect duplicated items.
                    if ( $this->isASINBlocked( $_aProduct[ 'ASIN' ] ) ) {
                        throw new Exception( '(product filter) The ASIN is black-listed: ' . $_aProduct[ 'ASIN' ] );
                    }

                    // Product Link (hyperlinked url) - ref=nosim, linkstyle, associate id etc.
                    $_aProduct[ 'product_url' ] = $this->getProductLinkURLFormatted(
                        $_aProduct[ 'product_url' ],
                        $_aProduct[ 'ASIN' ],
                        $this->oUnitOption->get( 'language' ),
                        $this->oUnitOption->get( 'preferred_currency' )
                    );

                    // Title
                    $_aProduct[ 'raw_title' ] = $this->getElement( $_aProduct, 'title' );
                    $_aProduct[ 'title' ]     = $this->getTitleSanitized( $_aProduct[ 'raw_title' ], $this->oUnitOption->get( 'title_length' ) );

                    // Description - plain text and HTML versions
                    // @remark The description (content) element is retrieved later from the database
//                    $_aProduct[ 'text_description' ] = '';
                    // @remark this is handled in the `replyToFormatProductWithDBRow()` method below.
//                    if ( $this->isDescriptionBlocked( $_aProduct[ 'text_description' ] ) ) {
//                        throw new Exception( 'The description is black-listed: ' . $_aProduct[ 'text_description' ] );
//                    }

                    // At this point, update the black&white lists as this item is parsed.
                    $this->setParsedASIN( $_aProduct[ 'ASIN' ] );

                    // Thumbnail
                    $_aProduct[ 'thumbnail_url' ]    = $this->getProductImageURLFormatted(
                        $_aProduct[ 'thumbnail_url' ],
                          $this->oUnitOption->get( 'image_size' ),
                          strtoupper( $this->oUnitOption->get( 'country' ) )  // locale
                    );

                    // Format the item
                    // Thumbnail
                    $_aProduct[ 'formatted_thumbnail' ] = $this->_getProductThumbnailFormatted( $_aProduct );

                    // Title
                    $_aProduct[ 'formatted_title' ]     = $this->getProductTitleFormatted( $_aProduct, $this->oUnitOption->get( 'title_format' ) );

                    // Button - check if the %button% variable exists in the item format definition.
                    // It accesses the database, so if not found, the method should not be called.
                    if ( $this->oUnitOption->hasItemFormatTags( array( '%button%', ) ) ) {
                        $_aProduct[ 'button' ] = $this->_getButton(
                            $this->oUnitOption->get( 'button_type' ),
                            $this->_getButtonID(),
                            $_aProduct[ 'product_url' ],
                            $_aProduct[ 'ASIN' ],
                            $_sLocale,
                            $_sAssociateID,
                            $this->oOption->get( 'authentication_keys', 'access_key' ), // public access key
                            $this->oUnitOption->get( 'override_button_label' ) ? $this->oUnitOption->get( 'button_label' ) : null
                        );
                    }

                    /**
                     * Let third-parties filter products.
                     * @since 3.4.13
                     */
                    $_aProduct = apply_filters(
                        'aal_filter_unit_each_product',
                        $_aProduct,
                        array(
                            'locale'        => $_sLocale,
                            'asin'          => $_aProduct[ 'ASIN' ],
                            'associate_id'  => $_sAssociateID,
                            'asin_locale'   => $_aProduct[ 'ASIN' ] . '_' . strtoupper( $_sLocale ),
                        ),
                        $this
                    );
                    if ( empty( $_aProduct ) ) {
                        throw new Exception( 'The product array is empty. Most likely it is filtered out.' );
                    }
                    return $_aProduct;

                }

    /**
     * @remark   The timing of filtering items by image and title is changed in order to support resuming with caches.
     * @param    array $aProduct
     * @return   array        A product array, empty when filtered out.
     * @callback add_filter() aal_filter_unit_each_product_with_database_row
     * @since    4.2.8
     */
    public function replyToFilterProducts( $aProduct ) {
        remove_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFilterProducts' ), 100 );
        if ( empty( $aProduct ) ) {
            return array();
        }
        if ( $this->isTitleBlocked( $this->getElement( $aProduct, array( 'raw_title' ) ) ) ) {
            return array();
        }
        // Check whether no-image should be skipped.
        if ( ! $this->isImageAllowed( $this->getElement( $aProduct, array( 'thumbnail_url' ) ) ) ) {
            return array();
        }
        return $aProduct;
    }

    /**
     * Called when the unit has access to the plugin custom database table.
     *
     * Sets the 'content' and 'description' elements in the product (item) array which require plugin custom database table.
     *
     * @param    array $aProduct
     * @param    array $aDBRow
     * @param    array $aScheduleIdentifier
     * @return   array
     * @callback add_filter      aal_filter_unit_each_product_with_database_row
     * @since    3.9.0
     */
    public function replyToFormatProductWithDBRow( $aProduct, $aDBRow, $aScheduleIdentifier=array() ) {

        remove_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFormatProductWithDBRow' ), 10 );
        if ( empty( $aProduct ) ) {
            return array();
        }

        // @todo Apply the no-pending-item filter and drop products without content/description/meta

        $_aProduct = parent::replyToFormatProductWithDBRow( $aProduct, $aDBRow, $aScheduleIdentifier );
        $_aProduct[ 'text_description' ] = strip_tags( $_aProduct[ 'description' ] );
        if ( $this->isDescriptionBlocked( $_aProduct[ 'text_description' ] ) ) {
            $this->aBlockedASINs[ $_aProduct[ 'ASIN' ] ] = $_aProduct[ 'ASIN' ];
            return array(); // will be dropped
        }
        return $_aProduct;

    }

}