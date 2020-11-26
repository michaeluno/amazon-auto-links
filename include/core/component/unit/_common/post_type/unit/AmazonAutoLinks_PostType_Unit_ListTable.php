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
 * Provides methods for rendering post listing table contents.
 * 
 * @package     Amazon Auto Links
 * @since       3
 */
class AmazonAutoLinks_PostType_Unit_ListTable extends AmazonAutoLinks_AdminPageFramework_PostType {
        
    /**
     * Sets up hooks.
     * @since       3.2.0
     */
    public function setUp() {
    
        if ( $this->_isInThePage() ) {

            new AmazonAutoLinks_PostType_Unit__ActionLink_CloneUnit( $this, $this->_sNonceKey, $this->_sNonce );
            new AmazonAutoLinks_PostType_Unit__ActionLink_RenewCache( $this, $this->_sNonceKey, $this->_sNonce );
            new AmazonAutoLinks_PostType_Unit__ActionLink_TagUnitWarning( $this, $this->_sNonceKey, $this->_sNonce );

            // unit listing table columns
            add_filter(    
                'columns_' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                array( $this, 'replyToModifyColumnHeader' )
            );

            add_filter( "post_row_actions", array( $this, 'replyToModifyActionLinks' ), 10, 2 );

            
        }
    
    }

