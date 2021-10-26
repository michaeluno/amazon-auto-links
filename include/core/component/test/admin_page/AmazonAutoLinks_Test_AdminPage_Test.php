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
 * Adds the `Test` page.
 * 
 * @since       4.3.0
 */
class AmazonAutoLinks_Test_AdminPage_Test extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return  array
     * @since   4.3.0
     */
    protected function _getArguments() {
        return array(
            'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'test' ],
            'title'     => 'Tests',
            'order'     => 99999, // to be the last menu item
//            'style'     => array(
//                AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',
//                AmazonAutoLinks_Registry::getPluginURL( '/asset/css/code.css' ),
//            ),
            'script'    => array(
                'src'   => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/accordion.js',
            ),
        );
    }

    /**
     * @param           AmazonAutoLinks_AdminPage $oFactory
     * @callback        action      load_{page slug}
     */    
    public function replyToLoadPage( $oFactory ) {

        // Auto-load test classes. Even though these are not used,
        // listing test classes requires to check parent classes
        // and it involves instantiation of them with the reflection class.
        new AmazonAutoLinks_AdminPageFramework_RegisterClasses( array(), array(), include( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/class-map.php' ) );

        // Tabs
        new AmazonAutoLinks_Test_AdminPage_Test_Tests( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_Test_AdminPage_Test_Scratch( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_Test_AdminPage_Test_Delete( $this->oFactory, $this->sPageSlug );

        $this->_doPageSettings( $oFactory );
        
    }
    
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        // Disable the setting notice in the next page load.
        $oFactory->setSettingNotice( '' );
        return $aOldInputs;

    }
}
