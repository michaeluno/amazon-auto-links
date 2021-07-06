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
 * Modifies unit outputs for feeds.
 *
 * JSON and RSS2 feeds are meant to be imported to third-party applications. And SVG icons will not be displayed on external sites without SVG source definitions embedded.*
 *
 * @since   4.6.4
 */
class AmazonAutoLinks_Unit_Event_Action_Feed_UnitOutputHooks extends AmazonAutoLinks_PluginUtility {

    /**
     * @since  4.6.4
     * @remark Already checked if the plugin debug mode is turned on.
     */
    public function __construct() {
        add_action( 'aal_action_setup_feed_output_hooks', array( $this, 'replyToSetHooks' ), 10, 1 );
    }

    /**
     * @param string $sType
     * @since 4.6.4
     */
    public function replyToSetHooks( $sType ) {
        add_filter( 'aal_filter_unit_format', array( $this, 'replyToGetUnitFormat' ) );
        add_filter( 'aal_filter_unit_item_format_tag_replacements', array( $this, 'replyToGetTagReplacements' ), 10, 2 );
        add_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToGetStarIconReplacement' ) );
    }

    /**
     * Forces the unit format to only have `%products%` to avoid RSS validation errors.
     * @param  string $sUnitFormat
     * @return string
     * @since  4.5.8
     * @since  4.6.4  Moved from `AmazonAutoLinks_Event___Feed_Base`.
     */
    public function replyToGetUnitFormat( $sUnitFormat ) {
        return '%products%';
    }

    /**
     * @param  array $aProduct
     * @since  4.6.1
     * @return array
     */
    public function replyToGetStarIconReplacement( array $aProduct ) {
        $aProduct[ 'formatted_rating' ] = $this->___getIndividualRatingStarOutput( $aProduct[ 'formatted_rating' ], $aProduct );
        return $aProduct;
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

            return "<div class='amazon-customer-rating-stars'>"
                    . AmazonAutoLinks_Unit_Utility::getRatingOutput( $_iRating, $_sReviewURL, $_iReviewCount, false )
                . "</div>";
        }

}