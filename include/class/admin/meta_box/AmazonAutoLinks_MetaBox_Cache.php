<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Defines the meta box that contains Template options.
 */
class AmazonAutoLinks_MetaBox_Cache extends AmazonAutoLinks_MetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     * 
     * The meta box will not be added to a unit type not listed in this array.
     * 
     * @remark      This property is checked in the `_isInThePage()` method
     * so set the unit types of that this meta box shuld apper.
     */     
    protected $aUnitTypes = array( 
        'category', 
        'similarity_lookup',
        'item_lookup',
        'search',
        'tag',
        'url',           // 3.2.0+
    );    
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {

        $_oFields = new AmazonAutoLinks_FormFields_Unit_Cache;
        $_aFields = $_oFields->get( 
            '',     // field id prefix
            'category'  // unit type
        );
        foreach( $_aFields as $_aField ) {           
            $this->addSettingFields( $_aField );
        }

    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( /* $aInput, $aOriginal, $oFactory */ ) {    
        
        $_aParams = func_get_args() + array( null, null, null );
        $aInput   = $_aParams[ 0 ];
        $aInput[ 'cache_duration' ] = $this->oUtil->fixNumber(
                $aInput[ 'cache_duration' ],     // number to sanitize
                1200,     // default
                0         // minimum
        );
        return $aInput;
        
    } 
    
}