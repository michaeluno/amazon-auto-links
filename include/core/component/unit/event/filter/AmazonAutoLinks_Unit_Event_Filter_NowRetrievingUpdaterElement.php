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
 * Inserts an SVG definition for the Ajax request.
 * @since   4.6.1
 */
class AmazonAutoLinks_Unit_Event_Filter_NowRetrievingUpdaterElement extends AmazonAutoLinks_PluginUtility {

    /**
     * @since  4.6.1
     * @remark Already checked if the plugin debug mode is turned on.
     */
    public function __construct() {
        add_filter( 'aal_filter_now_retrieving_product_element', array( $this, 'replyToInsertSVGDefinition' ), 10, 3 );
    }

    /**
     * @param  string $sElementOutput
     * @param  string $sContext
     * @param  array $aProduct
     * @return string
     * @since  4.6.1
     */
    public function replyToInsertSVGDefinition( $sElementOutput, $sContext, $aProduct ) {

        if ( 'formatted_rating' !== $sContext ) {
            return $sElementOutput;
        }

        $_oSVG = new AmazonAutoLinks_SVGGenerator_RatingStar();
        return $_oSVG->getDefinition() . $sElementOutput;

    }

}