<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Adds a form section to an in-page tab.
 *
 * @since       3.8.0
 * @action      schedule        aal_action_event_convert_unit_options
 */
class AmazonAutoLinks_LinkConverter_Setting_Section_Convert extends AmazonAutoLinks_AdminPage_Section_Base {

    protected function _getArguments() {
        return array(
            'section_id'    => 'convert_links',
            'tab_slug'      => $this->sTabSlug,
            'title'         => __( 'Convert Links', 'amazon-auto-links' ),
            'description'   => array(
                __( 'Convert Amazon links in post and comment contents.', 'amazon-auto-links' ),
            ),
        );
    }

    /**
     * A user constructor.
     *
     * @since       3.8.10
     * @return      void
     */
    protected function _construct( $oFactory ) {}

    /**
     * Adds form fields.
     * @since       3.8.10
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $_oOption     = AmazonAutoLinks_Option::getInstance();
        $_aRawOptions = $_oOption->getRawOptions();

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'              => 'enabled',
                'type'                  => 'radio',
                'title'                 => __( 'Enable', 'amazon-auto-links' ),
                'label'                 => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'value' => $this->getElement( $_aRawOptions, array( 'convert_links', 'enabled' ) ), // backward compatibility
            ),
            array(
                'field_id'  => 'where',
                'type'      => 'checkbox',
                'title'     => __( 'Applies to', 'amazon-auto-links' ),
                'label'     => array(
                    'the_content'     => __( 'Post contents', 'amazon-auto-links' ),
                    'comment_text'    => __( 'Comments', 'amazon-auto-links' ),
                ),
                'value' => $this->getElement( $_aRawOptions, array( 'convert_links', 'where' ) ), // backward compatibility
            ),
            array(
                'field_id'      => 'filter_hooks',
                'type'          => 'textarea',
                'title'         => __( 'Custom Filter Hooks', 'amazon-auto-links' ),
                'description'   => __( 'If the areas to apply link conversion are not listed above and if you know the filter hook to apply to, specify here one per line.', 'amazon-auto-links' ),
                'value' => $this->getElement( $_aRawOptions, array( 'convert_links', 'filter_hooks' ) ), // backward compatibility
            ),
            array(
                'field_id'  => 'hook_priority',
                'type'      => 'number',
                'title'     => __( 'Hook Priority', 'amazon-auto-links' ),
                'tip'       => __( 'If the links are not converted, try increase the value.', 'amazon-auto-links' ),
            ),
            array()
        );

    }

    /**
      * Called upon form validation.
      *
      * @callback        filter      'validation_{class name}_{section id}'
      */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {

        $_aErrors       = array();

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        $_oOption       = AmazonAutoLinks_Option::getInstance();
        $_sAssociateID  = trim( ( string ) $_oOption->getAssociateID( $_oOption->getMainLocale() ) );
        if ( ! $_sAssociateID ) {
            $oAdminPage->setFieldErrors( $_aErrors );
            $_sMessage = __( 'Please set the default Amazon Associate ID first.', 'amazon-auto-links' ) . ' '
                . sprintf(
                    __( 'Go to <a href="%1$s">set</a>.', 'amazon-auto-links' ),
                    esc_url( add_query_arg(
                        array(
                            'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                            'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                            'tab'       => 'associates',
                        ),
                        'edit.php'
                    ) )
                );
            $oAdminPage->setSettingNotice( $_sMessage );
            return $aOldInputs;
        }

        $this->___unsetLegacyOptions();
        return $aInputs;

    }
        /**
         * Cleans up old option values.
         * @since 4.7.0
         */
        private function ___unsetLegacyOptions() {
            $_aOptions = get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ] );
            if ( empty( $_aOptions ) ) {
                return;
            }
            unset( $_aOptions[ 'convert_links' ] );
            update_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], $_aOptions );
        }

}