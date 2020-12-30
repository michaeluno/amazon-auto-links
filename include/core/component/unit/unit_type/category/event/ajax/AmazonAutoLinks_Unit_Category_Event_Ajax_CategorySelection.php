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
 * Responds with the data of category list of user's choosing.
 *
 * @package      Amazon Auto Links
 * @since        4.2.0
 */
class AmazonAutoLinks_Unit_Category_Event_Ajax_CategorySelection extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookName = 'wp_ajax_aal_category_selection';

    /**
     * The nonce key passed to the `wp_create_nonce()`
     * @var string
     */
    protected $_sNonceKey = 'aalNonceCategorySelection';

    /**
     * @param  array $aPost
     *
     * @return array
     * @throws Exception        Throws a string value of an error message.
     */
    protected function _getResponse( array $aPost ) {

        // Passing the unit options via transient as passing through JS results in escaped characters and causes errors.
        $_aUnitOptions      = $this->_getUnitOptions( $aPost );
        $_sLocale           = $this->getElement( $_aUnitOptions, array( 'country' ), 'US' );
        $_sCategoryListURL  = $this->___getCategoryListURL( $aPost, $_sLocale );

        if ( ! $_sCategoryListURL ) {
            throw new Exception( __( 'Could not load the page as no URL is given.', 'amazon-auto-links' ) );
        }

        // Access the Amazon store site and retrieve the category list.

        $_oHTTP = new AmazonAutoLinks_HTTPClient( $_sCategoryListURL, 86400 * 7, array( 'timeout'   => 10, ) );
        // be careful that the $_POST values are passed as a string due to the JavaScript Ajax data handling
        // @see https://stackoverflow.com/questions/7408976/bool-parameter-from-jquery-ajax-received-as-literal-string-false-true-in-php
        if ( ( boolean ) $this->getElement( $aPost, array( 'reload' ) ) ) {
            // Case: the user first got a captcha error and enabled the proxy option then clicked the Reload button.
            // In that case, the previous cache with the captcha error should be cleared. Otherwise, the cache of the captcha error keeps to be returned.
            $_oHTTP->deleteCache();
        }
        $_sHTML = $_oHTTP->get();
        if ( ! $_sHTML ) {
            throw new Exception(
                sprintf( __( 'Could not load the page: %1$s', 'amazon-auto-links' ), $_sCategoryListURL )
                . ' ' . $this->___getReloadMessage()
            );
        }

        // DOM Helper creates a `DOMDocument` instance
        $_oDOMHelper  = new AmazonAutoLinks_DOM;
        $_oDoc        = $_oDOMHelper->loadDOMFromHTMLElement( $_sHTML, '', false );
        $_oBreadcrumb = new AmazonAutoLinks_Form_CategorySelect___Sidebar___Breadcrumb( $_oDoc, $_sLocale );

        $_sCategoryList = $this->___getCategoryList( $_oDoc, $_sCategoryListURL );
        if ( ! $_sCategoryList ) {
            throw new Exception(
                sprintf( __( 'Could not retrieve the category list: %1$s.', 'amazon-auto-links' ), $_sCategoryListURL )
                . ' ' . $_sHTML
                . ' ' . $this->___getReloadMessage()
            );
        }

        // Additional options for previews
        $_aUnitOptions = array(
            'template_path' => AmazonAutoLinks_Registry::$sDirPath . '/template/preview/template.php',
            'is_preview'    => true, // this disables the global ASIN blacklist.
            'show_errors'   => 1,
        ) + $_aUnitOptions;
        $_oCategoryPreview  = new AmazonAutoLinks_UnitOutput_category( $_aUnitOptions );
        $_sUnitOutput       = $_oCategoryPreview->get( array( $_sCategoryListURL ) );

        // The JavaScript script receives this response array
        $_sBreadcrumb       = $_oBreadcrumb->get();
        return array(
            'breadcrumb'        => $_sBreadcrumb,
            'category_list'     => $_sCategoryList,
            'selected_url'      => $_sCategoryListURL,
            'checkbox_added'    => AmazonAutoLinks_Unit_Utility_category::getCategoryCheckbox( $_sCategoryListURL, $_sBreadcrumb, 'added' ),
            'checkbox_excluded' => AmazonAutoLinks_Unit_Utility_category::getCategoryCheckbox( $_sCategoryListURL, $_sBreadcrumb, 'excluded' ),
            'category_preview'  => $_sUnitOutput,
        );

    }

        /**
         * @param array $aPost
         *
         * @return array
         */
        protected function _getUnitOptions( array $aPost ) {

            // For editing category selection, a post ID is passed.
            $_iPostID = ( integer ) $this->getElement( $aPost, array( 'postID' ), 0 );
            if ( $_iPostID ) {
                $_oUnitOption = new AmazonAutoLinks_UnitOption_category(
                    $_iPostID, // unit id
                    array() // unit options
                );
                return $this->getAsArray( $_oUnitOption->get() );
            }

            // Otherwise, unit options are stored in a transient for creating a new unit.
            $_aUnitOptions = $this->getAsArray(
                get_transient( $this->getElement( $aPost, array( 'transientID' ), '' ) )
            );

            // There is a reported case that the locale is not retrieved which seems to be failing to retrieve the transient.
            if ( empty( $_aUnitOptions ) ) {
                new AmazonAutoLinks_Error( 'CATEGORY_SELECTION_AJAX_RESPONSE', 'The unit options generated from a transient are empty. Transient: ' . $GLOBALS[ 'aal_transient_id' ], $_aUnitOptions, true );
            }

            return $_aUnitOptions;

        }
        /**
         * @param DOMDocument $oDoc
         * @param string $sPageURL
         *
         * @return string
         * @remark There are two site layout types
         */
        private function ___getCategoryList( $oDoc, $sPageURL ) {

            $_oCategoryList = new AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList( $oDoc, $sPageURL );
            $_sCategoryList = $_oCategoryList->get();
            if ( $_sCategoryList ) {
                return $_sCategoryList;
            }
            $_oCategoryList = new AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryListB( $oDoc, $sPageURL );
            return $_oCategoryList->get();

        }
        /**
         * @param array  $aPost
         * @param string $sLocale
         *
         * @return string
         * @since   4.2.0
         */
        private function ___getCategoryListURL( array $aPost, $sLocale ) {

            $_oLocale           = new AmazonAutoLinks_Locale( $sLocale );
            $_sCategoryListURL  = $this->getElement( $aPost, array( 'selected_url' ), '' );
            $_sCategoryListURL  = $_sCategoryListURL
                ? $_sCategoryListURL
                : $_oLocale->getBestSellersURL();

            $_sQuery = parse_url( $_sCategoryListURL, PHP_URL_QUERY );
            parse_str( $_sQuery, $_aQuery );
            $_sHref  = $this->getElement( $_aQuery, array( 'href' ) );

            // The default category root URLs do not have the `href` query argument.
            if ( ! $_sHref ) {
                return $_sCategoryListURL;
            }

            // Otherwise, it is a plugin generated link URL.

            // Decrypt the href value
            $_oEncrypt = new AmazonAutoLinks_Encrypt;
            return $_oEncrypt->decode( $_sHref );

        }

    /**
     * @return string
     */
    private function ___getReloadMessage() {

        $_aToolsOptions  = $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'tools' ], array() ) );
        $_bProxyEnabled  = $this->getElement( $_aToolsOptions, array( 'proxies', 'enable' ), false );
        $_sProxyMessage  = $_bProxyEnabled
            ? ''
            : sprintf(
                __( 'If this continues, try enabling the proxy from <a href="%1$s">here</a>.', 'amazon-auto-links' ),
                $this->getProxySettingScreenURL()
            ) . ' ';
        $_sReloadButton  = '<a class="button button-small button-reload">'
                . __( 'Reload', 'amazon-auto-links' )
            . '</a>';
        return $_sProxyMessage . $_sReloadButton;

    }

}