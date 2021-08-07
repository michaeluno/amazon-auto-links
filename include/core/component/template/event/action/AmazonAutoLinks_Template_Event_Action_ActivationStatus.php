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
 * Activates a template by a template id
 *
 * @since       3.7.4
 */
class AmazonAutoLinks_Template_Event_Action_ActivationStatus extends AmazonAutoLinks_PluginUtility {

    /**
     * @var     array
     * @since   4.6.7
     */
    private $___aToggledTemplateNames = array();

    public function __construct() {

        add_action( 'aal_action_activate_templates', array( $this, 'replyToActivateTemplates' ), 10 );
        add_action( 'aal_action_deactivate_templates', array( $this, 'replyToDeactivateTemplates' ), 10 );

    }

    /**
     * @param array an array holding template IDs or template option arrays.
     * This is sed for an addon to register new template with activated. The template list table's actions use this as true. true: yes, false: no.
     */
    public function replyToActivateTemplates( array $aTemplateIDs ) {
        $this->___getTemplatesStatusToggled( $aTemplateIDs, true );
    }
    /**
     * @param array an array holding template IDs or template option arrays.
     */
    public function replyToDeactivateTemplates( array $aTemplateIDs ) {
        $this->___getTemplatesStatusToggled( $aTemplateIDs, false );
    }

        /**
         * @param array $aTemplateIDs
         * @param boolean $bActivate true for activation; false for deactivation
         */
        private function ___getTemplatesStatusToggled( array $aTemplateIDs, $bActivate ) {
            $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
            $_aTemplates      = $this->getAsArray( $_oTemplateOption->get() );
            foreach( $aTemplateIDs as $_sTemplateID ) {
                $_aTemplates = $this->___getTemplateStatusToggled( $_sTemplateID, $_aTemplates, $bActivate );
            }
            $_oTemplateOption->aOptions = $_aTemplates;
            $_oTemplateOption->save();

            $this->___setSettingNoticeOfToggleTemplates( $bActivate );
        }
            /**
             * @param boolean $bActivate
             * @since 4.6.7
             */
            private function ___setSettingNoticeOfToggleTemplates( $bActivate ) {
                $this->___aToggledTemplateNames = array_filter( $this->___aToggledTemplateNames ); // drop non-true values
                $_aTemplateNames = implode( ', ', $this->___aToggledTemplateNames );
                $_sMessage       = $bActivate
                    ? sprintf( __( 'The template, %1$s, has been activated.', 'amazon-auto-links' ), $_aTemplateNames )
                    : sprintf( __( 'The template, %1$s, has been deactivated.', 'amazon-auto-links' ), $_aTemplateNames );
                do_action( 'aal_action_set_admin_setting_notice', $_sMessage, 'updated' );
            }
            /**
             * @param  string   $sID            The passed templated ID.
             * @param  array    $aTemplates
             * @param  boolean  $bActivate
             * @return array
             */
            private function ___getTemplateStatusToggled( $sID, array $aTemplates, $bActivate ) {

                $_oTemplateOption   = AmazonAutoLinks_TemplateOption::getInstance();
                if ( isset( $aTemplates[ $sID ] ) && $_oTemplateOption->exists( $sID ) ) {
                    return $this->___getTemplatesWithUpdatedActivationStatus( $aTemplates, $sID, $bActivate, $_oTemplateOption );
                }

                // At this point, an un-stored template is given.
                // The id may be a relative path of the template directory
                // $_sDirPath = $this->getAbsolutePathFromRelative( $sID ); // @deprecated 4.6.17 Causes duplicated templates with a slightly different template ID in the list table when the site has a custom WP_CONTENT_URL & WP_CONTENT_DIR
                $_sDirPath = $this->___getTemplateDirPath( $sID, $_oTemplateOption );

                unset( $aTemplates[ $sID ] );   // 4.6.17 drop the non-existent template
                if ( ! file_exists( $_sDirPath ) ) {
                    $_aErrorInfo = array(
                        'template_id' => $sID,
                        'dir_path'    => $_sDirPath,
                        'activate'    => $bActivate,
                    );
                    $_sMessage = __( 'The given template does not exist so could not toggle the activation status.', 'amazon-auto-links' );
                    new AmazonAutoLinks_Error( 'TEMPLATE_ACTIVATION_STATUS', $_sMessage, $_aErrorInfo, false );
                    do_action( 'aal_action_set_admin_setting_notice', $_sMessage, 'error' );
                    return $aTemplates;
                }

                $_aTemplate         = $_oTemplateOption->getTemplateArrayByDirPath( $_sDirPath );
                $_sTemplateID       = $_aTemplate[ 'id' ];

                $this->___aToggledTemplateNames[] = $this->getElement( $_aTemplate, 'name' );

                // The newly generated template ID already exists. So don't touch. The deactivated item will be deleted and gone.
                if ( isset( $aTemplates[ $_sTemplateID ] ) ) {
                    return $aTemplates;
                }

                $aTemplates[ $_sTemplateID ] = array(
                    'is_active' => $bActivate,
                ) + $this->getAsArray( $_aTemplate );
                return $aTemplates;

            }
                /**
                 * @param  string $sTemplateID
                 * @param  AmazonAutoLinks_TemplateOption $_oTemplateOption
                 * @return string
                 * @since  4.6.17
                 */
                private function ___getTemplateDirPath( $sTemplateID, $_oTemplateOption ) {
                    $_aAvailableTemplates = $_oTemplateOption->getActiveTemplates() + $_oTemplateOption->getUploadedTemplates();
                    foreach( $_aAvailableTemplates as $_sTemplateID => $_aTemplate ) {
                        if ( $_sTemplateID !== $sTemplateID ) {
                            continue;
                        }
                        return $this->getElement(
                            $_aTemplate,
                            array( 'dir_path' ),
                            $this->getElement( $_aTemplate, array( 'strDirPath' ) ) // backward-compat
                        );
                    }
                    return '';
                }
                /**
                 * @param  array     $aTemplates
                 * @param  string    $sTemplateID
                 * @param  boolean   $bActivate
                 * @param  AmazonAutoLinks_TemplateOption $oTemplateOption
                 * @return array
                 * @since  4.6.7
                 */
                private function ___getTemplatesWithUpdatedActivationStatus( array $aTemplates, $sTemplateID, $bActivate, $oTemplateOption ) {

                    $this->___aToggledTemplateNames[] = $this->getElement( $aTemplates, array( $sTemplateID, 'name' ) );

                    // @since 4.6.17 If deactivating, remove the template array
                    if ( ! $bActivate ) {
                        unset( $aTemplates[ $sTemplateID ] );
                        return $aTemplates;
                    }

                    // @since 3.10.0 If activating, renew the template information.
                    $_sDirPath  = $this->___getTemplateDirPath( $sTemplateID, $oTemplateOption );
                    $_aTemplate = file_exists( $_sDirPath )
                        ? $this->getAsArray( $oTemplateOption->getTemplateArrayByDirPath( $_sDirPath ) )
                        : array();
                    $_aTemplate = array(
                            'is_active' => true,
                        )
                        + $_aTemplate
                        + $this->getElementAsArray( $aTemplates, $sTemplateID );
                    $aTemplates[ $sTemplateID ] = $_aTemplate;
                    return $aTemplates;
                }

}