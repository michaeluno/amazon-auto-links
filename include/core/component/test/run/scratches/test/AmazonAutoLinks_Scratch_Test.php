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
 * Scratches to deliberately cause errors to test the UI.
 *  
 * @since       4.3.4
*/
class AmazonAutoLinks_Scratch_Test extends AmazonAutoLinks_Scratch_Base {

     /**
     */
    public function scratch_string() {
        return __METHOD__;
    }

    /**
     * Triggers an error.
     */
    public function scratch_SyntaxError() {
        return explode( '', array() );
    }

}