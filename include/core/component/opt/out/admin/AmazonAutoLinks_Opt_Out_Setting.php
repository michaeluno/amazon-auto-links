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
 * Loads the admin pages of the `Opt` component.
 *
 * @package      Amazon Auto Links/Opt
 * @since        4.7.0
 */
class AmazonAutoLinks_Opt_Out_Setting {

    public $sPageSlug;
    public $sTabSlug;

    /**
     * Sets up hooks.
     * @since 4.7.0
     */
    public function __construct() {

        add_action( 'load_' . 'AmazonAutoLinks_AdminPage', array( $this, 'replyToLoadAdminPages' ) );
        $this->sPageSlug = AmazonAutoLinks_Registry::$aAdminPages[ 'main' ];
        $this->sTabSlug  = 'opt';

    }

    public function replyToLoadAdminPages( $oFactory ) {
        add_action( "load_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToLoadTab' ) );
    }

    /**
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback    add_action()      load_{page slug}_{tab slug}
     * @since       4.7.0
     */
    public function replyToLoadTab( $oFactory ) {
        new AmazonAutoLinks_Opt_Out_Setting_Section_UserBase(
            $oFactory,
            $this->sPageSlug,   // page slug
            array( 'tab_slug' => $this->sTabSlug, )
        );
    }

}