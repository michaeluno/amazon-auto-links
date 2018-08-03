<?php
/**
 * Provides the definitions of form fields for the 'url' unit type.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_URLUnit_Main extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
            
        $_oOption = $this->oOption;
        $_aFields = array(  
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'url',
            ),          
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
                'type'          => 'text',
                'description'   => 'e.g. <code>My URL Unit</code>',
                'value'         => '',    // a previous value should not appear
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'country',
                'type'          => 'select',
                'title'         => __( 'Country', 'amazon-auto-links' ),
                'label'         => array(                        
                    'CA' => 'CA - ' . __( 'Canada', 'amazon-auto-links' ),
                    'CN' => 'CN - ' . __( 'China', 'amazon-auto-links' ),
                    'FR' => 'FR - ' . __( 'France', 'amazon-auto-links' ),
                    'DE' => 'DE - ' . __( 'Germany', 'amazon-auto-links' ),
                    'IT' => 'IT - ' . __( 'Italy', 'amazon-auto-links' ),
                    'JP' => 'JP - ' . __( 'Japan', 'amazon-auto-links' ),
                    'UK' => 'UK - ' . __( 'United Kingdom', 'amazon-auto-links' ),
                    'ES' => 'ES - ' . __( 'Spain', 'amazon-auto-links' ),
                    'US' => 'US - ' . __( 'United States', 'amazon-auto-links' ),
                    'IN' => 'IN - ' . __( 'India', 'amazon-auto-links' ),
                    'BR' => 'BR - ' . __( 'Brazil', 'amazon-auto-links' ),
                    'MX' => 'MX - ' . __( 'Mexico', 'amazon-auto-links' ),
                    'AU' => 'AU - ' . __( 'Australia', 'amazon-auto-links' ),
                ),
                'default' => 'US',
            ),                     
            array(
                'field_id'      => $sFieldIDPrefix . 'urls',
                'type'          => 'text',
                'title'         => __( 'URLs', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style' => 'min-width: 80%; max-width: 100%; ',
                    'size'  => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
                'repeatable'    => array(
                    'max'   => $_oOption->isAdvancedAllowed()
                        ? 0
                        : 1,
                ),
                'description'   => __( 'Enter URLs of the page to be parsed.', 'amazon-auto-links' )
                    . ' e.g. <code>http://www.amazon.com/gp/feature.html?docId=1000677541</code>',
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
            array(
                'field_id'      => $sFieldIDPrefix . 'Operation',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'ItemLookup',
            ),            
        );
        return $_aFields;
        
    }
  
}