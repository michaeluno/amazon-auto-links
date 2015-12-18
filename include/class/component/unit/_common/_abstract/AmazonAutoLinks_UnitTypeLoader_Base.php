<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Loads a particular unit type.
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitTypeLoader_Base {
    
    /**
     * Stores the unit type slug.
     * @remark      Each extended class should assign own unique unit type slug here.
     * @since       3.3.0
     */
    public $sUnitTypeSlug = '';
    
    /**
     * Stores class names of form fields.
     */
    public $aFieldClasses = array();
    
    /**
     * Stores protected meta key names.
     */
    public $aProtectedMetaKeys = array();
    
    /**
     * Loads necessary components.
     */
    public function __construct( $sScriptPath ) {
        
        add_filter( 'aal_filter_registered_unit_types', array( $this, 'replyToRegisterUnitTypeSlug' ) );
        
        if ( is_admin() ) {
        
            // Post meta boxes
            $this->loadAdminComponents( $sScriptPath );
            
            add_filter( 'aal_filter_custom_meta_keys', array( $this, 'replyToGetProtectedMetaKeys' ) );
            
        }
        
        $this->construct( $sScriptPath );
        
        
    }    
    
        /**
         * @since       3.3.0
         * @return      array
         * @callback    filter      aal_filter_registered_unit_types
         */
        public function replyToRegisterUnitTypeSlug( $aUnitTypeSlugs ) {
            if ( $this->sUnitTypeSlug ) {
                $aUnitTypeSlugs[] = $this->sUnitTypeSlug;
            }
            return $aUnitTypeSlugs;
        }
    
        /**
         * @return      array
         * @since       3.3.0
         * @callback    filter      aal_filter_custom_meta_keys
         * @remark      For field with a section, set keys in the $aProtectedMetaKeys property.
         */
        public function replyToGetProtectedMetaKeys( $aMetaKeys ) {                
            foreach( $this->aFieldClasses as $_sClassName ) {
                $_oFields = new $_sClassName;
                $aMetaKeys = array_merge( $aMetaKeys, $_oFields->getFieldIDs() );
            }            
            return array_merge( $aMetaKeys, $this->aProtectedMetaKeys );
        }    
    
    /**
     * Adds post meta boxes.
     * 
     * @since       3.3.0
     * @return      void
     */
    public function loadAdminComponents( $sScriptPath ) {}    
    
    /**
     * User constructor.
     * @since       3.3.0
     * @return      void
     */
    public function construct( $sScriptPath ) {}
    
}