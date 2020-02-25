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
 * A base class for unit classes, search, tag, and category.
 * 
 * Provides shared methods and properties for those classes.
 * 
 * @filter          aal_filter_template_path
 *  parameter 1:    (string) template path
 *  parameter 2:    (array) arguments(unit options) 
 * @filter          aal_filter_unit_output
 *  parameter 1:    (string) unit output
 *  parameter 2:    (array)    arguments(unit options)
 * 
 * @filter      add     aal_filter_unit_product_raw_title
 */
abstract class AmazonAutoLinks_UnitOutput_Base extends AmazonAutoLinks_UnitOutput_Utility {

    /**
     * Stores the unit type.
     * @remark      The constructor will create a unit option object based on this value.
     */
    public $sUnitType = '';
    
    /**
     * Stores a plugin option object.
     * @var AmazonAutoLinks_Option
     */ 
    public $oOption;
    
    /**
     * Stores a product database table object.
     */
    public $oProductTable;
    
    /**
     * Stores a product filter object.
     */
    public $oGlobalProductFilter;
    public $oUnitProductFilter;
    
    /**
     * Stores DOM parser object.
     */
    public $oDOM;  
    
    /**
     * Stores an encoder and decoder object.
     */
    public $oEncrypt;
    
    /**
     * The site character set.
     */
    public $sCharEncoding = '';
    
    /**
     * Indicates whether the unit needs to access custom database table.
     * @remark      Accessed from delegation classes publicly.
     * @since       3
     */
    public $bDBTableAccess = false;

    /**
     * Stores a unit option object.
     * @var AmazonAutoLinks_UnitOption_Base
     */
    public $oUnitOption;

    /**
     * @var     AmazonAutoLinks_UnitOutput__ImpressionCounter
     * @since   3.5.0
     */
    public $oImpressionCounter;

    /**
     * Stores an ID for the object instance.
     * Used for debugging and to determine the caller object and checks whether unnecessary calls are made for hook callbacks.
     * @var string
     */
    public $sCallID;

    /**
     * Lists the variables used in the Item Format unit option that require to access the custom database.
     * @since       3.5.0
     * @var array
     */
    protected $_aItemFormatDatabaseVariables = array(
        '%review%', '%rating%', '%image_set%', '%similar%', '%feature%', '%category%', '%rank%', '%prime%',
        '%_discount_rate%', '%_review_rate%', // 3.9.2  - used for advanced filters
        '%price%',     // 3.10.0 - as preferred currency is now supported, the `GetItem` operation is more up-to-date than `SearchItem` then sometimes it gives a different result so use it if available.
    );

    /**
     * Search unit types needs this property to determine and extract errors from API responses.
     * @var string
     * @since   3.10.0  This was actually added in 3.9.0 but not in the base class.
     */
    protected $_sResponseItemsParentKey = '';

    /**
     * Sets up properties.
     */
    public function __construct( $aoUnitOptions, $sUnitType='' ) {

        $this->sCallID              = uniqid();
        $this->sUnitType            = $sUnitType
            ? $sUnitType
            : $this->sUnitType;

        $_sUnitOptionClassName      = "AmazonAutoLinks_UnitOption_{$this->sUnitType}";
        $this->oUnitOption          = is_object( $aoUnitOptions )
            ? $aoUnitOptions
            : new $_sUnitOptionClassName(
                null,
                $aoUnitOptions
            );

        $this->oOption              = AmazonAutoLinks_Option::getInstance();
        $this->oProductTable        = new AmazonAutoLinks_DatabaseTable_aal_products;
        $this->sCharEncoding        = get_bloginfo( 'charset' );
        $this->bIsSSL               = is_ssl();        
        $this->oDOM                 = new AmazonAutoLinks_DOM;
        $this->oEncrypt             = new AmazonAutoLinks_Encrypt;
        $this->oImpressionCounter   = new AmazonAutoLinks_UnitOutput__ImpressionCounter( $this );
        $this->oGlobalProductFilter = new AmazonAutoLinks_ProductFilter(
            $this->getAsArray(
                $this->oOption->get( 'product_filters' )
            )
        );     
        $this->oUnitProductFilter   = new AmazonAutoLinks_ProductFilter(
            $this->getAsArray( 
                $this->oUnitOption->get( 'product_filters' ) 
            )
        );
        if ( $this->oUnitOption->get( 'is_preview' ) ) {
            $this->oGlobalProductFilter->bNoDuplicate = false;
            $this->oUnitProductFilter->bNoDuplicate = false;
        }

        // Properties set after the user constructor as some properties need to be updated in individual unit types.
        $this->bDBTableAccess    = $this->___hasCustomDBTableAccess();

        // Let extended classes set their own properties.
        $this->_setProperties();

    }

