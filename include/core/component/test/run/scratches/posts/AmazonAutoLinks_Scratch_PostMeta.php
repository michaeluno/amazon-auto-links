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
 * A scratch class that displays post meta
 *  
 * @since       4.6.6
*/
class AmazonAutoLinks_Scratch_PostMeta extends AmazonAutoLinks_Scratch_Base {

    /**
     * @tags post, post-meta
     * @purpose Display post meta data.
     */
    public function scratch_displayPostMeta() {
        $_aParameters = func_get_args();
        $this->_outputDetails( 'Parameters', $_aParameters );
        foreach( $_aParameters as $_nPostID ) {
            if ( ! $_nPostID ) {
                continue;
            }
            $_iPostID   = ( integer ) $_nPostID;
            $_aPostMeta = $this->getPostMeta( $_iPostID );
            $this->_outputDetails( get_the_title( $_iPostID ), $_aPostMeta );
        }
    }

}