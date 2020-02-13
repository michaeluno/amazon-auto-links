<?php
/**
 * Provides methods to retrieve field definitions for locales.
 *
 * @since           3.10.0
 */
class AmazonAutoLinks_FormFields_Unit_Locale extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {

        $_bAPIKeysSet  = $this->oOption->isAPIKeySet();
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
                'description'       => array(
                    __( 'When the desired language is not available for the item, the default one set by Amazon will be applied.', 'amazon-auto-links' ),
                    $_bAPIKeysSet
                        ? ''
                        : $this->getAPIKeyUnsetWarning(),
                ),
                'attributes'        => $_aAttributes,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'preferred_currency',
                'type'              => 'select',
                'title'             => __( 'Preferred Currency', 'amazon-auto-links' ),
                'label'             => array(), // will be assigned in the field_{class name} callback
                'description'       => array(
                    __( 'When the desired currency is not available for the item, the default one set by Amazon will be applied.', 'amazon-auto-links' ),
                    $_bAPIKeysSet
                        ? ''
                        : $this->getAPIKeyUnsetWarning(),
                ),
                'attributes'        => $_aAttributes,
            ),
        );

    }
  
}