<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Defines the meta box that shows the Select Categories submit button
 */
class AmazonAutoLinks_UnitPostMetaBox_ViewLink extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    public function setUp() {
        
        if ( ! isset( $_GET[ 'post' ] ) ) {     // sanitization unnecessary as just checking
            return;
        }        
        add_action(
            "do_" . $this->oProp->sClassName,
            array( $this, 'replyToPrintMetaBoxContent' )
        );
        
    }
    
    /**
     * Draws the Select Category submit button and some other links.
     */
    public function replyToPrintMetaBoxContent( $oFactory ) {
        ?>
        <div style="padding: 0.8em 0 1.5em; ">
            <div style="text-align: center;">
                <p style="font-size: 1.2em; margin-bottom: 1.5em;">
                    <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url( get_permalink( $this->_iPostID ) ); ?>">
                        <?php _e( 'View Unit', 'amazon-auto-links' ); ?>
                        &nbsp;<span class='dashicons dashicons-external' style="vertical-align: text-bottom;"></span>
                    </a>
                </p>
            </div>
        </div>
        <?php
    }

}