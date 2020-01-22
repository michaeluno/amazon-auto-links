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
 * Provides methods to extract and construct category list of the given page.
 *
 * @sicne       3.5.7
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryListB extends AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList {

    protected $_sSelector = 'crown-category-nav';

    /**
     * Generates an HTML output of the node tree list.
     *
     * @since           3.5.7
     * @since           3.9.1   No longer uses PHP Simple DOM Parser
     * @return          string
     */
    protected function _getCategoryList( DOMDocument $oDoc, $sPageURL ) {

        $_oNodeBrowseRoot = $oDoc->getElementById( $this->_sSelector );
        $this->_setHrefs( $_oNodeBrowseRoot, $sPageURL );

        // Remove headings
        $_oXpath        = new DOMXPath( $oDoc );
        foreach( $_oXpath->query( "//*/h3", $_oNodeBrowseRoot ) as $_nodeH3 ) {
            $_nodeH3->parentNode->removeChild( $_nodeH3 );
        }

        // Enclose each `<a>` in `<li>`.
        foreach( $_oXpath->query( "//*/a", $_oNodeBrowseRoot ) as $_nodeA ) {
            $_sNodeAHTML = $oDoc->saveXml( $_nodeA, LIBXML_NOEMPTYTAG );
            $_nodeNewLi  = $oDoc->createElement('li', $_sNodeAHTML );
            $oDoc->replaceChild( $_nodeNewLi, $_nodeA );
        }

        return "<ul>"
               . $oDoc->saveXml( $_oNodeBrowseRoot, LIBXML_NOEMPTYTAG ) // the sidebar html fragment
            . "</ul>";

    }
}