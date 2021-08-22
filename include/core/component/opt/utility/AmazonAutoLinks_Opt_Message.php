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
 * Provides messages of the `Opt` component.
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_Opt_Message {

    /**
     * @return  string
     * @since   4.7.0
     */
    static public function getGiveThePlugin5Stars() {
        $_oSVG    = new AmazonAutoLinks_SVGGenerator_RatingStar( true, __( 'Five stars', 'amazon-auto-links' ) );
        $_sStars  = $_oSVG->get( 50 );
        return sprintf(
            __( 'Give the plugin <a href="%1$s" target="_blank">%2$s</a> to encourage the development!', 'amazon-auto-links' ),
            'https://wordpress.org/support/plugin/amazon-auto-links/reviews/',
            $_sStars
        );
    }

}