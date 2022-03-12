<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Button2_FormFields_Preview extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        
        return array(
            array(
                'field_id'          => $sFieldIDPrefix . 'button_preview',
                'type'              => '_preview_button',
                'show_title_column' => false,
                'attributes'        => array(
                    'name'               => '',
                ), 
                'save'              => false,
                'before_field'      => "<div style='margin: 3em 3em 3em 0; width:100%;'>"
                    . "<div style='margin-left: auto; margin-right: auto; '>" // text-align:center;
                        . '<p>Iframed button preview will be displayed here</p>'
                    . "</div>"
                . "</div>",
                'default'   => '',
            
            )
        );
        
    }
      
}