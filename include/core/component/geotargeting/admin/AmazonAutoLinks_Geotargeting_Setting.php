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
 * Loads the admin pages of the Geo-targeting component.
 *
 * @package      Auto Amazon Links/Geotargeting
 * @since        4.6.0
 */
class AmazonAutoLinks_Geotargeting_Setting {

    /**
     * Sets up hooks.
     */
    public function __construct() {

        add_action( 'load_' . AmazonAutoLinks_Registry::$aAdminPages[ 'main' ], array( $this, 'replyToLoadPage' ) );

    }

    /**
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @return      void
     * @callback    action      load_{page slug}_{tab slug}
     */
    public function replyToLoadPage( $oFactory ) {
        new AmazonAutoLinks_Geotargeting_Setting_Tab_Geotargeting( $oFactory, AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] );
    }

}