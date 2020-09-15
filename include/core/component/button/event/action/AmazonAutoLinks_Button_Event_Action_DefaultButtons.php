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
 *  Creates default buttons.
 *  
 *  @package    Amazon Auto Links
 *  @since      4.3.0
 */
class AmazonAutoLinks_Button_Event_Action_DefaultButtons extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks and properties.
     */
    public function __construct() {
        add_action( 'aal_action_plugin_activated', array( $this, 'replyToCreateDefaultButtons' ) );
    }

    /**
     * @return void
     * @callback    add_action  aal_action_plugin_activated
     */
    public function replyToCreateDefaultButtons() {

        $_sButtonPostType = AmazonAutoLinks_Registry::$aPostTypes[ 'button' ];
        if ( ! post_type_exists( $_sButtonPostType ) ) {
            new AmazonAutoLinks_PostType_Button(
                $_sButtonPostType,                     // slug
                null,                        // post type argument. This is defined in the class.
                AmazonAutoLinks_Registry::$sFilePath   // script path
            );
        }
        new AmazonAutoLinks_DefaultButtonCreation;

    }

}