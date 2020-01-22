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
 * Loads the widgets component.
 *  
 * @package     Amazon Auto Links
 * @since       3.1.0
*/
class AmazonAutoLinks_WidgetsLoader {
    
    /**
     * Loads necessary components.
     */
    public function __construct() {

        $_oOption = AmazonAutoLinks_Option::getInstance();

        if ( $_oOption->get( 'widget', 'register', 'by_unit' ) ) {
            new AmazonAutoLinks_WidgetByID(
                sprintf(
                    __( '%1$s by Unit' ),
                    AmazonAutoLinks_Registry::NAME
                )
            );
        }

        if ( $_oOption->get( 'widget', 'register', 'contextual' ) ) {
            new AmazonAutoLinks_ContextualProductWidget(
                AmazonAutoLinks_Registry::NAME . ' - ' . __( 'Contextual Products', 'amazon-auto-links' )
            );
        }
    
    }    
    
}