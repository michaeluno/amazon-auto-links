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
 * @since 3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_CommonAdvanced extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {

    /**
     * @var   string[]
     * @since 5.2.0
     */
    protected $_aFieldClasses = array(
        'AmazonAutoLinks_FormFields_Unit_CommonAdvanced',
    );

}