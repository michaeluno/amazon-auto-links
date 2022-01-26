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
 * Loads the Gutenberg Block component.
 */
class AmazonAutoLinks_GutenbergBlock_Loader {
    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        new AmazonAutoLinks_GutenbergBlock_UnitBlock;
    }

}