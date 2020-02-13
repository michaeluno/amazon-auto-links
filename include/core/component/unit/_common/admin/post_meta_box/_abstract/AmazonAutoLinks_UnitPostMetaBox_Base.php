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
 * Defines the meta box added to the unit definition page.
 */
abstract class AmazonAutoLinks_UnitPostMetaBox_Base extends AmazonAutoLinks_PostMetaBox_Base {

    /**
     * Stores the current post ID.
     * @var int
     * @since   3.7.0
     */
    protected $_iPostID = 0;

    /**
     * Stores the unit type slug(s). 
     * 
     * Each unit type should add the slug with the `aal_filter_unit_types_common_unit_meta_boxes'`
     */
    protected $aUnitTypes = array();
    
    /**
     * Checks whether the meta box should be registered or not in the loading page.
     */
    protected function _isInThePage() {

        if ( ! parent::_isInThePage() ) {
            return false;
        }
                
        $this->aUnitTypes = empty( $this->aUnitTypes )
            ? apply_filters( 'aal_filter_registered_unit_types', $this->aUnitTypes ) // to allow all unit types so that common unit meta boxes can leave the property empty
            : $this->aUnitTypes;
                
        // At this point, it is TRUE evaluated by the framework.
        // but we need to evaluate it for the plugin.
        
        // Get the post ID.
        $this->_iPostID = AmazonAutoLinks_WPUtility::getCurrentPostID();
        
        // Maybe post-new.php
        if ( ! $this->_iPostID ) {
            return true;
        }
        
        $_sUnitType = get_post_meta(
            $this->_iPostID,
            'unit_type', // meta key
            true
        );
        return in_array( $_sUnitType, $this->aUnitTypes );
        
    }

    /**
     * Adds form fields.
     * @since       3.1.0
     */
    protected function _addFieldsByClasses( $aClassNames ) {
        foreach( $aClassNames as $_sClassName ) {
            $_oFields = new $_sClassName;
            foreach( $_oFields->get() as $_aField ) {
                $this->addSettingFields( $_aField );
            }
        }
    }

}