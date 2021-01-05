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
 * Creates Amazon product links by category.
 * 
 * @package Amazon Auto Links
 * @since   unknown
 * @since   3           Changed the name from `AmazonAutoLinks_UnitOutput_Category`.
 * @since   3.8.1       deprecated
 * @since   3.9.0       Serves as a base class for `AmazonAutoLinks_UnitOutput_category3`
 * @since   4.3.4       Merged with `AmazonAutoLinks_UnitOutput_category3`.
 */
class AmazonAutoLinks_UnitOutput_category extends AmazonAutoLinks_UnitOutput_Base_ElementFormat {

    /**
     * Stores the unit type.
     * @remark The base constructor creates a unit option object based on this value.
     */
    public $sUnitType = 'category';

    public static $aStructure_Product = array(
        'thumbnail_url'         => null,
        'ASIN'                  => null,
        'product_url'           => null,
        'raw_title'             => null,
        'title'                 => null,
        'description'           => null,    // the formatted feed item description - some elements are removed 
        'text_description'      => null,    // the non-html description
            
        // [3]
        'formatted_price'       => null, // 4.0.0+ (string|null) HTML formatted price. Changed from the name, `price` to be compatible with merged database table column key names.
        'review'                => null,
        'formatted_rating'      => null, // 4.0.0+ Changed from `rating` to distinguish from the database table column key name
        'image_set'             => null,
        'button'                => null,

        // [3.8.11]
        'proper_price'          => null,

        // used for disclaimer
        'updated_date'          => null,    // the date posted - usually it's the updated time of the feed at Amazon so it's useless
        
        // [3.3.0]
        'content'               => null,
        'meta'                  => null,
        'similar_products'      => null,

        // [3.8.0]
        'category'              => null,
        'feature'               => null,
        'sales_rank'            => null,

        // [3.9.0]
        'is_prime'              => null,

        // [4.1.0]
        'author'                => null,
    );

    /* @deprecated 4.3.4 Seems unnecessary. */
    /* public function get( $aURLs=array(), $sTemplatePath=null ) {
        return parent::get( $aURLs );
    }*/

    /**
     * Stores modified dates for HTTP requests so these can be applied to the product updated date.
     * @since 3.9.0
     * @since 4.0.0 Changed the scope to protected as the Embed unit type extends this class and uses this property.
     * @since 4.3.4 Moved from `AmazonAutoLinks_UnitOutput_category3`.
     * @var   array
     */
    protected $_aModifiedDates = array();

