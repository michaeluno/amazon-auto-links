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
 *
 * @since        4.7.0
 */
class AmazonAutoLinks_Opt_Event_Filter_SettingTabURL {

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_opt_setting_tab_url', array( $this, 'replyToGetSettingTabURL' ), 10, 2 );
    }

    /**
     * @param  string $sURL
     * @param  string $sAnchorID  The element ID to anchor
     * @return string
     * @since  4.7.0
     */
    public function replyToGetSettingTabURL( $sURL, $sAnchorID ) {
        $_sAnchorHash = $sAnchorID ? "#{$sAnchorID}" : '';
        return add_query_arg(
            array(
                'post_type'     => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'page'          => AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                'tab'           => 'opt',
            ),
            admin_url( 'edit.php' )
        ) . $_sAnchorHash;
    }

}