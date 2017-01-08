<?php
/**
 * Provides the definitions of form fields for the 'tag' unit type.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_TagUnit_Main extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
                'type'          => 'text',
                'description'   => 'e.g. <code>My Tag Unit</code>',
                'value'         => '',    // a previous value should not appear
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'country',
                'type'          => 'select',
                'title'         => __( 'Country', 'amazon-auto-links' ),
                'label'         => array(                        
                    'US' => 'US - ' . __( 'United States', 'amazon-auto-links' ),
                ),
                'default'       => 'US',
                'description'   => __( 'Currently only the U.S. locale is supported for this unit type.', 'amazon-auto-links' ),
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'tags',
                'type'          => 'text',
                'title'         => __( 'Tags', 'amazon-auto-links' ),
                'attributes'    => array(
                    'size'  => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
                'description'   => __( 'Enter keywords that represent tags separated by commas.', 'amazon-auto-links' )
                    . ' ' . __( 'If the customer ID is provided, this option is optional and if it is left empty, all products tagged by the customer will be fetched.', 'amazon-auto-links' )
                    . ' ' . __( 'Any upper-case characters will be converted to lower-cases.', 'amazon-auto-links' )
                    . '<br />e.g. <code>wordpress, php</code>',
            ),    
            array(
                'field_id'      => $sFieldIDPrefix . 'customer_id',
                'type'          => 'text',
                'title'         => __( 'Customer ID', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'description'   => __( 'Enter a 13-character ID of the customer who tagged the products.', 'amazon-auto-links' ) 
                    . ' ' . sprintf( __( 'You can find it by looking at the url of the customer profile page. The format is <code>%1$s</code>.', 'amazon-auto-links' ), 'http://www.amazon.com/gp/pdp/profile/[customer_id]/' )
                    . ' ' . __( 'This is recommended to filter spam tagged products.', 'amazon-auto-links' ) 
                    . ' ' . __( 'If not specified, product links tagged by all customers will be fetched.', 'amazon-auto-links' )
                    . '<br />e.g. <code>AJM38DLD0P3H8</code>' . ' ' . sprintf( __( 'An example of the <a href="%1$s" target="_blank">customer profile page</a>.', 'amazon-auto-links' ), 'http://www.amazon.com/gp/pdp/profile/AJM38DLD0P3H8/' ),
            ),    
            array(
                'field_id'      => $sFieldIDPrefix . 'feed_type',
                'type'          => 'checkbox',
                'title'         => __( 'Types', 'amazon-auto-links' ),
                'label'         => array(    
                    // These keys will be used in a url when generating the feed url.
                    'new'           => __( 'New', 'amazon-auto-links' ),
                    'popular'       => __( 'Popular', 'amazon-auto-links' ),
                    'recent'        => __( 'Recent', 'amazon-auto-links' ),
                ),
                'description'   => __( 'It is recommended to check only a few for faster page loading.', 'amazon-auto-links' )
                    . '&nbsp;' . __( 'If the customer ID is provided, this option will not take effect.', 'amazon-auto-links' ),
                'default'       => array(
                    'new'       => true,
                    'popular'   => false,
                    'recent'    => false,
                ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'threshold',
                'type'              => 'number',
                'title'             => __( 'Threshhold', 'amazon-auto-links' ),
                'attributes'    => array(
                    'min' => 1,
                ),
                'delimiter'         => '',
                'description'       => __( 'This option indicates a threshold for how many times an item must have been tagged in order to appear in the "recent" feed. ', 'amazon-auto-links' )
                    . ' ' . __( 'If the threshold is set to 5, items will not appear in the recency feed until they have been tagged 5 times, and will be bumped up every time they are tagged after that.', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>2</code>',
                'default'           => 2,
            ),            
            array(
                'field_id'          => $sFieldIDPrefix . 'sort',
                'type'              => 'select',
                'title'             => __( 'Sort Order', 'amazon-auto-links' ),
                'label'             => array(                        
                    'date'              => __( 'Date', 'amazon-auto-links' ),
                    'title'             => __( 'Title', 'amazon-auto-links' ),
                    'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
                    'random'            => __( 'Random', 'amazon-auto-links' ),
                ),
                'default'           => 'random',
            )                         
       
        );
        return $_aFields;

    }
  
}