<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Defines the meta box added to the category unit definition page.
 */
abstract class AmazonAutoLinks_PostMetaBox_Base extends AmazonAutoLinks_AdminPageFramework_MetaBox {
    
    /**
     * Stores the unit type slug(s). 
     * 
     * The default is 'category'.
     * 
     * Each extended class should override this value.
     */
    protected $aUnitTypes = array( 'category' );
    
    /**
     * Checks whether the meta box should be registered or not in the loading page.
     */
    public function _isInThePage() {

        if ( ! parent::_isInThePage() ) {
            return false;
        }
        
        // Register custom filed type.
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
        
        // At this point, it is TRUE evaluated by the framework.
        // but we need to evaluate it for the plugin.
        
        // Get the post ID.
        $_iPostID = AmazonAutoLinks_WPUtility::getCurrentPostID();
        
        // Maybe post-new.php
        if ( ! $_iPostID ) {
            return true;
        }
        
        $_sUnitType = get_post_meta(
            $_iPostID,      
            'unit_type', // meta key
            true
        );
        return in_array(
            $_sUnitType,
            $this->aUnitTypes
        );        
        
    }
 
}