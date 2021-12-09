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
 * Provides methods to generate breadcrumb of selected categories.
 *
 * @since 5.0.4
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___BreadcrumbD extends AmazonAutoLinks_Form_CategorySelect___Sidebar___BreadcrumbC {

    protected $_sClassSelectorSelected = '_zg-selected';

    /**
     * @param  $oNode
     * @return boolean
     * @since  5.0.4
     */
    protected function _isParentNode( $oNode ) {
        return false !== strpos( $oNode->getAttribute( 'class' ), 'zg-browse-group' );
    }

    /**
     * @param  $oNode
     * @return string
     * @since  5.0.4
     */
    protected function _getListItemText( $oNode ) {
        $_nodeUpperUl  = $oNode->parentNode;
        $_oXPath       = new DOMXPath( $this->_oDoc );
        $_oNodes       = $_oXPath->query( './/div[contains(@class, "zg-browse-item")]/a', $_nodeUpperUl );
        $_nodeA        = $_oNodes->item( 0 );
        return trim( $_nodeA->nodeValue );
    }

}