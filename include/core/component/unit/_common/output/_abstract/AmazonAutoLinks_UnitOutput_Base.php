<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
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
    );

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
        
        $_iButtonID = $this->oUnitOption->get( 'button_id' );
        
        // Consider cases that options are deleted by external means.
        $_sCSS = AmazonAutoLinks_ButtonResourceLoader::getButtonsCSS();
        if ( $_iButtonID && false !== strpos( $_sCSS, $_iButtonID ) ) {
            return $_iButtonID;
        }
        
        return 'default';
        
    }

    /**
     * Gets the output of product links by specifying a template.
     * 
     * @remark      The local variables defined in this method will be accessible in the template file.
     * @return      string
     */
    public function get( $aURLs=array(), $sTemplatePath=null ) {

        // Hooks of function-call basis.
        add_filter( 'aal_filter_unit_product_raw_title', array( $this, 'replyToModifyRawTitle' ), 10 );
        $_oFilterByRating      = new AmazonAutoLinks_UnitOutput__ProductFilter_ByRating( $this );
        $_oFilterAdultProducts = new AmazonAutoLinks_UnitOutput__ProductFilter_AdultProducts( $this );
        $_oFilterByDiscount    = new AmazonAutoLinks_UnitOutput__ProductFilter_ByDiscountRate( $this );
        $_oDebugInfoProduct    = new AmazonAutoLinks_UnitOutput__DebugInformation_Product( $this );
        $_oDebugInfoUnit       = new AmazonAutoLinks_UnitOutput__DebugInformation_Unit( $this );
        $_oCredit              = new AmazonAutoLinks_UnitOutput__Credit( $this );
        $_oFoundItemCount      = new AmazonAutoLinks_UnitOutput__ErrorChecker( $this );

        // 3.7.5+
        if ( $this->___hasCustomProductLinkURLQuery() ) {
            add_filter( 'aal_filter_product_link', array( $this, 'replyToModifyProductURLs' ), 100, 4 );
        }

        $_aOptions          = $this->oOption->aOptions;
        $_aArguments        = $this->oUnitOption->get();
        $_oTemplatePath     = new AmazonAutoLinks_UnitOutput__TemplatePath( $_aArguments );
        $_sTemplatePath     = $_oTemplatePath->get( $sTemplatePath );
        $_aProducts         = $this->fetch( $aURLs );
        $_aProducts         = apply_filters( 'aal_filter_products', $_aProducts, $aURLs, $this );   // 3.7.0+ Allows found-item-count class to parse the retrieved products.

        try {

            $_sError            = $this->_getError( $_aProducts );
            if ( $_sError ) {
                throw new Exception( $_sError );
            }

            $_sContent          = $this->getOutputBuffer(
                array( $this, 'replyToGetOutput' ),
                array(
                    $_aOptions,
                    $_aArguments,
                    $_aProducts,
                    $_sTemplatePath
                )
            );

        } catch ( Exception $_oException ) {
            $_sContent   = $this->oUnitOption->get( 'show_errors' )
                ? "<div class='warning'><p>"
                  . AmazonAutoLinks_Registry::NAME. ': ' . $_oException->getMessage()
                  . "</p></div>"
                : '';
        }

        $_sContent          = apply_filters(
            'aal_filter_unit_output',
            $_sContent,
            $_aArguments,
            $_sTemplatePath, // [3+]
            $_aOptions,      // [3+]
            $_aProducts      // [3+]
        );

        // Remove hooks of function-call basis.
        remove_filter( 'aal_filter_unit_product_raw_title', array( $this, 'replyToModifyRawTitle' ), 10 );
        remove_filter( 'aal_filter_product_link', array( $this, 'replyToModifyProductURLs' ), 100 );
        $_oFilterByRating->__destruct();
        $_oFilterAdultProducts->__destruct();
        $_oFilterByDiscount->__destruct();
        $_oDebugInfoProduct->__destruct();
        $_oDebugInfoUnit->__destruct();
        $_oCredit->__destruct();
        $_oFoundItemCount->__destruct();

        return $_sContent;

    }
        /**
         * @param       array       $aOptions
         * @param       array       $aArguments
         * @param       array       $aProducts
         * @param       string      $sTemplatePath
         * @since       3.5.0
         * @return      void
         * @callback    self::getOutputBuffer()     Not using the WordPress filter hook so there is no need to remove the filter within the `get()` method.
         * @remark      Not using include_once() because templates can be loaded multiple times.
         */
        public function replyToGetOutput( $aOptions, $aArguments, $aProducts, $sTemplatePath ) {

            if ( file_exists( $sTemplatePath ) ) {

                // Backward compatibility (old format variable names)
                $arrArgs       = $aArguments;
                $arrOptions    = $aOptions;
                $arrProducts   = $aProducts;

                defined( 'WP_DEBUG' ) && WP_DEBUG
                    ? include( $sTemplatePath )
                    : @include( $sTemplatePath );

                // Enqueue the impression counter script.
                $this->oImpressionCounter->add(
                    $this->oUnitOption->get( 'country' ),
                    $this->oUnitOption->get( 'associate_id' )
                );
                return;
            }
            echo '<p>'
                    . AmazonAutoLinks_Registry::NAME
                    . ': ' . __( 'the template could not be found. Try re-selecting the template in the unit option page.', 'amazon-auto-links' )
                . '</p>';

        }

        /**
         * @deprecated  Use `get()` instead.
         * @return      string
         */
        public function getOutput( $aURLs=array(), $sTemplatePath=null ) {
            return $this->get( $aURLs, $sTemplatePath );
        }

    /**
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
     * @callback    aal_filter_products
     * @remark      Although this hook into a filter hook but called within an another outer callback method and this does not require a return value.
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
                $_sError
            );
            return;
        }
        // At this point, the response has no error.

        $_snStoredError = get_post_meta( $iUnitID, $_sUnitStatusMetaKey, true );
        if ( 'normal' !== $_snStoredError ) {
            update_post_meta(
                $iUnitID, // post id
                $_sUnitStatusMetaKey, // meta key
                'normal'
            );
        }

    }

    /**
     * Checks whether response has an error.
     * @since       3
     * @return      boolean
     * @deprecated  3.7.0   Use _getError() instead
     */
