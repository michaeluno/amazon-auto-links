<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds a form section to an in-page tab.
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_Opt_In_Setting_Section_UserBase extends AmazonAutoLinks_AdminPage_Section_Base {

    protected function _getArguments() {
        return array(
            'section_id'    => 'opt_in_user_base',
            'tab_slug'      => $this->sTabSlug,
            'title'         => __( 'Opt-in', 'amazon-auto-links' ),
            'save'          => false,
            'description'   => array(
                 __( 'Decide whether to allow what the plugin asks you.', 'amazon-auto-links' ),
            ),
        );
    }

    /**
     * Adds form fields.
     * @since       4.7.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $_iUserID = get_current_user_id();
        $_oSVG    = new AmazonAutoLinks_SVGGenerator_RatingStar( true, __( 'Five stars', 'amazon-auto-links' ) );
        $_sStars  = $_oSVG->get( 50 );
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'              => 'aal_surveys',
                'type'                  => 'checkbox',
                'title'                 => __( 'Survey', 'amazon-auto-links' ),
                'label'                 => __( 'Allow the plugin to ask you questionnaires from time to time.', 'amazon-auto-links' ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_surveys', true ),
            ),
            array(
                'field_id'              => 'aal_announcements',
                'type'                  => 'checkbox',
                'title'                 => __( 'Announcement Feeds', 'amazon-auto-links' ),
                'label'                 => __( 'Allow the plugin to load plugin announcement feeds from external sources and display them.', 'amazon-auto-links' ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_announcements', true ),
            ),
            array(
                'field_id'              => 'aal_developer_amazon_tag',
                'type'                  => 'checkbox',
                'title'                 => __( 'Developer\'s Amazon Associates Tags in Settings', 'amazon-auto-links' ),
                'label'                 => __( 'Allow the plugin to insert developer\'s Amazon Associates tags in the Amazon product links shown in the plugin setting pages.', 'amazon-auto-links' ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_developer_amazon_tag', true ),
            ),
            array(
                'field_id'              => 'aal_usage_data',
                'type'                  => 'checkbox',
                'title'                 => __( 'Usage Data', 'amazon-auto-links' ),
                'label'                 => __( 'Allow the plugin to collect plugin usage data.', 'amazon-auto-links' ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_usage_data', true ),
            ),
            array(
                'field_id'              => 'aal_load_new_templates',
                'type'                  => 'checkbox',
                'title'                 => __( 'New Template Feeds', 'amazon-auto-links' ),
                'label'                 => __( 'Allow the plugin to load new template feeds from external sources.', 'amazon-auto-links' ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_load_new_templates', true ),
            ),
            array()
        );

    }

    /**
      * Called upon form validation.
      *
      * @callback   filter      'validation_{class name}_{section id}'
      * @since      4.7.0
      */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        $_iUserID = get_current_user_id();
        foreach( $aInputs as $_sMetaKey => $_mValue ) {
            update_user_meta( $_iUserID, $_sMetaKey, ( boolean ) $_mValue );
        }
        return array(); // do not save anything.
    }

}