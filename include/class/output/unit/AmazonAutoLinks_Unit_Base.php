<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
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
abstract class AmazonAutoLinks_Unit_Base extends AmazonAutoLinks_PluginUtility {

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
        $this->oProductTable        = new AmazonAutoLinks_DatabaseTable_product(
            AmazonAutoLinks_Registry::$aDatabaseTables[ 'product' ]
        );            
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
        
        $this->bDBTableAccess    = $this->_hasCustomVariable( 
            $this->oUnitOption->get( 'item_format' ),
            array( '%price%', '%review%', '%rating%', '%image_set%' )
        );
        
        // Sanitize product title for sorting.
        add_filter(
            'aal_filter_unit_product_raw_title',
            array( $this, 'replyToModifyRawTitle' ),
            10,
            2
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
        $_sCSS = AmazonAutoLinks_ButtonStyleLoader::getButtonsCSS();
        if ( $_iButtonID && false !== strpos( $_sCSS, $_iButtonID ) ) {
            return $_iButtonID;
        }
        
        return 'default';
        
    }
    
    /**
     * Gets the output of product links by specifying a template.
     * 
     * @remark      The local variables defined in this method will be accessible in the template file.
     */
    public function getOutput( $aURLs=array(), $sTemplatePath=null ) {

        $aOptions      = $this->oOption->aOptions; 
        
        // Let the template file to access the local $arrArgs variable.
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
        
        // Not using include_once() because templates can be loaded multiple times.
        
        $_bLoaded      = defined( 'WP_DEBUG' ) && WP_DEBUG
            ? include( $sTemplatePath )
            : @include( $sTemplatePath );
            
        if ( ! $_bLoaded ) {
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
                        
            $_sHTMLCOmment = AmazonAutoLinks_PluginUtility::getCommentCredit();
            if ( ! $this->oUnitOption->get( 'credit_link' ) ) {
                return $_sHTMLCOmment;
            }
            
            $_sQueryKey  = $this->oOption->get( 'query', 'cloak' );
            $_sVendorURL = add_query_arg(
                array(
                    $_sQueryKey => 'vendor',
                ),
                site_url()
            );
            return $_sHTMLCOmment
                . "<span class='amazon-auto-links-credit'>by "
                    ."<a href='" . esc_url( $_sVendorURL ) . "' title='" . esc_attr( AmazonAutoLinks_Registry::DESCRIPTION ) . "' rel='author'>"
                        . AmazonAutoLinks_Registry::NAME
                    . "</a>"
                . "</span>";
                
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