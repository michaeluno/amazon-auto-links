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
 * Provides methods to retrieve sidebar menu list elements of Amazon best selling products for R18 areas.
 *
 * @sicne       3.6.0
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar__R18 extends AmazonAutoLinks_Form_CategorySelect___Sidebar {

    protected $_sHTTPClientClass = 'AmazonAutoLinks_HTTPClient_FileGetContents';

    /**
     * @since       3.6.0
     */
    protected _handleExceptionsToSetElements( $oHTTP, $oSimpleDOM, $sPageURL, $sLocale ) {

        // For a new page layout design introduced around 2018/06,
        if ( $_oSimpleDOM->find( "#crown-category-nav", 0 ) ) {
            $this->_setElementsBy( '#crown-category-nav', $oSimpleDOM, $sPageURL, $sLocale );
            return;
        }

        // Otherwise, we have no more attempts.
        $this->_aElements[ 'Error' ] =sprintf(
            __( "Could not load a category list in this page: %1$s. Please consult the plugin developer.", 'amazon-auto-links' ),
            $sPageURL
        );

    }

}