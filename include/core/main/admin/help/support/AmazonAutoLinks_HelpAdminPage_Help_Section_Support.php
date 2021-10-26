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
 * Adds the `Support` form section.
 * 
 * @since       4.7.0
 */
class AmazonAutoLinks_HelpAdminPage_Help_Section_Support extends AmazonAutoLinks_AdminPage_Section_Base {

    public $sSectionID = 'support';

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => $this->sTabSlug,
            'section_id'    => $this->sSectionID,
            'description'   => __( 'Got stuck? Get technical support.', 'amazon-auto-links' ),
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
        $oFactory->addSettingFields( $this->sSectionID );
        $_aFields = $this->getAsArray( apply_filters( 'aal_filter_form_fields_technical_support', $this->___getFields() ) );
        foreach( $_aFields as $_aFieldset ) {
            $oFactory->addSettingFields( $_aFieldset );
        }
    }

    /**
     * @return array
     * @since  4.7.0
     */
    private function ___getFields() {
        $_oCurrentUser = wp_get_current_user();
        return array(
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
                'before_fieldset'   => "<div title='" . esc_attr( AmazonAutoLinks_Message::getUpgradePromptMessage( false ) ) . "'>",
                'after_fieldset'    => "</div>",
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
                'before_fieldset'   => "<div title='" . esc_attr( AmazonAutoLinks_Message::getUpgradePromptMessage( false ) ) . "'>",
                'after_fieldset'    => "</div>",
            ),
            array(
                'field_id'          => 'description',
                'title'             => __( 'What happened?', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'rich'              => array(
                    'textarea_rows' => 10,  // set height
                    'media_buttons' => false,
                ),
                'description'       => __( 'Tell us the problem with as much details as possible.', 'amazon-auto-links' ),
                'attributes'        => array(
                    'required'      => 'required',
                    'style'         => 'height: 300px; width: 100%;',
                ),
                'before_fieldset'   => "<div title='" . esc_attr( AmazonAutoLinks_Message::getUpgradePromptMessage( false ) ) . "'>",
                'after_fieldset'    => "</div>",
            ),
            array(
                'field_id'          => 'attachments',
                'title'             => __( 'Screenshots', 'amazon-auto-links' ),
                'type'              => 'image',
                'repeatable'        => array(
                    'max' => 1,
                    // 'disabled'      => array(
                    //     'message'       => AmazonAutoLinks_Message::getUpgradePromptMessage(),
                    //     'caption'       => AmazonAutoLinks_Message::get( 'available_in_pro' ),
                    //     'box_width'     => 300,
                    //     'box_height'    => 100,
                    // ),
                ),
                'attributes'        => array(
                    'size'  => 40,
                    'preview' => array(
                        'style' => 'max-width: 200px;'
                    ),
                ),
                'before_fieldset'   => "<div title='" . esc_attr( AmazonAutoLinks_Message::getUpgradePromptMessage( false ) ) . "'>",
                'after_fieldset'    => "</div>",
            ),
            array(
                'field_id'          => 'allow_sending_system_information',
                'title'             => __( 'Confirmation', 'amazon-auto-links' )
                    . ' (' . __( 'required', 'amazon-auto-links' ) . ')',
                'type'              => 'checkbox',
                'label'             => AmazonAutoLinks_Message::get( 'agree_to_send_info' ),
                'attributes'        => array(
                    'required'  => 'required',
                    // 'disabled'  => 'disabled',
                ),
                'before_fieldset'   => "<div title='" . esc_attr( AmazonAutoLinks_Message::getUpgradePromptMessage( false ) ) . "'>",
                'after_fieldset'    => "</div>",
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
                    'disabled' => 'disabled',
                ),
                'skip_confirmation' => true,
                'system_message'    => array(
                    'success' => __( 'We will get back to you soon!', 'amazon-auto-links' ),
                    'failure' => AmazonAutoLinks_Message::getMessageNotSent( 'aal-support@michaeluno.jp' ),
                ),
                'email'             => array(
                    // Each argument can accept a string or an array representing the dimensional array key.
                    // For example, if there is a field for the email title, and its section id is 'my_section'  and  the field id is 'my_field', pass an array, array( 'my_section', 'my_field' )
                    'to'            => '',
                    'subject'       => 'If this message is sent, it means hacked.',
                    'message'       => array( $this->sSectionID ), // the section name enclosed in an array. If it is a field, set it to the second element like array( 'seciton id', 'field id' ).
                    'headers'       => '',
                    'attachments'   => '', // the file path(s)
                    'name'          => '', // The email sender name. If the 'name' argument is empty, the field named 'name' in this section will be applied
                    'from'          => '', // The sender email address. If the 'from' argument is empty, the field named 'from' in this section will be applied.
                    'is_html'       => true,
                ),
                'before_fieldset'   => "<div title='" . esc_attr( AmazonAutoLinks_Message::getUpgradePromptMessage( false ) ) . "'>",
                'after_fieldset'    => "</div>",
                'after_input'       => "<p><span class='description warning'>"
                        . AmazonAutoLinks_Message::get( 'available_in_pro' )
                    . "</span></p>",
            ),
            array(
                'field_id'          => 'support_forums',
                'show_title_column' => false,
                'content'           => $this->getTableOfArray(
                    array(
                        __( 'Support Forums', 'amazon-auto-links' ) => sprintf(
                            __( 'To get free support, visit the <a href="%1$s" target="_blank">support forum</a>.', 'amazon-auto-links' ),
                    'https://wordpress.org/support/plugin/amazon-auto-links'
                        )
                        . "<a href='https://wordpress.org/support/plugin/amazon-auto-links' target='_blank'><span class='dashicons dashicons-external'></span></a>",
                        __( 'Priority Support', 'amazon-auto-links' ) => sprintf(
                            __( 'You can get priority email support by purchasing <a href="%1$s" target="_blank">Pro</a>.', 'amazon-auto-links' ),
                            'https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/'
                        )
                        . "<a href='https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/' target='_blank'><span class='dashicons dashicons-external'></span></a>",
                    ),
                    array(
                        'table' => array(
                            'class' => 'widefat striped fixed',
                        ),
                        'td'    => array(
                            array( 'class' => 'width-one-fourth', ),  // first td
                        )
                    ),
                    array(),
                    array(),
                    false
                ),
            ),
            array()
        );
    }

}