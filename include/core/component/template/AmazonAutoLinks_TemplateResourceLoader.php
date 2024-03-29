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
 * Loads template components such as style.css, template.php, functions.php etc.
 *  
 * @since       3
 * 
 * @filter      apply       aal_filter_template_custom_css
 */
class AmazonAutoLinks_TemplateResourceLoader extends AmazonAutoLinks_WPUtility {
  
    /**
     * Stores the template option object.
     */
    public $_oTemplateOption;
  
    public function __construct() {
        
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return;
        }
        
        $this->_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        
        $this->___loadFunctionsOfActiveTemplates();
        $this->___loadStylesOfActiveTemplates();
        $this->___loadSettingsOfActiveTemplates();
        
    }

    /**
     * Includes activated templates' `functions.php` files.
     * @since       3
     * @since       4.0.0   Changed the timing to the init action hook.
     */    
    private function ___loadFunctionsOfActiveTemplates() {
        add_action( 'init', array( $this, 'replyToLoadFunctions' ) );
        add_action( 'aal_action_unit_prefetch', array( $this, 'replyToLoadFunctions' ), 9 );    // before the main action callback runs
    }
        public function replyToLoadFunctions() {
            foreach( $this->_oTemplateOption->getCommonTemplates() + $this->_oTemplateOption->getActiveTemplates() as $_aTemplate ) {
                if ( ! isset( $_aTemplate[ 'dir_path' ] ) ) {
                    continue;
                }
                if ( file_exists( $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'functions.php' ) ) {
                    include_once( $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'functions.php' );
                }

            }
        }
    
    /**
     * 
     * @since       3
     */
    private function ___loadStylesOfActiveTemplates() {
        add_action( 
            'wp_enqueue_scripts', 
            array( $this, '_replyToEnqueueActiveTemplateStyles' ) 
        );
        add_action(
            'enqueue_embed_scripts',
            array( $this, '_replyToEnqueueActiveTemplateStyles' )
        ); // 4.0.0
        add_action(
            'wp_head',
            array( $this, '_replyToPrintActiveTemplateCustomCSSRules' )
        );
        add_action(
            'embed_head',
            array( $this, '_replyToPrintActiveTemplateCustomCSSRules' )
        ); // 4.0.0
    }
        /**
         * Enqueues activated templates' CSS file.
         * 
         * @callback        action      wp_enqueue_scripts
         */
        public function _replyToEnqueueActiveTemplateStyles() {
            
            // This must be called after the option object has been established.
            foreach( $this->_oTemplateOption->getCommonTemplates() + $this->_oTemplateOption->getActiveTemplates() as $_aTemplate ) {

                $_aTemplate = $_aTemplate + array( 'id' => '', 'version' => AmazonAutoLinks_Registry::VERSION );
                $_sCSSPath  = $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'style.css';
                $_sMinPath  = $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'style.min.css';
                $_sCSSPath  = ! $this->isDebugMode() && file_exists( $_sMinPath )
                    ? $_sMinPath
                    : $_sCSSPath;
                $_sURL     = $this->getSRCFromPath( $_sCSSPath );
                $_sHandle  = $this->___getStyleHandleID( $_aTemplate[ 'id' ] );
                wp_register_style( $_sHandle, $_sURL, array(), $_aTemplate[ 'version' ] );
                wp_enqueue_style( $_sHandle );
                
            }
            
        }
            /**
             * @var  string[]
             * @since 4.6.7
             */
            static private $___aStyleHandleIDs = array();

            /**
             * @param  string $sTemplateID
             * @since  4.6.7
             * @return string
             */
            private function ___getStyleHandleID( $sTemplateID ) {
                $_sHandleID  = 'amazon-auto-links-' . strtolower( basename( $sTemplateID ) );
                if ( ! in_array( $_sHandleID, self::$___aStyleHandleIDs, true ) ) {
                    self::$___aStyleHandleIDs[] = $_sHandleID;
                    return $_sHandleID;
                }
                $_sHandleID = $this->___getStringWithTrailingDigits( $_sHandleID, 2 );
                self::$___aStyleHandleIDs[] = $_sHandleID;
                return $_sHandleID;
            }
                private function ___getStringWithTrailingDigits( $sString, $iStartDigit=2 ) {
                    $_sStringOriginal = $sString;
                    $_sString         = preg_replace_callback( '/-\K(\d+)$/', array( $this, '___replyToIncrementMatchTailingDigits' ), $sString );
                    if ( $_sStringOriginal === $_sString ) {
                        return $_sString . '-' . $iStartDigit;
                    }
                    return $_sString;
                }
                    private function ___replyToIncrementMatchTailingDigits( $aMatches ) {
                        if ( isset( $aMatches[ 1 ] ) && is_numeric( $aMatches[ 1 ] ) ) {
                            return $aMatches[ 1 ] + 1;
                        }
                        return $aMatches[ 0 ];
                    }

        /**
         * Prints a style tag by joining all the custom CSS rules set in the active template options.
         * 
         * @since       3
         * @return      void
         */
        public function _replyToPrintActiveTemplateCustomCSSRules() {
            
            $_aCSSRules = array();
            
            // Retrieve 'custom_css' option value from all the active templates.
// @todo Add 'custom_css' field to all the template options.
            foreach( $this->_oTemplateOption->getActiveTemplates() as $_aTemplate ) {   
                $_aCSSRules[] = $this->getElement(
                    $_aTemplate,
                    'custom_css',
                    ''
                );
            }
                        
            $_sCSSRules = apply_filters(
                'aal_filter_template_custom_css',
                trim( implode( PHP_EOL, array_filter( $_aCSSRules ) ) )
            );
            if ( $_sCSSRules ) {
                echo "<style type='text/css' id='amazon-auto-links-template-custom-css'>"
                        . $_sCSSRules
                    . "</style>";
            }
            
        }
        
    /**
     * Stores loaded file paths so that PHP errors of including the same file multiple times can be avoided.
     * @deprecated  4.0.0   Not used anywhere.
     */
//    static public $_aLoadedFiles = array();
    
    /**
     * Includes activated templates' settings.php files.
     * @since       3
     * @since       4.0.0   Changed the timing to the `init` hook.
     */    
    private function ___loadSettingsOfActiveTemplates() {
        add_action( 'init', array( $this, 'replyToLoadSettings' ) );
    }
        public function replyToLoadSettings() {
            if ( ! is_admin() ) {
                return;
            }
            foreach( $this->_oTemplateOption->getActiveTemplates() as $_aTemplate ) {
                if ( ! isset( $_aTemplate[ 'dir_path' ] ) ) {
                    continue;
                }
                if ( file_exists( $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'settings.php' ) ) {
                    include_once( $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'settings.php' );
                }
            }
        }
  
}