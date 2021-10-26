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
 * Adds the 'Form' form section.
 *
 * @since       4.7.6
 */
class AmazonAutoLinks_Opt_Setting_Section_Form extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getArguments() {
        return array(
            'title'         => __( 'Form', 'amazon-auto-links' ),
            'section_id'    => 'form',
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
                'field_id'      => 'last_inputs',
                'type'          => 'checkbox',
                'title'         => __( 'Input Cache', 'amazon-auto-links' ),
                'label'         => __( 'Delete the input cache.', 'amazon-auto-links' ),
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
        if ( ! empty( $aInputs[ 'last_inputs' ] ) ) {
            delete_user_meta( $_iUserID, AmazonAutoLinks_Registry::$aUserMeta[ 'last_inputs' ] );
        }
        return array(); // do not save anything.
    }

}