        /**
         * @since   3.7.5
         * @return  boolean
         */
        private function ___hasCustomProductLinkURLQuery() {
            $_aLinkQueryRaw = $this->getAsArray( $this->oUnitOption->get( '_custom_url_query_string' ) );
            foreach( $_aLinkQueryRaw as $_iIndex => $_aKeyValue ) {
                $_aQueryKeyValue = array_filter( $_aKeyValue );
                if ( empty( $_aQueryKeyValue ) ) {
                    continue;
                }
                return true;
            }
            return false;
        }
        /**
         * @return  string
         * @since   3.7.5
         * @callback    filter  aal_filter_product_link
         */
        public function replyToModifyProductURLs( $sURL, $sRawURL, $sASIN, $aUnitOptions ) {
            $_aQuery     = array();
            $_aKeyValues = $this->getAsArray( $this->oUnitOption->get( '_custom_url_query_string' ) );
            foreach( $_aKeyValues as $_iIndex => $_aKeyValue ) {
                $_aQuery[ $_aKeyValue[ 'key' ] ] = $_aKeyValue[ 'value' ];
            }
            return add_query_arg( $_aQuery, $sURL );
        }

        /**
         * Sanitizes a raw product title.
         * @remark      Overridden by an extended class.
         * @return      string
         */
        public function replyToModifyRawTitle( $sTitle ) {
            return $sTitle;
        }
    
        /**
         * Checks whether the unit needs to access the plugin custom database table.
         * 
         * @remark      For the category unit type, the %description%, %content%, and %price% variables need to access the database table 
         * and it requires the API to be connected.
         * @since       3.3.0
         * @since       3.5.0       Changed the visibility scope from protected.
         * @return      boolean
         */
        private function ___hasCustomDBTableAccess() {

            $_bUseDatabaseVariables =  $this->hasCustomVariable(
                $this->oUnitOption->get( 'item_format' ),
                $this->_aItemFormatDatabaseVariables
            );
            if ( $_bUseDatabaseVariables ) {
                return true;
            }
            if ( $this->oUnitOption->get( '_no_pending_items' ) ) {
                return true;
            }
            if ( $this->oUnitOption->get( '_filter_adult_products' ) ) {
                return true;
            }
            if ( $this->oUnitOption->get( '_filter_by_free_shipping' ) ) {
                return true;
            }
            if ( $this->oUnitOption->get( '_filter_by_fba' ) ) {
                return true;
            }
            // @deprecated 3.9.2 these are checked with the item format option, %_discount_rate%, $review_rate%.
//            if ( $this->oUnitOption->get( '_filter_by_rating', 'enabled' ) ) {
//                return true;
//            }
//            if ( $this->oUnitOption->get( '_filter_by_discount_rate', 'enabled' ) ) {
//                return true;
//            }
            return false;

        }

    /**
     * Sets up properties.
     * @remark      Called after required properties are all set up.
     * @remark      Should be overridden in an extended class.
     * @return      void
     */
    protected function _setProperties() {}

    /**
     * 
     * @return      string|integer      The set button id. If not set, `default` will be returned.
     */
    protected function _getButtonID() {
        
        $_iButtonID = ( integer ) $this->oUnitOption->get( 'button_id' );
        
        // Consider cases that options are deleted by external means.
        $_sCSS = AmazonAutoLinks_ButtonResourceLoader::getButtonsCSS();
        if ( $_iButtonID && false !== strpos( $_sCSS, '-' . ( string ) $_iButtonID ) ) {
            return $_iButtonID;
        }
        
        return 'default';
        
    }

