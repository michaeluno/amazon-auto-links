<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
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