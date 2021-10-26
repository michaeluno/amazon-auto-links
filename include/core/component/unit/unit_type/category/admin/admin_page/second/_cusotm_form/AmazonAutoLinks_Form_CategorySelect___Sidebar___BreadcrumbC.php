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
 * @sicne 4.6.13
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___BreadcrumbC extends AmazonAutoLinks_Form_CategorySelect___Sidebar___Breadcrumb {

    protected $_sClassSelectorSelected = 'zg-selected';

    /**
     * @param  $oNode
     * @since  4.6.13
     * @return boolean
     */
    protected function _isNotListRoot( $oNode ) {
        return false === strpos( $oNode->getAttribute( 'id' ), 'CardInstance' );
    }

}