    /**
     * Gets the output of product links by specifying a template.
     *
     * @remark      The local variables defined in this method will be accessible in the template file.
     *
     * @param array $aURLs
     *
     * @return      string
     * @since
     * @since       4.0.2   Deprecated the second $sTemplatePath parameter.
     */
    public function get( $aURLs=array() ) {

        $_aHooks            = $this->___getHooksSetPerOutput();

        $_aOptions          = $this->oOption->aOptions;
        $_aArguments        = $this->oUnitOption->get();
        $_iUnitID           = ( integer ) $this->oUnitOption->get( 'id' ); // there are cases that called dynamically without units like the shortcode
        $_bHasPreviousError = $this->___hasPreviousUnitError( $_iUnitID );

        $_sTemplatePath     = $this->___getTemplatePath();

        $_aProducts         = $this->fetch( $aURLs );
        $_aProducts         = apply_filters( 'aal_filter_products', $_aProducts, $aURLs, $this );   // 3.7.0+ Allows found-item-count class to parse the retrieved products.

        try {

            $_sError = $this->_getError( $_aProducts );
            if ( $_sError ) {
                throw new Exception( $_sError );
            }

            // This time, there is no error so if there is a previous unit error, update it to normal.
            if ( $_bHasPreviousError && $_iUnitID ) {
                update_post_meta( $_iUnitID, '_error', 'normal' );
            }

            $_sContent  = $this->getOutputBuffer( array( $this, 'replyToGetOutput' ), array( $_aOptions, $_aArguments, $_aProducts, $_sTemplatePath ) );

        } catch ( Exception $_oException ) {

            $_sErrorMessage = $_oException->getMessage();
            $_sContent      = $this->oUnitOption->get( 'show_errors' )
                ? "<div class='warning'><p>"
                  . AmazonAutoLinks_Registry::NAME. ': ' . $_sErrorMessage
                  . "</p></div>"
                : '';
            if ( ! $_bHasPreviousError && $_iUnitID ) {
                update_post_meta( $_iUnitID, '_error', $_sErrorMessage );
            }

        }

        $_sContent          = apply_filters( 'aal_filter_unit_output', $_sContent, $_aArguments, $_sTemplatePath, $_aOptions, $_aProducts );

        $this->___removeHooksPerOutput( $_aHooks );
        return $_sContent;

    }

        /**
         * @since       4.0.2
         * @return      string  The template path.
         */
        private function ___getTemplatePath() {

            $_oTemplateOption   = AmazonAutoLinks_TemplateOption::getInstance();
            $_sTemplatePath     = $this->oUnitOption->get( 'template_path' );   // if directly specified, use it
            $_sTemplatePath     = $_sTemplatePath
                ? $_sTemplatePath
                : $_oTemplateOption->getPathFromID( $this->oUnitOption->get( 'template_id' ) ); // this method only returns from active templates

            $_sTemplatePath     = apply_filters( "aal_filter_template_path", $_sTemplatePath, $this->oUnitOption->get() );

            if ( ! file_exists( $_sTemplatePath ) ) {
                // use the default one
                return $_oTemplateOption->getDefaultTemplatePathByUnitType( $this->oUnitOption->sUnitType );
            }
            return $_sTemplatePath;
        }

        /**
         * Sets up hooks per output basis.
         * @since   4.0.0
         * @return  array   An array holding hook objects.
         */
        private function ___getHooksSetPerOutput() {

            add_filter( 'aal_filter_unit_product_raw_title', array( $this, 'replyToModifyRawTitle' ), 10 );
            $_aHooks = array(
                new AmazonAutoLinks_UnitOutput__ProductFilter_ByRating( $this ),
                new AmazonAutoLinks_UnitOutput__ProductFilter_AdultProducts( $this ),
                new AmazonAutoLinks_UnitOutput__ProductFilter_ByPrimeEligibility( $this ), // 3.10.0
                new AmazonAutoLinks_UnitOutput__ProductFilter_ByFBA( $this ), // 3.10.0
                new AmazonAutoLinks_UnitOutput__ProductFilter_ByFreeShipping( $this ), // 3.10.0
                new AmazonAutoLinks_UnitOutput__ProductFilter_ByDiscountRate( $this ),
                new AmazonAutoLinks_UnitOutput__DebugInformation_Product( $this ),
                new AmazonAutoLinks_UnitOutput__DebugInformation_Unit( $this ),
                new AmazonAutoLinks_UnitOutput__Credit( $this ),
                new AmazonAutoLinks_UnitOutput__ErrorChecker( $this ),
            );

            // 3.7.5+
            if ( $this->___hasCustomProductLinkURLQuery() ) {
                add_filter( 'aal_filter_product_link', array( $this, 'replyToModifyProductURLs' ), 100, 4 );
            }
            return $_aHooks;

        }

        /**
         * Removes hooks per output basis.
         *
         * @param array $aHooks An array holding hook objects set by the ___getHooksSetPerOutput() method.
         * @see ___getHooksSetPerOutput()
         * @since 4.0.0
         */
        private function ___removeHooksPerOutput( array $aHooks ) {
            remove_filter( 'aal_filter_unit_product_raw_title', array( $this, 'replyToModifyRawTitle' ), 10 );
            remove_filter( 'aal_filter_product_link', array( $this, 'replyToModifyProductURLs' ), 100 );
            foreach( $aHooks as $_oHook ) {
                $_oHook->__destruct();
            }
        }

