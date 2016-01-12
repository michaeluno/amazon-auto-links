<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Defines the meta box added to the unit definition page.
 */
abstract class AmazonAutoLinks_UnitPostMetaBox_Base extends AmazonAutoLinks_PostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     * 
     * Each unit type should add the slug with the `aal_filter_unit_types_common_unit_meta_boxes'`
     */
    protected $aUnitTypes = array();
    
    /**
     * Checks whether the meta box should be registered or not in the loading page.
     */
    public function _isInThePage() {

        if ( ! parent::_isInThePage() ) {
            return false;
        }
                
        $this->aUnitTypes = empty( $this->aUnitTypes )
            ? apply_filters( 'aal_filter_registered_unit_types', $this->aUnitTypes )
            : $this->aUnitTypes;
        
        // Register custom filed type.
        // new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
        
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