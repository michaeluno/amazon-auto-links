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
 * A base class for unit classes, search, tag, and category.
 * 
 * Provides shared methods and properties for those classes.
 *
 */
abstract class AmazonAutoLinks_UnitOutput_Base extends AmazonAutoLinks_UnitOutput_Utility {

    /**
     * Stores the unit type.
     * @remark The constructor will create a unit option object based on this value.
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

    public $bIsSSL;

    /**
     * Stores a product filter object.
     */
    public $oGlobalProductFilter;
    public $oUnitProductFilter;

    /**
     * Stores blocked product ASINs by filters.
     * Used to insert these in an output error of No Products Found so that it will be easier to debug.
     * @var   array
     * @since 4.1.0
     */
    public $aBlockedASINs = array();
    
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
     * @remark Accessed from delegation classes publicly.
     * @since  3
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
     * Stores a unique output ID per an object instance.
     * Used for debugging and to determine the caller object and checks whether unnecessary calls are made for hook callbacks.
     * @var string
     */
    public $sCallID;

    /**
     * Lists the tags (variables) used in the Item Format unit option that require to access the custom database.
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
     * Stores major unit error messages.
     *
     * If this has at least one item, the template will not be called but only the error will be shown.
     *
     * @since  4.6.17
     * @var    array
     * @remark The visibility scope is public as delegation classes might need to access it.
     */
    public $aErrors = array();

    /**
     * Stores additional notes about the unit outputs.
     *
     * Inserted at the bottom of the unit output as an HTML comment.
     *
     * @since  4.6.17
     * @var    array
     * @remark The visibility scope is public as delegation classes might need to access it.
     */
    public $aNotes = array();

    /**
     * Sets up properties.
     *
     * @param array|AmazonAutoLinks_UnitOption_Base $aoUnitOptions
     * @param string $sUnitType
     */
    public function __construct( $aoUnitOptions, $sUnitType='' ) {

        $this->sCallID              = uniqid();
        $this->sUnitType            = $sUnitType ? $sUnitType : $this->sUnitType;
        $_sUnitOptionClassName      = "AmazonAutoLinks_UnitOption_{$this->sUnitType}";
        $this->oUnitOption          = is_object( $aoUnitOptions )
            ? $aoUnitOptions
            : new $_sUnitOptionClassName( $this->getElement( $aoUnitOptions, array( 'id' ) ), $aoUnitOptions );
        $this->oUnitOption->sCallID = $this->sCallID;

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

        // Let extended classes set their own properties.
        // Must be called before `___hasCustomDBTableAccess()` as some extended classes adds properties to judge whether it requires db table access.
        $this->_setProperties();

        // Properties set after the user constructor as some properties need to be updated in individual unit types.
        $this->bDBTableAccess    = $this->___hasCustomDBTableAccess();

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
         * @param    string $sURL
         * @param    string $sRawURL
         * @param    string $sASIN
         * @param    AmazonAutoLinks_UnitOption_Base $aUnitOptions
         * @return   string
         * @since    3.7.5
         * @callback add_filter()  aal_filter_product_link
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
         * @param       string $sTitle
         */
        public function replyToModifyRawTitle( $sTitle ) {
            return $sTitle;
        }

        /**
         * Checks whether the unit needs to access the plugin custom database table.
         * 
         * @remark      For the category unit type, the %description%, %content%, and %price% tags (variables) need to access the database table
         * and it requires the API to be connected.
         * @remark      MUST be called after `_setProperties()`. This is important as some extended classes modifies `_aItemFormatDatabaseVariable` property.
         * @since       3.3.0
         * @since       3.5.0       Changed the visibility scope from protected.
         * @return      boolean
         */
        private function ___hasCustomDBTableAccess() {

            $_bUseDatabaseVariables =  $this->oUnitOption->hasItemFormatTags( $this->_aItemFormatDatabaseVariables );
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

            $_aArguments = $this->oUnitOption->get();   // the unit option can be modified while fetching so set the variable right before calling the template
            $_sContent   = $this->getOutputBuffer( array( $this, 'replyToGetOutput' ), array( $_aOptions, $_aArguments, $_aProducts, $_sTemplatePath ) );

            // [4.6.17+] Add notes
            $_sNotes     = trim( implode( ', ', $this->aNotes ) );
            $_sContent  .= $_sNotes ? "<!-- {$_sNotes} -->": '';

        } catch ( Exception $_oException ) {

            $_sErrorMessage  = $_oException->getMessage();
            $_iShowErrorMode = ( integer ) $this->oUnitOption->get( 'show_errors' );
            $_iShowErrorMode = ( integer ) apply_filters( 'aal_filter_unit_show_error_mode', $_iShowErrorMode, $this->oUnitOption->get() );
            $_sContent       = $this->___getErrorOutput( $_iShowErrorMode, $_sErrorMessage );

            if ( ! $_bHasPreviousError && $_iUnitID ) {
                update_post_meta( $_iUnitID, '_error', $_sErrorMessage );
            }

        }

        $_sContent          = $this->___getUnitFormatApplied( $_sContent );     // 4.3.0
        $_sContent          = apply_filters( 'aal_filter_unit_output', $_sContent, $this->oUnitOption->get(), $_sTemplatePath, $_aOptions, $_aProducts );

        $this->___removeHooksPerOutput( $_aHooks );
        return $_sContent;

    }
        /**
         * @param   string $sUnitOutput
         * @return  string
         * @since   4.3.0
         */
        private function ___getUnitFormatApplied( $sUnitOutput ) {
            return str_replace(
                array(
                    '%text%',
                    '%products%'
                ),
                array(
                    $this->oUnitOption->get( array( 'custom_text' ), '' ),
                    $sUnitOutput
                ),
                apply_filters( 'aal_filter_unit_format', $this->oUnitOption->get( array( 'unit_format' ), '' ), $this->oUnitOption ) // 4.5.8 - the RSS template should remove custom text
            );
        }
        /**
         * @param   $iShowErrorMode
         * @param   $sErrorMessage
         * @return  string
         * @since   4.1.0
         */
        private function ___getErrorOutput( $iShowErrorMode, $sErrorMessage ) {
            if ( ! $iShowErrorMode ) {
                return '';
            }
            $_iFilteredOut = count( $this->aBlockedASINs );
            $_iUnitID      = $this->oUnitOption->get( 'id' );
            $_sNotes       = implode( ',', $this->aNotes );
            return 2 === $iShowErrorMode
                ? "<!-- "
                    . AmazonAutoLinks_Registry::NAME. ": " . $sErrorMessage
                    . " {$_iFilteredOut} items are filtered out: [" . implode( ', ', $this->aBlockedASINs ) . "]"
                    . " Type: {$this->sUnitType} ID: {$_iUnitID}"
                    . ( $_sNotes ? " Notes: {$_sNotes}" : '' )
                  . " -->"
                : "<div class='warning' data-type='{$this->sUnitType}' data-id='{$_iUnitID}'>"
                    . "<p>"
                            . AmazonAutoLinks_Registry::NAME. ': ' . $sErrorMessage
                            . ( $_iFilteredOut ? ' (' . $_iFilteredOut . ' items filtered out)' : '' )
                        . "</p>"
                    . "</div>"
                    . ( $_sNotes ? "<!-- Notes: {$_sNotes} -->" : '' );
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
         * @return  bool
         * @since   3.10.0
         * @param   integer $iUnitID
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

            // Include the template
            defined( 'WP_DEBUG' ) && WP_DEBUG
                ? include( $sTemplatePath )
                : @include( $sTemplatePath );

            // Enqueue the impression counter script.
            $this->oImpressionCounter->add( $this->oUnitOption->get( 'country' ), $this->oUnitOption->get( 'associate_id' ) );

        }

