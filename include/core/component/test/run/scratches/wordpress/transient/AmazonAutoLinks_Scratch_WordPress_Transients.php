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
 * A scratch class for WordPress transients.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
 * @tags        transient
*/
class AmazonAutoLinks_Scratch_WordPress_Transients extends AmazonAutoLinks_Scratch_Base {

    private $___sTransientKey = 'aal_test_transient_key';

    private $___aTestData = array(
        'foo' => 'bar',
    );

    public function scratch_wp_using_ext_object_cache() {
        return wp_using_ext_object_cache();
    }

    public function scratch_getTransientWithoutCache() {
        $_iNow = time();
        $this->setTransient( $this->___sTransientKey, array( 'time' => $_iNow ) + $this->___aTestData, 2 );
//        sleep( 1 );
        return $this->getTransientWithoutCache( $this->___sTransientKey, "EXPIRED!" );

    }


}