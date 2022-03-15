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
 * Outputs the theme-based button preview.
 *
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Event_Query_ButtonPreview_ByID extends AmazonAutoLinks_Button_Event_Query_ButtonPreview_Base {

    /**
     * @var   array     Supported button types
     * @since 5.2.0
     */
    public $aButtonTypes = array( '_by_id' );

}