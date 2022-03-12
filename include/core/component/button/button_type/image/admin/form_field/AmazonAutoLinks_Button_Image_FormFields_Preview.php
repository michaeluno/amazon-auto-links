<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Image_FormFields_Preview extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        return array(
            array(
                'field_id'          => $sFieldIDPrefix . '_button_preview',
                'show_title_column' => false,
                'content'           => "<div style='margin: 3em 3em 3em 0; width:100%;'>"
                    . "<div style='margin-left: auto; margin-right: auto; '>" // text-align:center;
                        . $this->___getIframeButtonPreview()
                    . "</div>"
                . "</div>",
            )
        );
    }

        private function ___getIframeButtonPreview() {
            $_aAttributes = array(
                'class'       => 'frame-button-preview',
                'src'         => $this->___getButtonPreviewURL( $this->getHTTPQueryGET( array( 'post' ), '___button_id___' ), '' ),
                'frameborder' => '0',
                'border'      => '0',
                'style'       => 'height:60px;border:none;overflow:hidden;',
                // 'width'       => '200',
                // 'height'      => '60',
                'scrolling'   => 'no',
                'title'       => 'Image Button',
            );
            $_aContainerAttributes = array(
                'id'          => 'button-image-preview',
                'class'       => 'iframe-button-preview-container',
                // 'style'       => 'position:absolute;top:-9999px;z-depth:-100;',
            );
            return "<div " . $this->getAttributes( $_aContainerAttributes ) . ">"
                    . "<iframe " . $this->getAttributes( $_aAttributes ) . "></iframe>"
                . "</div>";
        }
        /**
         * @since  5.2.0
         * @param  integer|string $isButtonID
         * @return string
         */
        private function ___getButtonPreviewURL( $isButtonID, $sButtonLabel=null ) {
            $_aQuery = array(
                'aal-button-preview' => 'image',
                'button-id'          => $isButtonID,
                'button-label'       => $sButtonLabel,
            );
            $_aQuery = array_filter( $_aQuery, array( $this, 'isNotNull' ) );
            return add_query_arg( $_aQuery, get_site_url() );
        }

}