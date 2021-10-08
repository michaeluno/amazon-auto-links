<?php
/**
 * Provides the definitions of form fields for the 'tag' unit type.
 * 
 * @since  3
 * @since  4.5.0 Changed the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_SearchUnit_SearchType extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     *
     * @param  string $sFieldIDPrefix
     * @return array
     */    
    public function get( $sFieldIDPrefix='' ) {
        return array(
            array(
                'field_id'          => $sFieldIDPrefix . 'unit_title',
                'type'              => 'text',
                'title'             => __( 'Unit Name', 'amazon-auto-links' ),
                'description'       => 'e.g. <code>My Search Unit</code>',
                'class'             => array(
                    'input' => 'width-full',
                    'field' => 'width-half',
                ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'country',
                'type'              => 'select2',
                'title'             => __( 'Country', 'amazon-auto-links' ),
                'label'             => $this->getPAAPILocaleFieldLabels(),
                'icon'              => $this->getLocaleIcons( array_keys( $this->getPAAPILocaleFieldLabels() ) ),
                'description'       => sprintf(
                    __( 'If the country is not listed, set PA-API keys in the <a href="%1$s">Associates</a> section.', 'amazon-auto-links' ),
                    $this->getAPIAuthenticationPageURL()
                ),
                'default'           => AmazonAutoLinks_Option::getInstance()->getMainLocale(),
            ),                
            array(
                'field_id'          => $sFieldIDPrefix . 'associate_id',
                'type'              => 'text',
                'title'             => __( 'Associate ID', 'amazon-auto-links' ),
                'description'       => 'e.g. <code>miunosoft-20</code>',
            ),        
            array(
                'field_id'          => $sFieldIDPrefix . 'Operation',
                'type'              => 'radio',
                'title'             => __( 'Type', 'amazon-auto-links' ),
                'label_min_width'   => '100%',
                'label'             => $this->getAsArray( apply_filters( 'aal_filter_unit_paapi_unit_types', array() ) ),
                'default'           => 'SearchItems',
            ),
        );
    }

}