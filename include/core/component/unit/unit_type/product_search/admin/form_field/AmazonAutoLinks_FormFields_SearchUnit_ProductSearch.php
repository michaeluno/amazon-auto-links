<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_SearchUnit_ProductSearch extends AmazonAutoLinks_FormFields_SearchUnit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $aUnitOptions=array() ) {

        $_oOption   = $this->oOption;
        $_aFields   = array(
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'search',
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'type'          => 'text',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'Keywords',
                'type'          => 'text',
                'title'         => __( 'Search Keyword', 'amazon-auto-links' ),
                'attributes'    => array(
                    'size'          => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
                'tip'           => __( 'Enter the keyword to search.', 'amazon-auto-links' ),
                'description'   => __( 'For multiple items, separate them by commas.', 'amazon-auto-links' )
                    . ' e.g. <code>WordPress, PHP</code>',
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
                'field_id'      => $sFieldIDPrefix . 'Operation',
                'type'          => 'hidden',
                'title'         => __( 'Operation', 'amazon-auto-links' ),
                'hidden'        => true,
            ),                
            array(
                'field_id'      => $sFieldIDPrefix . 'country',
                'type'          => 'text',
                'title'         => __( 'Locale', 'amazon-auto-links' ),
                'attributes'    => array(
                    'readonly'=> 'readonly',
                ),
            ),                         
            array(
                'field_id'      => $sFieldIDPrefix . 'SearchIndex',
                'type'          => 'select',
                'title'         => __( 'Category', 'amazon-auto-links' ),            
                'label'         => $this->_getSearchIndex( $aUnitOptions ),
                'default'       => 'All',
                'tip'           => __( 'Select the category to limit the searching area.', 'amazon-auto-links' ),
                'description'   => __( 'If the above ID Type is ISBN, this will be automatically set to Books.', 'amazon-auto-links' )
                    . ' ' . __( 'If the ID Type is ASIN this option will not take effect.', 'amazon-auto-links' ),
            ),           
            array(
                // see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/SortingbyPopularityPriceorCondition.html
                'field_id'      => $sFieldIDPrefix . 'Sort',
                'title'         => __( 'Sort Order', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(                        
                    'price'           => "<strong>" . __( 'Price Ascending', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items from the cheapest to the most expensive.', 'amazon-auto-links' ) . '<br />',     // 3.5.5+ Changed from `pricerank` for consistency with `-price`.
                    '-price'          => "<strong>" . __( 'Price Descending', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items from the most expensive to the cheapest.', 'amazon-auto-links' ) . '<br />',    // 3.5.5+ Changed from `inversepricerank` as `-price` is supported by other locales.
                    'salesrank'       => "<strong>" . __( 'Sales Rank', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items based on how well they have been sold, from best to worst sellers.', 'amazon-auto-links' ) . '<br />',
                    'relevancerank'   => "<strong>" . __( 'Relevance Rank', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items based on how often the keyword appear in the product description.', 'amazon-auto-links' ) . '<br />',
                    'reviewrank'      => "<strong>" . __( 'Review Rank', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items based on how highly rated the item was reviewed by customers where the highest ranked items are listed first and the lowest ranked items are listed last.', 'amazon-auto-links' ) . '<br />',
                ),
                'default'       => 'salesrank',
                'description'   => __( 'When the search index is selected to <code>All</code>, this option does not take effect.', 'amazon-auto-links' ),
            ),                              
        );
        return $_aFields;
        
    }

}