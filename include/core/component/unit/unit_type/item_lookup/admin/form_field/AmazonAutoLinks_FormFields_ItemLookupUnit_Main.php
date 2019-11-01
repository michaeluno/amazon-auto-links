<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_ItemLookupUnit_Main extends AmazonAutoLinks_FormFields_SearchUnit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $aUnitOptions=array() ) {
            
        $aUnitOptions  = $aUnitOptions + array( 'country' => null );
        $_bUPCAllowed  = 'CA' !== $aUnitOptions[ 'country' ];
        $_bISBNAllowed = 'US' === $aUnitOptions[ 'country' ];
         
        $_aFields       =  array(
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'item_lookup',
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'type'          => 'text',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'ItemId',
                'type'          => 'textarea',
                'title'         => __( 'ASINs', 'amazon-auto-links' ),
                'attributes'    => array(
                    'size' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
                'description'   => __( 'Paste text that includes ASINs.', 'amazon-auto-links' )
                    . ' e.g. <code>B009ZVO3H6, B0043D2DZA</code>',
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'search_per_keyword',
                'type'          => 'checkbox',
                'title'         => __( 'Query per Term', 'amazon-auto-links' ),
                'tip'           => __( 'Although Amazon API allows multiple search terms to be set per request, when one of them returns an error, the entire result becomes an error. To prevent it, check this option so that the rest will be returned.', 'amazon-auto-links' ),
                'label'         => __( 'Perform search per item.', 'amazon-auto-links' ),
                'default'       => false,
            ),
            // @deprecated 3.9.0
//            array(
//                'field_id'      => $sFieldIDPrefix . 'IdType',
//                'type'          => 'radio',
//                'title'         => __( 'ID Type', 'amazon-auto-links' ),
//                'label'         => array(
//                    'ASIN'  => 'ASIN',
//                    'SKU'   => 'SKU',
//                    'UPC'   => '<span class="' . ( $_bUPCAllowed ? "" : "disabled" ) . '">UPC <span class="description">(' . __( 'Not available in the CA locale.', 'amazon-auto-links' ) . ')</span></span>',
//                    'EAN'   => 'EAN',
//                    'ISBN'  => '<span class="' . ( $_bISBNAllowed ? "" : "disabled" ) . '">ISBN <span class="description">(' . __( 'The US locale only, when the search index is Books.', 'amaozn-auto-links' ) .')</span></span>',
//                ),
//                'attributes' => array(
//                    'UPC' => array(
//                        'disabled' => $_bUPCAllowed
//                            ? null
//                            : 'disabled',
//                    ),
//                    'ISBN' => array(
//                        'disabled' => $_bISBNAllowed
//                            ? null
//                            : 'disabled',
//                    ),
//                ),
//                'default'       => 'ASIN',
//            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'Operation',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'GetItems',
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'country',
                'type'          => 'text',
                'title'         => __( 'Locale', 'amazon-auto-links' ),
                'attributes'    => array(
                    'readonly' => true,
                ),
            ),
// @deprecated 3.9.0
//            array(
//                'field_id'      => $sFieldIDPrefix . 'SearchIndex',
//                'type'          => 'select',
//                'title'         => __( 'Categories', 'amazon-auto-links' ),
//                'label'         => $this->_getSearchIndex( $aUnitOptions ),
//                'default'       => 'All',
//                'tip'           => __( 'Select the category to limit the searching area.', 'amazon-auto-links' ),
//                'description'   => __( 'If the above ID Type is ISBN, this will be automatically set to Books.', 'amazon-auto-links' )
//                    . ' ' . __( 'If the ID Type is ASIN this option will not take effect.', 'amazon-auto-links' ),
//            ),
            // 3.5.0+
            array(
                'field_id'          => $sFieldIDPrefix . '_sort',
                'type'              => 'select',
                'title'             => __( 'Sort Order', 'amazon-auto-links' ),
                'label'             => array(
                    'raw'               => __( 'Raw', 'amazon-auto-links' ),
                    'title'             => __( 'Title', 'amazon-auto-links' ),
                    'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
                    'random'            => __( 'Random', 'amazon-auto-links' ),
                ),
                'tip'               => __( 'In order not to sort and leave it as the found order, choose <code>Raw</code>.', 'amazon-auto-links' ),
                'default'           => 'raw',
            ),
        );
        return $_aFields; 
        
    }
          
}