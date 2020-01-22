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
 * Defines the meta box that shows a button preview.
 */
class AmazonAutoLinks_PostMetaBox_Button_CSS extends AmazonAutoLinks_PostMetaBox_Button {

    
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_Button_CSS;
        foreach( $_oFields->get() as $_aField ) {            
            $this->addSettingFields( $_aField );
        }        

    }
    
    /**
     * Draws the Select Category submit button and some other links.
     * @deprecated
     */
/*     public function replyToPrintMetaBoxConetnt( $oFactory ) {
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
    }     */
    
}