<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * A delegation class that adds the warning icon to the action link area in the unit listing table.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_PostType_Unit__ActionLink_TagUnitWarning extends AmazonAutoLinks_PostType_Unit__ActionLink_Base {

    protected $_sActionSlug  = 'tag_deprecated_warning';

    protected $_bAddBulkAction = false;

    /**
     * @param       object          $oPost
     * @return      string
     */
    protected function _getActionLink( $oPost ) {
        $_sUnitType = get_post_meta( $oPost->ID, 'unit_type', true );
        if ( 'tag' !== $_sUnitType )  {
            return '';
        }
        return $this->___getTagDeprecateWarning();
    }
        /**
         * @since       3.2.0
         * @since       3.5.0       Moved from `AmazonAutoLinks_PostType_Unit_Action`.
         * @return      string
         */
        private function ___getTagDeprecateWarning() {
            $_sTitle              = __( 'Amazon has deprecated the tags feature. So this is no longer functional.', 'amazon-auto-links' );
            $_sWarning            = __( 'Warning!', 'amazon-auto-links' );
            $_sURL                = 'https://www.amazon.com/gp/help/customer/display.html?nodeId=16238571';
            $_sExclamationIconURL = AmazonAutoLinks_Registry::getPluginURL( 'asset/image/exclamationmark_16x16.png' );
            return "<a href='" . esc_url( $_sURL ) . "' target='_blank'>"
                    . "<img src='" . esc_url( $_sExclamationIconURL ) . "' alt='" . esc_attr( $_sWarning ) . "' title='" . esc_attr( $_sTitle ) . "' />"
                . "</a> ";
        }

}