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
 * A base class for post meta boxes that define buttons of the `button2` button type and of the `button` post type.
 * @since 5.2.0
 */
abstract class AmazonAutoLinks_Button_Button2_PostMetaBox_Base extends AmazonAutoLinks_Button_ButtonType_PostMetaBox_Base {

    /**
     * @var   string
     * @since 5.2.0
     */
    protected $_sButtonType = 'button2';

}