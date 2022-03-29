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
 * A base class for web page scraper classes.
 * @since   3.8.12
 * @deprecated
 */
abstract class AmazonAutoLinks_WebScraper_Base extends AmazonAutoLinks_PluginUtility {

    protected $_sHTML = '';

    public function __construct( $sHTML ) {
        $this->_sHTML = $sHTML;
    }

    public function get() {
    }

}