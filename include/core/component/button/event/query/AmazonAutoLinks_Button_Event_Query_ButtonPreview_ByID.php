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
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Event_Query_ButtonPreview_ByID extends AmazonAutoLinks_Button_Event_Query_ButtonPreview_Base {

    /**
     * @var   array     Supported button types
     * @since 5.2.0
     */
    public $aButtonTypes = array( '_by_id' );

    /**
     * @since  5.2.0
     * @param  string $sHeadTagInnerHTML
     * @return string
     */
    protected function _getExtraResourcesAddedToHead( $sHeadTagInnerHTML ) {
        $sHeadTagInnerHTML = parent::_getExtraResourcesAddedToHead( $sHeadTagInnerHTML );
        // This is enabled for the list table for the trash entries which don't have stored CSS rules
        if ( ! empty( $_GET[ 'load-own-style' ] ) && ! empty( $_GET[ 'button-id' ] ) ) {
            $_iButtonID         = ( integer ) $this->getHTTPQueryGET( 'button-id' );
            $sHeadTagInnerHTML .= "<style id='aal-button-preview-style-{$_iButtonID}'>" . $this->___getStyleByButtonID( $_iButtonID ) . "</style>";
        }
        return $sHeadTagInnerHTML;
    }
        /**
         * @since  5.2.0
         * @param  integer $iButtonID
         * @return string
         */
        private function ___getStyleByButtonID( $iButtonID ) {
            return str_replace(
                '___button_id___',
                $iButtonID,
                ( string ) get_post_meta( $iButtonID, 'button_css', true )
            );
        }

}