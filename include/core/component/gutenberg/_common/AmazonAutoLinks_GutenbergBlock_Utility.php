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
 * A set of utility methods for the Gutenberg block component.
 * @since 5.1.0
 */
class AmazonAutoLinks_GutenbergBlock_Utility extends AmazonAutoLinks_Utility {

    /**
     * Checks whether the current HTTP request is from the back-end, meaning from the Gutenberg editor.
     * @return boolean
     * @since  5.1.0
     */
    static public function isBackendRequest() {
        return defined('REST_REQUEST')
            && true === REST_REQUEST
            && 'edit' === filter_input( INPUT_GET, 'context', FILTER_SANITIZE_STRING );
    }

}