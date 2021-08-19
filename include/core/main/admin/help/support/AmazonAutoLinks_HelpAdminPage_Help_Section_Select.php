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
 * Adds the `Support` form section.
 * 
 * @since       4.7.0
 */
class AmazonAutoLinks_HelpAdminPage_Help_Section_Select extends AmazonAutoLinks_AdminPage_Section_Base {

    public $sSectionID = 'select_contact';

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => $this->sTabSlug,
            'section_id'    => $this->sSectionID,
            'title'       => __( 'What this is regarding?', 'amazon-auto-links' ),
            // 'description'   => __( 'Select what you like to tell us.', 'amazon-auto-links' ),
        );
    }

    /**
     * Called when adding fields.
     * @param  AmazonAutoLinks_AdminPageFramework $oFactory
     * @param  string $sSectionID
     * @remark This method should be overridden in each extended class.
     * @since  4.7.0
     */
    protected function _addFields( $oFactory, $sSectionID ) {
        $oFactory->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'    => 'select_contact_type',
                'type'        => 'revealer',
                'select_type' => 'radio',
                'show_title_column' => false,
                'label'       => array(
                    'support'    => __( 'Technical Support', 'amazon-auto-links' ),
                    'feedback'   => __( 'Feedback', 'amazon-auto-links' ),
                    'bug_report' => __( 'Report Issues', 'amazon-auto-links' ),
                ),
                'is_global'   => true,
                'selectors'   => array(
                    'support'      => '#sections-support',
                    'feedback'     => '#sections-feedback',
                    'bug_report'   => '#sections-bug_report',
                ),
                'default'     => 'support',
            ),
            array()
        );
    }

}