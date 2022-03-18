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
 * Provides methods to generate credit links.
 * 
 * @since 3.2.3
 * @since 4.6.19 Renamed from `?`.
 */
class AmazonAutoLinks_Unit_Event_Filter_Credit extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_credit_comment', array( $this, 'replyToGetCreditComment' ) );
        add_filter( 'aal_filter_credit_link_0', array( $this, 'replyToGetCreditLink_0' ), 10, 2 );
        add_filter( 'aal_filter_credit_link_1', array( $this, 'replyToGetCreditLink_1' ), 10, 2 );
        add_filter( 'aal_filter_credit_link_2', array( $this, 'replyToGetCreditLink_2' ), 10, 2 );
    }

    /**
     * @return string
     * @since  3.2.3
     */
    public function replyToGetCreditComment( $sCredit ) {
        return $sCredit . self::getCommentCredit();
    }

    /**
     * @return string
     */
    public function replyToGetCreditLink_0( $sCredit, $oOption ) {
        $_sVendorURL = $this->___getVendorURL( $oOption );
        return $sCredit
            . "<div class='amazon-auto-links-credit' style='width: 100%;'>"
                . "<span style='margin:1em 0.4em;float:right;clear:both;background-image:url(" . esc_url( AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/icon/menu_icon_16x16.png', true ) ) . ");background-repeat:no-repeat;background-position: 0 50%;padding-left:20px;font-size: smaller'>"
                    ."<a href='" . esc_url( $_sVendorURL ) . "' title='" . esc_attr( $this->___getUserMessage( $oOption ) ) . "' rel='author' target='_blank' style='border:none'>"
                        . AmazonAutoLinks_Registry::NAME
                    . "</a>"
                . "</span>"
            . "</div>";
    }
    public function replyToGetCreditLink_1( $sCredit, $oOption ) {
        return $sCredit
            . $this->___getImageOutput(
                $this->___getVendorURL( $oOption ),
                AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/image/credit/amazon-auto-links-250x250.jpg', true ),
                $this->___getUserMessage( $oOption )
            );
    }
    public function replyToGetCreditLink_2( $sCredit, $oOption ) {
        return $sCredit
            . $this->___getImageOutput(
                $this->___getVendorURL( $oOption ),
                AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/image/credit/amazon-auto-links-horizontal.jpg', true ),
                $this->___getUserMessage( $oOption )
            );
    }
        /**
         * @param  string $sVendorURL
         * @param  string $sImageSRC
         * @param  string $sUserComment
         * @return string
         * @since  5.1.6
         */
        private function ___getImageOutput( $sVendorURL, $sImageSRC, $sUserComment ) {
            return "<div class='amazon-auto-links-credit' style='width:100%;max-width:100%'>"
                . "<a href='" . esc_url( $sVendorURL ) . "' target='_blank' title='" . esc_attr( $sUserComment ) . "'>"
                    . "<img alt='" . esc_attr( $sUserComment ) . "' src='". esc_url( $sImageSRC ) . "' style='max-width:100%;margin-left:auto;margin-right:auto;display:block' />"
                . "</a>"
            . "</div>";
        }
        /**
         * @param AmazonAutoLinks_Option $oOption
         * @sicne 5.1.6
         */
        private function ___getUserMessage( $oOption ) {
            $_sComment = trim( ( string ) $oOption->get( 'miunosoft_affiliate', 'user_comment' ) );
            return $_sComment
                ? $_sComment
                : AmazonAutoLinks_Registry::DESCRIPTION;
        }
    /**
     * @return string
     */
    private function ___getVendorURL( $oOption ) {
        $_sQueryKey  = $oOption->get( 'query', 'cloak' );
        return add_query_arg(
            array(
                $_sQueryKey => 'vendor',
            ),
            site_url()
        );
    }

}