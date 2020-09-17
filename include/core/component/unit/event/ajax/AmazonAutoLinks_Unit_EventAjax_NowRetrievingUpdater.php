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

        $_aElements = array();
        foreach( $this->getElementAsArray( $aPost, array( 'items' ) ) as $_sASINLocaleCurLang => $_aItems ) {
            $_aElements = array_merge(
                $_aElements,
                $this->___getElementsByASINLocaleCurLang( $_sASINLocaleCurLang, $_aItems  )
            );
        }

        return $_aElements;

    }

        /**
         * @param string $sASINLocaleCurLang ASIN|locale|currency|language
         * @param array $aItems
         * @return  array
         */
        private function ___getElementsByASINLocaleCurLang( $sASINLocaleCurLang, array $aItems ) {

            $_aProduct  = $this->___getProductByASINLocaleCurLang( $sASINLocaleCurLang );

            // If the database is not updated yet,
            // $_aProduct

            $_aElements = array();
            $_aElement  = array(
                'context'   => '', 'asin'      => '', 'tag'       => '',
                'locale'    => '', 'currency'  => '', 'language'  => '',
                'output'    => '', 'id'        => 0,
            );
            foreach( $aItems as $_sContext => $_aItem ) {
                $_sOutput = $this->___getElementOutput( $_aItem, $_aProduct );
                if ( ! $_sOutput ) {
                    continue;
                }
                $_aElements[] = array(
                    'output' => $_sOutput,
                ) + $_aItem + $_aElement;
            }
            return $_aElements;
        }
            /**
             * @param string $sASINLocaleCurLang
             *
             * @return array
             */
            private function ___getProductByASINLocaleCurLang( $sASINLocaleCurLang ) {
                // @todo
                return array();
            }
            /**
             * @param array $aItem
             * @return string
             */
            private function ___getElementOutput( array $aItem, array $aProduct ) {
                // @todo
                return '';
            }
    /**
     * Enqueues scripts and styles for unit outputs.
     */
    public function replyToEnqueueResources() {

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