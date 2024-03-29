<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Loads the unit option converter component.
 * 
 * @since        4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_Setting {
    
    /**
     * Sets up hooks.
     */
    public function __construct() {
       
        add_action( 
            'load_' . AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
            array( $this, 'replyToLoadPage' )
        );
                
    }
    
    /**
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @return      void
     * @callback    action      load_{page slug}_{tab slug}
     */
    public function replyToLoadPage( $oFactory ) {
        new AmazonAutoLinks_CustomOEmbed_Setting_Embed( $oFactory, AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] );
    }

}
