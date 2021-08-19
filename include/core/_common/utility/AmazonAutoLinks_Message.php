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
 * Provides plugin messages.
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_Message {

    /**
     * @return  string
     * @sicne   4.0.0
     * @since   4.7.0   Moved from `AmazonAutoLinks_PluginUtility`
     */
    static public function getUpgradePromptMessageToAddMoreUnits() {
        return sprintf(
            __( 'Please upgrade to <a href="%1$s">Pro</a> to add more units!', 'amazon-auto-links' ) . ' ' . __( 'Make sure to empty the <a href="%2$s">trash box</a> to delete the units completely!', 'amazon-auto-links' ),
            esc_url( AmazonAutoLinks_Registry::STORE_URI_PRO ),
            admin_url( 'edit.php?post_status=trash&post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] )
        );
    }

    /**
     * @since   3.2.4
     * @since   4.7.0   Moved from `AmazonAutoLinks_PluginUtility`
     * @return  string
     * @param   boolean $bHasLink
     */
    static public function getUpgradePromptMessage( $bHasLink=true ) {
        return $bHasLink
            ? sprintf(
                __( 'Please consider upgrading to <a href="%1$s" target="_blank">Pro</a> to enable this feature.', 'amazon-auto-links' ),
                esc_url( 'https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/' )
            )
            : __( 'Please consider upgrading to Pro to enable this feature.', 'amazon-auto-links' );
    }

    /**
     * @return string
     * @since  4.7.0
     * @deprecated
     */
    // static public function getThisRequiresPro() {
    //     return __( 'This requires Pro.', 'amazon-auto-links' );
    // }

    /**
     * @return string
     * @since  4.7.0
     */
    static public function getThisIsAvailableInPro() {
        return __( 'Available in Pro.', 'amazon-auto-links' );
    }

}