<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Provides abstract methods for form fields.
 * 
 * @since           3  
 */
abstract class AmazonAutoLinks_FormFields_Base extends AmazonAutoLinks_WPUtility {

    /**
     * Stores the option object.
     */
    public $oOption;
    
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
  
}