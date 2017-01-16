<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
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
abstract class AmazonAutoLinks_UnitOutput_Base extends AmazonAutoLinks_PluginUtility {

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
    public $sCharEncoding ='';
    
    /**
     * Indicates whether the unit needs to access custom databse table.
     * @since       3
     */
    public $bDBTableAccess = false;

    /**
     * Stores a unit option object.
     * @var object
     */
    public $oUnitOption;

    /**
     * Sets up properties and hooks.
     */
    public function __construct( $aoUnitOptions, $sUnitType='' ) {

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
        
        $this->bDBTableAccess    = $this->_hasCustomDBTableAccess();
        
        // Sanitize product title for sorting.
        add_filter( 'aal_filter_unit_product_raw_title', array( $this, 'replyToModifyRawTitle' ) );
 
    }   
        /**
         * Sanitizes a raw product title.
         * @return      Overridden by an extended class.
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
         * 
         * @since       3.3.0
         * @return      boolean
         */
        protected function _hasCustomDBTableAccess() {
            return $this->_hasCustomVariable( 
                $this->oUnitOption->get( 'item_format' ),
                apply_filters(
                    'aal_filter_item_format_database_query_variables',
                    array( '%price%', '%review%', '%rating%', '%image_set%', '%similar%' )
                )
            );
        }
    
        /**
         * Checks if a given custom variable(s) exists in a subject string.
         * @return      boolean
         * @since       3
         */
        protected function _hasCustomVariable( $sSubject, array $aKeys = array( 'price', 'rating', 'review', 'image_set' ) ) {
            $_aKeys = array();
            foreach( $aKeys as $_sKey ) {
                $_aKeys[] = '\Q' . $_sKey . '\E';
            }
            return preg_match( 
                '/(' . implode( '|', $aKeys ) . ')/',  // '/(\Q%price%\E|\Q%rating%\E|\Q%review%\E|\Q%image_set%\E)/'
                $sSubject
            );        
        }

    /**
     * Sets up properties.
     * @remark      Should be overridden in an extended class.
     * @return      void
     */
    protected function _setProperties() {}
  
    /**
     * Finds the template path from the given arguments(unit options).
     * 
     * The keys that can determine the template path are template, template_id, template_path.
     * 
     * The template_id key is automatically assigned when creating a unit. If the template_path is explicitly set and the file exists, it will be used.
     * 
     * The template key is a user friendly one and it should point to the name of the template. If multiple names exist, the first item will be used.
     * 
     * @return      string
     */
    protected function getTemplatePath( $aArguments ) {

        // If it is set in a request, use it.
        if ( isset( $aArguments[ 'template_path' ] ) && file_exists( $aArguments[ 'template_path' ] ) ) {
            return $aArguments[ 'template_path' ];
        }

        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        
        // If a template name is given in a request
        if ( isset( $aArguments[ 'template' ] ) && $aArguments[ 'template' ] ) {
            foreach( $_oTemplateOption->getActiveTemplates() as $_aTemplate ) {
                if ( strtolower( $_aTemplate[ 'name' ] ) == strtolower( trim( $aArguments[ 'template' ] ) ) ) {
                    return $_aTemplate[ 'template_path' ];
                }
            }
        }
                    
        // If a template ID is given,
        if ( isset( $aArguments[ 'template_id' ] ) && $aArguments[ 'template_id' ] ) {
            foreach( $_oTemplateOption->getActiveTemplates() as $_sID => $_aTemplate ) {
                if ( $_sID == trim( $aArguments[ 'template_id' ] ) ) {
                    return $_aTemplate[ 'template_path' ];        
                }
            }        
        }
        
        // Not found. In that case, use the default one.
        return $this->getDefaultTemplatePath();       
        
    }
    
    /**
     * 
     * @remark      Each unit has to define its own default template.
     * @since       3
     * @return      string
     */
    public function getDefaultTemplatePath() {
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $_aTemplate = $_oTemplateOption->getTemplateArrayByDirPath(
            AmazonAutoLinks_Registry::$sDirPath 
            . DIRECTORY_SEPARATOR . 'template'
            . DIRECTORY_SEPARATOR . 'category',
            false       // no extra info
        );
        return $_aTemplate[ 'dir_path' ] . '/template.php' ;
    }
    
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
     * @deprecated      Use `get()` instead.
     * @return      string
     */
    public function getOutput( $aURLs=array(), $sTemplatePath=null ) {
        return $this->get( $aURLs, $sTemplatePath );
    }
    
