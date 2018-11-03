<?php

class BootstrapTest extends \Codeception\Test\Unit {

    public function _before()
    {
    }

    public function testConstantDOING_TEST() {
        if ( ! defined( 'DOING_TESTS' ) ) {
            $I->fail( 'The DOING_TESTS not defined.' );
        }
    }
    public function testConstantABSPATH() {
        if ( ! defined( 'ABSPATH' ) ) {
            $I->fail( 'The ABSPATH not defined.' );
        }
    }

}
