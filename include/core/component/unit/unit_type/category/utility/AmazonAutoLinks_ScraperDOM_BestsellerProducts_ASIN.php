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
 * A class that extracts product ASINs from Amazon best seller pages.
 * @since   3.8.12
 */
class AmazonAutoLinks_ScraperDOM_BestsellerProducts_ASIN extends AmazonAutoLinks_ScraperDOM_BestsellerProducts_Base {

    /**
     * @return array    An array holding found ASINs.
     */
    public function get() {
        $_aASINs = array();
        foreach( $this->_oItemNodes as $_oItemNode ) {
            $_sLink    = $this->_getProductLink( $this->_oXPath, $_oItemNode );
            $_aASINs[] = AmazonAutoLinks_Unit_Utility::getASINFromURL( $_sLink );
        }
        return $_aASINs;
    }

}