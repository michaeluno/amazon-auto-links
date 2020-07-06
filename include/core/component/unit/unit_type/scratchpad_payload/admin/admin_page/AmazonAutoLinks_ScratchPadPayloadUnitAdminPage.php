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
 * Deals with the plugin admin pages.
 * 
 * @since       4.1.0
 */
final class AmazonAutoLinks_ScratchPadPayloadUnitAdminPage extends AmazonAutoLinks_URLUnitAdminPage {

    /**
     * @remark      Added for extended classes.
     * @since       4.1.0
     */
    protected function _addPages() {
        new AmazonAutoLinks_ScratchPadPayloadUnitAdminPage_ScratchPadPayloadUnit( $this );
    }

        
}