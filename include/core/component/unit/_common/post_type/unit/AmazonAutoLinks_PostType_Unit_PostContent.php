<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Provides method to render post contents in a single post page.
 * 
 * @since 3
 * 
 */
class AmazonAutoLinks_PostType_Unit_PostContent extends AmazonAutoLinks_PostType_Unit_ListTable {
    
    /**
     * Prints out the fetched product links.
     *
     * @param  string  $sContent The post content to filter.
     * @remark Used for the post type single page that functions as preview the result.
     * @since  3       Changed the name from `_replyToPrintPreviewProductLinks()`.
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
            . apply_filters( 'aal_filter_output', array( 'id' => $GLOBALS[ 'post' ]->ID ) );
    }
   
}