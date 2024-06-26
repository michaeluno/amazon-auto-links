<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */
 
 
class AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_ProductFilterAdvanced extends AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Base {

    /**
     * Sets up form fields.
     */ 
    public function setUp() {

        $_aClassNames = array(
            'AmazonAutoLinks_FormFields_Unit_ProductFilterAdvanced',
        );
        $this->_addFieldsByClasses( $_aClassNames );
                    
    }
 
}