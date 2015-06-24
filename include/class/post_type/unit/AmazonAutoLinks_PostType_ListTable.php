<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Provides methods for rendering post listing table contents.
 * 
 * @package     Amazon Auto Links
 * @since       3
 */
class AmazonAutoLinks_PostType_ListTable extends AmazonAutoLinks_AdminPageFramework_PostType {
    
    /**
    * Defines the column header of the unit listing table.
    * 
    * @callback     filter      columns_{post type slug}
    * @return       array
    */
   public function replyToModifyColumnHeader( $aHeaderColumns ) {    
        return array(
            'cb'                    => '<input type="checkbox" />',   
            'title'                 => __( 'Unit Name', 'amazon-auto-links' ),    
            'unit_type'             => __( 'Unit Type', 'amazon-auto-links' ),
            'template'              => __( 'Template', 'amazon-auto-links' ),
            'amazon_auto_links_tag' => __( 'Labels', 'amazon-auto-links' ),  
            'code'                  => __( 'Shortcode / PHP Code', 'amazon-auto-links' ),
        );                      
    }    
        
    /**
     * 
     * @callback        filter      cell_ + post type slug + column name
     * @return          string
     */
    public function cell_amazon_auto_links_amazon_auto_links_tag( $sCell, $iPostID ) {    
        
        // Get the genres for the post.
        $_aTerms = get_the_terms( 
            $iPostID, 
            AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ]
        );
    
        // If no tag is assigned to the post,
        if ( empty( $_aTerms ) ) { 
            return '—'; 
        }
        
        // Loop through each term, linking to the 'edit posts' page for the specific term. 
        $_aOutput = array();
        foreach( $_aTerms as $_oTerm ) {
            $_aOutput[] = sprintf( 
                '<a href="%s">%s</a>',
                esc_url( 
                    add_query_arg( 
                        array( 
                            'post_type' => $GLOBALS[ 'post' ]->post_type, 
                            AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ] => $_oTerm->slug 
                        ), 
                        'edit.php' 
                    ) 
                ),
                esc_html( 
                    sanitize_term_field( 
                        'name', 
                        $_oTerm->name, 
                        $_oTerm->term_id, 
                        AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ], 
                        'display' 
                    ) 
                )
            );
        }

        // Join the terms, separating them with a comma.
        return join( ', ', $_aOutput );
        
    }
    /**
     * @callback        filter      cell_{post type slug}_{column key}
     */
    public function cell_amazon_auto_links_unit_type( $sCell, $iPostID ) {
        
        $_sUnitType       = get_post_meta( $iPostID, 'unit_type', true );
        $_aUnitTypeLabels = AmazonAutoLinks_PluginUtility::getUnitTypeLabels();
        return isset( $_aUnitTypeLabels[ $_sUnitType ] )
            ? $_aUnitTypeLabels[ $_sUnitType ]
            : __( 'Category', 'amazon-auto-links' );
        
    }
    /**
     * @callback        filter      cell_{post type slug}_{column key}
     */
    public function cell_amazon_auto_links_template( $sCell, $iPostID ) {
        return AmazonAutoLinks_TemplateOption::getInstance()->getTemplateNameByID( 
            get_post_meta( $iPostID, 'template_id', true ) // template id
        );        
    }    
    
    /**
     * @callback        filter      cell_{post type slug}_{column name}
     */
    public function cell_amazon_auto_links_code( $sCell, $iPostID ) {
        return '<p>'
                . '<span>[amazon_auto_links id="' . $iPostID . '"]</span>' . '<br />'
                . '<span>&lt;?php AmazonAutoLinks( array( ‘id’ =&gt; ' . $iPostID . ' ) ); ?&gt;</span>'
            . '</p>';
    }

}