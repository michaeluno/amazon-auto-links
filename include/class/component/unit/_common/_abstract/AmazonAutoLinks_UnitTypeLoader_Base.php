<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Loads a particular unit type.
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitTypeLoader_Base extends AmazonAutoLinks_PluginUtility {
    
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
                
        add_filter( 'aal_filter_default_unit_options_' . $this->sUnitTypeSlug, array( $this, 'replyToGetDefaultUnitOptions' ) );

        // 3.5.0+
        add_filter( 'aal_filter_unit_output_' . $this->sUnitTypeSlug, array( $this, 'replyToGetUnitOutput' ), 10, 2 );

        // 3.5.0+
        add_filter( 'aal_filter_detected_unit_type_by_arguments', array( $this, 'replyToDetermineUnitType' ), 10, 2 );
        
        $this->construct( $sScriptPath );
        
    }    
    
        /**
         * @return      array
         * @sinec       3.3.0
         */
        public function replyToGetDefaultUnitOptions( $aUnitOptions ) {
            
            if ( empty( $this->sUnitTypeSlug ) ) {
                return $aUnitOptions;
            }
        
            $_sClassName   = "AmazonAutoLinks_UnitOption_{$this->sUnitTypeSlug}";
            $_oUnitOptions = new $_sClassName(
                null,   // unit id
                array() // arguments
            );        
            return $_oUnitOptions->get();
            
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

    /**
     * Return the unit output.
     *
     * @remark      Override this method in each unit type loader class.
     * @callback    add_filter      aal_filter_unit_output_{unit type slug}
     * @param       string $sOutput
     * @param       array $aArguments
     * @since       3.5.0
     * @return      string
     */
    public function replyToGetUnitOutput( $sOutput, $aArguments ) {
        $_sClassName = "AmazonAutoLinks_UnitOutput_" . strtolower( $this->sUnitTypeSlug );
        $_oUnit      = new $_sClassName( $aArguments );
        return $sOutput . trim( $_oUnit->get() );
    }

    /**
     * @callback    add_filter  aal_filter_detected_unit_type_by_arguments
     * @param       string      $sUnitType
     * @param       array       $aArguments
     * @return      string
     */
    public function replyToDetermineUnitType( $sUnitType, $aArguments ) {
        return $sUnitType;
    }
        /**
         * @remark      Shortcode argument keys are all lower-case.
         * @since       3.4.6
         * @since       3.5.0       Moved from `AmazonAutoLinks_Output`.
         * @return      string
         */
        protected function _getOperationArgument( $aArguments ) {
            $_sOperation = $this->getElement( $aArguments, 'Operation' );
            return $_sOperation
                ? $_sOperation
                : $this->getElement( $aArguments, 'operation', '' );
        }

}