<?php
/**
 * Provides methods to retrieve field definitions for locales.
 *
 * @since 3.10.0
 * @since 4.5.0 Change the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_Unit_Locale extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     *
     * @param   string $sFieldIDPrefix
     * @return  array
     */    
    public function get( $sFieldIDPrefix='' ) {

        $_sLocale      = $this->oFactory->getValue( 'country' );
        $_bAPIKeysSet  = $this->oOption->isPAAPIKeySet( $_sLocale );
        $_aAttributes  = $_bAPIKeysSet
            ? array()
            : array(
                'disabled' => 'disabled',
                'class'    => 'disabled read-only',
            );
        return array(
            array(
                'field_id'      => $sFieldIDPrefix . 'country',
                'type'          => 'text',
                'title'         => __( 'Country', 'amazon-auto-links' ),
                'attributes'    => array(
                    'readonly'=> 'readonly',
                ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'language',
                'type'              => 'select',
                'title'             => __( 'Preferred Language', 'amazon-auto-links' ),
                'label'             => array(), // will be assigned in the field_{class name} callback
                'tip'               => __( 'When the desired language is not available for the item, the default one set by Amazon will be applied.', 'amazon-auto-links' ),
                'description'       => $_bAPIKeysSet
                    ? ''
                    : $this->getAPIKeyUnsetWarning( $_sLocale ),
                'attributes'        => $_aAttributes,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'preferred_currency',
                'type'              => 'select',
                'title'             => __( 'Preferred Currency', 'amazon-auto-links' ),
                'label'             => array(), // will be assigned in the field_{class name} callback
                'tip'               => __( 'When the desired currency is not available for the item, the default one set by Amazon will be applied.', 'amazon-auto-links' ),
                'description'       => $_bAPIKeysSet
                    ? ''
                    : $this->getAPIKeyUnsetWarning( $_sLocale ),
                'attributes'        => $_aAttributes,
            ),
        );

    }
  
}