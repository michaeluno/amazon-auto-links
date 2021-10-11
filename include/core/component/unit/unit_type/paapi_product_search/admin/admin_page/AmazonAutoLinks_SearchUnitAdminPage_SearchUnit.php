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
 * Adds a setting page.
 * 
 * @since       3
 */
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'paapi_search_unit' ],
            'title'         => __( 'Add Unit by PA-API', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/image/icon/screen_icon_32x32.png", true ),
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
            'script'        => array(
                array(
                    'src'           => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/accordion.js',
                    'dependencies'  => array( 'jquery', 'jquery-ui-accordion', ),
                    'in_footer'     => true,
                ),
            ),
        );
    }

    /**
     *
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback    add_action()      load_{page slug}
     */
    protected function _loadPage( $oFactory ) {

        AmazonAutoLinks_Unit_Admin_Utility::checkAPIKeys( $oFactory );
        
        // Tabs
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_First( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_search_products( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_item_lookup( $this->oFactory, $this->sPageSlug );
 
        $this->___doPageSettings();
        
    }

        private function ___doPageSettings() {
            
            $this->oFactory->setPageHeadingTabsVisibility( false );
            $this->oFactory->setPageTitleVisibility( false ); 
            $this->oFactory->setInPageTabsVisibility( false );
            
        }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _doAfterPage( $oFactory ) {
        if ( ! AmazonAutoLinks_Option::getInstance()->isDebug( 'back_end' ) ) {
            return;
        }
        $_aTableArguments = array(
            'table' => array(
                'class' => 'widefat striped fixed width-full',
            ),
            'td'    => array(
                array( 'class' => 'width-one-fifth', ),  // first td
            )
        );
        echo "<h3>Debug</h3>";
        echo "<div class='aal-accordion'>"
            . "<h4>Last Inputs</h4>"
            . $this->getTableOfArray(
                get_user_meta( get_current_user_id(), AmazonAutoLinks_Registry::$aUserMeta[ 'last_inputs' ], true ),
                $_aTableArguments
            )
            . "</div>";
        echo "<div class='aal-accordion'>"
            . "<h4>Unit Options</h4>"
            . $this->getTableOfArray( $oFactory->oProp->aOptions, $_aTableArguments )
            . "</div>";
    }
        
}
