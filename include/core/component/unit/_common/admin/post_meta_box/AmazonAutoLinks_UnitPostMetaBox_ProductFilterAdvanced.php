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
 * Defines the meta box that contains advanced product filter unit options.
 */
class AmazonAutoLinks_UnitPostMetaBox_ProductFilterAdvanced extends AmazonAutoLinks_UnitPostMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {

        $_aClassNames = array(
            'AmazonAutoLinks_FormFields_ProductFilterAdvanced',
        );
        $this->_addFieldsByClasses( $_aClassNames );
                    
    }
    
}