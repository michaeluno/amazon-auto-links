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
 * A scratch class that displays options.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Scratch_DisplayOptions extends AmazonAutoLinks_Scratch_Base {

    /**
     * @tags display, option keys, keys, key
     */
    public function scratch_displayPluginOptionKeys() {
        array_walk_recursive( AmazonAutoLinks_Registry::$aOptionKeys, array( $this, '___replyToDisplayOptions' ) );
    }
        private function ___replyToDisplayOptions( $sOptionKey, $sRegistryKey ) {
            $this->_output( $sRegistryKey . ': ' .  $sOptionKey );
        }

    /**
     * @purpose Display options.
     * @tags    options, values, value, option values
     */
    public function scratch_displayOptions() {
        $_aParameters = func_get_args();
        foreach( $_aParameters as $_sOptionKey ) {
            if ( ! $_sOptionKey ) {
                continue;
            }
            $_mOption = get_option( $_sOptionKey );
            $this->_outputDetails( $_sOptionKey, $_mOption );
        }
    }

}