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
 * Provides abstract methods for form fields.
 * 
 * @since           3  
 */
abstract class AmazonAutoLinks_FormFields_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores the option object.
     * @var AmazonAutoLinks_Option
     */
    public $oOption;

    /**
     * @var AmazonAutoLinks_TemplateOption
     */
    public $oTemplateOption;
    
    public function __construct() {
        
        $this->oOption         = AmazonAutoLinks_Option::getInstance();
        $this->oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        
    }
    
    /**
     * Should be overridden in an extended class.
     * 
     * @remark      Do not even declare this method as parameters will be vary 
     * and if they are different PHP will throw errors.
     */
    // public function get() {}
  
    /**
     * Returns the field IDs.
     * Used to filter protected meta keys.
     * @since       3.1.0
     * @return      array
     */
    public function getFieldIDs() {
        $_aIDs = array();
        foreach( $this->get() as $_aField ) {
            if ( isset( $_aField[ 'field_id' ] ) ) {                
                $_aIDs[] = $_aField[ 'field_id' ];
            }
        }
        return $_aIDs;
    }
  
}