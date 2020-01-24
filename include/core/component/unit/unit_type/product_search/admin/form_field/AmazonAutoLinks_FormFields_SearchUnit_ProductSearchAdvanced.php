<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_SearchUnit_ProductSearchAdvanced extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
            
        $_oOption      = $this->oOption;

        $_aFields = array(
            array(
                'field_id'      => $sFieldIDPrefix . 'Title',
                'type'          => 'text',
                'title'         => __( 'Title', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'description'   => __( 'Enter keywords which should be matched in the product title. For multiple keywords, separate them by commas.', 'amazon-auto-links' )
                    . ' ' . __( 'If this is set, the Search Keyword option can be empty.', 'amazon-auto-links' ), 
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'additional_attribute',
                'type'          => 'text',
                'title'         => __( 'Additional Attribute', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
            ),    
            array(
                'field_id'      => $sFieldIDPrefix . 'search_by',
                'type'          => 'radio',
                'title'         => '', 
                'label'         => array(
                    'Actor'        => __( 'Actor', 'amazon-auto-links' ),
                    'Artist'       => __( 'Artist', 'amazon-auto-links' ),
                    'Author'       => __( 'Author', 'amazon-auto-links' ),
                    'Brand'        => __( 'Brand', 'amazon-auto-links' ),

// @deprecated 3.9.0    PA-API 5.0 Do not support these
//                    'Manufacturer' => __( 'Manufacturer', 'amazon-auto-links' ),
//                    'Composer'     => __( 'Composer', 'amazon-auto-links' ),
//                    'Conductor'    => __( 'Conductor', 'amazon-auto-links' ),
//                    'Director'     => __( 'Director', 'amazon-auto-links' ),
                ),
                'default'       => 'Author',
                'description'   => __( 'Enter a keyword to narrow down the results with one of the above attributes.', 'amazon-auto-links' )
                    . ' ' . __( 'If this is set, the Search Keyword option can be empty.', 'amazon-auto-links' ), 
            ),             
            array(
                'field_id'      => $sFieldIDPrefix . 'Availability',
                'type'          => 'checkbox',
                'title'         => __( 'Availability', 'amazon-auto-links' ),
                'label'         => __( 'Filter out most of the items that are unavailable as may products can become unavailable quickly.', 'amazon-auto-links' ),
                'default'       => 1,
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
            array(
                'field_id'      => $sFieldIDPrefix . 'MaximumPrice',
                'type'          => 'number',
                'title'         => __( 'Maximum Price', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'description'   => __( 'Specifies the maximum price of the items in the response. Prices are in terms of the lowest currency denomination, for example, pennies. For example, 3241 represents $32.41.', 'amazon-auto-links' ),
//                    . ' ' . __( 'This option will not take effect if the Category option is set to <code>All</code> or <code>Blended</code>', 'amazon-auto-links' ),
            ),                        
            array(
                'field_id'      => $sFieldIDPrefix . 'MinimumPrice',
                'type'          => 'number',
                'title'         => __( 'Minimum Price', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'description'   => __( 'Specifies the minimum price of the items to return. Prices are in terms of the lowest currency denomination, for example, pennies, for example, 3241 represents $32.41.', 'amazon-auto-links' ),
//                     . ' ' . __( 'This option will not take effect if the Category option is set to <code>All</code> or <code>Blended</code>', 'amazon-auto-links' ),
            ),                    
            array(
                'field_id'      => $sFieldIDPrefix . 'MinPercentageOff',
                'type'          => 'number',
                'title'         => __( 'Minimum Percentage Off', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'attributes'    => array(
                    'max'  => 100,
                    'min'  => 0,
                    'step' => 1,
                ),
                'after_input'   => ' %',
                'description'   => __( 'Specifies the minimum percentage off for the items to return.', 'amazon-auto-links' )
                    . ' e.g. ' . __( '<code>10</code> for 10%.', 'amazon-auto-links' ),
            ),    
            array(
                'field_id'      => $sFieldIDPrefix . 'BrowseNode',
                'type'          => 'number',
                'title'         => __( 'Browse Node ID', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'description'   => array(
                    __( 'If you know the browse node (the category ID) that you are searching, specify it here. It is a positive integer.', 'amazon-auto-links' )
                    . ' ' . sprintf( 
                        __( 'Browse nodes can be found <a href="%1$s" target="_blank">here</a>.', 'amazon-auto-links' ),
                        'http://www.findbrowsenodes.com/'
                    )
                    . ' e.g. <code>158597011</code>',
                ),
            ),    
            array(
                'field_id'      => $sFieldIDPrefix . 'MerchantId',
                'type'          => 'text',
                'title'         => __( 'Merchant ID', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'description'   => __( 'Filter search results and offer listings to only include items sold by Amazon. By default, Product Advertising API returns items sold by various merchants including Amazon. Use the Amazon to limit the response to only items sold by Amazon. Case sensitive. e.g.<code>Amazon</code>', 'amazon-auto-links' ),
            ),
            // 3.9.1
            array(
                'field_id'      => $sFieldIDPrefix . 'MinReviewsRating',
                'type'          => 'select',
                'title'         => __( 'Minimum Review Rating', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'label'         => array(
                    0 => __( 'None', 'amazon-auto-links' ),
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                ),
                'default'       => 0,
                'tip'   => __( 'Filters search results to items with customer review ratings above selected value.', 'amazon-auto-links' ),
            ),
            // 3.10.0
            array(
                'field_id'      => $sFieldIDPrefix . 'DeliveryFlags',
                'type'          => 'checkbox',
                'title'         => __( 'Delivery Flags', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'label'         => array(
                    'AmazonGlobal'      => __( 'Amazon Global', 'amazon-auto-links' ) . ' - ' . __( 'A delivery program featuring international shipping to certain Exportable Countries.', 'amazon-auto-links' ),
                    'FreeShipping'      => __( 'Free Shipping', 'amazon-auto-links' ) . ' - ' . __( 'A delivery program featuring free shipping of an item.', 'amazon-auto-links' ),
                    'FulfilledByAmazon' => __( 'Fulfilled by Amazon', 'amazon-auto-links' ) . ' - ' . __( 'Fulfilled by Amazon indicates that products are stored, packed and dispatched by Amazon.', 'amazon-auto-links' ),
                    'Prime'             => __( 'Prime', 'amazon-auto-links' ) . ' - ' . __( 'An offer for an item which is eligible for Prime Program.', 'amazon-auto-links' ),
                ),
//                'default'       => 0,
                'description'   => __( 'This option filters items which satisfy a certain delivery program promoted by the specific Amazon Marketplace. For example, Prime will return items having at least one offer which is Prime Eligible.', 'amazon-auto-links' ),
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