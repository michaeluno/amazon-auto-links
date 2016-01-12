<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Provides method to render post contents in a single post page.
 * 
 * @package     Amazon Auto Links
 * @since       3
 * 
 */
class AmazonAutoLinks_PostType_Unit_PostContent extends AmazonAutoLinks_PostType_Unit_Action {
    
    /**
     * Prints out the fetched product links.
     * 
     * @remark          Used for the post type single page that functions as preview the result.
     * @since           3       Changed the name from `_replytToPrintPreviewProductLinks()`.
     * */
    public function content( $sContent ) {
    
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isPreviewVisible() ) {
            return $sContent;
        }
        
        $_sUnitType            = get_post_meta(
            $GLOBALS[ 'post' ]->ID,
            'unit_type',
            true
        );
        $_sUnitType            = $_sUnitType 
            ? $_sUnitType 
            : 'category';
        $_sUnitOptionClassName = "AmazonAutoLinks_UnitOption_" . $_sUnitType;
        $_oUnitOptions         = new $_sUnitOptionClassName( $GLOBALS['post']->ID );
        $_aUnitOptions         = $_oUnitOptions->get();
        return $sContent 
            . AmazonAutoLinks_Output::getInstance( $_aUnitOptions )->get();

    }    
   
}