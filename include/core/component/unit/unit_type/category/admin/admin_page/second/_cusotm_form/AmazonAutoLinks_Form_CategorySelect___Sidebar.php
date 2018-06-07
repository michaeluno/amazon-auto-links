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
 * @sicne       3.6.0
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar extends AmazonAutoLinks_WPUtility {

    // @deprecated
//    private $___sPageURL  = '';
//    private $___sLocale   = 'US';
//    private $___iAttempt  = 0;

    protected $_sHTTPClientClass = 'AmazonAutoLinks_HTTPClient';

    private $___aElements = array(
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

        // Properties
        // $this->___sPageURL = $sPageURL;
        // $this->___sLocale  = $sLocale;

        // @deprecated
        // $this->___iAttempt = $iAttempt;

        // Elements
        $this->___setElements( $sPageURL, $sLocale );

    }

        /**
         * Loads sidebar elements.
         * @since       3.6.0
         */
        private function ___setElements( $sPageURL, $sLocale ) {

            // Fetch page HTML source contents.
            $_sClassName = $this->_sHTTPClientClass;
            $_oHTTP = new AmazonAutoLinks_HTTPClient( $sPageURL ); // has caching ability
            $_sHTML = $_oHTTP->get();
            if ( ! $_sHTML ) {
                $this->___aElements[ 'Error' ] = __( "Could not retrieve the category list: {$sPageURL}. Please consult the plugin developer.", 'amazon-auto-links' );
                return;
            }

            try {

                // Using the Simple DOM library for encoding problems with the PHP built-in DOM objects.
                $_oSimpleDOM = str_get_html( $_sHTML );

                // If the existing (as of 2018/06) page layout design is not used,
                if ( ! $_oSimpleDOM->find( "#zg_browseRoot", 0 ) ) {
                    throw new Exception;
                }

                $this->___aElements = $this->___getElements( $_oSimpleDOM, $sPageURL, $sLocale );
                return;

            } catch ( Exception $_oExceptio ) {

                // @todo For a new page layout design introduced around 2018/06,
                if ( $_oSimpleDOM->find( "#crown-category-nav", 0 ) ) {

                }

                // Try with a R18 confirmation redirect
                $_oHTTP->deleteCache();
                $_sRedirectURL = AmazonAutoLinks_Property::$aCategoryBlackCurtainURLs[ $sLocale ]
                    . '?redirect=true&redirectUrl=' . urlencode( $sPageURL );
                $_oSidebarR18  = new AmazonAutoLinks_Form_CategorySelect___Sidebar__R18( $_sRedirectURL, $sLocale );
                $this->___aElements = $_oSidebarR18->get();
                return;

            }

        }
            /**
             * Extracts and set sidebar elements.
             * @param   string  $oSimpleDOM
             * @param   string  $sPageURL
             * @param   string  $sLocale
             * @param   string  $sRSSURL
             * @since   3.6.0
             * @return  array
             */
            private function ___getElements( $oSimpleDOM, $sPageURL, $sLocale ) {

                $_oFeedURL      = new AmazonAutoLinks_Form_CategorySelect___Sidebar___FeedURL( $oSimpleDOM, $sPageURL );
                $_sRSSURL       = $_oFeedURL->get();
                $_oCategoryList = new AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList( $oSimpleDOM );
                $_oBreadcrumb   = new AmazonAutoLinks_Form_CategorySelect___Sidebar___Breadcrumb( $oSimpleDOM, $sLocale, $_sRSSURL );
                return array(
                    'RSSURL'       => $_sRSSURL,
                    'CategoryList' => $_oCategoryList->get(),   // must be done after the above `$_oFeedURL->get()` method as this method modifies the links.
                    'Breadcrumb'   => $_oBreadcrumb->get(),
                    'Error'        => '',
                ) + $this->___aElements;

            }



    /**
     * @param       string      $sElement       The sidebar element name to retrieve.
     * @return      string|array
     * @since       3.6.0
     */
    public function get( $sElementName='' ) {

        // If a key is not specified, return the entire element array.
        if ( '' === $sElementName ) {
            return $this->___aElements;
        }

        // If an error occurs, return the error message.
        $_sError = $this->getElement( $this->___aElements, 'Error' );
        if ( $_sError ) {
            return $_sError;
        }

        // Return the element
        return $this->getElement( $this->___aElements, $sElementName  );

    }

}