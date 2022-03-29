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
 * Handles PhpZon's shortcodes.
 * 
 * @since       4.1.0
 */
class AmazonAutoLinks_PhpZonSupport_Shortcode_phpzon extends AmazonAutoLinks_WPUtility {

    public $sShortcode = 'phpzon';

    /**
     * Registers the shortcode(s).
     */
    public function __construct() {

        add_shortcode( $this->sShortcode, array( $this, 'replyToGetOutput' ) );

    }

    /**
     * Returns the output based on the shortcode arguments.
     *
     * ### Example
     * [phpzon
     *      keywords=”Kitchen Aid Coffee Maker Water Filter Basket”
     *      num=”9″
     *      country=”us”
     *      searchindex=”All”
     *      templatename=”columns”
     *      columns=”3″
     * ]
     *
     * @param array $aArguments The shortcode arguments.
     *
     * @return      string|void
     * @since       4.1.0
     */
    public function replyToGetOutput( $aArguments ) {

        $_oOption               = AmazonAutoLinks_Option::getInstance();
        $_aConversionMap        = $_oOption->get( array( 'phpzon', 'template_conversion_map' ), array() );

        // `keywords` -> `count`
        $_sSearchIndex = $this->getElement( $aArguments, array( 'keywords' ), '' );
        if ( $_sSearchIndex ) {
            $aArguments[ 'search' ] = $_sSearchIndex;
        }
        unset( $aArguments[ 'keywords' ] );

        // `num` -> `count`
        $_iNum = ( integer ) $this->getElement( $aArguments, array( 'num' ), 0 );
        if ( $_iNum ) {
            $aArguments[ 'count' ] = $_iNum;
        }

        // `templatename` -> `template_id`
        $_sAALBTemplateID = $this->getElement( $aArguments, array( 'templatename' ), '' );
        if ( isset( $_aConversionMap[ $_sAALBTemplateID ] ) ) {
            $aArguments[ 'template_id' ] = $_aConversionMap[ $_sAALBTemplateID ];
        }
        unset( $aArguments[ 'templatename' ] );

        // `searchindex` -> `SearchIndex`
        $_sSearchIndex = $this->getElement( $aArguments, array( 'searchindex' ), '' );
        if ( $_sSearchIndex ) {
            $aArguments[ 'SearchIndex' ] = $_sSearchIndex;
        }
        unset( $aArguments[ 'searchindex' ] );

        // `columns` -> `column`
        $_iColumns = $this->getElement( $aArguments, array( 'columns' ), 0 );
        if ( $_iColumns ) {
            $aArguments[ 'column' ] = $_iColumns;
        }
        unset( $aArguments[ 'columns' ] );

        // country: lowercase -> upper case
        $_sLocale = $this->getElement( $aArguments, array( 'country' ), '' );
        if ( $_sLocale ) {
            $aArguments[ 'country' ] = strtoupper( $_sLocale );
        }

        // Drop empty string and null.
        $aArguments = array_filter( $aArguments, 'strlen' );
        return AmazonAutoLinks( $aArguments, false );

    }    

}