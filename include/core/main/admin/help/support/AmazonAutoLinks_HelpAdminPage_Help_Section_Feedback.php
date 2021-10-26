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
 * Adds the `Feedback` form section.
 * 
 * @since       4.7.0
 */
class AmazonAutoLinks_HelpAdminPage_Help_Section_Feedback extends AmazonAutoLinks_AdminPage_Section_Base {

    public $sSectionID = 'feedback';

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => $this->sTabSlug,
            'section_id'    => $this->sSectionID,
            // 'title'         => __( 'Feedback', 'amazon-auto-links' ),
            'description'   => __( 'Tell us about your experience with the plugin!', 'amazon-auto-links' ),
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
                'field_id'  => 'info',
                'type'      => 'system',
                'hidden'    => true,
                'data'      => array(
                    'WordPress' => array(
                        'Version' => $GLOBALS[ 'wp_version' ],
                    ),
                    'Plugin'    => array(
                        'Name'    => AmazonAutoLinks_Registry::NAME,
                        'Version' => AmazonAutoLinks_Registry::VERSION,
                    ),
                ) + array(
                    'Current Time' => '', 'Admin Page Framework' => '', 'WordPress' => '',
                    'PHP' => '', 'Server' => '', 'PHP Error Log' => '',
                    'MySQL' => '', 'MySQL Error Log' => '', 'Browser' => '',
                ),
            ),
            array(
                'field_id'          => 'pros',
                'title'             => __( 'What do you like about the plugin?', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'rich'              => array(
                    'media_buttons' => false,
                ),
                'description'       => __( 'Tell us what you use the plugin for mainly.', 'amazon-auto-links' ),
                'attributes'        => array(
                    // 'required'  => 'required',
                    'style'     => 'min-height: 200px;',
                ),
            ),
            array(
                'field_id'          => 'cons',
                'title'             => __( 'What are missing in the plugin?', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'rich'              => array(
                    'media_buttons' => false,
                ),
                'description'      => __( 'Tell us what you wish to have as a feature and what can be improved.', 'amazon-auto-links' ),
                'attributes'        => array(
                    'required'  => 'required',
                    'style'     => 'min-height: 200px;',
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
                'field_id'          => 'send',
                'type'              => 'contact',
                'label_min_width'   => 0,
                'value'             => __( 'Send', 'amazon-auto-links' ),
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline-block; text-align: right;',
                    ),
                ),
                'skip_confirmation' => true,
                'system_message'    => array(
                    'success' => __( 'Thanks for your feedback!', 'amazon-auto-links' ),
                ),
                'email'             => array(
                    // Each argument can accept a string or an array representing the dimensional array key.
                    // For example, if there is a field for the email title, and its section id is 'my_section'  and  the field id is 'my_field', pass an array, array( 'my_section', 'my_field' )
                    'to'            => 'aal-support@michaeluno.jp',
                    'subject'       => sprintf( 'Feedback on %1$s', AmazonAutoLinks_Registry::NAME . ' ' . AmazonAutoLinks_Registry::VERSION ),
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