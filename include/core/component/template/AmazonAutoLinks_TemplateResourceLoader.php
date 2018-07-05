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
        
        $this->_loadFunctionsOfActiveTemplates();
        $this->_loadStylesOfActiveTemplates();        
        $this->_loadSettingsOfActiveTemplates();             
        
    }

    /**
     * Includes activated templates' `functions.php` files.
     * @since       3
     */    
    private function _loadFunctionsOfActiveTemplates() {    
        foreach( $this->_oTemplateOption->getActiveTemplates() as $_aTemplate ) {
            if ( ! isset( $_aTemplate[ 'dir_path' ] ) ) {
                continue;
            }            
            $this->includeOnce(
                $_aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'functions.php'
            );
        }    
    }
    
    /**
     * 
     * @since       3
     */
    private function _loadStylesOfActiveTemplates() {
        add_action( 
            'wp_enqueue_scripts', 
            array( $this, '_replyToEnqueueActiveTemplateStyles' ) 
        );    
        add_action(
            'wp_head',
            array( $this, '_replyToPrintActiveTemplateCustomCSSRules' )
        );
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
            
            $_aCSSRUles = array();
            
            // Retrieve 'custom_css' option value from all the active templates.
// @todo Add 'custom_css' field to all the template options.
            foreach( $this->_oTemplateOption->getActiveTemplates() as $_aTemplate ) {   
                $_aCSSRUles[] = $this->getElement(
                    $_aTemplate,
                    'custom_css',
                    ''
                );
            }
                        
            $_sCSSRules = apply_filters(
                'aal_filter_template_custom_css',
                trim( implode( PHP_EOL, array_filter( $_aCSSRUles ) ) )
            );
            if ( $_sCSSRules ) {
                echo "<style type='text/css' id='amazon-auto-links-template-custom-css'>"
                        . $_sCSSRules
                    . "</style>";
            }
            
        }
        
    /**
     * Stores loaded file paths so that PHP errors of including the same file multiple times can be avoided.
     */
    static public $_aLoadedFiles = array();
    
    /**
     * Includes activated templates' settings.php files.
     * @since       3
     */    
    private function _loadSettingsOfActiveTemplates() {
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