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
 * Loads a particular unit type.
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitTypeLoader_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores each unit type component directory path.
     *
     * Component specific assets are placed inside the component directory and to load them the component path needs to be known.
     * @var string
     * @since   4.0.0
     */
    static public $sDirPath = '';

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
     * Determines whether the unit type requires the PA API access.
     * @var bool
     * @since   3.9.0
     * @todo    Research: callbacks for the filter, `aal_filter_unit_type_is_api_access_required_{unit type slug}`, seems to access this property but the filter does not seem to be applied anywhere.
     * Although the `category` unit type set this value to `false`, it also uses PA-API when API keys are set and encounters null value elements.
     */
    public $bRequirePAAPI = true;
    
    /**
     * Loads necessary components.
     */
    public function __construct( $sScriptPath ) {
        
        add_filter( 'aal_filter_registered_unit_types', array( $this, 'replyToRegisterUnitTypeSlug' ) );
        
        if ( is_admin() ) {
        
            // Post meta boxes
            $this->_loadAdminComponents( $sScriptPath );
            
            add_filter( 'aal_filter_custom_meta_keys', array( $this, 'replyToGetProtectedMetaKeys' ) );
            
        }
                
        add_filter( 'aal_filter_default_unit_options_' . $this->sUnitTypeSlug, array( $this, 'replyToGetDefaultUnitOptions' ) );

        // 3.5.0+
        add_filter( 'aal_filter_unit_output_' . $this->sUnitTypeSlug, array( $this, 'replyToGetUnitOutput' ), 10, 2 );

        // 3.5.0+
        add_filter( 'aal_filter_detected_unit_type_by_arguments', array( $this, 'replyToDetermineUnitType' ), 10, 2 );

        // 3.5.0+
        add_filter( 'aal_filter_registered_unit_type_labels', array( $this, 'replyToAddLabel' ) );

        // 3.9.0+
        add_filter( 'aal_filter_unit_type_is_api_access_required_' . $this->sUnitTypeSlug, array( $this, 'replyToDetermineAPIRequirement' ) );

        $this->_construct( $sScriptPath );
        
    }

        /**
         * @param $bRequired
         * @since   3.9.0
         * @callback    filter      aal_filter_unit_type_is_api_access_required_{unit type slug}
         * @return bool
         */
        public function replyToDetermineAPIRequirement( $bRequired ) {
            return $this->bRequirePAAPI;
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
         * @callback    add_filter      aal_filter_registered_unit_types
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
         * @callback    add_filter      aal_filter_custom_meta_keys
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
     * Loads admin components
     * 
     * @since       3.3.0
     * @since       3.5.0       Changed th visibility from public and renamed from `construct()`.
     * @return      void
     */
    protected function _loadAdminComponents( $sScriptPath ) {}
    
    /**
     * User constructor.
     * @since       3.3.0
     * @since       3.5.0       Changed th visibility from public and renamed from `construct()`.
     * @return      void
     */
    protected function _construct( $sScriptPath ) {}

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
        $_sClassName = 'AmazonAutoLinks_UnitOutput_' . strtolower( $this->sUnitTypeSlug );
        $_oUnit      = new $_sClassName( $aArguments );
        return $sOutput . trim( $_oUnit->get() );
    }

    /**
     * @callback    add_filter  aal_filter_detected_unit_type_by_arguments
     * @param       string      $sUnitTypeSlug
     * @param       array       $aArguments     Aa argument array passed to the output function.
     * @return      string
     * @since       3.5.0
     */
    public function replyToDetermineUnitType( $sUnitTypeSlug, $aArguments ) {
        return $this->_getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments );
    }
        /**
         * @param       string      $sUnitTypeSlug
         * @param       array       $aArguments
         * @return      string
         * @since       3.5.0
         */
        protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {
            return $sUnitTypeSlug;
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

    /**
     * @callback    add_filter      aal_filter_registered_unit_type_labels
     * @param       array           $aLables
     * @return      array
     * @since       3.5.0
     */
    public function replyToAddLabel( $aLables ) {
        return $aLables + array(
            $this->sUnitTypeSlug => $this->_getLabel(),
        );
    }
        /**
         * @return      string
         * @sicne       3.5.0
         */
        protected function _getLabel() {
            return __( 'Unknown', 'amazon-auto-links' );
        }

}