    /**
     * Gets the output of product links by specifying a template.
     * 
     * @remark      The local variables defined in this method will be accessible in the template file.
     * @return      string
     */
    public function get( $aURLs=array(), $sTemplatePath=null ) {

        $aOptions      = $this->oOption->aOptions; 
        
        // Let the template file access the local `$aArguments` variable.
        $aArguments    = $this->oUnitOption->get();
        
        $aProducts     = $this->fetch( $aURLs );
        if ( $this->_isError( $aProducts ) && ! $this->oUnitOption->get( 'show_errors' ) ) {
            return '';
        }
        
        $sTemplatePath = apply_filters( 
            "aal_filter_template_path", 
            isset( $sTemplatePath ) 
                ? $sTemplatePath 
                : $this->getTemplatePath( $aArguments ),
            $aArguments
        );

        // Capture the output buffer
        ob_start(); 
                
        // Backward compatibility (old format variable names)
        $arrArgs       = $aArguments;  
        $arrOptions    = $aOptions;
        $arrProducts   = $aProducts;        
        
        if ( file_exists( $sTemplatePath ) ) {
            
            // Not using include_once() because templates can be loaded multiple times.
            $_bLoaded      = defined( 'WP_DEBUG' ) && WP_DEBUG
                ? include( $sTemplatePath )
                : @include( $sTemplatePath ); 
                
            // Enqueue the impression counter script.
            $this->_enqueueImpressionCounter();
            
        } else {
            echo '<p>' 
                . AmazonAutoLinks_Registry::NAME 
                . ': ' . __( 'the template could not be found. Try reselecting the template in the unit option page.', 'amazon-auto-links' )
            . '</p>';
        }

        if ( $this->oOption->isDebug() && ! $this->oUnitOption->get( 'is_preview' ) ) {
            $this->_printDebugInfo(
                $sTemplatePath,
                $aArguments,
                $aOptions,
                $aProducts
            );
        }        
        $_sContent = ob_get_contents(); 
        ob_end_clean(); 
    
        
        return apply_filters( 
            "aal_filter_unit_output", 
            $_sContent . $this->_getCredit(), 
            $aArguments,
            $sTemplatePath, // [3+]
            $aOptions, // [3+]
            $aProducts // [3+]
        );
        
    }      
        /**
         * Stores the locales of the impression counter scripts to insert.
         * @since       3.1.0
         */
        static private $_aImressionCounterSciptLocales = array();
        /*
         * Enqueues the impression counter script.
         * @since       3.1.0
         */
        private function _enqueueImpressionCounter() {
            
            if ( ! $this->oOption->get( 'external_scripts', 'impression_counter_script' ) ) {
                return;
            }
            $_sLocale       = $this->oUnitOption->get( 'country' );
            $_sAssociateID  = $this->oUnitOption->get( 'associate_id' );
            self::$_aImressionCounterSciptLocales[ $_sLocale ] = isset( self::$_aImressionCounterSciptLocales[ $_sLocale ] )
                ? self::$_aImressionCounterSciptLocales[ $_sLocale ]
                : array();
            self::$_aImressionCounterSciptLocales[ $_sLocale ][ $_sAssociateID ] = $_sAssociateID;
                
            add_action( 'wp_footer', array( __CLASS__, '_replyToInsertImpressionCounter' ), 999 );
            
        }
            /**
             * Inserts impression counter scripts.
             * @since       3.1.0
             */
            static public function _replyToInsertImpressionCounter() {
                foreach( self::$_aImressionCounterSciptLocales as $_sLocale => $_aAssociateTags ) {
                    foreach( $_aAssociateTags as $_sAssociateTag ) {                        
                        echo str_replace(
                            '%ASSOCIATE_TAG%',  // needle
                            $_sAssociateTag,    // replacement
                            AmazonAutoLinks_Property::getImpressionCounterScript( $_sLocale ) // haystack
                        );
                    }
                }
            }
        
        /**
         * Checks whether response has an error.
         * @since       3
         * @return      boolean
         */
        protected function _isError( $aProducts ) {
            return empty( $aProducts );
        }
        /**
         * @return      string
         */
        protected function _getCredit() {            
                        
            $_sHTMLComment = apply_filters( 'aal_filter_credit_comment', '' );            
            if ( ! $this->oUnitOption->get( 'credit_link' ) ) {
                return $_sHTMLComment;
            }
            $_iCreditType = ( integer ) $this->oUnitOption->get( array( 'credit_link_type' ), 0 );
            return apply_filters( 
                'aal_filter_credit_link_' . $_iCreditType,
                $_sHTMLComment, 
                $this->oOption 
            );
                
        }
        
    /**
     * Renders the product links.
     * 
     * @return      void
     */
    public function render( $aURLs=array() ) {
        echo $this->getOutput( $aURLs );
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