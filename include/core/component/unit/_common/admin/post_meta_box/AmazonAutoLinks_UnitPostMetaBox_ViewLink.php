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
 * Defines the meta box that shows the Select Categories submit button
 */
class AmazonAutoLinks_UnitPostMetaBox_ViewLink extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    public function setUp() {
        
        if ( ! isset( $_GET[ 'post' ] ) ) {
            return;
        }        
        add_action(
            "do_" . $this->oProp->sClassName,
            array( $this, 'replyToPrintMetaBoxConetnt' )
        );
        
    }
    
    /**
     * Draws the Select Category submit button and some other links.
     */
    public function replyToPrintMetaBoxConetnt( $oFactory ) {
        
        $_sViewLink    = esc_url( get_permalink( $this->_iPostID ) );
        ?>
        <div style="padding: 0.8em 0 1.5em; ">
            <div style="text-align: center;">
                <p style="font-size: 1.2em; margin-bottom: 1.5em;"><a style="text-decoration: none;" href="<?php echo $_sViewLink; ?>"><?php _e( 'View Unit', 'amazon-auto-links' ); ?></a></p>
            </div>
        </div>
        <?php
    
    }

}