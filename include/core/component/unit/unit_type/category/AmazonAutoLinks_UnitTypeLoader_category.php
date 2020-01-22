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
 * Loads the units component.
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitTypeLoader_category extends AmazonAutoLinks_UnitTypeLoader_Base {
        
    /**
     * Stores the unit type slug.
     * @remark      Each extended class should assign own unique unit type slug here.
     * @since       3.3.0
     */
    public $sUnitTypeSlug = 'category';
    
    /**
     * Stores class names of form fields.
     */
    public $aFieldClasses = array(
        'AmazonAutoLinks_FormFields_CategoryUnit_BasicInformation',
    );    
    
    /**
     * Stores protected meta key names.
     */    
    public $aProtectedMetaKeys = array(
        'categories_exclude',
        'categories',
    );

    /**
     * Determines whether the unit type requires the PA API access.
     * @var bool
     * @since   3.9.0
     */
    public $bRequirePAAPI = false;


    /**
     * @param $sScriptPath
     * @since   3.7.6
     */
    protected function _construct( $sScriptPath ) {
          // Category unit specific event callbacks
        new AmazonAutoLinks_Unit_Category_Event_RenewCacheAction;

    }

    /**
     * @param $bRequired
     * @since   3.9.0
     * @callback    filter      aal_filter_unit_type_is_api_access_required_{unit type slug}
     * @return bool
     */
    public function replyToDetermineAPIRequirement( $bRequired ) {
        return false;
    }


    /**
     * Adds post meta boxes.
     * 
     * @since       3.3.0
     * @return      void
     */
    protected function _loadAdminComponents( $sScriptPath ) {

        new AmazonAutoLinks_CategoryUnitAdminPage(
            array(
                'type'      => 'transient',
                'key'       => $GLOBALS[ 'aal_transient_id' ],
                'duration'  => 60*60*24*2,
            ),
            $sScriptPath 
        );        
    
        new AmazonAutoLinks_UnitPostMetaBox_Main_category(
            null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Main', 'amazon-auto-links' ), // meta box title
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'normal', // context (what kind of metabox this is)
            'high' // priority                        
        );        
        
        new AmazonAutoLinks_UnitPostMetaBox_Submit_category(
            null, // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Added Categories', 'amazon-auto-links' ), // title
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'side', // context - e.g. 'normal', 'advanced', or 'side'
            'high' // priority - e.g. 'high', 'core', 'default' or 'low'
        );              
        
    }

    /**
     * Determines the unit type from given output arguments.
     * @param       string      $sUnitTypeSlug
     * @param       array       $aArguments
     * @return      string
     * @since       3.5.0
     */
    protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {
        return isset( $aArguments[ 'categories' ] )
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return      string
     * @since       3.5.0
     */
    protected function _getLabel() {
        return __( 'Category', 'amazon-auto-links' );
    }

    /**
     * Return the unit output.
     *
     * @since       3.8.1
     * @return      string
     */
    public function replyToGetUnitOutput( $sOutput, $aArguments ) {
        $_oUnit      = new AmazonAutoLinks_UnitOutput_category3( $aArguments );
        return $sOutput . trim( $_oUnit->get() );
    }

}