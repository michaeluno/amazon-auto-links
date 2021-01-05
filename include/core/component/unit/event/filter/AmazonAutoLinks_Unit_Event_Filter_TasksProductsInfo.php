<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Updates unit status via ajax calls.
 * @since   4.3.0
 */
class AmazonAutoLinks_Unit_Event_Filter_TasksProductsInfo extends AmazonAutoLinks_PluginUtility {

    /**
     * @since 4.3.0
     */
    public function __construct() {

        $_sActionName = 'aal_action_api_get_products_info';
        add_filter( "aal_filter_tasks_{$_sActionName}", array( $this, 'replyToBundleParameters' ), 10 );

    }

    /**
     * @param array $aTasksPerActionName The structure should look like:
     * ```
     *   Array(
     *      [0] => Array(
     *          [name] => (string) AINGEOIDBE|US|USD|en-US
     *          [action] => (string) aal_action_api_get_products_info
     *          [arguments] => Array(
     *              [0] => (string) EDFWEWAINGEOIDEWFWRJO
     *          )
     *          [creation_time] => (string) 2020-09-16 08:58:47
     *          [next_run_time] => (string) 2020-09-16 08:58:47
     *      )
     *      [1] => Array( ...
     *
     *  )
     * ```
     * @return array
     * @since 4.3.0
     */
    public function replyToBundleParameters( array $aTasksPerActionName ) {

        $_aTasksError = array();
        $_aTasks      = array();
        foreach( $aTasksPerActionName as $_aTask ) {
            /**
             * @var array $_aArguments
             * structure:
             * 0: array(
                    0 => array( 0 => $sAssociateID|Locale|Cur|Lang, 1 => $sASIN,  2 => $iCacheDuration, 3 => $bForceRenew, 4 => $sItemFormat ),
             *      1 => array( 0 => $sAssociateID|Locale|Cur|Lang, 1 => $sASIN,  2 => $iCacheDuration, 3 => $bForceRenew, 4 => $sItemFormat ),1
             *      ...
             * )
             * 1: Associate ID
             * 2: Locale
             * 3: Currency
             * 4: Language
             */
            $_aArguments         = $this->getElementAsArray( $_aTask, array( 'arguments' ) ) + array( array(), '', '', '', '' );
            $_sLocale            = $_aArguments[ 2 ];
            $_sCurrency          = $_aArguments[ 3 ];
            $_sLanguage          = $_aArguments[ 4 ];
            if ( ! $_sLocale ) {
                new AmazonAutoLinks_Error( 'MERGING_PLUGIN_TASKS', 'The task argument is corrupt.', $_aTask, false );
                $_aTasksError[] = array( 'name' => $_aTask[ 'name' ] );
                continue;
            }
            $_sKey               = "{$_sLocale}|{$_sCurrency}|{$_sLanguage}";
            $_aTasks[ $_sKey ]   = isset( $_aTasks[ $_sKey ] ) ? $_aTasks[ $_sKey ] : array();
            $_aTasks[ $_sKey ][] = $_aTask;

        }
        return array_merge( $this->___getTaskItemsJoined( $_aTasks ), $_aTasksError );

    }
        /**
         * Join items up to 10.
         * @param array $aTasksByLocaleCurLang
         * @return array
         * @since 4.3.0
         */
        private function ___getTaskItemsJoined( array $aTasksByLocaleCurLang ) {
            $_aFormatted = array();
            foreach( $aTasksByLocaleCurLang as $_sLocaleCurLang => $_aTasksByLocaleCurLang ) {
                $_aFormatted = array_merge(
                    $_aFormatted,
                    $this->___getTaskItemsJoinedByLocaleCurLang( $_aTasksByLocaleCurLang )
                );
            }
            return $_aFormatted;
        }
            /**
             * @param array $aTasksByLocaleCurLang
             * @return array
             * @since   4.3.0
             */
            private function ___getTaskItemsJoinedByLocaleCurLang( array $aTasksByLocaleCurLang ) {
                $_aTasks  = array();
                $_aChunks = array_chunk( $aTasksByLocaleCurLang, 10 );    // PA-API 5 accepts up to 10 products to be queried at once.
                foreach( $_aChunks as $_aChunked ) {
                    $_a1stTask   = reset( $_aChunked );
                    if ( ! is_array( $_a1stTask ) ) {
                        continue;
                    }
                    $_aItems     = array();
                    $_aNames     = array();
                    foreach( $_aChunked as $_aTask ) {
                        $_aNames[] = $_aTask[ 'name' ];
                        $_aItems   = array_merge(
                            $_aItems,
                            $this->getElementAsArray( $_aTask, array( 'arguments', 0 ) )
                        );
                    }
                    $_aTask                = $_a1stTask;
                    $_aTask[ 'arguments' ] = array( $_aItems ) + $_a1stTask[ 'arguments' ];
                    $_aTask[ 'name' ]      = $_aNames;
                    $_aTasks[]             = $_aTask;

                }
                return $_aTasks;
            }

}