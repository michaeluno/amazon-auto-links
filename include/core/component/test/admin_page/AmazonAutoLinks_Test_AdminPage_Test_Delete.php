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
 * Adds an in-page tab to an admin page.
 * 
 * @since       4.3.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_Test_AdminPage_Test_Delete extends AmazonAutoLinks_Test_AdminPage_Test_Tests {

    /**
     * @return  array
     * @since   4.3.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'delete',
            'title'     => 'Delete',
        );
    }

    protected function _loadTab( $oAdminPage ) {
        $oAdminPage->addSettingFields(
            '_default',
            array(
                'title'     => '<span class="error">Danger Zone</span>',
                'content'   => '<p class="description"><span class="error">The plugin database tables will be deleted.</span></p>',
                'field_id'  => '_danger_zone',
                'save'      => 'false',
            )
        );
        parent::_loadTab( $oAdminPage );
    }

    /**
     * @return array
     */
    protected function _getTagLabelsForCheckBox() {
        return $this->_getTagLabels( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/delete', array( 'AmazonAutoLinks_Scratch_Base' ) );
    }

    protected function _printFiles() {
        echo "<div class='files-container'>";
        echo "<h4>Files</h4>";
        $_oFinder = new AmazonAutoLinks_Test_ClassFinder( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/delete', array( 'AmazonAutoLinks_Scratch_Base' ) );
        AmazonAutoLinks_Debug::dump( $_oFinder->getFiles() );
        echo "</div>";
    }
            
}