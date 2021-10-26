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
 * Provides shared utility methods for the widget component.
 *  
 * @package     Auto Amazon Links
 * @since       4.6.8
*/
class AmazonAutoLinks_Widget_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * Checks if the current page is in the widget preview page.
     *
     * WordPress 5.8 calls the widget output for preview in REST Request.
     * @since  4.6.8
     * @return boolean
     */
    static public function isInWidgetPreview() {
        if ( self::isRESTRequest() ) {
            return true;
        }
        return 'widgets.php' === $GLOBALS[ 'pagenow' ];
    }

}