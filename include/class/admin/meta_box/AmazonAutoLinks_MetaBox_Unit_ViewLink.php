<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Defines the meta box that shows the Select Categories submit button
 */
class AmazonAutoLinks_MetaBox_Unit_ViewLink extends AmazonAutoLinks_MetaBox_Base {

    /**
     * Stores the unit type slug(s). 
     * 
     * The meta box will not be added to a unit type not listed in this array.
     * 
     * @remark      This property is checked in the `_isInThePage()` method
     * so set the unit types of that this meta box shuld apper.
     */    
    protected $aUnitTypes = array( 
        'category', 
        'similarity_lookup',
        'item_lookup',
        'search',
        'tag',
        'url',   // 3.2.0+
    );    
    
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
        
        $_sViewLink              = esc_url( get_permalink( $_GET[ 'post' ] ) );
        ?>

        <div style="padding: 0.8em 0 1.5em; ">
            <div style="text-align: center;">
                <p style="font-size: 1.2em; margin-bottom: 1.5em;"><a style="text-decoration: none;" href="<?php echo $_sViewLink; ?>"><?php _e( 'View Unit', 'admin-page-framewor' ); ?></a></p>
            </div>            
        </div>
        <?php
    
    }    
    
}