    /**
     * @param $aActions
     * @param WP_Post $oPost
     * @return array
     * @since   4.3.0
     */
    public function replyToModifyActionLinks( $aActions, $oPost ) {
        if ( $oPost->post_type !== $this->oProp->sPostType ) {
            return $aActions;
        }
        $aActions[ 'view' ] = links_add_target( $aActions[ 'view' ] );
        return $aActions;
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
            'status'                => __( 'Status', 'amazon-auto-links' ),
            'details'               => __( 'Details', 'amazon-auto-links' ),
            // 'unit_type'             => __( 'Unit Type', 'amazon-auto-links' ),
            // 'template'              => __( 'Template', 'amazon-auto-links' ),
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
     * @param    string       $sCell
     * @param    integer      $iPostID
     * @callback add_filter() cell_{post type slug}_{column key}
     * @return   string
     */
    public function cell_amazon_auto_links_details( $sCell, $iPostID ) {

        $_sID                 = __( 'ID', 'amazon-auto-links' );
        $_sLocale             = __( 'Country', 'amazon-auto-links' );
        $_sUnitType           = __( 'Unit Type', 'amazon-auto-links' );
        $_sTemplate           = __( 'Template', 'amazon-auto-links' );
        $_sThisUnitType       = get_post_meta( $iPostID, 'unit_type', true );
        $_sThisUnitTypeLabel  = AmazonAutoLinks_PluginUtility::getUnitTypeLabel( $_sThisUnitType );
        $_sThisTemplate       = $this->___getTemplateNameOfUnit( $iPostID );
        $_sThisLocale         = get_post_meta( $iPostID, 'country', true );
        return "<ul>"
                . "<li><span class='detail-title'>{$_sID}:</span><span class='detail-value'>{$iPostID}</span></li>"
                . "<li><span class='detail-title'>{$_sLocale}:</span><span class='detail-value'>{$_sThisLocale}</span></li>"
                . "<li><span class='detail-title'>{$_sUnitType}:</span><span class='detail-value'>{$_sThisUnitTypeLabel}</span></li>"
                . "<li><span class='detail-title'>{$_sTemplate}:</span><span class='detail-value'>{$_sThisTemplate}</span></li>"
            . "</ul>";
    }
        /**
         * @param  integer $iPostID
         * @return string
         * @since  4.4.1
         */
        private function ___getTemplateNameOfUnit( $iPostID ) {
            return AmazonAutoLinks_TemplateOption::getInstance()->getTemplateNameByID(
                untrailingslashit( get_post_meta( $iPostID, 'template_id', true ) ) // template id
            );
        }
    
    /**
     * @callback        filter      cell_{post type slug}_{column key}
     * @return          string
     * @deprecated      4.4.1       Now displayed in the Details column.
     */
    // public function cell_amazon_auto_links_unit_type( $sCell, $iPostID ) {
    //
    //     $_sUnitType       = get_post_meta( $iPostID, 'unit_type', true );
    //     $_aUnitTypeLabels = AmazonAutoLinks_PluginUtility::getUnitTypeLabels();
    //     return isset( $_aUnitTypeLabels[ $_sUnitType ] )
    //         ? $_aUnitTypeLabels[ $_sUnitType ]
    //         : __( 'Unknown', 'amazon-auto-links' );
    //
    // }
    /**
     * @callback        filter      cell_{post type slug}_{column key}
     * @return          string
     * @deprecated      4.4.1       Now displayed in the Details column.
     */
    // public function cell_amazon_auto_links_template( $sCell, $iPostID ) {
    //     return AmazonAutoLinks_TemplateOption::getInstance()->getTemplateNameByID(
    //         untrailingslashit( get_post_meta( $iPostID, 'template_id', true ) ) // template id
    //     );
    // }
    
    /**
     * @callback        filter      cell_{post type slug}_{column name}
     * @return          string
     */
    public function cell_amazon_auto_links_code( $sCell, $iPostID ) {
        return '<p>'
                . '<span>[amazon_auto_links id="' . $iPostID . '"]</span>' . '<br />'
                . "<span>&lt;?php AmazonAutoLinks( array( 'id' =&gt; " . $iPostID . " ) ); ?&gt;</span>"
            . '</p>';
    }

    /**
     * @param    string       $sCell
     * @param    integer      $iPostID
     * @callback add_filter() cell_{post type slug}_{column name}
     * @return   string
     */
    public function cell_amazon_auto_links_feed( $sCell, $iPostID ) {

        $_aOutput            = array();
        $_sThisUnitType      = get_post_meta( $iPostID, 'unit_type', true );
        if ( in_array( $_sThisUnitType, array( 'contextual', '', null, false, 'unknown' ), true ) ) {
            return $sCell;
        }
        $_aOutput[]          = "<p>"
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
        return implode( '', $_aOutput ) . $sCell;
        
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
                    ? AmazonAutoLinks_UnitLoader::$sDirPath . '/asset/image/rss16x16.gif'
                    : AmazonAutoLinks_UnitLoader::$sDirPath . '/asset/image/json16x16.gif',
                true
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

    /**
     * @callback        filter      cell_{post type slug}_{column key}
     * @return          string
     * @since       3.7.0
     */
    public function cell_amazon_auto_links_status( $sCell, $iPostID ) {

        $_sUnitType = get_post_meta( $iPostID, 'unit_type', true );
        if ( 'contextual' === $_sUnitType ) {
            return "<span class='unit-status circle unknown' title='" . __( 'Unknown', 'amazon-auto-links' ) . "'></span>";
        }

        $_snStatus = get_post_meta( $iPostID, '_error', true );
        $_sData    = "data-post-id='{$iPostID}'";

        if ( 'normal' === $_snStatus ) {
            return "<span class='unit-status circle green' title='" . __( 'Normal', 'amazon-auto-links' ) . "' {$_sData}></span>";
        }
        // '': the meta key does not exists, the very first status.
        // null: backward-compatibility for v3.7.6 or below for the loading status.
        if ( '' === $_snStatus || null === $_snStatus ) {
            return "<span class='unit-status circle gray' title='" . __( 'Ready', 'amazon-auto-links' ) . ' / ' . __( 'Loading', 'amazon-auto-links' ) . "' {$_sData}></span>";
        }

        $_iModalID = 'response-error-' . $iPostID;
        $_sTitle   = esc_attr( __( 'Unit Error', 'amazon-auto-links' ) );
        return "<span class='unit-status circle red' title='" . $_sTitle . "' {$_sData}></span>"
            . "<div id='{$_iModalID}' style='display:none;'>"
                . "<p>" . $_snStatus . "</p>"
            . "</div>"
            . "<div class='row-actions'>"
                . "<span class='show'>"
                    . "<a href='#TB_inline?width=600&height=72&inlineId={$_iModalID}' class='inline hide-if-no-js thickbox' title='" . $_sTitle . "'>"
                        . __( 'Show', 'amazon-auto-links' )
                    . "</a>"
                . "</span>"
                . " | "
                . "<span class='copy'>"
                    . "<a class='inline hide-if-no-js' data-target='{$_iModalID}'>"
                        . __( 'Copy', 'amazon-auto-links' )
                    . "</a>"
                . "</span>"
            . "</div>";

    }


}