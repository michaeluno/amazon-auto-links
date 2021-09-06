<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Modifies arguments sent to Web Page Dumper.
 *
 * @since        4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Filter_WebPageDumperArguments extends AmazonAutoLinks_Proxy_WebPageDumper_Utility {

    /**
     * Sets up hooks.
     * @since 4.5.0
     */
    public function __construct() {
        add_filter(
            'aal_filter_web_page_dumper_arguments',
            array( $this, 'replyToGetWebPageDumperArguments' ),
            10,
            2
        );
    }

    /**
     * @param  array  $aArguments
     * @param  string $sRequestURL
     * @return array
     * @since  4.5.0
     */
    public function replyToGetWebPageDumperArguments( $aArguments, $sRequestURL ) {

        if ( $this->isUserRatingURL( $sRequestURL ) ) {
            $aArguments[ 'cache' ]  = 1;
            $aArguments[ 'reload' ] = 1;
            // @deprecated
            // $aArguments[ 'block' ]  = array(
            //     'types' => array( 'script' )
            // );
            return $aArguments;
        }

        if ( $this->isBestSellerURL( $sRequestURL ) ) {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_sLocale = AmazonAutoLinks_Locales::getLocaleFromURL( $sRequestURL );
            $_oLocale = new AmazonAutoLinks_PAAPI50_Locale( $_sLocale );
            $aArguments[ 'cookies' ] = array(
                array(
                    'name'  => 'lc-acb' . strtolower( $_sLocale ),
                    'value' => $_oOption->get( array( 'associates', $_sLocale, 'paapi', 'language' ), $_oLocale->getDefaultLanguage() ),
                ),
                array(
                    'name'  => 'i18n-prefs',
                    'value' => $_oOption->get( array( 'associates', $_sLocale, 'paapi', 'currency' ), $_oLocale->getDefaultCurrency() ),
                ),
            );
            return $aArguments;
        }

        if ( $this->___isTextRequest( $sRequestURL ) ) {
            $aArguments[ 'output' ] = 'text';
            return $aArguments;
        }

        return $aArguments;
    }
        /**
         * @param  string  $sURL
         * @return boolean
         * @since  4.7.5
         */
        private function ___isTextRequest( $sURL ) {
            if ( $this->___isXMLRequest( $sURL ) ) {
                return true;
            }
            if ( $this->___isJSONRequest( $sURL ) ) {
                return true;
            }
            return false;
        }
            /**
             * @param  string  $sURL
             * @return boolean
             * @since  4.7.5
             */
            private function ___isXMLRequest( $sURL ) {
                if ( false !== strpos( $sURL, '://feeds.feedburner.com' ) ) {
                    return true;
                }
                return false;
            }
            /**
             * @param  string  $sURL
             * @return boolean
             * @since  4.7.5
             */
            private function ___isJSONRequest( $sURL ) {
                if ( $this->___isAdWidgetRequest( $sURL ) ) {
                    return true;
                }
                return false;
            }
                /**
                 * @param  string  $sURL
                 * @return boolean
                 * @since  4.7.5
                 */
                private function ___isAdWidgetRequest( $sURL ) {
                    if ( ! in_array( parse_url( $sURL, PHP_URL_HOST ), AmazonAutoLinks_Locales::getAdWidgetAPIHosts(), true ) ) {
                        return false;
                    }
                    if ( false !== strpos( $sURL, 'Operation=GetResults' ) ) {
                        return true;
                    }
                    return false;
                }

}