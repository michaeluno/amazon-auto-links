<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
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
     * @return          string
     */
    protected function _getCategoryList( $oSimpleDOM, $sPageURL ) {

        $_oNodeBrowseRoot = $oSimpleDOM->getElementById( $this->_sSelector );
        $this->_setHrefs( $_oNodeBrowseRoot, $sPageURL );

        // Remove headings
        foreach( $_oNodeBrowseRoot->find( 'h3' ) as $_nodeH3 ) {
            $_nodeH3->outertext = '';
        }

        // Enclose each `<a>` in `<li>`.
        foreach( $_oNodeBrowseRoot->find( 'a' ) as $_nodeA ) {
            $_nodeA->outertext = '<li>' . $_nodeA->outertext . '</li>';
        }

        return "<ul>"
                . $_oNodeBrowseRoot->outertext // the sidebar html code
            . "</ul>";

    }
}