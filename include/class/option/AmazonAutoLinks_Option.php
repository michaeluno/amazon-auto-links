<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Handles plugin options.
 * 
 * @since       3
 * @filter      apply       aal_filter_option_class_name
 */
class AmazonAutoLinks_Option extends AmazonAutoLinks_Option_Base {

    /**
     * Stores instances by option key.
     * 
     * @since       3
     */
    static public $aInstances = array(
        // key => object
    );
        
    /**
     * Stores the default values.
     */
    // public $aDefault = array();
    public $aDefault = array(
    
        'capabilities' => array(
            'setting_page_capability' => 'manage_options',
        ),        
        'debug' => array(
            'debug_mode' => 0,
        ),
        'form_options' => array(
            'allowed_html_tags' => 'style, noscript',
        ),
        'product_filters'       => array(
            'black_list'     => array(
                'asin'        => '',
                'title'       => '',
                'description' => '',
            ),
            'white_list'        => array(
                'asin'        => '',
                'title'       => '',
                'description' => '',
            ),
            'case_sensitive' => 0,
            'no_duplicate'   => 0,    // in 2.0.5.1 changed to 0 from 1.
        ),
        'support' => array(
            'rate'   => 0,            // asked for the first load of the plugin admin page
            'ads'    => false,        // asked for the first load of the plugin admin page
            'review' => 0,            // not implemented yet
            'agreed' => false,        // hidden
        ),
        'cache'    =>    array(
            'chaching_mode' => 'normal',
        ),
        'query' => array(
            'cloak' => 'productlink'
        ),
        'authentication_keys' => array(
            'access_key'                => '',  // public key
            'access_key_secret'         => '',  // private key
            'api_authentication_status' => false,
        ),            
        // Hidden options
        'template' => array(
            'max_column' => 1,
        ),
        'import_v1_options' => array(
            'dismiss' => false,
        ),
        // 2.2.0+
        'unit_preview' => array(
            'preview_post_type_slug' => '',
            'visible_to_guests'      => true,
            'searchable'             => false,
        ),
        
        // 3+
        'reset_settings'    => array(
            'reset_on_uninstall'    => false,
        ),
        
        // 3+ Changed the name from `arrTemplates`.
        // stores information of active templates.   
// @todo    format v2 options to v3 
        // 'templates' => array(),    
    
    );
         
    /**
     * Returns the instance of the class.
     * 
     * This is to ensure only one instance exists.
     * 
     * @since      3
     */
    static public function getInstance( $sOptionKey='' ) {
        
        $sOptionKey = $sOptionKey 
            ? $sOptionKey
            : AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ];
        
        if ( isset( self::$aInstances[ $sOptionKey ] ) ) {
            return self::$aInstances[ $sOptionKey ];
        }
        $_sClassName = apply_filters( 
            AmazonAutoLinks_Registry::HOOK_SLUG . '_filter_option_class_name',
            __CLASS__ 
        );
        self::$aInstances[ $sOptionKey ] = new $_sClassName( $sOptionKey );
        return self::$aInstances[ $sOptionKey ];
        
    }         
            
    /* Plugin specific methods */    
    public function isUnitLimitReached( $iNumberOfUnits=null ) {
        
        if ( ! isset( $iNumberOfUnits ) ) {
            $_oNumberOfUnits = AmazonAutoLinks_WPUtility::countPosts( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );
            $iNumberOfUnits  = $_oNumberOfUnits->publish 
                + $_oNumberOfUnits->private 
                + $_oNumberOfUnits->trash;
        } 
        return ( boolean ) ( $iNumberOfUnits >= 3 );
        
    }    
    public function getRemainedAllowedUnits( $iNumberOfUnits=null ) {
        
        if ( ! isset( $iNumberOfUnits ) ) {
            $_oNumberOfUnits   = AmazonAutoLinks_WPUtility::countPosts( 
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            );
            $iNumberOfUnits   = $_oNumberOfUnits->publish 
                + $_oNumberOfUnits->private 
                + $_oNumberOfUnits->trash;
        } 
        
        return 3 - $iNumberOfUnits;
        
    }
    public function isReachedCategoryLimit( $iNumberOfCategories ) {
        return ( boolean ) ( $iNumberOfCategories >= 3 );
    }    
    public function getMaximumProductLinkCount() {
        return 10;
    }    
    public function getMaxSupportedColumnNumber(){
        return apply_filters( 
            'aal_filter_max_column_number', 
            $this->aOptions[ 'template' ][ 'max_column' ]
        );                    
    }
    
    public function isAdvancedAllowed() {
        return false;
    }
    
    public function canExport() {
        return false;
    }
    public function isSupported() {
        return false;
    }
    
    /**
     * Checks whether the API keys are set and it has been verified.
     * @since       3
     * @return      boolean
     */
    public function isAPIConnected() {
        return ( boolean ) $this->get( 
            'authentication_keys', 
            'api_authentication_status' 
        );
    }
    
    /**
     * Checks whether the plugin debug mode is on.
     * @return      boolean
     */
    public function isDebug() {
        if ( ! self::isDebugModeEnabled() ) {
            return false;
        }                
        return ( boolean ) $this->get( 
            'debug', 
            'debug_mode' 
        );
    }
    
    /**
     * 
     * @since       2.2.0
     * @return      boolean
     */
    public function isCustomPreviewPostTypeSet()  {
        
        $_sPreviewPostTypeSlug = $this->get( 'unit_preview', 'preview_post_type_slug' );
        if ( ! $_sPreviewPostTypeSlug ) {
            return false;
        }
        return AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] !== $_sPreviewPostTypeSlug;
        
    }    
    
    /**
     * 
     * @since       2.2.0
     * @return      boolean
     */
    public function isPreviewVisible() {
        
        if ( $this->get( 'unit_preview', 'visible_to_guests' ) ) {
            return true;
        }
        return ( boolean ) is_user_logged_in();
        
    }
    
    
}