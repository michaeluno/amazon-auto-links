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
 * Adds the `Bug Report` form section.
 * 
 * @since       4.7.0
 */
class AmazonAutoLinks_HelpAdminPage_Help_Section_BugReport extends AmazonAutoLinks_AdminPage_Section_Base {

    public $sSectionID = 'bug_report';

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => $this->sTabSlug,
            'section_id'    => $this->sSectionID,
            // 'title'         => __( 'Bug Report', 'amazon-auto-links' ),
            'description'   => __( 'If you find unexpected behaviour of the plugin, please report it.', 'amazon-auto-links' ),
        );
    }

    /**
     * Called when adding fields.
     * @param  AmazonAutoLinks_AdminPageFramework $oFactory
     * @param  string $sSectionID
     * @remark This method should be overridden in each extended class.
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $_oCurrentUser = wp_get_current_user();

        $oFactory->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'          => 'name',
                'title'             => __( 'Your Name', 'amazon-auto-links' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_firstname
                    ? $_oCurrentUser->user_lastname . ' ' .  $_oCurrentUser->user_lastname
                    : '',
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   => __( 'Your name.', 'amazon-auto-links' ),
                ),
            ),
            array(
                'field_id'          => 'from',
                'title'             => __( 'Your Email Address', 'amazon-auto-links' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_email,
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   =>  __( 'Type your email that the developer replies back to.', 'amazon-auto-links' ),
                ),
            ),
            array(
                'field_id'          => 'expected_result',
                'title'             => __( 'Expected Behavior', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'description'       => __( 'Tell how the framework should work.', 'amazon-auto-links' ),
                'attributes'        => array(
                    'required'  => 'required',
                    'style'     => 'min-height: 260px;',
                ),
            ),
            array(
                'field_id'          => 'actual_result',
                'title'             => __( 'Actual Behavior', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'description'      => __( 'Describe the behavior of the framework.', 'amazon-auto-links' ),
                'attributes'        => array(
                    'required'  => 'required',
                    'style'     => 'min-height: 260px;',
                ),
            ),
            array(
                'field_id'          => 'attachments',
                'title'             => __( 'Screenshots', 'amazon-auto-links' ),
                'type'              => 'image',
                'repeatable'        => true,
                'attributes'        => array(
                    'size'  => 40,
                    'preview' => array(
                        'style' => 'max-width: 200px;'
                    ),
                ),
            ),
            array(
                'field_id'          => 'allow_sending_system_information',
                'title'             => __( 'Confirmation', 'amazon-auto-links' )
                    . ' (' . __( 'required', 'amazon-auto-links' ) . ')',
                'type'              => 'checkbox',
                'label'             => __( 'I understand that the system information including a PHP version and WordPress version etc. will be sent along with the messages to help developer trouble-shoot the problem.', 'amazon-auto-links' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),
            ),
            array(
                'field_id'          => 'send',
                'type'              => 'contact',
                'label_min_width'   => 0,
                'value'             => __( 'Send', 'amazon-auto-links' ),
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),
                'skip_confirmation' => true,
                'system_message'    => array(
                    'success' => __( 'Thanks for reporting!', 'amazon-auto-links' ),
                ),
                'email'             => array(
                    // Each argument can accept a string or an array representing the dimensional array key.
                    // For example, if there is a field for the email title, and its section id is 'my_section'  and  the field id is 'my_field', pass an array, array( 'my_section', 'my_field' )
                    'to'            => 'aal-support@michaeluno.jp',
                    'subject'       => sprintf( 'Reporting Issue of %1$s', AmazonAutoLinks_Registry::NAME . ' ' . AmazonAutoLinks_Registry::VERSION ),
                    'message'       => array( $this->sSectionID ), // the section name enclosed in an array. If it is a field, set it to the second element like array( 'seciton id', 'field id' ).
                    'headers'       => '',
                    'attachments'   => '', // the file path(s)
                    'name'          => '', // The email sender name. If the 'name' argument is empty, the field named 'name' in this section will be applied
                    'from'          => '', // The sender email address. If the 'from' argument is empty, the field named 'from' in this section will be applied.
                    // 'is_html'       => true,
                ),
            ),
            array()
        );

    }

}