        /**
         * @deprecated  Use `get()` instead.
         * @return      string
         * @param       array $aURLs
         * @param       string $sTemplatePath
         */
        public function getOutput( $aURLs=array(), $sTemplatePath=null ) {
            return $this->get( $aURLs );
        }

    /**
     * Updates unit status.
     *
     * Called when the unit HTTP request cache is set.
     *
     * Cases:
     * - An entirely new request is made and has no error. -> do nothing.
     * - An entirely new request is made and has an error. -> save the error.
     * - A cached request had no error and it is renewed and has no error. -> do nothing.
     * - A cached request had no error and it is renewed and has an error. -> save the error.
     * - A cached request had an error and it is renewed and has no error. -> delete the error.
     * - A cached request had an error and it is renewed and has an error. -> override the error.
     *
     * @param       array   $aProducts
     * @param       integer $iUnitID        The unit (post) ID.
     * @callback    filter  aal_filter_products
     * @see         AmazonAutoLinks_UnitOutput__ErrorChecker::_replyToCheckErrors()
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
     * @return      string  The found error message. An empty string if no error is found.
     * @param       array   $aProducts
     */
    protected function _getError( $aProducts ) {
        $this->___setErrors( $aProducts );
        return trim( implode( ' ', $this->aErrors ) );
    }
        /**
         * @since  4.6.17   Moved from AmazonAutoLinks_UnitOutput_Base::_getError()
         */
        private function ___setErrors( $aProducts ) {

            // a: No items
            if ( empty( $aProducts ) ) {
                $this->aErrors[] = __( 'No products found.', 'amazon-auto-links' );
                return;
            }

            // b: items and errors
            $_aError    = $this->getElement( $aProducts, array( 'Error' ), array() );
            if (
                ! empty( $_aError )
                && isset( $aProducts[ $this->_sResponseItemsParentKey ] )    // There are cases that error is set but items are returned
            ) {
                return;
            }

            // c: only errors
            if ( isset( $_aError[ 'Message' ] ) ) {
                $_aError = $_aError + array( 'Code' => '' );
                $_sCode  = $_aError[ 'Code' ] ? $_aError[ 'Code' ] . ': ' : '';
                $this->aErrors[] = $_sCode . $_aError[ 'Message' ];
                return;
            }

            // d: no error
        }

    /**
     * Renders the product links.
     *
     * @param       array $aURLs
     * @return      void
     */
    public function render( $aURLs=array() ) {
        echo $this->get( $aURLs );
    }

    /**
     * Retrieves product link data from a remote server.
     * @remark      should be extended and must return an array.
     * @return      array
     * @param       array $aURLs
     */
    public function fetch( $aURLs ) {
        return array(); 
    }

}