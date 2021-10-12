<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Updates unit status via ajax calls.
 * @since   4.3.0
 *
 */
class AmazonAutoLinks_Unit_EventAjax_NowRetrievingUpdater extends AmazonAutoLinks_AjaxEvent_Base {

    /**
     * The part after `wp_ajax_` or `wp_ajax_nopriv_`.
     * @var string
     */
    protected $_sActionHookSuffix = 'aal_action_update_now_retrieving';

    protected $_bLoggedIn = true;
    protected $_bGuest    = true;

    protected function _construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'replyToEnqueueResources' ) );
        add_action( 'enqueue_embed_scripts', array( $this, 'replyToEnqueueResources' ) ); // [4.4.0]
    }

    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.6.18
     */
    protected function _getPostSanitized( array $aPost ) {
        return array(
            'items' => $this->getElementAsArray( $aPost, array( 'items' ) ),
        );
    }

    /**
     * @return string|array
     * @throws Exception        Throws a string value of an error message.
     * @since  4.3.0
     * @param array $aPost      POST data array containing, the `items` array element.
     */
    protected function _getResponse( array $aPost ) {

        // If there are pending plugin tasks, finish them now.
        AmazonAutoLinks_Shadow::doTasks();
        if ( ! did_action( 'aal_action_check_tasks' ) ) {
            do_action( 'aal_action_check_tasks' );
        }

        $_aAllItems           = $this->getElementAsArray( $aPost, array( 'items' ) );
        $this->___updateProductsWithAdWidgetAPI( $_aAllItems );
        $_aProducts           = $this->___getProducts( $_aAllItems );
        $_aElements           = array();
        foreach( $_aAllItems as $_sASINLocaleCurLang => $_aDueElements ) {
            $_aElements = array_merge(
                $_aElements,
                $this->___getElementsByASINLocaleCurLang( $_aDueElements, $_aProducts )
            );
        }
        // @deprecated 4.3.4
        // $this->___handleRatingsAndReviews( $_aElements );
        return $_aElements;

    }
        /**
         * @param array $aAllItems
         * The structure:
         * Array(
         *     [B08MF4NHXZ|IT|EUR|it_IT] => Array(
         *         [formatted_rating] => Array(
         *             [item_format_tags] => (string, len`gth: 88) &image&,&image_set&,&title&,&rating&,&prime&,&price&,&description&,&button&,&disclaimer&
         *             [call_id] => (string, length: 13) 60fecdb30833c
         *             [cache_duration] => (string, length: 5) 86400
         *             [attempt] => (string, length: 1) 1
         *             [id] => (string, length: 3) 693
         *             [context] => (string, length: 16) formatted_rating
         *             [tag] => (string, length: 15) amazonwidget-21
         *             [asin] => (string, length: 10) B08MF4NHXZ
         *             [language] => (string, length: 5) it_IT
         *             [currency] => (string, length: 3) EUR
         *             [type] => (string, length: 6) search
         *             [locale] => (string, length: 2) IT
         *          )
         *      ),
         *     [B08FHT7ZVH|IT|EUR|it_IT] => Array(
         *         [formatted_rating] => Array(
         *             [item_format_tags] => (string, length: 88) &image&,&image_set&,&title&,&rating&,&prime&,&price&,&description&,&button&,&disclaimer&
         *             [call_id] => (string, length: 13) 60fecdb30833c
         *             [cache_duration] => (string, length: 5) 86400
         *             [attempt] => (string, length: 1) 1
         *             [id] => (string, length: 3) 693
         *             [context] => (string, length: 16) formatted_rating
         *             [tag] => (string, length: 15) amazonwidget-21
         *             [asin] => (string, length: 10) B08FHT7ZVH
         *             [language] => (string, length: 5) it_IT
         *             [currency] => (string, length: 3) EUR
         *             [type] => (string, length: 6) search
         *             [locale] => (string, length: 2) IT
         *         ),
         *     ),
         * )
         * @since  4.6.9
         * @remark The Ad Widget API does not support currency and language.
         */
        private function ___updateProductsWithAdWidgetAPI( array $aAllItems ) {
            $_aAdWidgetLocales  = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport();
            $_aItemsByLocale    = array();
            $_aAllowedContexts  = array( 'formatted_price', 'formatted_rating', 'title' );
            foreach( $aAllItems as $_sASINLocaleCurLang => $_aElements ) {
                $_aContexts          = array_keys( $_aElements );
                if ( ! count( array_intersect( $_aContexts, $_aAllowedContexts ) ) ) {
                    continue;
                }
                $_aASINLocaleCurLang = explode( '|', $_sASINLocaleCurLang );
                $_sASINThis          = $_aASINLocaleCurLang[ 0 ];
                $_sLocaleThis        = $_aASINLocaleCurLang[ 1 ];
                if ( ! in_array( $_sLocaleThis, $_aAdWidgetLocales, true ) ) {
                    continue;
                }
                $_aItemsByLocale[ $_sLocaleThis ] = isset( $_aItemsByLocale[ $_sLocaleThis ] ) ? $_aItemsByLocale[ $_sLocaleThis ] : array();
                $_aItemsByLocale[ $_sLocaleThis ][ $_sASINThis ] = array(
                    'ASIN' => $_sASINThis,
                ) + reset( $_aElements ); // the first item
            }
            foreach( $_aItemsByLocale as $_sLocale => $_aItems ) {
                $_aFirstItem     = reset( $_aItems );
                $_iCacheDuration = ( integer ) $this->getElement( $_aFirstItem, array( 'cache_duration' ) );
                do_action( 'aal_action_update_products_with_ad_widget_api', $_sLocale, $_aItems, $_iCacheDuration, false );
            }
        }
        /**
         * @param array $aResults
         * @since 4.3.1
         * @deprecated 4.3.4    These request requires certain intervals so leave it to wp-cron.
         */
