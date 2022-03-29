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
 * Provides methods to extract and construct category list of the given page.
 *
 * @since 5.0.4
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryListD extends AmazonAutoLinks_Form_CategorySelect___Sidebar___CategoryList {

    protected $_sSelector = 'zg-left-col';

    /**
     * @since   3.5.7
     * @return  string
     */
    public function get() {
        $_sCategoryListHTML = parent::get();
        if ( ! $_sCategoryListHTML ) {
            return $_sCategoryListHTML;
        }
        $_aAllowedTags      = $GLOBALS[ 'allowedposttags' ];
        $this->unsetDimensionalArrayElement( $_aAllowedTags, array( 'div', 'style' ) );
        $_sCategoryListHTML = wp_kses( $_sCategoryListHTML, $_aAllowedTags );
        return force_balance_tags( $_sCategoryListHTML );
    }

}