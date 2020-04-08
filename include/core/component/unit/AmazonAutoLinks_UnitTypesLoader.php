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
class AmazonAutoLinks_UnitTypesLoader extends AmazonAutoLinks_UnitTypeLoader_Base {
    
    /**
     * Stores class names of common form fields among all the unit types.
     */
    public $aFieldClasses = array(
        // 'AmazonAutoLinks_FormFields_Unit_Template', // kept for backward-compatibility -> 4.0.5 Removed completely to avoid the error saying the class not found.
        'AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport', // 4.0.0+
        'AmazonAutoLinks_FormFields_Unit_CommonAdvanced',
        'AmazonAutoLinks_FormFields_Button_Selector',
        'AmazonAutoLinks_FormFields_Unit_Common',    
        'AmazonAutoLinks_FormFields_Unit_Cache',
    );      
    
    /**
     * Stores protected meta key names.
     */    
    public $aProtectedMetaKeys = array(
        'product_filters',      // section id
    );
    
    
    /**
     * 
     */
    protected function _construct( $sScriptPath ) {

        // Overall Unit Events
        new AmazonAutoLinks_Unit_AjaxEvent_UnitStatusUpdater;

        // Post types
        new AmazonAutoLinks_PostType_Unit( 
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],  // slug
            null,          // post type argument. This is defined in the class.
            $sScriptPath   // script path
        );            
        new AmazonAutoLinks_PostType_UnitPreview;            

        // Unit types
        new AmazonAutoLinks_UnitTypeLoader_category( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_tag( $sScriptPath );  
        new AmazonAutoLinks_UnitTypeLoader_search( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_item_lookup( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_similarity_lookup( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_url( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_contextual( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_embed( $sScriptPath ); // 4.0.0
        new AmazonAutoLinks_UnitTypeLoader_feed( $sScriptPath );  // 4.0.0

        // Unit specific events
        add_action( 'aal_action_events', array( $this, 'replyToLoadEvents' ) );
        
    }
    
        /**
         * @callback        action      aal_action_events
         */
        public function replyToLoadEvents() {

            new AmazonAutoLinks_Event___Action_UnitPrefetchByID;
            new AmazonAutoLinks_Event___Action_ProductAdvertisingAPICacheRenewal;
            new AmazonAutoLinks_Event___Action_APIRequestSearchProduct;
            new AmazonAutoLinks_Event___Action_APIRequestSearchProducts;    // 3.7.7+
            // new AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview;   // @deprecated 3.9.0    Should be commented out once it is confirmed its unnecessity
            new AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview2;  // 3.9.0
            new AmazonAutoLinks_Event___Action_APIRequestSimilarProducts;
            new AmazonAutoLinks_Event___Action_APIRequestCacheRenewal;  // 3.5.0+

            new AmazonAutoLinks_Unit_Log_PAAPIErrors; // 3.9.0

        }
        
    
    /**
     * Loads necessary components.
     */
    protected function _loadAdminComponents( $sScriptPath ) {
                
        new AmazonAutoLinks_UnitPostMetaBox_ViewLink(
            null,
            __( 'View', 'amazon-auto-links' ), // meta box title
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'side', // context - e.g. 'normal', 'advanced', or 'side'
            'high'  // priority - e.g. 'high', 'core', 'default' or 'low'
        );                    

        new AmazonAutoLinks_UnitPostMetaBox_Common(
            null,       // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Common', 'amazon-auto-links' ), // title
            array(      // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'normal',   // context - e.g. 'normal', 'advanced', or 'side'
            'core'      // priority - e.g. 'high', 'core', 'default' or 'low'
        );                   

        new AmazonAutoLinks_UnitPostMetaBox_Template(
            null,       // meta box ID - null to auto-generate
            __( 'Template', 'amazon-auto-links' ), // title
            array(      // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'advanced', // context - e.g. 'normal', 'advanced', or 'side'
            'low'       // priority - e.g. 'high', 'core', 'default' or 'low'
        );  

        new AmazonAutoLinks_UnitPostMetaBox_ProductFilter(
            null,       // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Unit Product Filters', 'amazon-auto-links' ), // title
            array(      // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'advanced', // context - e.g. 'normal', 'advanced', or 'side'
            'low'       // priority - e.g. 'high', 'core', 'default' or 'low'
        );

        new AmazonAutoLinks_UnitPostMetaBox_ProductFilterAdvanced(
            null,       // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Advanced Unit Product Filters', 'amazon-auto-links' ), // title
            array(      // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            ),
            'advanced', // context - e.g. 'normal', 'advanced', or 'side'
            'low'       // priority - e.g. 'high', 'core', 'default' or 'low'
        );

        // Common meta boxes

        new AmazonAutoLinks_UnitPostMetaBox_Locale(
            'amazon_auto_links_locale',       // meta box ID is given so that the Feed unit type can remove this meta box using this ID
            __( 'Locale', 'amazon-auto-links' ), // title
            array(      // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            ),
            'side',   // context - e.g. 'normal', 'advanced', or 'side'
            'default'      // priority - e.g. 'high', 'core', 'default' or 'low'
        );

        new AmazonAutoLinks_UnitPostMetaBox_Cache(
            null,       // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Cache', 'amazon-auto-links' ), // title
            array(      // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'side',     // context - e.g. 'normal', 'advanced', or 'side'
            'core'   // priority - e.g. 'high', 'core', 'default' or 'low'
        );                   

        new AmazonAutoLinks_UnitPostMetaBox_CommonAdvanced(
            null,       // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Common Advanced', 'amazon-auto-links' ), // title
            array(      // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'side',     // context - e.g. 'normal', 'advanced', or 'side'
            'low'       // priority - e.g. 'high', 'core', 'default' or 'low'
        );

        new AmazonAutoLinks_UnitPostMetaBox_DebugInfo(
            null,       // meta box ID - null to auto-generate
            __( 'Debug Information', 'amazon-auto-links' ),
            array(      // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'advanced', // context - e.g. 'normal', 'advanced', or 'side'
            'low'       // priority - e.g. 'high', 'core', 'default' or 'low'
        );                       

    }
    
}