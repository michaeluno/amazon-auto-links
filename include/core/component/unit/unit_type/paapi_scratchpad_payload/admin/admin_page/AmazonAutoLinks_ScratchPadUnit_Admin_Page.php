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
 * Adds a tab to a setting page.
 * 
 * @since 5.0.0
 */
class AmazonAutoLinks_ScratchPadUnit_Admin_Page {

    public $sPageSlug = '';

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        $this->sPageSlug = AmazonAutoLinks_Registry::$aAdminPages[ 'paapi_search_unit' ];
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToLoadPage' ), 20 );
    }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    public function replyToLoadPage( $oAdminPage ) {
        new AmazonAutoLinks_ScratchPadUnit_Admin_Tab_Second( $oAdminPage, $this->sPageSlug );
    }

}