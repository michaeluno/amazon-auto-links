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
 *  Flush rewrite rules on plugin activation to avoid 404 errors for plugin custom post type posts, especially for units.
 *  
 *  @since 5.1.2
 */
class AmazonAutoLinks_Unit_Event_Action_FlushRewriteRules extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks and properties.
     * @since 5.1.2
     */
    public function __construct() {
        add_action( 'aal_action_plugin_activated', array( $this, 'replyToFlushRewriteRules' ) );
    }

    /**
     * @callback add_action()  aal_action_plugin_activated
     * @since    5.1.2
     */
    public function replyToFlushRewriteRules() {

        $_sPostTypeSlug = AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ];
        if ( ! post_type_exists( $_sPostTypeSlug ) ) {
            new AmazonAutoLinks_PostType_Unit(
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],  // slug
                null,          // post type argument. This is defined in the class.
                AmazonAutoLinks_Registry::$sFilePath
            );
        }
        flush_rewrite_rules();  // avoid 404 errors

    }

}