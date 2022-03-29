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
 * Responds with the data of category list of user's choosing.
 *
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
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.6.18
     */
    protected function _getPostSanitized( array $aPost ) {
        $_aPost = array(
            'postID'              => absint( $this->getElement( $aPost, array( 'postID' ) ) ),
            'transientID'         => sanitize_text_field( $this->getElement( $aPost, array( 'transientID' ), '' ) ),
            'selected_url'        => $this->getURLSanitized( $this->getElement( $aPost, array( 'selected_url' ), '' ) ),
            'reload'              => ( boolean ) $this->getElement( $aPost, array( 'reload' ) ),
        );
        $_aPost = apply_filters( 'aal_filter_ajax_post_sanitization_category_selection', $_aPost );
        return $this->getAsArray( $_aPost );
    }

    /**
     * @return array
     * @throws Exception        Throws a string value of an error message.
     * @param  array     $aPost Sanitized POST data containing `postID`, `transientID`, `selected_url`, and `reload`.
     */
    protected function _getResponse( array $aPost ) {

        do_action( 'aal_action_ajax_response_category_selection', $aPost ); // [4.6.23+]

        // Passing the unit options via transient as passing through JS results in escaped characters and causes errors.
        $_aUnitOptions      = $this->_getUnitOptions( $aPost );
        $_sLocale           = $this->getElement( $_aUnitOptions, array( 'country' ), 'US' );
        $_sCategoryListURL  = $this->___getCategoryListURL( $aPost, $_sLocale );

        if ( ! $_sCategoryListURL ) {
            throw new Exception( "<span class='warning'>" . __( 'Could not load the page as no URL is given.', 'amazon-auto-links' ) . "</span>" );
        }

        $_oLocale = new AmazonAutoLinks_PAAPI50_Locale( $_sLocale );
        $_oOption = AmazonAutoLinks_Option::getInstance();

        // Access the Amazon store site and retrieve the category list.
        $_oHTTP = new AmazonAutoLinks_HTTPClient(
            $_sCategoryListURL,
            86400 * 7,
            array(
                'timeout'   => 10,

                // Without these, the language becomes English in some locales.
                // Note that this is not possible with Web Page Dumper. So if the site is blocked and let Web Page Dumper to assist, the language may not be displayed as desired.
                'cookies'   => array(
                    'i18n-prefs' =>	$_oOption->get( array( 'associates', $_sLocale, 'paapi', 'currency' ), $_oLocale->getDefaultCurrency() ),
                    'lc-acb' . strtolower( $_sLocale ) => $_oOption->get( array( 'associates', $_sLocale, 'paapi', 'language' ), $_oLocale->getDefaultLanguage() ),
                ),
            )
        );
        // be careful that the POST values are passed as a string due to the JavaScript Ajax data handling
        // @see https://stackoverflow.com/questions/7408976/bool-parameter-from-jquery-ajax-received-as-literal-string-false-true-in-php
        if ( ( boolean ) $this->getElement( $aPost, array( 'reload' ) ) ) {
            // Case: the user first got a captcha error and enabled the proxy option then clicked the Reload button.
            // In that case, the previous cache with the captcha error should be cleared. Otherwise, the cache of the captcha error keeps to be returned.
            $_oHTTP->deleteCache();
        }
        $_sHTML = $_oHTTP->get();
        if ( ! $_sHTML ) {
            throw new Exception(
                "<span class='warning'>" . sprintf( __( 'Could not load the page: %1$s', 'amazon-auto-links' ), $_sCategoryListURL ) . "</span>"
                . ' ' . $this->___getReloadMessage()
            );
        }

        // DOM Helper creates a `DOMDocument` instance
        $_oDOMHelper    = new AmazonAutoLinks_DOM;
        $_oDoc          = $_oDOMHelper->loadDOMFromHTML( $_sHTML, '', false );

        $_sCategoryList = $this->___getCategoryList( $_oDoc, $_sCategoryListURL );
        if ( ! $_sCategoryList ) {
            throw new Exception(
                "<span class='warning'>" . sprintf( __( 'Could not retrieve the category list: %1$s.', 'amazon-auto-links' ), $_sCategoryListURL ) . "</span>"
                . ' ' . $this->___getReloadMessage()
            );
        }

        $_sBreadcrumb       = $this->___getBreadcrumb( $_oDoc, $_sLocale );

        // Additional options for previews
        $_aUnitOptions = array(
            'template_path' => AmazonAutoLinks_Registry::$sDirPath . '/template/preview/template.php',
            'is_preview'    => true, // this disables the global ASIN blacklist.
            'show_errors'   => 1,
            'categories'    => array(
                'unit_preview' => array(
                    'breadcrumb'   => $_sBreadcrumb,
                    'page_url'     => $_sCategoryListURL,
                ),
            ),
        ) + $_aUnitOptions;
        $_oCategoryPreview  = new AmazonAutoLinks_UnitOutput_category( $_aUnitOptions );
        $_sUnitOutput       = $_oCategoryPreview->get();

        // The JavaScript script receives this response array
        return array(
            'breadcrumb'        => $_sBreadcrumb,
            'category_list'     => $_sCategoryList . "<!-- Current Page: {$_sCategoryListURL} -->",
            'selected_url'      => $_sCategoryListURL,
            'checkbox_added'    => AmazonAutoLinks_Unit_Utility_category::getCategoryCheckbox( $_sCategoryListURL, $_sBreadcrumb, 'added' ),
            'checkbox_excluded' => AmazonAutoLinks_Unit_Utility_category::getCategoryCheckbox( $_sCategoryListURL, $_sBreadcrumb, 'excluded' ),
            'category_preview'  => $_sUnitOutput,
        );
    }

        /**
         * @param  array $aPost
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
         * @since  4.6.13
         * @return string
         */
        private function ___getBreadcrumb( $oDoc, $sLocale ) {

            $_aClassNames = array(
                'AmazonAutoLinks_Form_CategorySelect___Sidebar___BreadcrumbD',
                'AmazonAutoLinks_Form_CategorySelect___Sidebar___BreadcrumbC',
                'AmazonAutoLinks_Form_CategorySelect___Sidebar___Breadcrumb',
            );
            foreach( $_aClassNames as $_sClassName ) {
                $_oBreadcrumb = new $_sClassName( $oDoc, $sLocale );
                $_sBreadcrumb = $_oBreadcrumb->get();
                if ( $_sBreadcrumb ) {
                    return $_sBreadcrumb;
                }
            }
            return __( 'Failed to generate the breadcrumb.', 'amazon-auto-links' );

        }
        /**
         * @param  DOMDocument $oDoc
         * @param  string $sPageURL
         * @return string
         * @remark There are several versions of site layouts.
         */
        private function ___getCategoryList( $oDoc, $sPageURL ) {
            $_aClassNames = array(
                'AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryListD',
                'AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryListC',
                'AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList',
                'AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryListB'
            );
            foreach( $_aClassNames as $_sClassName ) {
                $_oCategoryList = new $_sClassName( $oDoc, $sPageURL );
                $_sCategoryList = $_oCategoryList->get();
                if ( $_sCategoryList ) {
                    return $_sCategoryList;
                }
            }
            return $_sCategoryList;
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
            $_sCategoryListURL  = wp_normalize_path( $_sCategoryListURL );
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

            /// Decrypt the href value
            $_oEncrypt = new AmazonAutoLinks_Encrypt;
            return  $_oEncrypt->decode( $_sHref );

        }

    /**
     * @return string
     */
    private function ___getReloadMessage() {

        $_aToolsOptions  = $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'tools' ], array() ) );
        $_bWPDEnabled    = $this->getElement( $_aToolsOptions, array( 'web_page_dumper', 'enable' ), false );
        $_sProxyMessage  = $_bWPDEnabled
            ? ''
            : sprintf(
                __( 'If this continues, try enabling <a href="%1$s" target="_blank">proxies</a>.', 'amazon-auto-links' ),
                $this->getProxySettingScreenURL()
            ) . ' ';
        $_sReloadButton  = "<a class='button button-small button-reload'>"
                    . "<span class='dashicons dashicons-image-rotate'></span>"
                    . __( 'Reload', 'amazon-auto-links' )
                . "</a>"
            . "</span>";
        return "<span class='warning'>" . $_sProxyMessage . "</span>"
            . apply_filters( 'aal_filter_output_category_selection_reload_message', $_sReloadButton );

    }

}