    /**
     * Sets up type-specific properties.
     */
    protected function _setProperties() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->getPAAPIStatus( $this->oUnitOption->get( 'country' ) ) ) {
            $this->_aItemFormatDatabaseVariables[] = '%description%'; // updated in `replyToFormatProductWithDBRow()`.
            $this->_aItemFormatDatabaseVariables[] = '%content%';
            $this->_aItemFormatDatabaseVariables[] = '%feature%';     // 3.8.0
            $this->_aItemFormatDatabaseVariables[] = '%category%';    // 3.8.0
            $this->_aItemFormatDatabaseVariables[] = '%rank%';        // 3.8.0
            $this->_aItemFormatDatabaseVariables[] = '%prime%';       // 3.9.0
        }
    }

        /**
         * @param  $aProducts
         * @return array
         * @since  4.3.4
         */
        private function ___getProductsSorted( $aProducts ) {
            $_sSortType   = $this->___getSortOrder();
            $_sMethodName = "_getItemsSorted_{$_sSortType}";
            return $this->{$_sMethodName}( $aProducts );
        }
            /**
             * Gets the sort type.
             *
             * ### Accepted Values
             * 'title'             => __( 'Title', 'amazon-auto-links' ),
             * 'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
             * 'random'            => __( 'Random', 'amazon-auto-links' ),
             * 'raw'               => __( 'Raw', 'amazon-auto-links' ),
             *
             * @since  3
             * @return string
             * @since  3.9.3  Changed the visibility to `protected`.
             * @since  4.3.4  Changed the visibility to `private` as unused except by this class.
             */
            private function ___getSortOrder() {
                $_sSortOrder = $this->oUnitOption->get( 'sort' );
                switch( $_sSortOrder ) {
                    case 'raw':
                        return 'raw';
                    case 'date':
                        return 'date_descending';
                    case 'title':
                        return 'title_ascending';
                    case 'title_descending':
                    case 'random':
                        return $_sSortOrder;
                    default:
                        return 'random';
                }
            }

    /**
     * Fetches and returns the associative array containing the output of product links.
     *
     * If the first parameter is not given,
     * it will determine the RSS urls by the post IDs from the given arguments set in the constructor.
     *
     * @param  array $aURLs
     * @return array The array contains product information.
     * @since  4.3.4 Moved from `AmazonAutoLinks_UnitOutput_category3`.
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
        $_aProducts          = $this->___getProductsSorted( $_aProducts );

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
            $_iPage         = 2;    // starts from 2
            $_iFoundCount   = count( $_aProducts );
            while( $_iFoundCount < $iItemCount ) {
                $_aURLs          = $this->___getURLsPageIncremented( $aURLs, $_iPage );
                $_aExcludeURLs   = $this->___getURLsPageIncremented( $aExcludeURLs, $_iPage );
                $_aThisFound     = $this->___getFoundProducts( $_aURLs, $_aExcludeURLs, $_iFoundCount - $iItemCount, $aExcludeASINs );  // recursive call
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
                $_sAssociateID    = $this->oUnitOption->get( 'associate_id' );
                $_oLocale         = new AmazonAutoLinks_Locale( $this->oUnitOption->get( 'country' ) );
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
                            $this->oOption->getPAAPIAccessKey( $_sLocale ), // public access key
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
     * @since    4.3.4 Moved from `AmazonAutoLinks_UnitOutput_category3`.
     */
    public function replyToFilterProducts( $aProduct ) {
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
        if ( $this->isDescriptionBlocked( $aProduct[ 'text_description' ] ) ) {
            $this->aBlockedASINs[ $aProduct[ 'ASIN' ] ] = $aProduct[ 'ASIN' ];
            return array(); // will be dropped
        }
        return $aProduct;
    }

    /**
     * Called when the unit has access to the plugin custom database table.
     *
     * Sets the 'content' and 'description' elements in the product (item) array which require plugin custom database table.
     *
     * @param array $aProduct
     * @param array $aDBRow
     * @param array $aScheduleIdentifier
     * @return      array
     * @callback    add_filter      aal_filter_unit_each_product_with_database_row
     * @since       3.3.0
     */
    public function replyToFormatProductWithDBRow( $aProduct, $aDBRow, $aScheduleIdentifier=array() ) {

        if ( empty( $aProduct ) ) {
            return array(); // probably filtered out.
        }

        $aProduct[ 'content' ]      = $this->___getContents( $aProduct, $aDBRow, $aScheduleIdentifier );
        $_sDescriptionExtracted     = $this->_getDescriptionSanitized(
            $aProduct[ 'content' ],
            $this->oUnitOption->get( 'description_length' ),
            $this->_getReadMoreText( $aProduct[ 'product_url' ] )
        );

        $_sDescriptionExtracted     = $_sDescriptionExtracted
            ? "<div class='amazon-product-description'>"
                . $_sDescriptionExtracted
            . "</div>"
            : '';
        $_sDescription              = ( $aProduct[ 'description' ] || $_sDescriptionExtracted )
            ? trim( $aProduct[ 'description' ] . " " . $_sDescriptionExtracted ) // only the meta is added by default
            : ''; // 3.10.0 If there is no description, do not even add the div element, which cause an extra margin as a block element.
        $aProduct[ 'description' ]  = $_sDescription;
        $_aProduct[ 'text_description' ] = strip_tags( $aProduct[ 'description' ] );
        return $aProduct;

    }
        /**
         * @return string
         * @since  3.3.0
         * @since  4.3.4  Changed the visibility to private from protected.
         * @param  array  $aProduct
         */
        private function ___getContents( $aProduct /*, $aDBRow, $aScheduleIdentifier */ ) {

            $_aParams            = func_get_args();
            $aDBRow              = $_aParams[ 1 ];
            $aScheduleIdentifier = $_aParams[ 2 ];
            $_oRow               = new AmazonAutoLinks_UnitOutput___Database_Product(
                $aScheduleIdentifier[ 'asin' ],
                $aScheduleIdentifier[ 'locale' ],
                $aScheduleIdentifier[ 'associate_id' ],
                $aDBRow,
                $this->oUnitOption
            );

            $_ansReviews         = $_oRow->getCell( 'editorial_reviews', array() );
            if ( $this->___hasEditorialReviews( $_ansReviews ) ) {
                $_oContentFormatter = new AmazonAutoLinks_UnitOutput__Format_content(
                    $_ansReviews,
                    $this->oDOM,
                    $this->oUnitOption
                );
                $_sContents = $_oContentFormatter->get();
                return "<div class='amazon-product-content'>"
                        . $_sContents
                    . "</div>";
            }
            $_snFeatures = $_oRow->getCell( 'features', '' );
            return $_snFeatures
                ? "<div class='amazon-product-content'>"
                    . $_snFeatures
                . "</div>"
                : '';

        }
            /**
             * For backward compatibility of a case that still the editorial reviews are stored in the cache.
             * @param  $anReviews
             * @remark This element is deprecated in PA-API 5.
             * @return bool
             * @since  3.10.0
             */
            private function ___hasEditorialReviews( $anReviews ) {
                // if null, the product data is not inserted in the plugin's database table.
                if ( is_null( $anReviews ) ) {
                    return false;
                }

                if ( is_string( $anReviews ) && $anReviews ) {
                    return true;
                }
                return is_array( $anReviews );
            }

    /**
     * @param    array  $aCache
     * @callback add_filter() aal_filter_http_response_cache
     * @return   array
     */
    public function replyToCaptureUpdatedDate( $aCache ) {
        remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );
        $this->_aModifiedDates[ $aCache[ 'request_uri' ] ] = $this->___getLastModified( $aCache[ 'data' ], $aCache[ '_modified_timestamp' ] );
        return $aCache;
    }

    /**
     * @param    array|WP_error $aoResponse
     * @param    string         $sURL
     * @callback add_filter()   aal_filter_http_request_response
     * @since    4.2.2
     * @return   array|WP_Error
     */
    public function replyToCaptureUpdatedDateForNewRequest( $aoResponse, $sURL ) {
        remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10 );
        $this->_aModifiedDates[ $sURL  ] = $this->___getLastModified( $aoResponse, time() );
        return $aoResponse;
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

}