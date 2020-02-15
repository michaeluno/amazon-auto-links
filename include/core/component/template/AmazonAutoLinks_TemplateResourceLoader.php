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
 * Loads template components such as style.css, template.php, functions.php etc.
 *  
 * @package     Amazon Auto Links
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
    }
        public function replyToLoadFunctions() {
            foreach( $this->_oTemplateOption->getActiveTemplates() as $_aTemplate ) {
                if ( ! isset( $_aTemplate[ 'dir_path' ] ) ) {
                    continue;
                }
                $this->includeOnce( $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'functions.php' );
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
            foreach( $this->_oTemplateOption->getActiveTemplates() as $_aTemplate ) {
                
                $_sCSSPath = $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'style.css';
                $_sCSSURL  = $this->getSRCFromPath( $_sCSSPath );
                wp_register_style( "amazon-auto-links-{$_aTemplate[ 'id' ]}", $_sCSSURL );
                wp_enqueue_style( "amazon-auto-links-{$_aTemplate[ 'id' ]}" );        
                
            }
            
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
                $this->includeOnce( $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'settings.php' );
            }
        }
        

  
}