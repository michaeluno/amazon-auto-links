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
 * Outputs the theme-based button preview.
 *
 * @since 4.3.0
 * @since 5.2.0 Extends `AmazonAutoLinks_Button_Event_Query_ButtonPreview_Base`. Renamed from `AmazonAutoLinks_Button_Event_Query_ButtonPreview`.
 */
class AmazonAutoLinks_Button_Event_Query_ButtonPreview_Theme extends AmazonAutoLinks_Button_Event_Query_ButtonPreview_Base {

    /**
     * @var   array     Supported button types
     * @since 5.2.0
     */
    public $aButtonTypes = array( 'theme' );

}