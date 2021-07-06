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
 * A scratch base class.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
abstract class AmazonAutoLinks_Scratch_Base extends AmazonAutoLinks_Run_Base {

    /**
     * Override this method.
     * @return mixed
     */
    public function scratch() {
        return true;
    }


}