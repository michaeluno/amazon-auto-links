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
 * Adds the 'PhpZon' form section to the '3rd Party' tab.
 * 
 * @since       4.1.0
 */
class AmazonAutoLinks_PhpZonSupport_Setting_3rdParty_PhpZon extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @since  4.1.0
     * @return array
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => '3rd_party',
            'section_id'    => 'phpzon',       // avoid hyphen(dash), dots, and white spaces
            'capability'    => 'manage_options',
            'title'         => 'PhpZon',
            'description'   => array(
                __( 'Parse the shortcode of the PhpZon plugin.', 'amazon-auto-links' ),
            ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       4.1.0
     * @return      void
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       4.1.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'      => 'shortcodes',
                'type'          => 'checkbox',
                'title'         => __( 'Shortcodes', 'amazon-auto-links' ),
                'description'   => __( 'Check the shortcode of PhpZon to convert its outputs into Auto Amazon Links\'.', 'amazon-auto-links' ),
                'capability'    => 'manage_options',
                'label'         => array(
                    'phpzon'    => '<code>phpzon</code>',
                    'phpbay'    => '<code>phpbay</code>',
                ),
            ),
            array(
                'field_id'      => 'template_conversion',
                'repeatable'    => true,
                'type'          => 'inline_mixed',
                'title'         => __( 'Template Conversion', 'amazon-auto-links' ),
                'content'       => array(
                    array(
                        'field_id'      => 'aalb',
                        'title'         => 'AALB',
                        'type'          => 'select',
                        'label'         => $this->___getTemplateList(),
                    ),
                    array(
                        'field_id'  => '_arrow',
                        'save'      => 'false',
                        'content'   => '<span class="icon-right-arrow dashicons dashicons-arrow-right-alt"></span>',
                        'class'         => array(
                            'fieldset' => 'container-right-arrow'
                        ),
                    ),
                    array(
                        'field_id'      => 'aal',
                        'title'         => 'AAL',
                        'type'          => 'select',
                        'label'         => AmazonAutoLinks_TemplateOption::getInstance()->getUsableTemplateLabels(),
                    ),
                ),
            )
        );    
    
    }

        /**
         * @return array
         */
        private function ___getTemplateList() {
            return array(
                'asin'          => 'asin',
                'asin-tabs'     => 'asin-tabs',
                'columns'       => 'columns',
                'default'       => 'default',
                'default_'      => 'default_',
            );
        }


    /**
     * Validates the submitted form data.
     * 
     * @since       4.1.0
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        

        $_aConversionFormatted = array();
        foreach( $aInputs[ 'template_conversion' ] as $_iIndex => $_aPair ) {
            $_aConversionFormatted[ $_aPair[ 'aalb' ] ] = $_aPair[ 'aal' ];
        }
        $aInputs[ 'template_conversion_map' ] = $_aConversionFormatted;

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInputs;
        }
                
        return $aInputs;
        
    }
   
}