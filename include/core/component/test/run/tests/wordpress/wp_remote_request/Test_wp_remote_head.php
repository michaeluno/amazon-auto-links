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
 * Tests wp_remote_head()
 *
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
        return $this->___requestHTTPRemoteHead( 'https://www.amazon.co.uk/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin=B01B8R6V2E' );
    }
        private function ___requestHTTPRemoteHead( $sURL ) {
            $_aArguments = array(
                'user-agent'  => 'WordPress/' . $GLOBALS[ 'wp_version' ],
            );
            $_aoResponse3 = wp_remote_head( $sURL, $_aArguments );
            $this->_outputDetails( 'wp_remote_head()', $this->sLastRequestURL, $this->aLastArguments, $this->aoLastResponse );
            $_bResult = $this->hasPrefix( '2', $this->getElement( $_aoResponse3, array( 'response', 'code' ) ) );
            if ( ! $_bResult ) {
                $_aoResponse = wp_remote_get( $sURL, $_aArguments );
                $this->_output( 'HTML Raw Body' );
                $this->_output( $this->_getHTMLBody( wp_remote_retrieve_body( $_aoResponse ) ) );
            }
            return $_bResult;
        }

    /**
     * @return string
     * @tags head
     * @remark this always fails.
     * @depreacated
     */
    // public function test_amazon_dot_com() {
    //     return $this->___requestHTTPRemoteHead( 'https://www.amazon.com' );
    // }

    /**
     * @return string
     * @tags head
     */
    public function test_wordpress_dot_org() {
        return $this->___requestHTTPRemoteHead( 'https://wordpress.org' );
    }

}