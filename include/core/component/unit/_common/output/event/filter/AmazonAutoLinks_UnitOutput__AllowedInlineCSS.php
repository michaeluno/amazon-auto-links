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
 * Customize allowed inline CSS rules for WordPress sanitization functions such as wp_kses().
 *
 * @since       4.6.19
 */
class AmazonAutoLinks_UnitOutput__AllowedInlineCSS extends AmazonAutoLinks_UnitOutput__DelegationBase {

    /**
     * @return array
     * @since  4.6.19
     */
    protected function _getFilterArguments() {
        return array(
            array(
                'safe_style_css',
                array( $this, 'replyToGetStyleCSS' ),
                100,  // priority
                1     // number of parameters
            ),
        );
    }

    /**
     * @param array $aAllowedCSSProperties
     *
     * @return array
     * @since  4.6.19
     */
    public function replyToGetStyleCSS( $aAllowedCSSProperties ) {;
        return array_merge( $aAllowedCSSProperties, $this->_oUnitOutput->oOption->getAllowedHTMLInlineStyles() );
    }

}