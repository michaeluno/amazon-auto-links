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
        add_filter( 'safe_style_css', array( $this, 'replyToAddSafeCSSProperties' ) );
        $_sSVG = wp_kses( $sSVGOutput, $_oOption->getAllowedHTMLTags() );
        remove_filter( 'safe_style_css', array( $this, 'replyToAddSafeCSSProperties' ) );
        return $_sSVG;
    }

    /**
     * @param array $aCSSAttributes
     * @since 5.1.0
     */
    public function replyToAddSafeCSSProperties( $aCSSAttributes ) {
        $_aSafe         = array(
            'width',    'height',
            'overflow', 'visibility',
            'position', 'left', 'top', 'right', 'bottom',
        );
        return array_merge( $aCSSAttributes, $_aSafe );
    }

}