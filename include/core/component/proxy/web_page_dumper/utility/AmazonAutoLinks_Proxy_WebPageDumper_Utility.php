<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Provides utility methods for the Proxy/WebPageDumper component.
 *
 * @package      Amazon Auto Links
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * @since  4.5.0
     * @return string
     */
    static public function getWebPageDumperURL() {
        $_oToolOption       = AmazonAutoLinks_ToolOption::getInstance();
        $_sList             = ( string ) $_oToolOption->get( array( 'web_page_dumper', 'list' ), '' );
        $_aWebPageDumpers   = self::getAsArray( preg_split( "/\s+/", trim( $_sList ), 0, PREG_SPLIT_NO_EMPTY ) );
        if ( empty( $_aWebPageDumpers ) ) {
            return '';
        }
        shuffle( $_aWebPageDumpers );
        $_sURLWebPageDumper = reset( $_aWebPageDumpers );
        if ( ! filter_var( $_sURLWebPageDumper, FILTER_VALIDATE_URL ) ) {
            return '';
        }
        return ( string ) $_sURLWebPageDumper;
    }

    /**
     * @param  string $sURL
     * @return boolean
     * @since  4.5.0
     */
    static public function isUserRatingURL( $sURL ) {
        if ( ! self::isAmazonURL( $sURL ) ) {
            return false;
        }
        return false !== strpos( strtok( $sURL, '?' ), 'customer-reviews/widgets/average-customer-review/popover' );
    }

    /**
     * @param  string $sURL
     * @since  4.6.4
     * @return boolean
     */
    static public function isBestSellerURL( $sURL ) {
        if ( ! self::isAmazonURL( $sURL ) ) {
            return false;
        }
        return false !== strpos( strtok( $sURL, '?' ), '/gp/bestsellers/' );
    }

}