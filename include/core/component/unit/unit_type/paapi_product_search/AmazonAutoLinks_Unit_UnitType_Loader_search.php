<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Loads the units component.
 *
 * @since 3.3.0
 * @since 5.0.0 Renamed from `AmazonAutoLinks_UnitTypeLoader_search`.
 * @remak This class does not have the `final` notation as this is extended by some other classes.
*/
class AmazonAutoLinks_Unit_UnitType_Loader_search extends AmazonAutoLinks_Unit_UnitType_Loader_Base {

    /**
     * Stores each unit type component directory path.
     *
     * Component specific assets are placed inside the component directory and to load them the component path needs to be known.
     * @remark Without this declaration, the value refers to the parent one.
     * @var    string
     * @since  4.2.0
     */
    static public $sDirPath = '';

    /**
     * Stores the unit type slug.
     * @remark Each extended class should assign own unique unit type slug here.
     * @since  3.3.0
     */
    public $sUnitTypeSlug = 'search';
    
    /**
     * Stores class names of form fields.
     */
    public $aFieldClasses = array(
        'AmazonAutoLinks_FormFields_SearchUnit_ProductSearch',
        'AmazonAutoLinks_FormFields_SearchUnit_ProductSearchAdvanced',
    );

    /**
     * @var   string
     * @since 5.0.0
     */
    protected $_sPAAPIOperation = 'SearchItems';

    /**
     * @param string $sScriptPath
     * @since 5.0.0
     */
    protected function _construct( $sScriptPath ) {

        add_filter( 'aal_filter_unit_paapi_unit_types', array( $this, 'replyToGetPAAPIUnitTypes' ) );
        add_filter( 'aal_filter_admin_unit_paapi_unit_types_unit_creation_page_url', array( $this, 'replyToGetUnitCreationPageURL' ), 10, 2 );

        // Extended classes have a different unit type slug
        if ( 'search' === $this->sUnitTypeSlug ) {
            new AmazonAutoLinks_Unit_PAAPISearch_Event_Filter_ProductsFetcher;
            new AmazonAutoLinks_Unit_PAAPISearch_Event_Filter_ProductsSorter;
            new AmazonAutoLinks_Unit_PAAPISearch_Event_Filter_ProductsFormatter;
        }

    }
        /**
         * @since  5.0.0
         * @param  array $aUnitTypes
         * @return string[]
         */
        public function replyToGetPAAPIUnitTypes( $aUnitTypes ) {
            return $aUnitTypes + array(
                $this->_sPAAPIOperation => "<strong>" . $this->_getShortName() . "</strong>" . ' - ' . $this->_getDescription(),
            );
        }
        /**
         * @param    string $sURL
         * @param    array  $aInputs
         * @since    5.0.0
         * @return   string
         * @callback add_filter() aal_filter_admin_unit_paapi_unit_types_unit_creation_page_url
         */
        public function replyToGetUnitCreationPageURL( $sURL, $aInputs ) {
            if ( ! in_array( $aInputs[ 'Operation' ], array( 'ItemSearch', $this->_sPAAPIOperation ), true ) ) {
                return $sURL;
            }
            return add_query_arg(
                array(
                    'tab'          => 'search_products',
                    'transient_id' => $aInputs[ 'transient_id' ],
               ) + $this->getHTTPQueryGET(),
               $aInputs[ 'bounce_url' ]
            );
        }

    /**
     * Adds post meta boxes.
     * 
     * @since       3.3.0
     * @return      void
     */
    protected function _loadAdminComponents( $sScriptPath ) {
        
        // Admin pages
        new AmazonAutoLinks_SearchUnitAdminPage(
            array(
                'type'      => 'transient',
                'key'       => $GLOBALS[ 'aal_transient_id' ],
                'duration'  => 60*60*24*2,
            ),
            $sScriptPath
        );
        
        // Post meta boxes
        new AmazonAutoLinks_UnitPostMetaBox_Main_search(
            null,   // meta box ID - null for auto-generate
            __( 'Product Search Main', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ),                 
            'normal', // context - e.g. 'normal', 'advanced', or 'side'
            'high'  // priority - e.g. 'high', 'core', 'default' or 'low'
        );            
        new AmazonAutoLinks_UnitPostMetaBox_Advanced_search(
            null,   // meta box ID - null for auto-generate
            __( 'Product Search Advanced', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ),                 
            'normal', // context - e.g. 'normal', 'advanced', or 'side'
            'low' // priority - e.g. 'high', 'core', 'default' or 'low'
        );        
        
    }     

    /**
     * @remark Shortcode argument keys are all lower-case.
     * @since  3.4.6
     * @since  3.5.0  Moved from `AmazonAutoLinks_Output`.
     * @since  5.0.0  Moved from `AmazonAutoLinks_Unit_UnitType_Loader_Base`.
     * @return string
     * @param  array  $aArguments
     */
    protected function _getOperationArgument( $aArguments ) {
        $_sOperation = $this->getElement( $aArguments, 'Operation' );
        return $_sOperation
            ? $_sOperation
            : $this->getElement( $aArguments, 'operation', '' );
    }

    /**
     * Determines the unit type from given output arguments.
     * @param  string $sUnitTypeSlug
     * @param  array  $aArguments
     * @return string
     * @since  3.5.0
     */
    protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {

        // Check the shortcode argument
        if ( isset( $aArguments[ 'search' ] ) ) {
            return $this->sUnitTypeSlug;
        }

        // Check the PA-API arguments
        return in_array( $this->_getOperationArgument( $aArguments ), array( 'ItemSearch', $this->_sPAAPIOperation ), true ) // ItemSearch is for backward-compatibility
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;

    }

    /**
     * @return string
     * @since  5.0.0
     */
    protected function _getShortName() {
        return __( 'Product Search', 'amazon-auto-links' );
    }

    /**
     * @return string
     * @since  3.5.0
     */
    protected function _getLabel() {
        return __( 'PA-API Product Search', 'amazon-auto-links' );
    }

    /**
     * @return string
     * @since  5.0.0
     */
    protected function _getDescription() {
        return __( 'Returns items that satisfy the search criteria in the title and descriptions.', 'amazon-auto-links' );
    }

}