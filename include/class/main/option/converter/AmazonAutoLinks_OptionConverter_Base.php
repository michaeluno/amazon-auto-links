<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Provides shared class members for option converter classes.
 * 
 * @since       3
 */
class AmazonAutoLinks_OptionConverter_Base extends AmazonAutoLinks_WPUtility {

    public $aOptions = array();

    /**
     * Sets up properties.
     */
    public function __construct( $aOptions ) {
        $this->aOptions = $aOptions;
    }
    
    /**
     * 
     * @since       3
     * @return      array
     */
    public function get() {
        return $this->aOptions;
    }
    
    /**
     * @since       3
     * @remark      As of v3, the template options were separated.
     * @return      array
     */
    public function getTemplateOptions() {
        return array();        
    }

}