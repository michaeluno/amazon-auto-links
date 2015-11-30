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
     * Sets up hooks.
     * @since       3.2.0
     */
    public function setUp() {
    
        if (  $this->_isInThePage() ) {
            
            // unit listing table columns
            add_filter(    
                'columns_' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                array( $this, 'replyToModifyColumnHeader' )
            );            
            
            // Add an warning icon to the tag unit type's action link.
            add_filter( 
                'post_row_actions',
                array( $this, 'replyToModifyActionLinks' ), 
                10, 
                2 
            );
            
        }
    
    }
    
    /**
     * @return  array
     */
    public function replyToModifyActionLinks( $aActionLinks, $oPost ){
        
        if ( $oPost->post_type !== $this->oProp->sPostType ){
            return $aActionLinks;
        }
        
        $_sUnitType = get_post_meta( $oPost->ID, 'unit_type', true );
        if ( 'tag' !== $_sUnitType )  {
            return $aActionLinks;
        }
            
        $aActionLinks[ 'tag_deprecated_warning' ] = $this->_getTagDeprecateWarning();
        return $aActionLinks;
        
        
    }    
        /**
         * @since       3.2.0
         * @return      string       
         */
        private function _getTagDeprecateWarning() {
            
            $_sTitle              = esc_attr( 
                __( 'Amazon has deprecated the tags feature. So this is no longer functional.', 'amazon-auto-links' )
            );
            $_sWarning            = esc_attr( __( 'Warning!', 'amazon-auto-links' ) );
            $_sURL                = 'https://www.amazon.com/gp/help/customer/display.html?nodeId=16238571';
            $_sExclamationIconURL = AmazonAutoLinks_Registry::getPluginURL( 'asset/image/exclamationmark_16x16.png' );
            return "<a href='{$_sURL}' target='_blank'>"
                . "<img src='{$_sExclamationIconURL}' alt='{$_sWarning}' title='{$_sTitle}' />"
                . "</a> ";
            
        }
    
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
            'feed'                  => __( 'Feed', 'amazon-auto-links' ), // 3.1.0+
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
     * @return          string
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
     * @return          string
     */
    public function cell_amazon_auto_links_template( $sCell, $iPostID ) {
        return AmazonAutoLinks_TemplateOption::getInstance()->getTemplateNameByID( 
            get_post_meta( $iPostID, 'template_id', true ) // template id
        );        
    }    
    
    /**
     * @callback        filter      cell_{post type slug}_{column name}
     * @return          string
     */
    public function cell_amazon_auto_links_code( $sCell, $iPostID ) {
        return '<p>'
                . '<span>[amazon_auto_links id="' . $iPostID . '"]</span>' . '<br />'
                . '<span>&lt;?php AmazonAutoLinks( array( ‘id’ =&gt; ' . $iPostID . ' ) ); ?&gt;</span>'
            . '</p>';
    }

    /**
     * @callback        filter      cell_{post type slug}_{column name}
     * @return          string
     */
    public function cell_amazon_auto_links_feed( $sCell, $iPostID ) {
           
        // Feed by ID
        $_aOutput   = array();
        $_aOutput[] = "<p>"
                . $this->_getFeedIcon( 
                    'rss2', 
                    __( 'RSS Feed by ID', 'amazon-auto-links' ), 
                    'id', 
                    $iPostID 
                )
                . $this->_getFeedIcon( 
                    'json', 
                    __( 'JSON Feed by ID', 'amazon-auto-links' ), 
                    'id', 
                    $iPostID 
                ) 
            . "</p>";
        return implode( '', $_aOutput );
        
    }    
        /**
         * 
         * @since       3.1.0
         * @return      string
         */
        private function _getFeedIcon( $sType, $sLabel, $sKey, $mValue ) {
            $_oOption     = AmazonAutoLinks_Option::getInstance();
            $_sQueryKey   = $_oOption->get( 'query', 'cloak' );                    
            $_sImgURL     = AmazonAutoLinks_Registry::getPluginURL(                 
                'rss2' === $sType 
                    ? 'asset/image/rss16x16.gif'
                    : 'asset/image/json16x16.gif'
            );
            return "<span class='feed-icon'>"
                . $this->oUtil->generateHTMLTag(
                    'a',
                    array(
                        'href'      => add_query_arg(
                            array(
                                $_sQueryKey => 'feed',
                                'output'    => $sType,
                                $sKey       => $mValue,
                            ),
                            site_url()
                        ),
                        'target'    => '_blank',
                        'title'     => $sLabel,
                    ),
                    $this->oUtil->generateHTMLTag(
                        'img',
                        array(
                            'src' => esc_url( $_sImgURL ),
                            'alt' => $sLabel,
                        )
                    )
                )
                . "</span>";
        }
    
}