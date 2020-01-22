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
 * Provides methods to retrieve sidebar menu list elements of Amazon best selling products for R18 areas.
 *
 * @sicne       3.5.7
 * @deprecated       3.9.1
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar__R18 extends AmazonAutoLinks_Form_CategorySelect___Sidebar {

    // for redirected pages, `AmazonAutoLinks_HTTPClient_FileGetContents` needed to be used but it seems to work fine with `AmazonAutoLinks_HTTPClient`.
    protected $_sHTTPClientClass = 'AmazonAutoLinks_HTTPClient';


    /**
     * @since       3.5.7
     * @since       3.9.1       Removed the `$oHTTP` parameter.
     */
    protected function _handleExceptionsToSetElements( DOMDocument $oDoc, $sPageURL, $sLocale ) {

        // For a new page layout design introduced around 2018/06,
        $_nodeCategory = $oDoc->getElementById( 'crown-category-nav' );
        if ( $_nodeCategory ) {
            $this->_aElements = $this->_getElements_crown_category_nav( $oDoc, $sPageURL, $sLocale );
            return;
        }

        // Otherwise, we have no more attempts.
        $this->_aElements[ 'Error' ] = sprintf(
            __( 'Could not load a category list in this page: %1$s. Please consult the plugin developer.', 'amazon-auto-links' ),
            $sPageURL
        );

    }

}