<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_SimilarityLookupUnit_Main extends AmazonAutoLinks_FormFields_SearchUnit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $aUnitOptions=array() ) {
            
        $_aFields      = array(
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'type'          => 'text',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
            ),         
             array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'similarity_lookup',
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'ItemId',
                'title'         => __( 'Item ASIN', 'amazon-auto-links' ),
                'type'          => 'textarea',
                'attributes'    => array(
                    'size' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 40 : 60,
                ),             
                'description'   => __( 'Enter the ASIN(s) of the product per line or simply paste text that includes ASINs.', 'amazon-auto-links' ) 
                    . ' e.g. <code>B009ZVO3H6</code>',
            ),    
            array(
                'field_id'      => $sFieldIDPrefix . 'search_per_keyword',
                'type'          => 'checkbox',
                'title'         => __( 'Query per Term', 'amazon-auto-links' ),
                'tip'           => __( 'Although Amazon API allows multiple search terms to be set per request, when one of them returns an error, the entire result becomes an error. To prevent it, check this option so that the rest will be returned.', 'amazon-auto-links' ),
                'label'         => __( 'Perform search per item.', 'amazon-auto-links' ),
                'default'       => false,
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'SimilarityType',
                'type'          => 'radio',
                'title'         => __( 'Similarity Type', 'amazon-auto-links' ),
                'label'         => array(                        
                    'Intersection'  => __( 'Intersection', 'amazon-auto-links' ) . ' - ' . __( 'returns the intersection of items that are similar to all of the ASINs specified', 'amazon-auto-links' ),
                    'Random'        => __( 'Random', 'amazon-auto-links' ) . ' - ' . __( 'returns the union of randomly picked items that are similar to all of the ASINs specified.', 'amazon-auto-links' ),
                ),
                'description'   => __( 'The maximum of only ten items can be retrieved.' , 'amazon-auto-links' ),
                'default'       => 'Intersection',
            ),             
            array(
                'field_id'      => $sFieldIDPrefix . 'Operation',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'SimilarityLookup',
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'country',
                'title'         => __( 'Locale', 'amazon-auto-links' ),
                'type'          => 'text',
                'attributes'    => array(
                    'readonly' => true,
                ),
            ),                                
        );
        return $_aFields;
        
    }
          
}