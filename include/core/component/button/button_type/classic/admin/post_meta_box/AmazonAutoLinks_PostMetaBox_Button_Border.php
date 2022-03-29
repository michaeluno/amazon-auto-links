<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Defines the meta box for the button post type.
 */
class AmazonAutoLinks_PostMetaBox_Button_Border extends AmazonAutoLinks_PostMetaBox_Button_Base {

    /**
     * @var   array Stores field definition class names.
     * @since 5.2.0
     */
    protected $_aFieldClasses = array(
        'AmazonAutoLinks_FormFields_Button_Border',
    );
    
}