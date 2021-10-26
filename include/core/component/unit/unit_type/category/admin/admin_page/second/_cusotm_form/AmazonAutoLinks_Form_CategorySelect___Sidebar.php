<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Provides methods to retrieve sidebar menu list elements of Amazon best selling products.
 *
 * @since       3.5.7
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar extends AmazonAutoLinks_WPUtility {

    /**
     * @deprecated 4.2.0
     */
    protected $_sHTTPClientClass = 'AmazonAutoLinks_HTTPClient';

    protected $_aElements = array(
        'RSSURL'       => null,
        'CategoryList' => null,
        'Breadcrumb'   => null,
        'Error'        => null,
        'PageURL'      => null, // 3.9.1    Will be assigned for a case that the is redirected
    );

    public function __construct( $sPageURL, $sLocale='US' ) {
        $this->___setElements( $sPageURL, $sLocale );
    }

        /**
         * Loads sidebar elements.
         * @since       3.5.7
         */
        private function ___setElements( $sPageURL, $sLocale ) {

            $this->_aElements[ 'PageURL' ] = $sPageURL;
            $_sHTML = $this->___getPageHTML( $sPageURL );
            if ( ! $_sHTML ) {
                $this->_aElements[ 'Error' ] = sprintf( __( 'Could not retrieve the category list: %1$s. Please consult the plugin developer.', 'amazon-auto-links' ), $sPageURL );
                return;
            }

            // DOM Helper creates a `DOMDocument` instance
            $_oDOMHelper = new AmazonAutoLinks_DOM;
            $_oDoc       = $_oDOMHelper->loadDOMFromHTMLElement(
                $_sHTML,
                '', // mb_lang
                false // detect encoding
            );

            try {

                // The existing page layout.
                $this->_aElements = $this->___getElements_zg_browseRoot( $_oDoc, $sPageURL, $sLocale );
                return;

            } catch ( Exception $_oException ) {
                $this->_handleExceptionsToSetElements( $_oDoc, $sPageURL, $sLocale );
            }

        }
            /**
             * Fetches page HTML source contents.
             * @param  string $sPageURL
             * @return string
             * @since  3.9.1
             */
            private function ___getPageHTML( $sPageURL ) {
                $_oHTTP = new AmazonAutoLinks_HTTPClient(
                    $sPageURL,
                    86400,
                    array(
                        'timeout'   => 10,
                    )
                );
                return $_oHTTP->get();
            }
            /**
             * Called when the default sidebar container element does not exist.
             * In this case, there are following possibilities.
             *  - the user navigated to the R18 area.
             *  - the page layout has changed to the new design.
             *  - other unknown reasons.
             * @since 3.5.7
             * @since 3.9.1 Removed the `$oHTTP` parameter.
             */
            protected function _handleExceptionsToSetElements( DOMDocument $oDoc, $sPageURL, $sLocale ) {

                // For a new page layout design introduced around 2018/06,
                $_nodeCategory = $oDoc->getElementById( 'crown-category-nav' );
                if ( $_nodeCategory ) {
                    $this->_aElements = $this->_getElements_crown_category_nav( $oDoc, $sPageURL, $sLocale );
                    return;
                }

                // Try with a R18 confirmation redirect
                /// 4.2.0 if the redirect page is not supported, return an error
                $_oLocale = new AmazonAutoLinks_Locale( $sLocale );
                $_sBlackCurtainURL = $_oLocale->getBlackCurtainURL();
                if ( ! $_sBlackCurtainURL ) {
                    $this->_aElements = array(
                        'PageURL'      => $sPageURL,
                        'RSSURL'       => '',
                        'CategoryList' => '',
                        'Breadcrumb'   => $sLocale, // at the moment, this page layout is only used in the top root category (All Category). So just show the locale.
                        'Error'        => __( 'Failed to load a category list.', 'amazon-auto-links' ),
                    );
                    return;
                }

                $_sRedirectURL = add_query_arg(
                    array(
                        'redirect'    => 'true',
                        'redirectUrl' => urlencode( $sPageURL ),
                    ),
                    $_sBlackCurtainURL
                );
                $_oSidebarR18  = new AmazonAutoLinks_Form_CategorySelect___Sidebar__R18( $_sRedirectURL, $sLocale );
                $this->_aElements = $_oSidebarR18->get();
                $this->_aElements[ 'PageURL' ] = $_sRedirectURL;
                return;

            }

                    /**
                     * Extracts and set sidebar elements.
                     * @param   DOMDocument $oDoc
                     * @param   string  $sPageURL
                     * @param   string  $sLocale
                     * @since   3.5.7
                     * @since   3.9.1   Deprecated the use of PHP Simple DOM Parser
                     * @return  array
                     * @throws  Exception
                     */
                    private function ___getElements_zg_browseRoot( DOMDocument $oDoc, $sPageURL, $sLocale ) {

                        // @deprecated 3.9.1 - Amazon sites no longer provide feeds for best seller items
                        // $_oFeedURL      = new AmazonAutoLinks_Form_CategorySelect___Sidebar___FeedURL( $oSimpleDOM, $sPageURL );
                        // $_sRSSURL       = $_oFeedURL->get();

                        $_oCategoryList = new AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList( $oDoc, $sPageURL );
                        $_sCategoryList = $_oCategoryList->get();
                        if ( ! $_sCategoryList ) {
                            throw new Exception();
                        }
                        $_oBreadcrumb   = new AmazonAutoLinks_Form_CategorySelect___Sidebar___Breadcrumb( $oDoc, $sLocale );
                        return array(
                            'RSSURL'       => '',   // @deprecated 3.9.1
                            'CategoryList' => $_sCategoryList,   // must be done after the above `$_oFeedURL->get()` method as this method modifies the links.
                            'Breadcrumb'   => $_oBreadcrumb->get(),
                            'Error'        => '',
                        ) + $this->_aElements;

                    }

                    /**
                     * @param  DOMDocument  $oDoc
                     * @param  string       $sPageURL
                     * @param  string       $sLocale
                     * @return array
                     * @since  3.5.7
                     * @since  3.9.1   No longer uses PHP Simple DOM Parser
                     * @since  3.9.1   Changed the scope to protected.
                     */
                    protected function _getElements_crown_category_nav( DOMDocument $oDoc, $sPageURL, $sLocale ) {

                        // @deprecated 3.9.1 Amazon sites no longer provide feeds.
                        // $_oFeedURL      = new AmazonAutoLinks_Form_CategorySelect___Sidebar___FeedURL( $oDoc, $sPageURL );
                        // $_sRSSURL       = $_oFeedURL->get();

                        $_oCategoryList = new AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryListB( $oDoc, $sPageURL );
                        return array(
                            'RSSURL'       => '',
                            'CategoryList' => $_oCategoryList->get(),
                            'Breadcrumb'   => $sLocale, // at the moment, this page layout is only used in the top root category (All Category). So just show the locale.
                            'Error'        => '',
                        ) + $this->_aElements;

                    }

    /**
     * @param  string       $sElementName The sidebar element name to retrieve.
     * @return string|array
     * @since  3.5.7
     */
    public function get( $sElementName='' ) {

        // If a key is not specified, return the entire element array.
        if ( '' === $sElementName ) {
            return $this->_aElements;
        }

        // If an error occurs, return the error message.
        $_sError = $this->getElement( $this->_aElements, 'Error' );
        if ( $_sError ) {
            return $_sError;
        }

        // Return the element
        return $this->getElement( $this->_aElements, $sElementName  );

    }

}