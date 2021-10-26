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
 * Loads the admin pages of the `Opt` component.
 *
 * @package      Auto Amazon Links/Opt
 * @since        4.7.0
 */
class AmazonAutoLinks_Opt_Setting {

    /**
     * Sets up hooks.
     * @since 4.7.0
     */
    public function __construct() {
        add_action( 'load_' . AmazonAutoLinks_Registry::$aAdminPages[ 'main' ], array( $this, 'replyToLoadPage' ) );
    }

    /**
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback    add_action()      load_{page slug}
     * @since       4.7.0
     */
    public function replyToLoadPage( $oFactory ) {
        new AmazonAutoLinks_Opt_Setting_Tab_Opt( $oFactory, AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] );
    }

}