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
 * Sets the product disclaimer tooltip text.
 *
 * @since 4.7.0
 */
class AmazonAutoLinks_Disclosure_Event_Filter_DisclaimerTooltip extends AmazonAutoLinks_Disclosure_Utility {

    /**
     * @var AmazonAutoLinks_Option
     */
    public $oOption;

    /**
     * @since 4.7.0
     */
    public function __construct() {
        add_filter( 'aal_filter_unit_output_disclaimer_text', array( $this, 'replyToGetDisclaimerText' ) );

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->get( 'disclosure', 'link_disclaimer_to_page' ) ) {
            add_filter( 'aal_filter_unit_output_disclaimer_link_url', array( $this, 'replyToGetDisclaimerURL' ) );
        }
        $this->oOption = $_oOption;
    }

    /**
     * @var string
     * @since 4.7.0
     */
    public $sDisclaimerURLCache;

    /**
     * @callback add_filter() aal_filter_unit_output_disclaimer_link_url
     * @param    string       $sURL
     * @return   string
     * @since    4.7.0
     */
    public function replyToGetDisclaimerURL( $sURL ) {
        if ( isset( $this->sDisclaimerURLCache ) ) {
            return $this->sDisclaimerURLCache ? $this->sDisclaimerURLCache : $sURL;
        }
        $_aPage   = $this->oOption->get( 'disclosure', 'page' );
        $_iPostID = ( integer ) $this->getElement( $_aPage, 'value' );
        $_sURL    = get_permalink( $_iPostID );
        $this->sDisclaimerURLCache = $_sURL;
        return $this->sDisclaimerURLCache ? $this->sDisclaimerURLCache : $sURL;
    }

    /**
     * @var   string
     * @since 4.7.0
     */
    public $sDisclaimerCache;

    /**
     * @param  string $sDisclaimer
     * @return string
     * @since  4.7.0
     */
    public function replyToGetDisclaimerText( $sDisclaimer ) {
        if ( isset( $this->sDisclaimerCache ) ) {
            return $this->sDisclaimerCache;
        }
        $_sDisclaimer = $this->oOption->get( 'disclosure', 'disclaimer_text' );
        $this->sDisclaimerCache = $_sDisclaimer;
        return $this->sDisclaimerCache;
    }

}