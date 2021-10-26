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
 * Checks if request URLs are allowed with Web Page Dumper.
 *
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_IsAllowedURL extends AmazonAutoLinks_Proxy_WebPageDumper_Utility {

    private $___aAlways   = array();
    private $___aExcludes = array();

    /**
     * Sets up hooks.
     * @since 4.5.0
     */
    public function __construct() {

        $this->___setProperties();
        if ( empty( $this->___aAlways ) && empty( $this->___aExcludes ) ) {
            return;
        }
        add_filter( 'aal_filter_web_page_dumper_is_allowed', array( $this, 'replyToCheckAllowedURL' ), 10, 4 );

    }
        /**
         * @since 4.5.0
         */
        private function ___setProperties() {
            $_oToolOption       = AmazonAutoLinks_ToolOption::getInstance();
            $_sAlways           = ( string ) $_oToolOption->get( array( 'web_page_dumper', 'always' ), '' );
            $_sExcludes         = ( string ) $_oToolOption->get( array( 'web_page_dumper', 'excludes' ), '' );
            $this->___aAlways   = self::getAsArray( preg_split( "/\s+/", trim( $_sAlways ), 0, PREG_SPLIT_NO_EMPTY ) );
            $this->___aExcludes = self::getAsArray( preg_split( "/\s+/", trim( $_sExcludes ), 0, PREG_SPLIT_NO_EMPTY ) );
        }

    /**
     * @param  boolean $bAllowed
     * @param  string  $sRequestURL
     * @param  array   $aArguments
     * @param  string  $sRequestType
     * @return boolean
     * @since  4.5.0
     */
    public function replyToCheckAllowedURL( $bAllowed, $sRequestURL, $aArguments, $sRequestType ) {
        if ( $this->___isOfPatterns( $sRequestURL, $this->___aAlways ) ) {
            return true;
        }
        if ( $this->___isOfPatterns( $sRequestURL, $this->___aExcludes ) ) {
            return false;
        }
        return $bAllowed;
    }
        /**
         * @param  string  $sURL
         * @param  array   $aPatterns
         * @return boolean
         */
        private function ___isOfPatterns( $sURL, array $aPatterns ) {
            foreach( $aPatterns as $_sPattern ) {
                if ( fnmatch( $_sPattern, $sURL ) ) {
                    return true;
                }
            }
            return false;
        }

}