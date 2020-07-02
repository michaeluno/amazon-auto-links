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
 * @since       4.0.0
 */
class AmazonAutoLinks_AALBSupport_Shortcode_amazon_textlink extends AmazonAutoLinks_AALBSupport_Shortcode_amazon_link {

    public $sShortcode = 'amazon_textlink';

    /**
     * Returns the output based on the shortcode arguments.
     *
     * ### Example
     * [amazon_textlink
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
     * @since       4.0.0
     */
    public function replyToGetOutput( $aArguments ) {
        $aArguments[ '_no_outer_container' ] = true;    // when the Text template is selected, this is redundant but kept for cases of other templates.
        return parent::replyToGetOutput( $aArguments );
    }

}