        /**
         * @return bool
         * @since   3.10.0
         */
        private function ___hasPreviousUnitError( $iUnitID ) {
            if ( ! $iUnitID ) {
                return false;
            }
            $_snPreviousError   = $this->oUnitOption->get( '_error' );
            if ( is_null( $_snPreviousError ) ) {
                return false;
            }
            if ( 'normal' === $_snPreviousError ) {
                return false;
            }
            return true;
        }
        /**
         * @param       array       $aOptions
         * @param       array       $aArguments
         * @param       array       $aProducts
         * @param       string      $sTemplatePath
         * @since       3.5.0
         * @return      void
         * @callback    method      self::getOutputBuffer()     Not using the WordPress filter hook so there is no need to remove the filter within the `get()` method.
         * @remark      Not using include_once() because templates can be loaded multiple times.
         */
        public function replyToGetOutput( $aOptions, $aArguments, $aProducts, $sTemplatePath ) {

//            if ( file_exists( $sTemplatePath ) ) {

                // Include the template
                defined( 'WP_DEBUG' ) && WP_DEBUG ? include( $sTemplatePath ) : @include( $sTemplatePath );

                // Enqueue the impression counter script.
                $this->oImpressionCounter->add( $this->oUnitOption->get( 'country' ), $this->oUnitOption->get( 'associate_id' ) );
                return;
//            }

            /**
             * @deprecated 4.0.2 The file existent check is done above @see $this::___getTemplatePath()
             */
//            echo '<p>'
//                    . AmazonAutoLinks_Registry::NAME
//                    . ': ' . __( 'the template could not be found. Try re-selecting the template in the unit option page.', 'amazon-auto-links' )
//                . '</p>';

        }

        /**
         * @deprecated  Use `get()` instead.
         * @return      string
         */
        public function getOutput( $aURLs=array(), $sTemplatePath=null ) {
            return $this->get( $aURLs, $sTemplatePath );
        }

    /**
     * Updates unit status.
     *
     * Called when the unit HTTP request cache is set.
     *
     * Cases:
     * - An entirely new request is made and has no error. -> do nothing
     * - An entirely new request is made and has an error. -> save an error
     * - A cached request had no error and it is renewed and has no error. -> do nothing
     * - A cached request had no error and it is renewed and has an error. -> save an error
     * - A cached request had an error and it is renewed and has no error. -> delete an error
     * - A cached request had an error and it is renewed and has an error. -> override an error
     *
     * @param       array   $aProducts
     * @param       integer $iUnitID        The unit (post) ID.
     * @callback    filter  aal_filter_products
     * @remark      Although this hooks into a filter hook but called within an another outer callback method and this does not require a return value.
     * @retuen      void
     * @since       3.7.0
     */
    public function replyToCheckErrors( $aProducts, $iUnitID ) {

        $_sUnitStatusMetaKey = '_error';
        $_sError = $this->_getError( $aProducts );
        if ( $_sError ) {
            update_post_meta(
                $iUnitID, // post id
                $_sUnitStatusMetaKey, // meta key
                $_sError    // value
            );
            return;
        }

        // At this point, the response has no error.

        $_snStoredError = get_post_meta( $iUnitID, $_sUnitStatusMetaKey, true );
        if ( 'normal' !== $_snStoredError ) {
            update_post_meta(
                $iUnitID, // post id
                $_sUnitStatusMetaKey, // meta key
                'normal' // value
            );
        }

    }

    /**
     * Returns the error message if found.
     *
     * Cases:
     * a: no items -> error
     * b: items and errors are returned -> fine
     * c: only errors -> error
     * d: no errors -> fine
     *
     * @since       3.7.0
     * @remark      Override this method in each extended class.
     * @return      string  The found error.
     * @return      string  The error message.
     */
    protected function _getError( $aProducts ) {

        // a: No items
        if ( empty( $aProducts ) ) {
            return __( 'No products found.', 'amazon-auto-links' );
        }

        // b: items and errors
        $_aError    = $this->getElement( $aProducts, array( 'Error' ), array() );
        if (
            ! empty( $_aError )
            && isset( $aProducts[ $this->_sResponseItemsParentKey ] )    // There are cases that error is set but items are returned
        ) {
            return '';
        }

        // c: only errors
        if ( isset( $_aError[ 'Message' ], $_aError[ 'Code' ] ) ) {
            return $_aError[ 'Code' ] . ': ' . $_aError[ 'Message' ];
        }

        // d: no error
        return '';
    }

    /**
     * Renders the product links.
     * 
     * @return      void
     */
    public function render( $aURLs=array() ) {
        echo $this->get( $aURLs );
    }

    /**
     * Retrieves product link data from a remote server.
     * @remark      should be extended and must return an array.
     * @return      array
     */
    public function fetch( $aURLs ) {
        return array(); 
    }

}