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
class AmazonAutoLinks_UnitPostMetaBox_Submit_category extends AmazonAutoLinks_UnitPostMetaBox_Base {

    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'category' );
    
    public function setUp() {
        
        if ( ! isset( $_GET[ 'post' ] ) ) {
            return;
        }        
        add_action(
            "do_" . $this->oProp->sClassName,
            array( $this, 'printMetaBoxContent' )
        );
        
    }
    
    /**
     * Draws the Select Category submit button and some other links.
     */
    public function printMetaBoxContent( $sContent ) {
        
        $_sViewLink              = esc_url( get_permalink( $_GET[ 'post' ] ) );
        $_sSelectCategoryPageURL = add_query_arg( 
            array( 
                'mode'          => 0,
                'page'          => AmazonAutoLinks_Registry::$aAdminPages[ 'category_select' ],
                'tab'           => 'second',
                'post_type'     => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'post'          => $_GET[ 'post' ],
                'transient_id'  => uniqid(),
                'aal_action'    => 'select_category',
            ),
            admin_url( 'edit.php' ) 
        );
        ?>

        <div style="padding: 1.5em 0">
            <div style="text-align: center;">
                <a class="button button-primary button-large" href="<?php echo esc_url( $_sSelectCategoryPageURL ); ?>">
                    <?php _e( 'Select Categories', 'amazon-auto-links' ); ?>
                </a>
            </div>        
        </div>
        <?php

        
        $_aCategories           = $this->oUtil->getAsArray(
            get_post_meta( 
                $_GET[ 'post' ], 
                'categories', 
                true 
           )
        );
        $_aExcludingCategories  = $this->oUtil->getAsArray(
            get_post_meta( 
                $_GET[ 'post' ], 
                'categories_exclude', 
                true 
            )
        );
       
        $_aCategoryList = array();
        foreach( $_aCategories as $_aCategory ) {
            $_aCategoryList[] = "<li style=''>" 
                    . $_aCategory[ 'breadcrumb' ] 
                . "</li>";
        }
        $_aExcludingCategoryList = array();
        foreach( $_aExcludingCategories as $_aCategory ) {
            $_aExcludingCategoryList[] = "<li style=''>" 
                    . $_aCategory['breadcrumb'] 
                . "</li>";            
        }
        
        if ( empty( $_aCategoryList ) ) {
            $_aCategoryList[] = "<li>" 
                    . __( 'No category added.', 'amazon-auto-links' ) 
                . "</li>";
        }
        if ( empty( $_aExcludingCategoryList ) ) {
            $_aExcludingCategoryList[] = "<li>" 
                    . __( 'No excluding sub-category added.', 'amazon-auto-links' ) 
                . "</li>";
        }
            
        echo "<h4 style='text-align: center'>" 
                . __( 'Added Categories', 'amazon-auto-links' ) 
            . "</h4>"
            . "<div style='text-align: center; font-weight: normal; padding-bottom: 0.5em;'>"
                . "<ul style='margin-left:0;'>" 
                    . implode( '', $_aCategoryList ) 
                . "</ul>"
            . "</div>"
            . "<h4 style='text-align: center'>" 
                . __( 'Added Excluding Sub-categories', 'amazon-auto-links' ) 
            . "</h4>"
            . "<div style='text-align: center; font-weight: normal; padding-bottom: 0.5em;'>"
                . "<ul style='margin-left:0;'>" 
                    . implode( '', $_aExcludingCategoryList )
                . "</ul>"
            . "</div>";
    
    }    
    
}