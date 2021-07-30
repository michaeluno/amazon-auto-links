<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the unit component.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitLoader extends AmazonAutoLinks_PluginUtility {

    /**
     * Stored the component directory path.
     *
     * Referred to enqueue resources.
     *
     * @var string
     * @since   4.3.0
     */
    static public $sDirPath = '';

    /**
     * AmazonAutoLinks_UnitLoader constructor.
     *
     * @param string $sScriptPath the plugin main file path.
     */
    public function __construct( $sScriptPath ) {

        self::$sDirPath = dirname( __FILE__ );

        // Post types
        new AmazonAutoLinks_PostType_Unit(
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],  // slug
            null,          // post type argument. This is defined in the class.
            $sScriptPath   // script path
        );
        new AmazonAutoLinks_PostType_UnitPreview;

        // Unit Types
        new AmazonAutoLinks_UnitTypesLoader( $sScriptPath );

        // [4.4.0] PA-API Request Counter
        new AmazonAutoLinks_Unit_PAAPIRequestCounter_Loader;

        // Admin
        if ( is_admin() ) {
            $this->___loadAdminComponents();
        }

        // Events
        add_action( 'aal_action_events', array( $this, 'replyToLoadEvents' ) );

    }

    
        /**
         * @callback        action      aal_action_events
         */
        public function replyToLoadEvents() {

            new AmazonAutoLinks_Event___Action_UnitPrefetchByID;
            new AmazonAutoLinks_Event___Action_APIRequestSearchProducts;    // [3.7.7]
            new AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview;   // [3.9.0]
            new AmazonAutoLinks_Event___Action_APIRequestCacheRenewal;      // [3.5.0]
            new AmazonAutoLinks_Event___Action_HTTPRequestRating;           // [4.3.4]

            new AmazonAutoLinks_Unit_Log_PAAPIErrors; // [3.9.0]

            new AmazonAutoLinks_Unit_EventAjax_UnitLoading; // [3.6.0]
            new AmazonAutoLinks_Unit_EventAjax_UnitStatusUpdater;
            new AmazonAutoLinks_Unit_EventAjax_NowRetrievingUpdater; // [4.3.0]

            new AmazonAutoLinks_Unit_EventFilter_UnitOutputAjaxPlaceholder; // [4.3.0]

            new AmazonAutoLinks_Event_Filter_ProductLinks;    // [4.3.0]

            new AmazonAutoLinks_Unit_Event_Action_CheckTasks; // [4.3.0]
            new AmazonAutoLinks_Unit_Event_Filter_TaskBundler_ProductsInfo; // [4.3.0]
            new AmazonAutoLinks_Unit_Event_Filter_TaskBundler_ProductRatings; // [4.6.11]

            new AmazonAutoLinks_Unit_Event_Filter_PAAPIErrors; // [4.3.5]
            new AmazonAutoLinks_Unit_Event_Filter_UnitOutput_Warning; // [4.4.0]

            new AmazonAutoLinks_Unit_Event_Filter_NowRetrievingUpdaterElement; // [4.6.1]

            new AmazonAutoLinks_Unit_Event_Action_Feed_UnitOutputHooks; // [4.6.4]

            new AmazonAutoLinks_Unit_Event_Action_UpdateProductsWithAdWidgetAPI; // [4.6.9]

            new AmazonAutoLinks_Unit_Event_Filter_ShowErrorMode; // [4.6.11]

            $this->___loadDebugEvents(); // [4.3.5]

        }
            /**
             * @since 4.3.5
             */
            private function ___loadDebugEvents() {

                $_oOption = AmazonAutoLinks_Option::getInstance();
                if ( ! $_oOption->isDebug() ) {
                    return;
                }
                new AmazonAutoLinks_Unit_Event_Filter_Debug_ProductOutput;
                new AmazonAutoLinks_Unit_Event_Filter_Debug_UnitOutput;

            }
        
    
    /**
     * Loads admin components.
     */
    private function ___loadAdminComponents() {
                
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