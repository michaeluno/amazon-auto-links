<?php
/**
 * Provides the form fields definitions.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_Button_Selector extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        
        $_aFields       = array(
            array(
                'field_id'          => $sFieldIDPrefix . 'button_id',
                'type'              => 'select',            
                'title'             => __( 'Select Button', 'amazon-auto-links' ),
                'class'             => array(
                    'fieldrow'      => 'button-select-row',
                ),   
                'tip'               => sprintf(
                    __( 'Select the button for the <code>%1$s</code> parameter of the Item Format option.', 'amazon-auto-links' ),
                    '%button%'
                ),
                'description'       => array(
                    sprintf(
                        __( 'Buttons can be created from <a href="%1$s">this screen</a>.', 'amazon-auto-links' ),
                        add_query_arg(
                            array(
                                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                            ),
                            admin_url( 'edit.php' )
                        )
                    ),                    
                ),
                // The label argument will be set with the 'field_definition_{...}' filter as it performs a database query.
                'after_field'       => "<div style='margin: 3em 3em 3em 0; width:100%;'>"
                    . "<div style='margin-left: auto; margin-right: auto; '>" // text-align:center;
                        // plugin button type
                        . AmazonAutoLinks_PluginUtility::getButton( 
                            '__button_id__', 
                            '',     // label - use default by passing an empty string
                            false   // hidden - the script will make it visible
                        )
                        // The <button> tag type
                        . $this->___getIframeButtonPreview()
                    . "</div>"
                . "</div>"
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'override_button_label',
                'type'              => 'revealer',
                'select_type'       => 'checkbox',
                'show_title_column' => false,
                'label'             => __( 'Override the button label.', 'amazon-auto-links' ),
                'selectors'         => '.button-label-field-row',
                'attributes'        => array(
                    'class' => 'override-button-label',
                ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'button_label',
                'type'              => 'text',
                'show_title_column' => false,
                'class'             => array(
                    'fieldrow' => 'button-label-field-row',
                ),
                'attributes'        => array(
                    'class' => 'button-label',
                ),
                'default'           => '',
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'button_type',
                'type'              => 'radio',            
                'title'             => __( 'Button Link Type', 'amazon-auto-links' ),
                'label'             => array(
                    0   => __( 'Link to the product page.', 'amazon-auto-links' ),
                    1   => __( 'Add to cart.', 'amazon-auto-links' ),
                ),
                'default'           => 1,
            ), 
        );
       
        return $_aFields;
        
    }
        private function ___getIframeButtonPreview() {
            $_aAttributes = array(
                'class'       => 'frame-button-preview',
                'src'         => $this->___getButtonPreviewURL( 0 ),
                'frameborder' => '0',
                'border'      => '0',
//                'onload'      => "javascript:(function(o){_oButton=o.contentWindow.document.getElementById('preview-button'); if ('undefined' !== typeof _oButton && null !== _oButton){var _iW=_oButton.offsetWidth; var _iH=_oButton.offsetHeight;o.width=_iW;o.height=_iH;o.style.width=_iW+'px';o.style.height=_iH+'px';o.style.display='block';o.style.margin='0 auto';console.log('inline',_oButton.offsetWidth,_oButton.offsetHeight);}}(this));",
                'style'       => 'height:60px;border:none;overflow:hidden;',
                'width'       => '200',
                'height'      => '60',
                'scrolling'   => 'no',
            );
            $_aContainerAttributes = array(
                'class'       => 'iframe-button-preview-container',
                'style'       => 'position:absolute;top:-9999px;z-depth:-100;',
            );
            return "<div " . $this->getAttributes( $_aContainerAttributes ) . ">"
                    . "<iframe " . $this->getAttributes( $_aAttributes ) . "></iframe>"
                . "</div>";
        }
        /**
         * @param integer $iButtonID
         * @return string
         * @since   4.3.0
         */
        private function ___getButtonPreviewURL( $iButtonID, $sButtonLabel=null ) {
            $_aQuery = array(
                'aal-button-preview' => 1,
                'button-id'          => $iButtonID,
                'button-label'       => $sButtonLabel,
            );
            $_aQuery = array_filter( $_aQuery, array( $this, 'isNotNull' ) );
            return add_query_arg( $_aQuery, get_site_url() );
        }

}