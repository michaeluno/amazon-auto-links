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
 * WordPress-related general tests.
 *  
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_WordPress extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @purpose Checks if it is in the admin area.
     * @return bool
     */
    public function test_is_admin() {
        return is_admin();
    }

    /**
     * @purpose Checks if it is in the admin area.
     * @return bool
     */
    public function test_doingAjax() {
        return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
    }

    /**
     * @purpose Checks if it is in the admin area.
     * @return bool
     */
    public function test_isAjaxPage() {
        return 'admin-ajax.php' === $GLOBALS[ 'pagenow' ];
    }

}