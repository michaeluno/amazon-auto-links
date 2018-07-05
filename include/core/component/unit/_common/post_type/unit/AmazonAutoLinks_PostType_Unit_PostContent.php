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
 * Provides method to render post contents in a single post page.
 * 
 * @package     Amazon Auto Links
 * @since       3
 * 
 */
class AmazonAutoLinks_PostType_Unit_PostContent extends AmazonAutoLinks_PostType_Unit_ListTable {
    
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
        
//        $_sUnitType            = get_post_meta(
//            $GLOBALS[ 'post' ]->ID,
//            'unit_type',
//            true
//        );
//        $_sUnitType            = $_sUnitType
//            ? $_sUnitType
//            : 'category';
//        $_sUnitOptionClassName = "AmazonAutoLinks_UnitOption_" . $_sUnitType;
//        $_oUnitOptions         = new $_sUnitOptionClassName( $GLOBALS['post']->ID );
//        $_aUnitOptions         = $_oUnitOptions->get();
$_aUnitOptions = array( 'id' => $GLOBALS['post']->ID );
        return $sContent
            . AmazonAutoLinks_Output::getInstance( $_aUnitOptions )->get();

    }    
   
}