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
class AmazonAutoLinks_Test_AdminPage_Test_Scratch extends AmazonAutoLinks_Test_AdminPage_Test_Tests {

    /**
     * @return  array
     * @since   4.3.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'scratches',
            'title'     => 'Scratches',
        );
    }

    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    protected function _loadTab( $oAdminPage ) {
        parent::_loadTab( $oAdminPage );
    }

    /**
     * @return array
     */
    protected function _getTagLabelsForCheckBox() {
        return $this->_getTagLabels( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/scratches', array( 'AmazonAutoLinks_Scratch_Base' ) );
    }


    /**
     * Write scratches here to test something.
     * @callback        action      do_{page slug}_{tab slug}
     */
    protected function _doTab( $oFactory ) {
        parent::_doTab( $oFactory );
//        echo "<h3>Scratches for Tests</h3>";

//        $this->___testOverrideRow();
//        $this->___testOverrideRows();
//        $this->___testGetRows();

    }
        protected function _printFiles() {
            echo "<div class='files-container'>";
            echo "<h4>Files</h4>";
            $_oFinder = new AmazonAutoLinks_Test_ClassFinder( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/scratches', array( 'AmazonAutoLinks_Scratch_Base' ) );
            AmazonAutoLinks_Debug::dump( $_oFinder->getFiles() );
            echo "</div>";
        }


        private function ___testOverrideRows() {
            echo "<h3>DB Set Multiple Rows in a Single Query</h3>";
            $_oProducts      = new AmazonAutoLinks_DatabaseTable_aal_products;
            $_aRows          = array(
                array(
                    'product_id'            => 'B00VLN9IC6|IT|EUR|it_IT',
                    'title'                 => 'B00VLN9IC6 no. 18'
                ),
                array(
                    'product_id'            => 'B07K6R41FH|IT|EUR|it_IT',
                    'title'                 => 'B00VLN9IC6 no. 4'
                ),
            );
            $_result = $_oProducts->setRows( $_aRows );
            echo "<h4>Passed Rows</h4>";
            var_dump( $_aRows );
            echo "<h4>Result</h4>";
            var_dump( $_result );
            echo "<h4>Database Query</h4>";
            echo "<pre>"
                    . var_dump( $GLOBALS[ 'wpdb' ]->last_query )
                . "</pre>";
        }
        private function ___testOverrideRow() {
            echo "<h3>DB Override Row</h3>";
            $_oProducts      = new AmazonAutoLinks_DatabaseTable_aal_products;
            $_aRow           = array(
                'object_id'             => 476,
                'asin_locale'           => 'B00VLN9IC6_IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => '19!!!!'
            );
//            $_aKeysToCheck = array( 'asin_locale', 'language', 'preferred_currency', 'object_id' );
            $_result = $_oProducts->setRow( $_aRow );

            echo "<h4>Passed Row</h4>";
            var_dump( $_aRow );
//            echo "<h4>Duplicate Keys</h4>";
//            var_dump( $_aKeysToCheck );
            echo "<h4>Result</h4>";
            var_dump( $_result );
            echo "<h4>Database Query</h4>";
            echo "<pre>"
                    . var_dump( $GLOBALS[ 'wpdb' ]->last_query )
                . "</pre>";
        }
        private function ___testGetRows() {
            echo "<h3>DB Override Row</h3>";
            $_aASINLocales = array(
                // $_sASINLocaleCurLang => 'asin locale'
                'B00VLN9IC6|IT|EUR|it_IT' => array(
                    'asin'      => 'B00VLN9IC6',
                    'locale'    => 'IT',
                    'currency'  => 'EUR',
                    'language'  => 'it_IT',
                ),
            );
            $_oProducts = new AmazonAutoLinks_ProductDatabase_Rows( $_aASINLocales, 'EUR', 'it_IT' );
            $_aResult = $_oProducts->get();
            var_dump( $_aResult );
        }
            
}