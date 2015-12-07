<?php
/**
 * Provides the field definitions of Cache unit options.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_Unit_Cache extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        return array(
            array(
                'field_id'        => $sFieldIDPrefix . 'cache_duration',
                'title'           => __( 'Cache Duration', 'amazon-auto-links' ),
                'tip'             => __( 'The cache lifespan in seconds.', 'amazon-auto-links' ) 
                    . ' ' . __( 'Default:', 'amazon-auto-links' ) 
                        . ': <code>' .  ( 60 * 60 * 24 ) . '</code>'
                        . " (" . __( 'One day', 'amazon-auto-links' ) . ")",                
                'type'            => 'number',
                'after_input'     => __( 'seconds', 'amazon-auto-links' ),
                'attributes'      => array(
                    'style' => 'width: 120px; margin-right: 0.5em;',
                    'min'   => 60 * 10, // 10 minutes at minimum as the user can get banned from Amazon for too many accesses.
                ),
                'description'     => array(
                    sprintf(
                        __( 'Minumum: <code>%1$s</code>', 'amazon-auto-links' ),
                        600
                    )
                    . ' ' . sprintf(
                        __( 'Default: <code>%1$s</code>', 'amazon-auto-links' ),
                        60 * 60 * 24
                    )                    
                ),
                'default'         => 60 * 60 * 24, // 60 * 60 * 24 // one day
            )        
        );
    }

}