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
 * Adds the 'UI' form section.
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_Opt_Setting_Section_UI extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getArguments() {
        return array(
            'title'         => __( 'UI', 'amazon-auto-links' ),
            'section_id'    => 'ui',
            'tab_slug'      => $this->sTabSlug,
            'save'          => false,
        );
    }

    /**
     * Adds form fields.
     * @since       4.7.0
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @param       string $sSectionID
     */
    protected function _addFields( $oFactory, $sSectionID ) {
        $oFactory->addSettingFields(
            $sSectionID, // the target section id,
            array(
                'field_id'      => 'reset_metabox_order',
                'type'          => 'checkbox',
                'title'         => __( 'Meta-box Order', 'amazon-auto-links' ),
                'label'         => __( 'Reset the plugin metabox order.', 'amazon-auto-links' ),
                'save'          => false,
            ),
            array(
                'field_id'      => 'reset_metabox_fold_state',
                'type'          => 'checkbox',
                'title'         => __( 'Meta-box Fold States', 'amazon-auto-links' ),
                'label'         => __( 'Reset the plugin metabox fold states.', 'amazon-auto-links' ),
                'save'          => false,
            ),
            array(
                'field_id'      => 'reset_screen_layouts',
                'type'          => 'checkbox',
                'title'         => __( 'Screen Layout', 'amazon-auto-links' ),
                'label'         => __( 'Reset the plugin screen layouts.', 'amazon-auto-links' ),
                'save'          => false,
            ),
            array()
        );
    }

    
    /**
     * Validates the submitted form data.
     * 
     * @since 4.7.0
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        $_iUserID = get_current_user_id();
        if ( ! empty( $aInputs[ 'reset_metabox_order' ] ) ) {
            $this->___resetMetaboxOrder( $_iUserID, $oAdminPage );
        }
        if ( ! empty( $aInputs[ 'reset_screen_layout' ] ) ) {
            $this->___resetScreenLayouts( $_iUserID, $oAdminPage );
        }
        if ( ! empty( $aInputs[ 'reset_metabox_fold_state' ] ) ) {
            $this->___resetMetaboxFoldStates( $_iUserID, $oAdminPage );
        }
        return array(); // do not save anything.
    }
        /**
         * @param  integer $iUserID
         * @param  AmazonAutoLinks_AdminPageFramework $oAdminPage
         * @since  4.7.0
         */
        private function ___resetScreenLayouts( $iUserID, $oAdminPage ) {
            global $wpdb;
            $_sSQL = "DELETE FROM  `{$wpdb->base_prefix}usermeta` 
WHERE `user_id` = {$iUserID} 
AND   `meta_key` LIKE 'screen_layout_amazon_auto_links%';";
            $_biResult = $wpdb->query( $_sSQL );
            if ( false === $_biResult ) {
                $oAdminPage->setSettingNotice( __( 'Plugin screen layouts could not be reset.', 'amazon-auto-links' ), 'error' );
            }
        }
        /**
         * @param  integer $iUserID
         * @param  AmazonAutoLinks_AdminPageFramework $oAdminPage
         * @since  4.7.0
         */
        private function ___resetMetaBoxOrder( $iUserID, $oAdminPage ) {
            global $wpdb;
            $_sSQL = "DELETE FROM  `{$wpdb->base_prefix}usermeta` 
WHERE `user_id` = {$iUserID} 
AND   `meta_key` LIKE 'meta-box-order_amazon_auto_links%'
OR    `meta_key` LIKE 'metaboxhidden_amazon_auto_links%';";
            $_biResult = $wpdb->query( $_sSQL );
            if ( false === $_biResult ) {
                $oAdminPage->setSettingNotice( __( 'Plugin meta box order could not be reset.', 'amazon-auto-links' ), 'error' );
            }
        }
        /**
         * @param  integer $iUserID
         * @param  AmazonAutoLinks_AdminPageFramework $oAdminPage
         * @since  4.7.0
         */
        private function ___resetMetaBoxFoldStates( $iUserID, $oAdminPage ) {
            global $wpdb;
            $_sSQL = "DELETE FROM  `{$wpdb->base_prefix}usermeta` 
WHERE `user_id` = {$iUserID} 
AND   `meta_key` LIKE 'closedpostboxes_amazon_auto_links%';";
            $_biResult = $wpdb->query( $_sSQL );
            if ( false === $_biResult ) {
                $oAdminPage->setSettingNotice( __( 'Plugin meta box fold state could not be reset.', 'amazon-auto-links' ), 'error' );
            }
        }
}