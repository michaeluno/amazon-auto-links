<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Defines the meta box that shows a button preview.
 */
class AmazonAutoLinks_MetaBox_Button_CSS extends AmazonAutoLinks_MetaBox_Button {

    
    public function setUp() {
        
        $this->addSettingFields(
            array(
                'field_id'      => 'button_css',
                'type'          => 'textarea',
                'title'         => __( 'Generated CSS', 'amazon-auto-links' ),
                'description'   => __( 'The generated CSS rules will look like this.', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style' => 'width: 100%; height: 320px;',
                ),
            ),
            array(
                'field_id'      => 'custom_css',
                'type'          => 'textarea',
                'title'         => __( 'Custom CSS', 'amazon-auto-links' ),
                'description'   => __( 'Enter additional CSS rules here.', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style' => 'width: 100%; height: 200px;',
                ),                
            )     
        );
        
        
    }
    
    /**
     * Draws the Select Category submit button and some other links.
     */
    public function replyToPrintMetaBoxConetnt( $oFactory ) {
        $_sPostTitle = isset( $_GET[ 'post' ] )
            ? get_the_title( $_GET[ 'post' ] )
            : '';
        $_sPostTitle = $_sPostTitle
            ? $_sPostTitle
            : __( 'Buy Now', 'amazon-auto-links' );

        ?>
        <div style="margin: 3em 0 1.5em 1em;">
            <div style="margin-left: auto; margin-right: auto; text-align:center;">
                <div class="amazon-auto-links-button"><?php echo $_sPostTitle; ?></div>
            </div>            
        </div>
        <?php
    }    
    
}