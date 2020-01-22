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
 * @since   3.7.7
 * @deprecated  3.9.0
 */
class AmazonAutoLinks_Event___Filter_CustomerReviewCache extends AmazonAutoLinks_Event___Filter_Base {

    protected $_sFilterHookName = 'aal_filter_http_request_set_cache_customer_review';
    protected $_iPriority       = 10;
    protected $_iParameters     = 4;

    protected function _construct() {}

    protected $_aTagsToRemove       = array( 'script', 'style', 'meta', 'link', 'title' );
    protected $_aAttributesToRemove = array( 'onload', 'onclick' );

    protected function _getFiltered( /* $mData, $sCacheName, $sCharSet, $iCacheDuration */ ) {

        $_aArguments = func_get_args() + array( array(), '', '', 86000 );
        $mData       = $_aArguments[ 0 ];
        $sCharSet    = $_aArguments[ 2 ];

        if ( is_wp_error( $mData ) ) {
            return $mData;
        }
        if ( ! isset( $mData[ 'body' ] ) ) {
            return $mData;
        }

        // @deprecated 3.7.9 The product ratings have started becoming a too long number sometimes and it might be because of this HTML clean-up
        /*
        $_sHTML = $mData[ 'body' ];
        $_oHTMLCleaner = new AmazonAutoLinks_HTMLCleaner( $_sHTML, $sCharSet );
        $_oHTMLCleaner->setRemovingTags( $this->_aTagsToRemove );
        $_oHTMLCleaner->setRemovingAttributes( $this->_aAttributesToRemove );
        $mData[ 'body' ] = $_oHTMLCleaner->get();
        */
        return $mData;

    }

}