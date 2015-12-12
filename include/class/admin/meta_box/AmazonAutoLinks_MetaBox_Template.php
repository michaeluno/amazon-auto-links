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
    public function validate( $aInputs, $aOldInputs, $oFactory ) {    
        
        // Sanitize format options.
        $aInputs = $this->_getItemFormatsSanitized( $aInputs, $aOldInputs );
                
        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInputs !== $aOldInputs ) {
            AmazonAutoLinks_Event_Scheduler::prefetch( 
                AmazonAutoLinks_PluginUtility::getCurrentPostID()
            );
        }
        
        return $aInputs;
        
    }
    
        /**
         * @return      array
         * @since       3.2.4
         */
        private function _getItemFormatsSanitized( $aInputs, $aOldInputs ) {
            
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
            
            if ( ! $_oOption->isAdvancedAllowed() ) {
                $_aItemFormat   = AmazonAutoLinks_UnitOption_Base::getDefaultItemFormat();
                $aInputs[ 'item_format' ] = $_oUtil->getElement(
                    $aOldInputs,
                    'item_format',
                    $_aItemFormat[ 'item_format' ]
                );
                $aInputs[ 'image_format' ] = $_oUtil->getElement(
                    $aOldInputs,
                    'image_format',
                    $_aItemFormat[ 'image_format' ]
                );
                $aInputs[ 'title_format' ] = $_oUtil->getElement(
                    $aOldInputs,
                    'title_format',
                    $_aItemFormat[ 'title_format' ]
                );                
            }
            $aInputs[ 'item_format' ]  = $_oUtil->escapeKSESFilter( $aInputs[ 'item_format' ], $_aAllowedHTMLTags );
            $aInputs[ 'image_format' ] = $_oUtil->escapeKSESFilter( $aInputs[ 'image_format' ], $_aAllowedHTMLTags );
            $aInputs[ 'title_format' ] = $_oUtil->escapeKSESFilter( $aInputs[ 'title_format' ], $_aAllowedHTMLTags );
            remove_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
            
            return $aInputs;
            
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