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
class AmazonAutoLinks_Button_Flat_PostMetaBox_Preview extends AmazonAutoLinks_Button_Flat_PostMetaBox_Base {

    /**
     * @var   string[]
     * @since 5.2.0
     */
    protected $_aFieldClasses = array(
        'AmazonAutoLinks_Button_Flat_FormFields_Preview',
    );

    /**
     * @param  array $aInputs
     * @param  array $aOldInputs
     * @param  AmazonAutoLinks_AdminPageFramework_MetaBox $oFactory
     * @return array
     * @since  5.2.0
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {
        $aInputs[ 'button_label' ] = sanitize_text_field( $this->oUtil->getElement( $aInputs, array( '_text', 'label' ), '' ) );
        return $aInputs;
    }

}