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
 * Handles plugin's shortcodes.
 * 
 * @package     Amazon Auto Links
 * @since       3.11.0
 */
class AmazonAutoLinks_AALBSupport_Shortcode_amazon_link extends AmazonAutoLinks_WPUtility {

    public $sShortcode = 'amazon_link';

    /**
     * Registers the shortcode(s).
     */
    public function __construct() {

        add_shortcode( $this->sShortcode, array( $this, 'replyToGetOutput' ) );
        add_filter( 'aal_filter_shortcode_' . $this->sShortcode, array( $this, 'replyToFilterShortcodeContents' ), 10, 2 );

    }

    /**
     * Called when ALLB Gutenberg block contents are parsed.
     *
     * Those contents come with their shortcodes and the second parameter receives the shortcode attributes.
     * @return      string
     * @callback    filter  aal_filter_shortcode_amazon_link
     */
    public function replyToFilterShortcodeContents( $sContent, $aAttributes ) {
        return $sContent . $this->replyToGetOutput( $aAttributes );
    }

    /**
     * Returns the output based on the shortcode arguments.
     *
     * ### Example
     * [amazon_link
     *      asins='B00S74K3LM,B0793ZJYVB,B00WVL37U6'
     *      template='ProductGrid'
     *      store='xxxx-20'
     *      marketplace='US|UK'
     *      link_id='e9dff51c-72c3-4033-b5ae-40fd85995bed'
     * ]
     * [amazon_link
     *      asins='B0753VX2CB,B074PCR86M,B076FGMBJR,B075NT6T39|B00YD545CC,B01N9YOF3R,B00YD54HZ2,B071W3DDM7,B00YD546IA|B0764FLPKQ,B0714DP3BG,B01LZKSVRB'
     *      template='ProductCarousel'
     *      store='br-1|us-1|in-1'
     *      marketplace='BR|DE|IN'
     *      link_id='f863a353-cea3-11e7-a36d-bbeba5c8a631'
     * ]
     *
     * @param array $aArguments The shortcode arguments.
     *
     * @return string|void
     * @since       3.11.0
     */
    public function replyToGetOutput( $aArguments ) {

        $_oOption               = AmazonAutoLinks_Option::getInstance();
        $_aConversionMap        = $_oOption->get( array( 'aalb', 'template_conversion_map' ), array() );
        $_sDefaultLocale        = $_oOption->get( array( 'unit_default', 'country' ), 'US' );
        $_sDefaultAssociateID   = $_oOption->get( array( 'unit_default', 'associate_id' ), '' );

        // `asins` -> `asin`
        $_sASIN = $this->getElement( $aArguments, array( 'asin' ), '' );
        $aArguments[ 'asin' ] = $_sASIN . ',' . $this->getElement( $aArguments, array( 'asins' ), '' );

        // `template` -> `template_id`
        $_sAALBTemplateID = $this->getElement( $aArguments, array( 'template' ), '' );
        if ( isset( $_aConversionMap[ $_sAALBTemplateID ] ) ) {
            $aArguments[ 'template_id' ] = $_aConversionMap[ $_sAALBTemplateID ];
        }
        unset( $aArguments[ 'template' ] );

        // marketplace -> country
        $_sLocales = $this->getElement( $aArguments, array( 'marketplace' ) );
        $_aLocales = $this->getStringIntoArray( $_sLocales, '|' );
        $aArguments[ 'country' ] = count( $_aLocales ) > 1
            ? $_sDefaultLocale
            : $_sLocales;
        unset( $aArguments[ 'marketplace' ] );

        // store -> associate_id
        $_sAssociateIDs = $this->getElement( $aArguments, array( 'store' ) );
        $_aAssociateIDs = $this->getStringIntoArray( $_sAssociateIDs, '|' );
        $_sAssociateID  = count( $_aAssociateIDs ) > 1
            ? $this->getElement( $_aAssociateIDs, array( $aArguments[ 'country' ] ), $_sDefaultAssociateID )
            : $_sAssociateIDs;
        $aArguments[ 'associate_id' ] = $_sAssociateID;
        unset( $aArguments[ 'store' ] );

        // text -> product_title
        $_sProductTitle = $this->getElement( $aArguments, array( 'text' ) );
        $aArguments[ 'product_title' ] = $_sProductTitle;
        unset( $aArguments[ 'text' ] );

        // Drop empty string and null.
        $aArguments = array_filter( $aArguments, 'strlen' );
        return AmazonAutoLinks( $aArguments, false );

    }    

}