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
class AmazonAutoLinks_MetaBox_Template extends AmazonAutoLinks_MetaBox_Base {
    
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
        'url',      // 3.2.0+
    );    
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_Unit_Template;
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
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        $_oUtil   = new AmazonAutoLinks_WPUtility;
        
        add_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
        $_aAllowedHTMLTags = $_oUtil->convertStringToArray(
            $_oOption->get( 
                'form_options', // first dimensional key
                'allowed_html_tags' // second dimensional key
            ), 
            ',' 
        );
        $aInput[ 'item_format' ]  = $_oUtil->escapeKSESFilter( $aInput[ 'item_format' ], $_aAllowedHTMLTags );
        $aInput[ 'image_format' ] = $_oUtil->escapeKSESFilter( $aInput[ 'image_format' ], $_aAllowedHTMLTags );
        $aInput[ 'title_format' ] = $_oUtil->escapeKSESFilter( $aInput[ 'title_format' ], $_aAllowedHTMLTags );
        remove_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
        
        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInput !== $aOriginal ) {
            AmazonAutoLinks_Event_Scheduler::prefetch( 
                AmazonAutoLinks_PluginUtility::getCurrentPostID()
            );
        }
        
        return $aInput;
        
    }
        /**
         * @return      array
         */
        public function replyToAddAllowedInlineCSSProperties( $aProperty ) {
            $aProperty[] = 'max-width';
            $aProperty[] = 'min-width';
            $aProperty[] = 'max-height';
            $aProperty[] = 'min-height';
            return $aProperty;
        }
    
}