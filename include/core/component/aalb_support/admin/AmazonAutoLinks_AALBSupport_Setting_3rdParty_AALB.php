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
 * Adds the 'AALB' form section to the '3rd Party' tab.
 * 
 * @since       3.11.0
 */
class AmazonAutoLinks_AALBSupport_Setting_3RdParty_AALB extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @since   3.11.1
     * @return array
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => '3rd_party',
            'section_id'    => 'aalb',       // avoid hyphen(dash), dots, and white spaces
            'capability'    => 'manage_options',
            'title'         => __( 'Amazon Associates Link Builder', 'amazon-auto-links' ),
            'description'   => array(
                __( 'Parse the shortcode and the Gutenberg block contents of Amazon Associates Link Builder (AALB) as the plugin is discontinued as of Feb 11, 2020.', 'amazon-auto-links' ),
            ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3.11.0
     * @return      void
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3.11.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'      => 'support',
                'type'          => 'checkbox',
                'title'         => __( 'Enable', 'amazon-auto-links' ),
                'capability'    => 'manage_options',
                'label'         => __( 'Attempts to display product links of the Amazon Associate Link Builder block contents and shortcode, <code>[amazon_link ...]</code>', 'amazon-auto-links' ),
                'default'       => 0,
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
                        'label'         => $this->___getAALBTemplateList(),
                    ),
                    array(
                        'field_id'  => '_arrow',
                        'save'      => 'false',
                        'content'   => ' -> ',
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
    private function ___getAALBTemplateList() {
        $_aTemplates = $this->getAsArray( get_option( 'aalb_template_names', array() ) );
        foreach( $_aTemplates as $_i => $_sName ) {
            unset( $_aTemplates[ $_i ] );
            $_aTemplates[ $_sName ] = $_sName;
        }
        return $_aTemplates + array(
            'PriceLink'       => 'PriceLink',
            'ProductAd'       => 'ProductAd',
            'ProductCarousel' => 'ProductCarousel',
            'ProductGrid'     => 'ProductGrid',
            'ProductLink'     => 'ProductLink',
        );
    }


    /**
     * Validates the submitted form data.
     * 
     * @since       3.11.0
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