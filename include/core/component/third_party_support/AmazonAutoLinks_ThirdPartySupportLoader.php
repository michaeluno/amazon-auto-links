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
 * Loads the component Third Party Support
 *
 * @since        4.1.0
 */
class AmazonAutoLinks_ThirdPartySupportLoader {
        
    public function __construct() {

        new AmazonAutoLinks_AALBSupportLoader;      // 3.11.0
        new AmazonAutoLinks_PhpZonSupportLoader;    // 4.1.0

    }

}