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
 * This component has an event handler so needs to be loaded in the front-end as well.
 *
 * @since 3.3.0
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
        new AmazonAutoLinks_Unit_UnitOptionConverter_Event_Action_UnitOptionConverter;
    }
    
}