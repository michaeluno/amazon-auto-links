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
 * Loads the unit option converter component.
 * 
 * @package      Amazon Auto Links
 * @since        3.3.0
 */
class AmazonAutoLinks_UnitOptionConverterLoader {
        
    public function __construct() {
       
        // Events
        add_action( 'aal_action_events', array( $this, 'replyToLoad' ) );
        
        if ( is_admin() ) {
            new AmazonAutoLinks_UnitOptionConverter_Setting;
        }
        
    }
    
    public function replyToLoad() {
        new AmazonAutoLinks_Event___Action_UnitOptionConverter;
    }
    
}