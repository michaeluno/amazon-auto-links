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
 * Adds the 'Reset Settings' form section to the 'Misc' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_Reset_ResetOptions extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'reset_settings',
            'title'         => __( 'Reset Settings', 'amazon-auto-links' ),
            'description'   => array(
                __( 'If you get broken options, initialize them by performing reset.', 'amazon-auto-links' ),
            ),
        );
    }

    /**
     * Adds form fields.
     * @since 3
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @param string $sSectionID
     */
    protected function _addFields( $oFactory, $sSectionID ) {

       $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'              => 'reset_components',
                'title'                 => __( 'Restore Defaults', 'amazon-auto-links' ),
                'type'                  => 'checkbox',
                'select_all_button'     => true,
                'select_none_button'    => true,
                'label'                 => array(
                    'general'  => __( 'General', 'amazon-auto-links' ),
                    'template' => __( 'Templates', 'amazon-auto-links' ),
                    'tool'     => __( 'Tools', 'amazon-auto-links' ),
                    'opt'      => __( 'Opt', 'amazon-auto-links' ),
                    // 'button'   => __( 'Buttons', 'amazon-auto-links' ),
                    // 'opt_in'   => __( 'Opt-in', 'amazon-auto-links' ),   // not implemented yet
                ),
                'save'                  => false,
                'default'               => array(
                    'general'   => true,
                    'template'  => true,
                    'tool'      => true,
                    'opt'       => true,
                    // 'button'   => true,
                ),
            ),
            array( 
                'field_id'          => 'restore',
                'type'              => 'submit',
                'save'              => false,
                // 'reset'             => true,
                'value'             => __( 'Restore', 'amazon-auto-links' ),
                'confirm'           => __( 'Confirm that this deletes the current stored options and restores the default options.', 'amazon-auto-links' ),
                'skip_confirmation' => true,
                'tip'               => __( 'Restore the default options.', 'amazon-auto-links' ),
                'attributes'        => array(
                    'size'          => 30,
                ),
            ),
            array(
                'field_id'          => 'reset_on_uninstall',
                'title'             => __( 'Delete Options upon Uninstall', 'amazon-auto-links' ),
                'type'              => 'checkbox',
                'label'             => __( 'Delete options and caches when the plugin is uninstalled.', 'amazon-auto-links' ),
            )           
        );
    
    }

    /**
     * @var   array Stores restore field input values when the Restore button is pressed.
     * @since 4.7.2
     */
    private $___aRestore = array();

    /**
     * @return array
     */
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        if ( 'restore' === $aSubmitInfo[ 'field_id' ] ) {
            $this->___aRestore = $aInputs[ 'reset_components' ];
            add_filter( 'validation_' . $oFactory->oProp->sClassName, array( $this, 'replyToResetOptions' ), 999, 4 );
            return $aOldInputs;
        }
        return $aInputs;
    }
        public function replyToResetOptions( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
            $this->___resetOptions( $this->___aRestore, $oFactory );
            return $aInputs;    // do not return an empty array as the user might not check `general`.
        }
            /**
             * @param AmazonAutoLinks_AdminPageFramework $oFactory
             */
            private function ___resetOptions( $aResetComponents, $oFactory ) {

                $_aCheckedComponents = array_filter( $aResetComponents );
                if ( empty( $_aCheckedComponents ) ) {
                    $oFactory->setSettingNotice( __( 'At least one item needs to be checked.', 'amazon-auto-links' ), 'error' );
                    return;
                }

                if ( $aResetComponents[ 'general' ] ) {
                    add_action( 'shutdown', array( $this, 'replyToDeleteOptions' ) );
                }

                // Delete the template options as well.
                if ( $aResetComponents[ 'template' ] ) {
                    delete_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'template' ] );
                }

                // Button options
                // if ( $aResetComponents[ 'button' ] ) {
                //     delete_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'button_css' ] );
                // }

                // Tools
                if ( $aResetComponents[ 'tool' ] ) {
                    delete_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'tools' ] );
                }

                // Opt
                if ( $aResetComponents[ 'opt' ] ) {
                    $this->___deleteOptMeta();
                }

                $oFactory->setSettingNotice( __( 'The default options have been restored.', 'amazon-auto-links' ), 'updated' );

            }
                /**
                 * @since 4.7.2
                 */
                private function ___deleteOptMeta() {
                    $_iUserID  = get_current_user_id();
                    $_aOptKeys = AmazonAutoLinks_Registry::$aUserMeta;
                    unset( $_aOptKeys[ 'first_saved' ] );
                    foreach( AmazonAutoLinks_Registry::$aUserMeta as $_sUserMetaKey ) {
                        delete_user_meta( $_iUserID, $_sUserMetaKey );
                    }
                }
                /**
                 * @since 4.6.19
                 */
                public function replyToDeleteOptions() {
                    // General
                    delete_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ] );

                    // Last inputs
                    delete_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'last_input' ] );

                    // Opt-in
                    delete_user_meta( get_current_user_id(), 'aal_rated' );

                }
}