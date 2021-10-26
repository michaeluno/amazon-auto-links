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
 * Adds a form section to an in-page tab.
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_Opt_Out_Setting_Section_UserBase extends AmazonAutoLinks_Opt_In_Setting_Section_UserBase {

    protected function _getArguments() {
        return array(
            'section_id'    => 'opt_out_user_base',
            'tab_slug'      => $this->sTabSlug,
            'title'         => __( 'Opt-out', 'amazon-auto-links' ),
            'save'          => false,
            'description'   => array(
                 __( 'Decide whether to disallow what the plugin asks you.', 'amazon-auto-links' ),
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

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'              => 'aal_rated',
                'type'                  => 'checkbox',
                'title'                 => __( 'Plugin Review', 'amazon-auto-links' ),
                'label'                 => sprintf(
                    __( 'Have you <a href="%1$s" target="_blank">rated the plugin</a>?', 'amazon-auto-links' ),
                    'https://wordpress.org/support/plugin/amazon-auto-links/reviews/'
                ),
                'description'           => array(
                    "<span class='aal-have-you-rated-answer'>"
                        . "<span>"
                            . AmazonAutoLinks_Opt_Message::getGiveThePlugin5Stars()
                        . "</span>"
                    . "</span>"
                ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_rated', true ),
            ),
            array(
                'field_id'              => 'aal_never_ask_surveys',
                'type'                  => 'checkbox',
                'title'                 => __( 'Survey', 'amazon-auto-links' ),
                'label'                 => __( 'Never ask whether the plugin can ask you questionnaires from time to time.', 'amazon-auto-links' ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_never_ask_surveys', true ),
            ),
            array(
                'field_id'              => 'aal_never_ask_announcements',
                'type'                  => 'checkbox',
                'title'                 => __( 'Announcement Feeds', 'amazon-auto-links' ),
                'label'                 => __( 'Never ask whether the plugin can load plugin announcement feeds from external sources and display them.', 'amazon-auto-links' ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_never_ask_announcements', true ),
            ),
            array(
                'field_id'              => 'aal_never_ask_developer_amazon_tag',
                'type'                  => 'checkbox',
                'title'                 => __( 'Developer\'s Amazon Associates Tags in Settings', 'amazon-auto-links' ),
                'label'                 => __( 'Never ask whether the plugin can insert developer\'s Amazon Associates tags in the Amazon product links shown in the plugin setting pages.', 'amazon-auto-links' ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_never_ask_developer_amazon_tag', true ),
            ),
            array(
                'field_id'              => 'aal_never_ask_usage_data',
                'type'                  => 'checkbox',
                'title'                 => __( 'Usage Data', 'amazon-auto-links' ),
                'label'                 => __( 'Never ask whether the plugin can collect plugin usage data.', 'amazon-auto-links' ),
                'save'                  => false,
                'value'                 => get_user_meta( $_iUserID, 'aal_never_ask_usage_data', true ),
            ),
            array()
        );

    }

}