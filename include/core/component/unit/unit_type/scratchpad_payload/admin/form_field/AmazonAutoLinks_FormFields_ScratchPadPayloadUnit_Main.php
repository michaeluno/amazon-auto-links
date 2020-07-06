<?php
/**
 * Provides the definitions of form fields for the 'scratchpad_payload' unit type.
 * 
 * @since           4.1.0
 */
class AmazonAutoLinks_FormFields_ScratchPadPayloadUnit_Main extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
            
        $_oOption = $this->oOption;
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
                'description'   => 'e.g. <code>My ScratchPad Payload Unit</code>',
                'value'         => '',    // a previous value should not appear
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
                    'e.g. <pre><code>{
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
            ),
        );
        return $_aFields;
        
    }
  
}