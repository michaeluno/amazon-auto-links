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
 *
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Flat_PostMetaBox_CSS extends AmazonAutoLinks_Button_Flat_PostMetaBox_Base {

    /**
     * @var   array Stores field definition class names.
     * @since 5.2.0
     */
    protected $_aFieldClasses = array(
        'AmazonAutoLinks_Button_Flat_FormFields_CSS',
    );

    /**
     * Validates submitted form data.
     * @since 5.3.2
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {
        // Prevent script injections
        $aInputs[ 'button_css' ] = strip_tags( $aInputs[ 'button_css' ] );
        $aInputs[ 'custom_css' ] = strip_tags( $aInputs[ 'custom_css' ] );
        return $aInputs;
    }

}