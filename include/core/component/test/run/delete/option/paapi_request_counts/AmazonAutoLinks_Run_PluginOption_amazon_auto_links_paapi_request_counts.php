<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A scratch class to manage plugin options.
 *  
 * @package     Amazon Auto Links
 * @since       4.4.0
*/
class AmazonAutoLinks_Run_PluginOption_amazon_auto_links_paapi_request_counts extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Removes the `paapi_request_counts` key from the option array.
     * @tags paapi_request_counts
     */
    public function scratch_reset_paapi_request_counts() {
        $_aMainOptions = get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], array() );
        unset( $_aMainOptions[ 'paapi_request_counts' ] );
        update_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], $_aMainOptions );
    }

}