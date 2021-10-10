<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_ItemLookupUnit_Advanced extends AmazonAutoLinks_FormFields_SearchUnit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $aUnitOptions=array() ) {
            
        $_oOption      = $this->oOption;
 

        $_aFields = array(
            array(
                'field_id'      => $sFieldIDPrefix . 'MerchantId',
                'type'          => 'radio',
                'title'         => __( 'Merchant ID', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'label'         => array(
                    'All'    => 'All',    // not that the API will not accept the value All so do appropriate sanitization when performing the API request.
                    'Amazon' => 'Amazon',
                ),
                'description'   => __( 'Select <code>Amazon</code> if you only want to see items sold by Amazon; otherwise, <code>All</code>.', 'amazon-auto-links' ),
                'default'       => 'All',
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'Condition',
                'type'          => 'radio',
                'title'         => __( 'Condition', 'amazon-auto-links' ),              
                'label'         => array(
                    'New'           => __( 'New', 'amazon-auto-links' ),
                    'Used'          => __( 'Used', 'amazon-auto-links' ),
                    'Collectible'   => __( 'Collectible', 'amazon-auto-links' ),
                    'Refurbished'   => __( 'Refurbished', 'amazon-auto-links' ),
                    'Any'           => __( 'Any', 'amazon-auto-links' ),
                ),
                'default'       => 'Any',
                'description'   => __( 'If the search index is All, this option does not take effect.', 'amazon-auto-links' ),        
            ),
        );     
     
        // Insert common field arguments.
        $_bIsDisabled  = ! $_oOption->isAdvancedAllowed();
        $_sOpeningTag  = $_bIsDisabled 
            ? "<div class='upgrade-to-pro' style='margin:0; padding:0; display: inline-block;' title='" . __( 'Please consider upgrading to Pro to use this feature!', 'amazon-auto-links' ) . "'>" 
            : "";
        $_sClosingTag  = $_bIsDisabled 
            ? "</div>" 
            : "";        
        foreach( $_aFields as &$_aField ) {
            $_aField = array(
                    'before_field' => $_sOpeningTag,
                    'after_field'  => $_sClosingTag,
                )
                + $_aField
                + array( 'attributes' => array() )
            ;
            $_aField[ 'attributes' ] = array(
                'disabled' => $_bIsDisabled
                    ? 'disabled'
                    : null,
                'class' => $_bIsDisabled 
                    ? 'disabled read-only' 
                    : '',
            ) + $_aField[ 'attributes' ];
            
        }             
        return $_aFields;
    }
          
}