<?php
/**
 * Provides the definitions of form fields for the 'scratchpad_payload' unit type.
 * 
 * @since  4.1.0
 * @since  4.5.0    Changed the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_ScratchPadPayloadUnit_Main extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options.
     *
     * @param  string $sFieldIDPrefix
     * @return array
     */    
    public function get( $sFieldIDPrefix='' ) {

        $_aFields = array(  
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'scratchpad_payload',
            ),          
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
                'type'          => 'text',
                'description'   => 'e.g. <code>My Custom PA-API Payload Unit</code>',
                'value'         => '',    // a previous value should not appear
                'class'         => array(
                    'input' => 'width-full',
                    'field' => 'width-half',
                ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'payload',
                'type'          => 'textarea',
                'title'         => __( 'Payload JSON', 'amazon-auto-links' ),
                'description'   => array(
                    sprintf(
                    __( 'Set the payload value generated on <a href="%1$s" target="_blank">ScratchPad</a>.', 'amazon-auto-links' ),
                    'https://webservices.amazon.it/paapi5/scratchpad/index.html#'
                    ),
                    'e.g. <pre style="width:75%"><code>{
 "Keywords": "WordPress",
 "PartnerTag": "testing-21",
 "PartnerType": "Associates",
 "Marketplace": "www.amazon.com",
 "Operation": "SearchItems"
}</code></pre>',
                ),
                'tip'           => '<img src="' . $this->getSRCFromPath( AmazonAutoLinks_UnitTypeLoader_scratchpad_payload::$sDirPath . '/asset/image/payload_json.jpg' ) . '" style="width:100%"/>',
                'attributes'    => array(
                    'style' => 'height: 200px; width: 100%; min-width: 400px;'
                ),
                'class'         => array(
                    'input' => 'width-full',
                    'field' => 'width-two-third',
                ),
            ),
        );
        return $_aFields;
        
    }
  
}