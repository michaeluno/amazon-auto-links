<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 * 
 * @copyright   Copyright (c) 2013-2022, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Provides methods for defining columns of listing table of the auto-insert post type.
 * @since       3
 */
class AmazonAutoLinks_PostType_AutoInsert_Column extends AmazonAutoLinks_AdminPageFramework_PostType {

    /**
     * Stores a custom nonce.
     * @deprecated 4.3.0
     */
//    protected $_sCustomNonce;
//    protected $_sNonceKey = 'aal_nonce_auto_insert';

    /**
     * Sets up hooks.
     */
    public function setUp() {
            
        if ( $this->_isInThePage() ) {

            // @deprecated 4.3.0
//            $this->_sCustomNonce = wp_create_nonce( 'aal_nonce_auto_insert' );

            // unit listing table columns
            add_filter(    
                'columns_' . AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ],
                array( $this, 'replyToModifyColumnHeader' )
            );                 
               
        }
        
        parent::setUp();
        
    }

    /**
     * Defines the column header of the unit listing table.
     * 
     * Extensible methods
     * @callback     filter      columns_{post type slug}
     */
    public function replyToModifyColumnHeader( $aHeaderColumns ) {    
        // Set the table header.
        return array(
            'cb'                => '<input type="checkbox" />',    // Checkbox for bulk actions. 
            'auto_inserts'      => __( 'Auto-insert Definitions', 'amazon-auto-links' ),
            'status'            => __( 'Status', 'amazon-auto-links' ),
            'area'              => __( 'Areas', 'amazon-auto-links' ),  // Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
        );        
        
    }
    // public function getSortableColumns( $aColumns ) {
        // return array_merge( $aColumns, $this->oProp->aColumnSortable );        
    // }    
        
    /*
     *     Modify cells
     */
    public function cell_aal_auto_insert_auto_inserts( $sCell, $iPostID ) {
                
        $_aUnitIDs = array_filter(
            $this->oUtil->getAsArray(
                get_post_meta( $iPostID, 'unit_ids', true ) 
            )
        );
        return $this->___getUnitTitles( $_aUnitIDs ) ;

    }
        /**
         * 
         * @return      string
         */
        private function ___getUnitTitles( array $aUnitIDs ) {
            
            $_aTitles = array();
            foreach( $aUnitIDs as $_iUnitID ) {
                
                $_sTitle = get_the_title( $_iUnitID );
                if ( empty( $_sTitle ) ) { 
                    continue; 
                }
                if ( 'publish' === get_post_status( $_iUnitID ) ) {
                    $_sUnitPreviewURL = esc_url( get_permalink( $_iUnitID ) );
                    $_sTitle          = "<a href='{$_sUnitPreviewURL}'>" 
                            . $_sTitle 
                        . "</a>";                    
                }
                
                $_aTitles[] = "<strong>" . $_sTitle . "</strong>";
                if ( count( $_aTitles ) >= 3 ) {
                    $_aTitles[] = __( 'etc.', 'amazon-auto-links' );
                    break;
                }
                
            }
            $_aTitles = array_filter( $_aTitles );    // drop empty values.
            if ( empty( $_aTitles ) ) {
                $_aTitles[] = __( '(No unit is selected)', 'amazon-auto-links' );    // this happens if an associated unit is deleted.        
            }            
            return implode( ', ', $_aTitles );
            
        }


    public function cell_aal_auto_insert_area( $sCell, $iPostID ) {
        $_oUtil = new AmazonAutoLinks_WPUtility;
        $_aList = array();
        $aSelectedAreas = ( ( array ) get_post_meta( $iPostID, 'built_in_areas', true ) )
            + ( ( array ) get_post_meta( $iPostID, 'static_areas', true ) );
        $aSelectedAreas = array_filter( $aSelectedAreas );

        $aAreasLabel = $_oUtil->getPredefinedFilters() 
            + $_oUtil->getPredefinedFiltersForStatic( false );
        foreach( $aSelectedAreas as $sArea => $fEnable ) {            
            if ( isset( $aAreasLabel[ $sArea ] ) ) {
                $_aList[] = $aAreasLabel[ $sArea ];
            }
        }
        $aFilters = $_oUtil->getStringIntoArray( get_post_meta( $iPostID, 'filter_hooks', true ), ',' );
        $aActions = $_oUtil->getStringIntoArray( get_post_meta( $iPostID, 'action_hooks', true ), ',' );
        $_aList   = array_merge( $aFilters, $aActions, $_aList );
        return '<p>' 
                . implode( ', ', $_aList ) 
            . '</p>';
        
    }

}