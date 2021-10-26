<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A scratch class for WordPress posts.
 *  
 * @package     Auto Amazon Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Scratch_WordPress_Post extends AmazonAutoLinks_Scratch_Base {

    /**
     * Override this method.
     * @return mixed
     */
    public function scratch_nonExistentMetaKey_get_post_meta() {

        $_aPosts  = wp_get_recent_posts( array( 'numberposts' => 1 ), OBJECT );
        $_oPost   = reset( $_aPosts );
        $_mResult = get_post_meta( $_oPost->ID, '____non_existent_meta_key', true );
        return $this->_getDetails( $_mResult );

    }

}