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
 * Provides shared methods for the category select form.
 * 
 */
abstract class AmazonAutoLinks_Form_CategorySelect__Base extends AmazonAutoLinks_Form_CategorySelect__Utility {

    /**
     * Sets up basic properties.
     */
    public function __construct() {
        
        $this->sCharEncoding = get_bloginfo( 'charset' );
        $this->oDOM          = new AmazonAutoLinks_DOM;        
        
        $_aParams = func_get_args();
        call_user_func_array(
            array( $this, 'construct' ),
            $_aParams
        );
    }
    
    /**
     * User constructor.
     */
    public function construct() {}
    

    /**
     * Checks whether the category item limit is reached.
     * 
     */
    protected function _isNumberOfCategoryReachedLimit( $iNumberOfCategories ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return ( boolean ) $_oOption->isReachedCategoryLimit( 
            $iNumberOfCategories
        );            
    }   
    
}
        