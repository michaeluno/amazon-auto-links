<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Provides methods to retrieve sidebar menu list elements of Amazon best selling products.
 *
 * @sicne       3.5.7
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar extends AmazonAutoLinks_WPUtility {
    
    protected $_sHTTPClientClass = 'AmazonAutoLinks_HTTPClient';

    protected $_aElements = array(
        'RSSURL'       => null,
        'CategoryList' => null,
        'Breadcrumb'   => null,
        'Error'        => null,
    );

    public function __construct( $sPageURL, $sLocale='US' ) {

        // Include the library.
        if ( ! class_exists( 'simple_html_dom_node', false ) ) {
            include_once( AmazonAutoLinks_Registry::$sDirPath . '/include/library/simple_html_dom.php' );
        }

        $this->___setElements( $sPageURL, $sLocale );

    }

        /**
         * Loads sidebar elements.
         * @since       3.5.7
         */
        private function ___setElements( $sPageURL, $sLocale ) {

            // Fetch page HTML source contents.
            $_sClassName = $this->_sHTTPClientClass;
            $_oHTTP = new $_sClassName( $sPageURL ); // has caching ability
            $_sHTML = trim( $_oHTTP->get() );
            if ( ! $_sHTML ) {
                $this->_aElements[ 'Error' ] = sprintf(
                    __( 'Could not retrieve the category list: %1$s. Please consult the plugin developer.', 'amazon-auto-links' ),
                    $sPageURL
                );
                return;
            }

            try {

                // Using the Simple DOM library for encoding problems with the PHP built-in DOM objects.
                $_oSimpleDOM = str_get_html( $_sHTML );

                // If the existing (as of 2018/06) page layout design is not used,
                if ( ! $_oSimpleDOM->find( "#zg_browseRoot", 0 ) ) {
                    throw new Exception;
                }

                $this->_setElementsBy( "#zg_browseRoot", $_oSimpleDOM, $sPageURL, $sLocale );
                return;

            } catch ( Exception $_oException ) {
                $this->_handleExceptionsToSetElements( $_oHTTP, $_oSimpleDOM, $sPageURL, $sLocale );
            }

        }
            /**
             * Called when the default sidebar container element does not exist.
             * In this case, there are following possibilities.
             *  - the user navigated to the R18 area.
             *  - the page layout has changed to the new design.
             *  - other unknown reasons.
             * @since       3.5.7
             */
            protected function _handleExceptionsToSetElements( $oHTTP, $oSimpleDOM, $sPageURL, $sLocale ) {

                // @todo For a new page layout design introduced around 2018/06,
                if ( $oSimpleDOM->find( "#crown-category-nav", 0 ) ) {
                    $this->_setElementsBy( '#crown-category-nav', $oSimpleDOM, $sPageURL, $sLocale );
                    return;
                }

                // Try with a R18 confirmation redirect
                $oHTTP->deleteCache();
                $_sRedirectURL = AmazonAutoLinks_Property::$aCategoryBlackCurtainURLs[ $sLocale ]
                    . '?redirect=true&redirectUrl=' . urlencode( $sPageURL );
                $_oSidebarR18  = new AmazonAutoLinks_Form_CategorySelect___Sidebar__R18( $_sRedirectURL, $sLocale );
                $this->_aElements = $_oSidebarR18->get();
                return;

            }
                /**
                 * @since       3.5.7
                 * @param       string  The selector for the sidebar container element that contains the listed categories.
                 * @return      void
                 */
                protected function _setElementsBy( $sSelector, $oSimpleDOM, $sPageURL, $sLocale ) {

                    // The existing page layout.
                    if ( '#zg_browseRoot' === $sSelector ) {
                        $this->_aElements = $this->___getElements_zg_browseRoot( $oSimpleDOM, $sPageURL, $sLocale );
                        return;
                    }

                    // The new page layout as of around 2018/06
                    if ( '#crown-category-nav' === $sSelector ) {
                        $this->_aElements = $this->___getElements_crown_category_nav( $oSimpleDOM, $sPageURL, $sLocale );
                        return;
                    }

                }

                    /**
                     * Extracts and set sidebar elements.
                     * @param   string  $oSimpleDOM
                     * @param   string  $sPageURL
                     * @param   string  $sLocale
                     * @param   string  $sRSSURL
                     * @since   3.5.7
                     * @return  array
                     */
                    private function ___getElements_zg_browseRoot( $oSimpleDOM, $sPageURL, $sLocale ) {

                        $_oFeedURL      = new AmazonAutoLinks_Form_CategorySelect___Sidebar___FeedURL( $oSimpleDOM, $sPageURL );
                        $_sRSSURL       = $_oFeedURL->get();
                        $_oCategoryList = new AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList( $oSimpleDOM, $sPageURL );
                        $_oBreadcrumb   = new AmazonAutoLinks_Form_CategorySelect___Sidebar___Breadcrumb( $oSimpleDOM, $sLocale, $_sRSSURL );
                        return array(
                            'RSSURL'       => $_sRSSURL,
                            'CategoryList' => $_oCategoryList->get(),   // must be done after the above `$_oFeedURL->get()` method as this method modifies the links.
                            'Breadcrumb'   => $_oBreadcrumb->get(),
                            'Error'        => '',
                        ) + $this->_aElements;

                    }

                    /**
                     * @param $oSimpleDOM
                     * @param $sPageURL
                     * @param $sLocale
                     *
                     * @return array
                     * @since   3.5.7
                     */
                    private function ___getElements_crown_category_nav( $oSimpleDOM, $sPageURL, $sLocale ) {

                        $_oFeedURL      = new AmazonAutoLinks_Form_CategorySelect___Sidebar___FeedURL( $oSimpleDOM, $sPageURL );
                        $_sRSSURL       = $_oFeedURL->get();
                        $_oCategoryList = new AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryListB( $oSimpleDOM, $sPageURL );
                        return array(
                            'RSSURL'       => $_sRSSURL,
                            'CategoryList' => $_oCategoryList->get(),
                            'Breadcrumb'   => $sLocale, // at the moment, this page layout is only used in the top root category (All Category). So just show the locale.
                            'Error'        => '',
                        ) + $this->_aElements;

                    }



    /**
     * @param       string      $sElement       The sidebar element name to retrieve.
     * @return      string|array
     * @since       3.5.7
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