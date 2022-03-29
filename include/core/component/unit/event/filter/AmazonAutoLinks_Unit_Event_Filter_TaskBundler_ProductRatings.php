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
 * Bundles plugin tasks of `aal_action_api_get_product_rating`.
 * @since   4.6.11
 */
class AmazonAutoLinks_Unit_Event_Filter_TaskBundler_ProductRatings extends AmazonAutoLinks_PluginUtility {

    /**
     * @since 4.3.0
     */
    public function __construct() {
        
        $_sActionName = 'aal_action_api_get_product_rating';
        add_filter( "aal_filter_tasks_{$_sActionName}", array( $this, 'replyToHandleDoableTasks' ), 10 );

    }

    /**
     * Handles plugin tasks of retrieving a product rating all at once if the item locale supports the Ad Widget API.
     * @param array $aTasksPerActionName The structure should look like:
     * ```
     *   Array(
     *      [0] => Array(
     *          [name] => (string) get_rating_AINGEOIDBE|US|USD|en-US
     *          [action] => (string) aal_action_api_get_product_rating
     *          [arguments] => Array(
     *              [0] => (string) AINGEOIDBE|US|USD|en-US
     *              [1] => (integer) 86400
     *              [2] => (bolean) false
     *          )
     *          [creation_time] => (string) 2020-09-16 08:58:47
     *          [next_run_time] => (string) 2020-09-16 08:58:47
     *      )
     *      [1] => Array( ...
     *  )
     * ```
     * @return array
     * @since  4.6.11
     */
    public function replyToHandleDoableTasks( array $aTasksPerActionName ) {

        $_aAdWidgetLocales = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport();
        $_iCacheDuration   = 86400;
        $_bForceRenew      = false;
        $_aItemsByLocale   = array();
        foreach( $aTasksPerActionName as $_iIndex => $_aTask ) {
            /**
             * @var array $_aArguments
             * structure:
             * 0: (string) AINGEOIDBE|US|USD|en-US - ASIN|locale|currency|language e.g
             * 1: (integer) 86400 - Cache duration
             * 2: (boolean) true/false- Force renew
             */
            $_aArguments         = $this->getElementAsArray( $_aTask, array( 'arguments' ) ) + array( '', 86400, false );
            $_sProductID         = $_aArguments[ 0 ];
            $_iCacheDuration     = $_aArguments[ 1 ];
            $_bForceRenew        = $_aArguments[ 2 ];
            $_aProductInfo       = explode( '|', $_sProductID ) + array( '', '', '', '' );
            $_sASIN              = $_aProductInfo[ 0 ];
            $_sLocale            = $_aProductInfo[ 1 ];
            $_sCurrency          = $_aProductInfo[ 2 ];
            $_sLanguage          = $_aProductInfo[ 3 ];
            if ( ! in_array( $_sLocale, $_aAdWidgetLocales, true ) ) {
                continue;
            }
            $aTasksPerActionName[ $_iIndex ][ 'arguments' ] = '__delete';   // will be treated as a completed task
            $_aItemsByLocale[ $_sLocale ] = isset( $_aItemsByLocale[ $_sLocale ] ) ? $_aItemsByLocale[ $_sLocale ] : array();
            $_aItemsByLocale[ $_sLocale ][ $_sASIN ] = array(
                'ASIN'      => $_sASIN,
                'asin'      => $_sASIN,
                'locale'    => $_sLocale,
                'currency'  => $_sCurrency,
                'language'  => $_sLanguage,
            ); // will be passed to AmazonAutoLinks_ProductDatabase_Rows.

        }

        // Do bundled tasks
        foreach( $_aItemsByLocale as $_sLocale => $_aItems ) {
            $_aChunks = array_chunk( $_aItems, 20 );
            foreach( $_aChunks as $_aChunk ) {
                do_action(
                    'aal_action_update_products_with_ad_widget_api',
                    $_sLocale,
                    $_aItems,
                    $_iCacheDuration,
                    $_bForceRenew
                );
            }
        }

        // Return tasks
        return $aTasksPerActionName;

    }

}