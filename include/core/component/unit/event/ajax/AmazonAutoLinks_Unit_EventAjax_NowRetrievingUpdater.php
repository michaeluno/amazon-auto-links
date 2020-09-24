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
    }

    /**
     * @param array $aPost
     *
     * @return string|array
     * @throws Exception        Throws a string value of an error message.
     */
    protected function _getResponse( array $aPost ) {

        // If there are pending plugin tasks, finish them now.
        AmazonAutoLinks_Shadow::doTasks();
        if ( ! did_action( 'aal_action_check_tasks' ) ) {
            do_action( 'aal_action_check_tasks' );
        }

        $_aAllItems           = $this->getElementAsArray( $aPost, array( 'items' ) );
        $_aProducts           = $this->___getProducts( $_aAllItems );
        $_aElements           = array();
        foreach( $_aAllItems as $_sASINLocaleCurLang => $_aDueElements ) {
            $_aElements = array_merge(
                $_aElements,
                $this->___getElementsByASINLocaleCurLang( $_aDueElements, $_aProducts )
            );
        }
        return $_aElements;

    }
        /**
         * @param array $aAllItems
         * @return array
         */
        private function ___getProducts( array $aAllItems ) {
            $_aUnitOptionSets     = $this->___getUnitOptionSets( $aAllItems );
            $_aProducts           = array();
            foreach( $_aUnitOptionSets as $_aUnitOptions ) {
                $_oUnit      = new AmazonAutoLinks_UnitOutput_item_lookup( $_aUnitOptions );
                $_aProducts  = $_aProducts + $this->___getProductsFormatted( $_oUnit->fetch(), $_oUnit->oUnitOption );
            }
            return $_aProducts;
        }
        /**
         * @return array An array holding sets of unit options, separated by locale-currency-language.
         * @param array $aItems Due items to update.
         */
        private function ___getUnitOptionSets( array $aItems ) {

            $_aUnitOptionSets = array();
            foreach( array_keys( $aItems ) as $_sASINLocaleCurLang ) {
                $_aElements      = $this->getElementAsArray( $aItems, array( $_sASINLocaleCurLang ) );
                $_aElement       = reset( $_aElements );
                $_sAssociateTag  = $this->getElement( $_aElement, array( 'tag' ) );
                $_aParts         = explode( '|', $_sASINLocaleCurLang );
                $_sASIN          = $_aParts[ 0 ];
                $_sLocale        = $_aParts[ 1 ];
                $_sCurrency      = $_aParts[ 2 ];
                $_sLanguage      = $_aParts[ 3 ];
                $_sLocaleCurLang = "{$_sLocale}|{$_sCurrency}|{$_sLanguage}";
                $_aUnitOptionSets[ $_sLocaleCurLang ] = isset( $_aUnitOptionSets[ $_sLocaleCurLang ] )
                    ? $_aUnitOptionSets[ $_sLocaleCurLang ]
                    : array(
                        'country'            => $_sLocale,
                        'preferred_currency' => $_sCurrency,
                        'language'           => $_sLanguage,
                        'show_now_retrieving_message'  => true,
                        'ItemIds'            => array(),
                        'associate_id'       => $_sAssociateTag,
                    );
                $_aUnitOptionSets[ $_sLocaleCurLang ][ 'ItemIds' ][] = $_sASIN;
            }
            return $_aUnitOptionSets;
        }

        /**
         * Converts numeric indices to associate keys.
         * @param array $aProducts
         * @param AmazonAutoLinks_UnitOption_Base $oUnitOption
         * @return array A formatted products array.
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
         */
        private function ___getElementsByASINLocaleCurLang( array $aDueElements, array $aProducts ) {

            // If the element is still pending, the 'output' element will be empty. Then it will be queued again.
            $_aElements   = array();
            $_aStructure  = array(
                'context'   => '', 'asin'      => '', 'tag'       => '',
                'locale'    => '', 'currency'  => '', 'language'  => '',
                'output'    => '', 'id'        => 0,  'type'      => '',
                'set'       => 0,
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
                return $_aProduct[ $aDueElement[ 'context' ] ];
            }

    /**
     * Enqueues scripts and styles for unit outputs.
     */
    public function replyToEnqueueResources() {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isAPIConnected() ) {
            return;
        }
        $_sScriptHandle = 'aal-now-retrieving-updater';
        $_sFileBaseName = $this->isDebugMode()
            ? 'now-retrieving-updater.js'
            : 'now-retrieving-updater.min.js';
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script(
            $_sScriptHandle,
            $this->getSRCFromPath( AmazonAutoLinks_UnitLoader::$sDirPath . '/asset/js/' . $_sFileBaseName ),
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