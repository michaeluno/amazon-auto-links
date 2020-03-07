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
     * @param       string  $sContent       The post content to filter.
     * @remark          Used for the post type single page that functions as preview the result.
     * @since           3       Changed the name from `_replytToPrintPreviewProductLinks()`.
     * */
    public function content( $sContent ) {
    
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isPreviewVisible() ) {
            return $sContent;
        }

        if ( ! in_the_loop() ) {
            return $sContent;
        }

        return $sContent
            . AmazonAutoLinks( array( 'id' => $GLOBALS[ 'post' ]->ID ), false );

    }    
   
}