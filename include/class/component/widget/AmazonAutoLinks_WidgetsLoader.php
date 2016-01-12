<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
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
        
        new AmazonAutoLinks_WidgetByID(
            sprintf( 
                __( '%1$s by Unit' ),
                AmazonAutoLinks_Registry::NAME
            )
        );
        new AmazonAutoLinks_ContextualProductWidget(
            AmazonAutoLinks_Registry::NAME . ' - ' . __( 'Contextual Products', 'amazon-auto-links' )
        );        
        
    
    }    
    
}