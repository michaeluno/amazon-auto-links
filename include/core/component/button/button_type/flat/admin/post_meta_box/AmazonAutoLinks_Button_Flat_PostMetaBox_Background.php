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
 * Defines the meta box that shows a button preview.
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Flat_PostMetaBox_Background extends AmazonAutoLinks_Button_Flat_PostMetaBox_Base {

    /**
     * @var   array Stores field definition class names.
     * @since 5.2.0
     */
    protected $_aFieldClasses = array(
        'AmazonAutoLinks_Button_Flat_FormFields_Background',
    );

    public function setUp() {
        parent::setUp();
        new AmazonAutoLinks_Select2CustomFieldType( $this->oProp->sClassName );
    }

}