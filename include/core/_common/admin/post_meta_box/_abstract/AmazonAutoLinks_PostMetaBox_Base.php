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
 * Defines a post meta box.
 */
abstract class AmazonAutoLinks_PostMetaBox_Base extends AmazonAutoLinks_AdminPageFramework_MetaBox {
    
    public function start() {
        
        // Register custom filed type.
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );        
        
    }
     
}