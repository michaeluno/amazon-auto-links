<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Activates a template by a template id
 *
 * @since       3.7.4
 */
class AmazonAutoLinks_TemplateActivator extends AmazonAutoLinks_PluginUtility {

    public function __construct() {

        add_action( 'aal_action_activate_templates', array( $this, 'replyToActivateTemplates' ), 10, 2 );;
        add_action( 'aal_action_deactivate_templates', array( $this, 'replyToDeactivateTemplates' ), 10, 2 );;

    }

    /**
     * @param array an array holding template IDs or template option arrays.
     * @param   boolean $bForce     Whether or not to override the existing `is_active` option.
     * This is sed for an addon to register new template with activated. The template list table's actions use this as true. true: yes, false: no.
     */
    public function replyToActivateTemplates( array $aTemplateIDs, $bForce ) {
        $this->___getTemplatesStatusToggled( $aTemplateIDs, true, $bForce );
    }
    /**
     * @param array an array holding template IDs or template option arrays.
     */
    public function replyToDeactivateTemplates( array $aTemplateIDs, $bForce ) {
        $this->___getTemplatesStatusToggled( $aTemplateIDs, false, $bForce );
    }

        /**
         * @param array $aTemplateIDs
         * @param boolean   $bActivate true for activation; false for deactivation
         */
        private function ___getTemplatesStatusToggled( array $aTemplateIDs, $bActivate, $bForce ) {
            $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
            $_aTemplates      = $this->getAsArray( $_oTemplateOption->get() );
            foreach( $aTemplateIDs as $_sID ) {
                $_aTemplates = $this->___getTemplateStatusToggled( $_sID, $_aTemplates, $bActivate, $bForce );
            }
            $_oTemplateOption->aOptions = $_aTemplates;
            $_oTemplateOption->save();

        }
            /**
             * @param $sID
             * @param array $aTemplates
             * @param $bActivate
             * @param $bForce
             *
             * @return array
             */
            private function ___getTemplateStatusToggled( $sID, array $aTemplates, $bActivate, $bForce ) {

                $_oTemplateOption   = AmazonAutoLinks_TemplateOption::getInstance();
                $_aTemplate         = array();

                if ( isset( $aTemplates[ $sID ] )  ) {
                    if ( $bForce ) {
                        // @since 3.10.0 If activating, renew the template information.
                        if ( $bActivate ) {
                            $_sDirPath  = $this->getAbsolutePathFromRelative( $sID );
                            $_aTemplate = file_exists( $_sDirPath )
                                ? $_oTemplateOption->getTemplateArrayByDirPath( $_sDirPath )
                                : array();
                            $_aTemplate = $this->getAsArray( $_aTemplate );
                        }

                        $aTemplates[ $sID ] = array(
                            'is_active' => $bActivate,
                        )
                            + $_aTemplate
                            + $this->getElementAsArray( $aTemplates, $sID );
                    }
                    return $aTemplates;
                }

                // At this point, an unstored template is given.

                // The id may be a relative path of the template directory
                $_sDirPath = $this->getAbsolutePathFromRelative( $sID );
                if ( ! file_exists( $_sDirPath ) ) {
                    return $aTemplates;
                }

                $_aTemplate         = $_oTemplateOption->getTemplateArrayByDirPath( $_sDirPath );
                $aTemplates[ $sID ] = array(
                    'is_active' => $bActivate,
                ) + $_aTemplate;
                return $aTemplates;

            }

}