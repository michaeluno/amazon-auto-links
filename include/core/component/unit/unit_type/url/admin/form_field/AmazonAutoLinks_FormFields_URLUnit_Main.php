<?php
/**
 * Provides the definitions of form fields for the 'url' unit type.
 * 
 * @since  3
 * @since  4.5.0    Changed the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_URLUnit_Main extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     *
     * @param  string $sFieldIDPrefix
     * @return array
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
                'class'         => array(
                    'input' => 'width-full',
                    'field' => 'width-half',
                ),
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'country',
                'type'          => 'select',
                'title'         => __( 'Country', 'amazon-auto-links' ),
                'label'         => $this->getPAAPILocaleFieldLabels(),
                'description'   => sprintf(
                    __( 'If the country is not listed, set PA-API keys in the <a href="%1$s">Associates</a> section.', 'amazon-auto-links' ),
                    $this->getAPIAuthenticationPageURL()
                ),
                'default'       => $_oOption->getMainLocale(),
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
                'class'         => array(
                    'input' => 'width-full',
                    'field' => 'two-third',
                ),
            ),
            // @deprecated 4.6.22
            // array(
            //     'field_id'      => $sFieldIDPrefix . 'search_per_keyword',
            //     'type'          => 'checkbox',
            //     'title'         => __( 'Query per Term', 'amazon-auto-links' ),
            //     'tip'           => __( 'Although Amazon API allows multiple search terms to be set per request, when one of them returns an error, the entire result becomes an error. To prevent it, check this option so that the rest will be returned.', 'amazon-auto-links' ),
            //     'label'         => __( 'Perform search per item.', 'amazon-auto-links' ),
            //     'default'       => false,
            // ),
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