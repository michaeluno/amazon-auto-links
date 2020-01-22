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
 * @since       3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Submit extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $this->addSettingFields(
            array(
                'field_id'          => '__submit',
                'type'              => 'submit',
                'value'             => __( 'Save', 'amazon-auto-links' ),
                'save'              => false,
                'label_min_width'   => '100%',
                'attributes'        => array(
                    'field'    => array(
                        'style' => 'width: 100%; text-align: center;',
                    ),
                )
            )        
        );
        
        
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        return $aInput;        
    }
    
}
