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
 * Defines the meta box added to the 'contextual' unit definition page.
 *
 * @since   4.1.0
 */
class AmazonAutoLinks_UnitPostMetaBox_Advanced_contextual extends AmazonAutoLinks_UnitPostMetaBox_Main_contextual {

    /**
     * @return      array
     */
    protected function _getFieldClasses() {
        return array(
            'AmazonAutoLinks_FormFields_ContextualUnit_Advanced',
        );
    }

    
}