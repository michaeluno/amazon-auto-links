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
 * @since       3.5.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_ProductFilterAdvanced extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {

    /**
     * Sets up form fields.
     */ 
    public function setUp() {

        $this->addSettingSections(
            array(
                'section_id'    => $this->_sSectionID,
            )
        );
        $this->addSettingFields( $this->_sSectionID );

        $_aClassNames = array(
            'AmazonAutoLinks_FormFields_ProductFilterAdvanced',
        );
        $this->_addFieldsByClasses( $_aClassNames );
                    
    }

    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {
        return $aInputs;
    }

    
}
