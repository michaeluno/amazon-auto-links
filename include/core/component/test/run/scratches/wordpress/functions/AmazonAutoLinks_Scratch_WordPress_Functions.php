<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * A scratch class for WordPress functions defined in functions.php.
 *  
 * @since       4.6.4
*/
class AmazonAutoLinks_Scratch_WordPress_Functions extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpse Tests add_query_arg()
     * @return mixed
     */
    public function scratch_add_query_arg() {
        $_sURL = add_query_arg(
            array(
                'a' => 'aaa',
                'b' => 'bbb',
            ),
            'https://wordpress.org'
        );
        return $this->_getDetails( $_sURL );
    }

    /**
     * @purpse Tests add_query_arg()
     * @return mixed
     */
    public function scratch_add_query_arg_02() {
        $_sURL = add_query_arg(
            array(
                'url'        => 'https://www.amazon.co.jp/gp/bestsellers/',
                'output'     => 'png',
                'cookies'    => array(
                    '0' => array(
                        'name'  => 'lc-acbjp',
                        'value' => 'ja_JP'
                    ),
                )
            ),
            'https://web-page-dumper-dev.herokuapp.com/www/'
        );
        return $this->_getDetails( $_sURL );
    }

}