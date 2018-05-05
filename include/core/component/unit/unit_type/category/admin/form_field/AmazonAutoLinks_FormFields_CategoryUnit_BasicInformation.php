<?php
/**
 * Provides the definitions of form fields for the category type unit.
 * 
 * @since           3  
 * @remark          The admin page and meta box access it.
 */
class AmazonAutoLinks_FormFields_CategoryUnit_BasicInformation extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => 'unit_type',
                'type'          => 'hidden',
                'value'         => 'category',
                'hidden'        => true,            // hides the field row as well.
            ),                    
            array(
                'field_id'      => 'country',
                'title'         => __( 'Country', 'amazon-auto-links' ),
                'type'          => 'select',
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
                    'AU' => 'AU - ' . __( 'Australia', 'amazon-auto-links' ), // 3.5.5+
                ),
                'default'       => 'US',
            ),              
            array(
                'field_id'      => 'sort',
                'title'         => __( 'Sort Order', 'amazon-auto-links' ),
                'type'          => 'select',
                'label'         => array(                        
                    'date'              => __( 'Date', 'amazon-auto-links' ),
                    'title'             => __( 'Title', 'amazon-auto-links' ),
                    'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
                    'random'            => __( 'Random', 'amazon-auto-links' ),
                ),
                'default'       => 'random',
            ),        
            array(
                'field_id'      => 'keep_raw_title',
                'title'         => __( 'Sort Option', 'amazon-auto-links' ),
                'type'          => 'checkbox',
                'label'         => __( 'Keep raw titles.', 'amazon-auto-links' ),
                'description'   => __( 'If checked, unsanitized titles will be used. This is useful to sort by rank.', 'amazon-auto-links' ),
                'default'       => false,
            ),                
            array(
                'field_id'      => 'feed_type',
                'title'         => __( 'Types', 'amazon-auto-links' ),
                'type'          => 'checkbox',
                'label'         => array(    
                    // These keys will be used in a url when generating the feed url.
                    'bestsellers'           => __( 'Best Sellers', 'amazon-auto-links' ),
                    'new-releases'          => __( 'Hot New Releases', 'amazon-auto-links' ),
                    'movers-and-shakers'    => __( 'Mover and Shakers', 'amazon-auto-links' ),
                    'top-rated'             => __( 'Top Rated', 'amazon-auto-links' ),
                    'most-wished-for'       => __( 'Most Wished For', 'amazon-auto-links' ),
                    'most-gifted'           => __( 'Gift Ideas', 'amazon-auto-links' ),
                ),
                'description'   => __( 'It is recommended to check only a few for faster page loading.', 'amazon-auto-links' )
                    . '&nbsp;' . __( 'Some of the types other than Best Sellers are not supported in some locales.', 'amazon-auto-links' ),
                'default'       => array( 'bestsellers' => true ),
            )       
    
        );
        return $_aFields;
        
    }
  
}