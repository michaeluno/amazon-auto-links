<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds a tab to a setting page.
 * 
 * @since 3
 */
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_search_products extends AmazonAutoLinks_Unit_UnitType_Admin_Tab_SearchUnit_Second_Base {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => 'search_products',
            'title'         => __( 'Add Unit by PA-API Product Search', 'amazon-auto-links' ),
            'description'   => __( 'Create a search unit.', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @since  3
     * @since  5.0.0   Changed the visibility scope from private to protected.
     */
    protected function _getFormFieldClasses() {
        return array(
            'AmazonAutoLinks_FormFields_SearchUnit_ProductSearch',
            'AmazonAutoLinks_FormFields_Unit_Common',
            'AmazonAutoLinks_FormFields_Unit_Credit',
            'AmazonAutoLinks_FormFields_Unit_AutoInsert',
            'AmazonAutoLinks_FormFields_SearchUnit_CreateButton',
        );
    }

}