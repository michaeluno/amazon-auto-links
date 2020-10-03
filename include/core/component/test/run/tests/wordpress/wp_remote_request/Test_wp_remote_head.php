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
 * Tests wp_remote_head()
 *
 * @package Amazon Auto Links
 * @since   4.3.4
 * @see     wp_remote_head()
 * @tags    http, wp_remote_head
*/
class Test_wp_remote_head extends AmazonAutoLinks_UnitTest_HTTPRequest_Base {



    /**
     * @return string
     * @tags head
     */
    public function test_uk_widget_page() {
        return $this->___requestHTTP( 'https://www.amazon.co.uk/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin=B01B8R6V2E' );
    }
        private function ___requestHTTP( $sURL ) {
            $_aArguments = array(
                'user-agent'  => 'WordPress/5.5.1',
            );
            $_aoResponse3 = wp_remote_head( $sURL, $_aArguments );
            $this->_outputDetails( 'wp_remote_head()', $this->sLastRequestURL, $this->aLastArguments, $this->aoLastResponse );
            return $this->hasPrefix( '2', $this->getElement( $_aoResponse3, array( 'response', 'code' ) ) );
        }

    /**
     * @return string
     * @tags head
     */
    public function test_amazon_dot_com() {
        return $this->___requestHTTP( 'https://www.amazon.com' );
    }

    /**
     * @return string
     * @tags head
     */
    public function test_wordpress_dot_org() {
        return $this->___requestHTTP( 'https://wordpress.org' );
    }

}