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
 * Adds a setting page for creating tag units.
 * 
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_UnitType_AdminPages_Page_ad_widget_search extends AmazonAutoLinks_Unit_UnitType_Admin_Page_UnitCreationWizardBase {

    /**
     * @var string
     * @since 5.0.0
     */
    public $sUnitType = 'ad_widget_search';

    /**
     * @return array
     * @since  5.0.0
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'search_unit' ],
            'title'         => __( 'Add Unit by Search', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/image/icon/screen_icon_32x32.png", true ),
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
            'order'         => 41,
        );
    }

    /**
     * @return  array
     * @since   5.0.0
     */
    protected function _getSectionArguments() {
        return array(
            'tab_slug'      => $this->sTabSlug,
            'section_id'    => '_default',
            'description'   => array(
                __( 'The search unit type allows you to search products with keywords.', 'amazon-auto-links' ),
            ),
        );
    }

    /**
     * @since  5.0.0
     * @return array
     */
    protected function _getFormFieldClasses() {
        return array(
            'AmazonAutoLinks_FormFields_AdWidgetSearchUnit_Main',
            'AmazonAutoLinks_FormFields_Unit_Common',
            'AmazonAutoLinks_FormFields_Unit_Credit',
            'AmazonAutoLinks_FormFields_Unit_AutoInsert',
            'AmazonAutoLinks_FormFields_AdWidgetSearchUnit_Submit',
        );
    }

}