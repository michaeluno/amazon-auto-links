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
 * Handles feed outputs.
 *
 * @since       4.6.0
 */
class AmazonAutoLinks_Event___Feed_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'replyToSetHooks' ) );
        add_action( 'init', array( $this, 'replyToLoadFeed' ), 999 );
        $this->_construct();
    }

    /**
     * @callback add_action init
     */
    public function replyToSetHooks() {
        add_filter( 'aal_filter_unit_format', array( $this, 'replyToGetUnitFormat' ) );
        add_filter( 'aal_filter_unit_item_format_tag_replacements', array( $this, 'replyToGetTagReplacements' ), 10, 2 );
    }

    /**
     * @callback add_action init
     */
    public function replyToLoadFeed() {
        $this->_load();
        exit();
    }

    /**
     * Forces the unit format to only have `%products%` to avoid RSS validation errors.
     * @param  string $sUnitFormat
     * @return string
     * @since  4.5.8
     */
    public function replyToGetUnitFormat( $sUnitFormat ) {
        return '%products%';
    }

    /**
     * Modifies the tag replacements of unit outputs.
     *
     * This is to have individual independent SVG icons, not reusing existent SVG elements.
     * Since feed outputs for exporting and parsed by third-parties, each post should have independent definition for embedded SVG elements.
     *
     * @param  array $aReplacements
     * @param  array $aProduct
     * @return array
     * @since  4.6.0
     */
    public function replyToGetTagReplacements( array $aReplacements, array $aProduct ) {

        // Rating stars
        $aReplacements[ '%rating%' ] = $this->___getIndividualRatingStarOutput( $aReplacements[ '%rating%' ], $aProduct );

        // Prime icon
        $aReplacements[ '%prime%' ]  = AmazonAutoLinks_Unit_Utility::getPrimeMark( $aProduct, false );
        return $aReplacements;

    }
        /**
         * @param  string $sOutput  The original rating output
         * @param  array  $aProduct
         * @return string
         * @since  4.6.0
         */
        private function ___getIndividualRatingStarOutput( $sOutput, array $aProduct ) {
            preg_match( '/data-rating\=[\'"](\d+)[\'"]/', $sOutput, $_aMatches );
            if ( ! isset( $_aMatches[ 1 ] ) ) {
                return $sOutput;
            }
            $_iRating       = $_aMatches[ 1 ];
            preg_match( '/data-review-count\=[\'"](\d+)[\'"]/', $sOutput, $_aMatches );
            $_iReviewCount  = $_aMatches[ 1 ];

            preg_match( '/data-review-url\=[\'"](.+?)[\'"]/', $sOutput, $_aMatches );
            $_sReviewURL    = isset( $_aMatches[ 1 ] ) ? $_aMatches[ 1 ] : '';

            return AmazonAutoLinks_Unit_Utility::getRatingOutput( $_iRating, $_sReviewURL, $_iReviewCount, false );
        }

    /**
     * A user constructor.
     *
     * @remark Override this method in extended classes.
     * @since  4.6.0
     */
    protected function _construct() {}

    /**
     * Performs the actual task of rendering feeds.
     *
     * @remark Override this method in extended classes.
     * @since  4.6.0
     */
    protected function _load() {}

}