//    protected function _isError( $aProducts ) {
//        return empty( $aProducts );
//    }

    /**
     * Returns the error message if found.
     * @since       3.7.0
     * @remark      Override this method in each extended class.
     * @return      string  The found error.
     * @return      string  The error message.
     */
    protected function _getError( $aProducts ) {
        if ( empty( $aProducts ) ) {
            return __( 'No products found.', 'amazon-auto-links' );
        }
        if ( isset( $aProducts[ 'Error' ][ 'Message' ], $aProducts[ 'Error' ][ 'Code' ] ) ) {
            return $aProducts[ 'Error' ][ 'Code' ] . ': ' . $aProducts[ 'Error' ][ 'Message' ];
        }
        // @todo deprecate below as PA-API 5 changed the data structure but they may need to be kept for backward compatibility
        if ( isset( $aProducts[ 'Items' ][ 'Request' ][ 'Errors' ] ) ) {
            return $aProducts[ 'Items' ][ 'Request' ][ 'Errors' ][ 'Code' ]
                . ': ' . $aProducts[ 'Items' ][ 'Request' ][ 'Errors' ][ 'Message' ];
        }
        if ( isset( $aProducts[ 'Items' ][ 'Request' ][ 'Errors' ][ 0 ] ) ) {
            return $aProducts[ 'Items' ][ 'Request' ][ 'Errors' ][ 0 ][ 'Code' ]
                . ': ' . $aProducts[ 'Items' ][ 'Request' ][ 'Errors' ][ 0 ][ 'Message' ];
        }
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