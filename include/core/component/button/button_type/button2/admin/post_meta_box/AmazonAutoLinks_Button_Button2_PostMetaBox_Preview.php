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
 * Defines the meta box that shows a button preview.
 */
class AmazonAutoLinks_Button_Button2_PostMetaBox_Preview extends AmazonAutoLinks_Button_Button2_PostMetaBox_Base {

    public function setUp() {

        $_oFields = new AmazonAutoLinks_Button_Button2_FormFields_Preview( $this );
        foreach( $_oFields->get() as $_aField ) {
            $this->addSettingFields( $_aField );
        }

    }
}