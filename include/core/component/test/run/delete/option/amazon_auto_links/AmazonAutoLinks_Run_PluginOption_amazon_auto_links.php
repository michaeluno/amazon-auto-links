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
 * A scratch class to manage plugin options.
 *  
 * @since       4.4.0
*/
class AmazonAutoLinks_Run_PluginOption_amazon_auto_links extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Removes main options with the given dimensional keys.
     * @tags main
     */
    public function scratch_reset() {
        $_aParameters  = func_get_args();
        $_aMainOptions = get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], array() );
        $this->unsetDimensionalArrayElement( $_aMainOptions, $_aParameters );
        update_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], $_aMainOptions );
    }

}