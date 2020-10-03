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
 * @since 4.3.4
 * @tags database product
 * @see AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140
 */
class Test_AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140 extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @var AmazonAutoLinks_DatabaseTable_aal_products
     */
    public $oTable;

    /**
     * Test_AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140 constructor.
     */
    public function __construct() {
        $this->oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
    }

    public function test____getCountOfEmptyCell_product_id() {
        $_aRows  = array(
            array(
                'product_id'            => '',
                'asin_locale'           => 'B00VLN9IC6_IT',
                'asin'                  => '',
                'locale'                => 'IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => 'TESTING ROW 1'
            ),
        );
        $this->oTable->setRows( $_aRows );
        $_oMockClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140' );
        return 1 === $_oMockClass->call( '___getCountOfEmptyCell', array( $this->oTable, 'product_id' ) );
    }
    public function test____getCountOfEmptyCell_asin() {
        $_oMockClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140' );
        return 1 === $_oMockClass->call( '___getCountOfEmptyCell', array( $this->oTable, 'asin' ) );
    }
    public function test____updateColumn_product_id() {
        $_oMockClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140' );
        $_mResult = $_oMockClass->call( '___updateProductIDColumn', array( $this->oTable ) );
//        $this->_output( $this->_getDetails( $_mResult ) );
        return null === $_mResult;
    }
    public function test____updateColumn_asin() {
        $_oMockClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140' );
        $_mResult = $_oMockClass->call( '___updateASINColumn', array( $this->oTable ) );
//        $this->_output( $this->_getDetails( $_mResult ) );
        return null === $_mResult;
    }

    /**
     *
     */
    public function test_deleteAndCheck() {
        $this->oTable->delete( array( 'product_id' => 'B00VLN9IC6|IT|EUR|it_IT' ) );
        $this->oTable->delete( array( 'asin_locale' => 'B00VLN9IC6_IT' ) );
        $_oMockClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140' );
        return 0 === $_oMockClass->call( '___getCountOfEmptyCell', array( $this->oTable, 'product_id' ) );
    }

    public function test_replyToDoAction() {
        $_oMockClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140' );
        $_oMockClass->call( 'replyToDoAction' );
        return true;
    }

}