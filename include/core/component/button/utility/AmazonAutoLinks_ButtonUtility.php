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
 *  Provides shared utility methods among the button component.
 *  
 *  @package    Amazon Auto Links
 *  @since      4.0.1
 */
class AmazonAutoLinks_ButtonUtility extends AmazonAutoLinks_PluginUtility {

    /**
     * Returns all CSS rules of active buttons.
     *
     * @return      string
     * @since       3
     * @since       4.0.1   Moved from `AmazonAutoLinks_PluginUtility`.
     */
    static public function getCSSRulesOfActiveButtons() {

        $_aCSSRules = array();
        foreach( self::getActiveButtonIDs() as $_iID ) {
            $_aCSSRules[]  = str_replace(
                '___button_id___',
                $_iID,
                trim( get_post_meta( $_iID, 'button_css', true ) )
            );
            $_aCSSRules[] = trim( get_post_meta( $_iID, 'custom_css', true ) );
        }
        return trim( implode( PHP_EOL, array_filter( $_aCSSRules ) ) );

    }

}