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
 * Defines a post meta box of `Button`.
 *
 * @since 5.2.0
 */
class AmazonAutoLinks_UnitPostMetaBox_Button extends AmazonAutoLinks_UnitPostMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {

        $_aClassNames = array(
            'AmazonAutoLinks_FormFields_Unit_Button',
        );
        $this->_addFieldsByClasses( $_aClassNames );

        $this->enqueueScript(
            apply_filters( 'aal_filter_admin_button_js_preview_src', '' ),
            $this->oProp->aPostTypes,
            apply_filters( 'aal_filter_admin_button_js_preview_enqueue_arguments', array() )
        );

    }
    
}