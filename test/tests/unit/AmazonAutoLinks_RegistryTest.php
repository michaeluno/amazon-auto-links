<?php

/*
 $this->assertEquals()
$this->assertContains()
$this->assertFalse()
$this->assertTrue()
$this->assertNull()
$this->assertEmpty()
*/

class AmazonAutoLinks_RegistryTest extends \Codeception\Test\Unit {

    public function testGetPluginURL() {

    }

    public function testSetAdminNotice() {

    }

    public function testSetUp() {

        AmazonAutoLinks_Registry::$sDirPath = '';

        AmazonAutoLinks_Registry::setUp();
        $this->assertEquals(
            dirname( AmazonAutoLinks_Registry::$sFilePath ),
            AmazonAutoLinks_Registry::$sDirPath
        );

    }

    public function testReplyToShowAdminNotices() {

    }

    public function testRegisterClasses() {

        $_aClassFiles = $this->getStaticAttribute( 'AmazonAutoLinks_Registry', '___aAutoLoadClasses' );
        AmazonAutoLinks_Registry::registerClasses( $_aClassFiles );
        $this->assertAttributeEquals( $_aClassFiles , '___aAutoLoadClasses', 'AmazonAutoLinks_Registry' );

        $_aClassFiles = array( 'SomeClass' => 'SomeClass.php' );
        AmazonAutoLinks_Registry::registerClasses( $_aClassFiles );
        $this->assertAttributeNotEquals(
            $_aClassFiles ,
            '___aAutoLoadClasses',
            'AmazonAutoLinks_Registry'
        );

        $this->assertArrayHasKey(
            'SomeClass',
            $this->getStaticAttribute( 'AmazonAutoLinks_Registry', '___aAutoLoadClasses' ),
            'The key just set does not exist.'
        );

    }

    public function testReplyToLoadClass() {

        $this->assertFalse(
            class_exists( 'JustAClass' ),
            'The JustAClass class must not exist at this stage.'
        );
        include( codecept_root_dir() . '/tests/include/class-list.php' );
        AmazonAutoLinks_Registry::registerClasses( $_aClassFiles );
        $this->assertTrue(
            class_exists( 'JustAClass' ),
            'The class auto load failed with the AmazonAutoLinks_Registry::registerClasses() method.'
        );

    }

}
