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
 * Escapes SVG outputs.
 *
 * @since   4.6.19
 */
class AmazonAutoLinks_Unit_Event_Filter_SVGOutputEscape extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_output_svg_definition', array( $this, 'replyToEscapeSVG' ) );
    }

    /**
     * @param  string $sSVGOutput
     * @since  4.6.19
     * @return string
     */
    public function replyToEscapeSVG( $sSVGOutput ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return wp_kses( $sSVGOutput, $_oOption->getAllowedHTMLTags() );
    }

}