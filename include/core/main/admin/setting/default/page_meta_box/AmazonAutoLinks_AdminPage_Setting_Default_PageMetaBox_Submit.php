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
 * @since       3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Submit extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $this->addSettingFields(
            array(
                'field_id'          => '_submit',
                'type'              => 'submit',
                'value'             => __( 'Save', 'amazon-auto-links' ),
                'save'              => false,
                'label_min_width'   => '100%',
                'after_fieldset'    => '<hr />',
                'attributes'        => array(
                    'field'    => array(
                        'style' => 'width: 100%; text-align: center;',
                    ),
                )
            ),
            array(
                'field_id'          => '_reset_defaults',
                'type'              => 'inline_mixed',
                'content'           => array(
                    array(
                        'field_id'      => 'reset',
                        'type'          => 'submit',
                        'label'         => __( 'Reset', 'amazon-auto-links' ),
                        'attributes'    => array(
                            'class' => 'button button-secondary reset-defaults'
                        ),
                    ),
                    array(
                        'field_id'      => 'confirm',
                        'type'          => 'checkbox',
                        'label'         => __( "I'd like to reset unit defaults.", 'amazon-auto-links' ),
                        'class'         => array(
                            'input' => 'reset-confirm'
                        ),
                    ),
                ),
                'save'              => false,
                'show_title_column' => false,
                'label_min_width'   => '100%',
                'attributes'        => array(
                    'field'    => array(
                        'style' => 'width: 100%; text-align: center;',
                    ),
                )
            )
        );

        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        $this->enqueueScript(
            $this->oUtil->isDebugMode()
                ? AmazonAutoLinks_SettingsAdminPageLoader::$sDirPath . '/asset/js/default-settings-submit.js'
                : AmazonAutoLinks_SettingsAdminPageLoader::$sDirPath . '/asset/js/default-settings-submit.min.js',
            $_sPageSlug,
            $this->oProp->getCurrentTabSlug( $_sPageSlug ),
            array(
                'handle_id'   => 'aalSubmit',
                'translation' => array(
                    'pluginName' => AmazonAutoLinks_Registry::NAME,
                    'debugMode'  => $this->oUtil->isDebugMode(),
                    'label'      => array(
                        'please_confirm' => __( 'Please check the check box to reset the default settings.', 'amazon-auto-links' ),
                    ),
                ),
                'in_footer' => true,
            )
        );
        
    }

    /**
     * Validates submitted form data.
     *
     * @param array $aInputs
     * @param array $aOriginal
     * @param AmazonAutoLinks_AdminPageFramework_MetaBox $oAdminPage
     * @param array $aSubmitInfo
     *
     * @return array
     */
    public function validate( $aInputs, $aOriginal, $oAdminPage, $aSubmitInfo ) {

        // When the Reset button is pressed,
        if (
            $this->oUtil->getElement( $aInputs, array( '_reset_defaults', 'reset' ) )
            && $this->oUtil->getElement( $aInputs, array( '_reset_defaults', 'confirm' ) )
        ) {
            add_action( 'shutdown', array( $this, 'replyToUnsetUnitDefaults' ) );
            $oAdminPage->setSettingNotice( __( 'The unit default options have been reset.', 'amazon-auto-links' ), 'update' );
        }

        return $aInputs;

    }
        /**
         * @since   4.3.0
         */
        public function replyToUnsetUnitDefaults() {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_oOption->update( array( 'unit_default' ), array() );
        }
    
}