/*        private function ___handleRatingsAndReviews( array $aResults ) {

            // Extract only the review and rating items.
            $_aRatingItems = array();
            $_aReviewItems = array();
            foreach( $aResults as $_iIndex => $_aItem ) {
                if ( $_aItem[ 'set' ] ) {
                    continue;
                }
                if ( 'formatted_rating' === $_aItem[ 'context' ] ) {
                    $_aRatingItems[] = $_aItem;
                    continue;
                }
                if ( 'review' === $_aItem[ 'context' ] ) {
                    $_aReviewItems[] = $_aItem;
                    continue;
                }
            }

            // Perform an HTTP request only for the first item. Schedule fetching the rest. This is to give some interval between requests to avoid being blocked.
            $_aFirstItem = array_shift($_aRatingItems );
            if ( empty( $_aFirstItem ) ) {
                return;
            }
            do_action(
                'aal_action_api_get_product_rating',
                array(
                    "{$_aFirstItem[ 'asin' ]}|{$_aFirstItem[ 'locale' ]}|{$_aFirstItem[ 'currency' ]}|{$_aFirstItem[ 'language' ]}", // product id
                    $_aFirstItem[ 'cache_duration' ],
                    false,
                )
            );

            // Rating
            foreach( $_aRatingItems as $_aItem ) {
                AmazonAutoLinks_Event_Scheduler::scheduleRating(
                    "{$_aItem[ 'asin' ]}|{$_aItem[ 'locale' ]}|{$_aItem[ 'currency' ]}|{$_aItem[ 'language' ]}", // product id
                    $_aItem[ 'cache_duration' ],
                    false
                );
            }
            // Reviews
            foreach( $_aReviewItems as $_aItem ) {
                AmazonAutoLinks_Event_Scheduler::scheduleReview(
                    "{$_aItem[ 'asin' ]}|{$_aItem[ 'locale' ]}|{$_aItem[ 'currency' ]}|{$_aItem[ 'language' ]}", // product id
                    $_aItem[ 'cache_duration' ],
                    false
                );
            }

        }*/

        /**
         * @remark Fetches products as the item_lookup unit type regardless of passed unit types.
         * This is because in a unit with a large number of item count with the random sort order, queried items become different.
         * @param  array $aAllItems
         * @return array
         * @since  4.3.0
         */
        private function ___getProducts( array $aAllItems ) {
            $_aUnitOptionSets     = $this->___getUnitOptionSets( $aAllItems );
            $_aProducts           = array();
            foreach( $_aUnitOptionSets as $_aUnitOptions ) {
                $_aItemIds   = $_aUnitOptions[ 'ItemIds' ];
                $_aChunks    = array_chunk( $_aItemIds, 10 );
                foreach( $_aChunks as $_aChunkedItemIds ) {
                    $_aUnitOptions[ 'ItemIds' ] = $_aChunkedItemIds;
                    $_oUnit      = new AmazonAutoLinks_UnitOutput_item_lookup( $_aUnitOptions );
                    $_aProducts  = $_aProducts + $this->___getProductsFormatted( $_oUnit->fetch(), $_oUnit->oUnitOption );
                }
            }
            return $_aProducts;
        }
        /**
         * @return array An array holding sets of unit options, separated by locale-currency-language.
         * @param  array $aAllItems Items to update.
         * @since  4.3.0
         */
        private function ___getUnitOptionSets( array $aAllItems ) {

            // Classify items first.
            $_aSetsByUnitID   = array();
            $_aSetsByCallID   = array();    // when unit id is not set, this will be used
            foreach( $aAllItems as $_sProductID => $_aItemsByContext ) {
                $_aItem = reset( $_aItemsByContext );
                $_iID   = ( integer ) $this->getElement( $_aItem, array( 'id' ), 0 );
                if ( $_iID ) {
                    $_aSetsByUnitID[ $_iID ][ $_sProductID ] = $_aItemsByContext;
                    unset( $aAllItems[ $_sProductID ] );
                    continue;
                }
                $_sCallID = $this->getElement( $_aItem, array( 'call_id' ), '' );
                if ( $_sCallID ) {
                    $_aSetsByCallID[ $_sCallID ][ $_sProductID ] = $_aItemsByContext;
                    unset( $aAllItems[ $_sProductID ] );
                    continue;
                }
            }

            // Generate and merge.
            return $this->___getUnitOptionSetsByItems( $_aSetsByUnitID )
                + $this->___getUnitOptionSetsByItems( $_aSetsByCallID );
            
        }
            /**
             * @param  array $aSetsByID
             * @return array
             * @since  4.3.4
             */
            private function ___getUnitOptionSetsByItems( array $aSetsByID ) {
                $_aUnitOptions = array();
                foreach( $aSetsByID as $_iID => $_aItemsByProductID ) {
                    $_aUnitOptions = $_aUnitOptions
                         + $this->___getUnitOptionSetsGeneratedWithID( $_iID, $_aItemsByProductID );
                }
                return $_aUnitOptions;
            }
                /**
                 * Generates unit arguments with the first item and then add ASINs to the `ItemIDs` argument.
                 * @param  integer|string $isID A call ID or unit ID 
                 * @param  array $aItemsByProductID
                 * @return array
                 */
                private function ___getUnitOptionSetsGeneratedWithID( $isID, $aItemsByProductID ) {
                    
                    $_aUnitOptionSets = array();
                    foreach( array_keys( $aItemsByProductID ) as $_sASINLocaleCurLang ) {
                        
                        $_aElements      = $this->getElementAsArray( $aItemsByProductID, array( $_sASINLocaleCurLang ) );
                        $_aElement       = reset( $_aElements );
                        $_sAssociateTag  = $this->getElement( $_aElement, array( 'tag' ) );
                        $_aParts         = explode( '|', $_sASINLocaleCurLang );
                        $_sASIN          = $_aParts[ 0 ];
                        $_sLocale        = $_aParts[ 1 ];
                        $_sCurrency      = $_aParts[ 2 ];
                        $_sLanguage      = $_aParts[ 3 ];
                        $_sKey           = "{$isID}|{$_sLocale}|{$_sCurrency}|{$_sLanguage}";
                        $_aUnitOptionSets[ $_sKey ] = isset( $_aUnitOptionSets[ $_sKey ] )
                            ? $_aUnitOptionSets[ $_sKey ]
                            : array(
                                'country'            => $_sLocale,
                                'preferred_currency' => $_sCurrency,
                                'language'           => $_sLanguage,
                                'show_now_retrieving_message'  => true,
                                'ItemIds'            => array(),
                                'associate_id'       => $_sAssociateTag,
                                'item_format'        => str_replace( '&', '%', $this->getElement( $_aElement, array( 'item_format_tags' ) ) ),
                            );
                        $_aUnitOptionSets[ $_sKey ][ 'ItemIds' ][] = $_sASIN;
                    }
                    return $_aUnitOptionSets;                    
                    
                }


        /**
         * Converts numeric indices to associate keys.
         * @param array $aProducts
         * @param AmazonAutoLinks_UnitOption_Base $oUnitOption
         * @return array A formatted products array.
         * @since 4.3.0
         */
        private function ___getProductsFormatted( array $aProducts, AmazonAutoLinks_UnitOption_Base $oUnitOption ) {
            $_aNewProducts = array();
            foreach( $aProducts as $_aProduct ) {
                if ( ! is_array( $_aProduct ) ) {
                    continue;
                }
                $_aProduct = array(
                    'locale'   => $oUnitOption->get( 'country' ),
                    'currency' => $oUnitOption->get( 'preferred_currency' ),
                    'language' => $oUnitOption->get( 'language' ),
                ) + $_aProduct + array(
                    'ASIN'     => '',
                );
                $_sKey = "{$_aProduct[ 'ASIN' ]}|{$_aProduct[ 'locale' ]}|{$_aProduct[ 'currency' ]}|{$_aProduct[ 'language' ]}";
                $_aNewProducts[ $_sKey ] = $_aProduct;
            }
            return $_aNewProducts;
        }

        /**
         * @param  array  $aDueElements
         * @param  array  $aProducts
         * @return array
         * @since 4.3.0
         */
        private function ___getElementsByASINLocaleCurLang( array $aDueElements, array $aProducts ) {

            // If the element is still pending, the 'output' element will be empty. Then it will be queued again.
            $_aElements   = array();
            $_aStructure  = array(
                'context'   => '', 'asin'      => '', 'tag'       => '',
                'locale'    => '', 'currency'  => '', 'language'  => '',
                'output'    => '', 'id'        => 0,  'type'      => '',
                'set'       => 0,  'cache_duration'   => 86400,
                'call_id'   => '', 'item_format_tags' => '',
            );
            foreach( $aDueElements as $_sContext => $_aDueElement ) {
                $_aDueElement = $_aDueElement + $_aStructure;
                $_snOutput    = $this->___getElementOutput( $_aDueElement, $aProducts );
                $_aUpdateItem = array(
                    'output'    => $_snOutput,
                    'set'       => ( integer ) $this->___isElementOutputSet( $_snOutput ),
                ) + $_aDueElement;
                $_aElements[] = $_aUpdateItem;
            }
            return $_aElements;

        }
            /**
             * @param $snOutput
             * @return bool
             * @since 4.3.0
             */
            private function ___isElementOutputSet( $snOutput ) {
                if ( is_null( $snOutput ) ) {
                    return false;
                }
                if (
                    strpos( $snOutput, 'now-retrieving' )
                    && strpos( $snOutput, 'data-locale' )
                    && strpos( $snOutput, 'data-type' )
                    && strpos( $snOutput, 'data-currency' )
                    && strpos( $snOutput, 'data-language' )
                    && strpos( $snOutput, 'data-asin' )
                ) {
                    return false;
                }
                return is_scalar( $snOutput );
            }
            /**
             * @param array $aDueElement
             * @param array $aProducts
             * @return string|null       When the element is not set, null is returned. This is to cache up cases that an empty string is returned which means the database already stores the result of an empty string. This is different from a case of unset (null) that the API request has not been done yet.
             * @since 4.3.0
             */
            private function ___getElementOutput( array $aDueElement, array $aProducts ) {
                $_sASINLocaleCurLang = "{$aDueElement[ 'asin' ]}|{$aDueElement[ 'locale' ]}|{$aDueElement[ 'currency' ]}|{$aDueElement[ 'language' ]}";
                if ( ! isset( $aProducts[ $_sASINLocaleCurLang ] ) ) {
                    return null;
                }
                $_aProduct = $aProducts[ $_sASINLocaleCurLang ];
                if ( ! isset( $_aProduct[ $aDueElement[ 'context' ] ] ) ) {
                    return null;
                }
                /**
                 * Allows components to modify product element for Ajax requests.
                 * @since 4.6.0
                 */
                return apply_filters( 'aal_filter_now_retrieving_product_element', $_aProduct[ $aDueElement[ 'context' ] ], $aDueElement[ 'context' ], $_aProduct );
            }

    /**
     * Enqueues scripts and styles for unit outputs.
     * @since 4.3.0
     */
    public function replyToEnqueueResources() {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isPAAPIConnectedByAnyLocale() ) {
            return;
        }
        $_sScriptHandle = 'aal-now-retrieving-updater';
        $_sFileBaseName = $this->isDebugMode()
            ? 'now-retrieving-updater.js'
            : 'now-retrieving-updater.min.js';
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script(
            $_sScriptHandle,
            $this->getSRCFromPath( AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/js/' . $_sFileBaseName ),
            array( 'jquery' ),
            false,
            true
        );
        wp_localize_script(
            $_sScriptHandle,
            'aalNowRetrieving', // variable name
            array(
                'ajaxURL'            => admin_url( 'admin-ajax.php' ),
                'nonce'              => wp_create_nonce( $this->_sNonceKey ), // _sNonceKey is same as the action hook suffix when it's not declared in properties.
                'actionHookSuffix'   => 'aal_action_update_now_retrieving',
                'spinnerURL'         => admin_url( 'images/loading.gif' ),
                'label'              => array(
                    'nowLoading'   => __( 'Now loading...', 'amazon-auto-links' ),
                ),
            )
        );

    }

}