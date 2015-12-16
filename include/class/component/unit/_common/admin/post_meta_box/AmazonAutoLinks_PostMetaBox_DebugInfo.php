<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Displays the stored unit option values.
 */
class AmazonAutoLinks_PostMetaBox_DebugInfo extends AmazonAutoLinks_PostMetaBox_Base {
    
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
        'url',  // 3.2.0+
    );    

    /**
     * Checks whether the meta box should be registered or not in the loading page.
     */
    public function _isInThePage() {

        if ( ! parent::_isInThePage() ) {
            return false;
        }
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return $_oOption->isDebug();
        
    }
    
    public function content( $sOutput ) {        
        return $sOutput 
            . "<h4>" . __( 'Unit Options', 'amazon-auto-links' ) . "</h4>"
            . $this->oDebug->get(
                AmazonAutoLinks_WPUtility::getPostMeta( $GLOBALS['post']->ID )
            );
    }
    
}