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
class AmazonAutoLinks_Run_PluginOption_option_key extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Removes a record of the given key of the options table.
     * @throws Exception
     */
    public function scratch_deleteOptionRecord() {
        $_aParameters  = func_get_args();
        $this->_outputDetails( 'Passed Arguments', $_aParameters );
        if ( empty( $_aParameters ) ) {
            throw new Exception( 'Set option keys in the arguments field.' );
        }
        foreach( $_aParameters as $_sOptionKey ) {
            $_bDeleted = delete_option( $_sOptionKey );
            $this->_outputDetails( "Deleting {$_sOptionKey}", $_bDeleted ? 'Deleted' : 'Failed to delete' );
        }
    }

}