<?php
/**
 * Provides the definitions of form fields for the category type unit.
 * 
 * @since            2.0.0
 * @remark            The admin page and meta box access it.
 */
 
abstract class AmazonAutoLinks_Form {

    protected $strPageSlug = '';
    
    function __construct( $strPageSlug='' ) {    
    
        $this->strPageSlug = $strPageSlug ? $strPageSlug : $this->strPageSlug;
        $this->oUserAds = isset( $GLOBALS['oAmazonAutoLinksUserAds'] ) ? $GLOBALS['oAmazonAutoLinksUserAds'] : new AmazonAutoLinks_UserAds;    
        $this->oOption = $GLOBALS['oAmazonAutoLinks_Option'];
        
    }
    
    public function getSections() {}

    public function getFields( $strSectionID='', $strPrefix='' ) {}
    
    
}