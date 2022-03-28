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
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Flat_FormFields_IconRight extends AmazonAutoLinks_Button_Flat_FormFields_IconLeft {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='' ) {
        return array(
            array(
                'field_id'          => '_icon_right',
                'class'             => array(
                    'field'     => 'dynamic-button-field fields-button-icon-right',
                ),
                'content'           => $this->_getIconNestedFieldsets(
                    'right',
                    false,
                    AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/controls-play.svg', true )
                ),
            ),
        );
    }

}