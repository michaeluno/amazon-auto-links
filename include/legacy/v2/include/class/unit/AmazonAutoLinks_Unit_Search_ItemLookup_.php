<?php
/**
 * Creates Amazon product links by ItemLookup. 
 * 
 * @package         Amazon Auto Links
 * @copyright       Copyright (c) 2013, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since            2.0.2
 */

abstract class AmazonAutoLinks_Unit_Search_ItemLookup_ extends AmazonAutoLinks_Unit_Search_ {


    /**
     * Represents the array structure of the API request arguments.
     * @since            2.0.2
     */
    public static $aStructure_ItemLookup = array(    
        'Operation'             => 'ItemLookup',
        'Condition'             => 'New',
        'IdType'                => null,
        'IncludeReviewsSummary' => null,
        'ItemId'                => null,
        'MerchantId'            => null,
        'RelatedItemPage'       => null,
        'RelationshipType'      => null,
        'SearchIndex'           => null,
        'TruncateReviewsAt'     => null,
        'VariationPage'         => null,
        'ResponseGroup'         => null,
    );
    
    /**
     * Performs an Amazon Product API request.
     * 
     * @since            2.0.2
     */
    protected function getRequest( $iCount ) {
        
        $_oAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 
            $this->arrArgs['country'], 
            $this->oOption->getAccessPublicKey(),
            $this->oOption->getAccessPrivateKey(),
            $this->arrArgs['associate_id']
        );
            
        // Perform the search for the first page regardless the specified count (number of items).
        // Keys with an empty value will be filtered out when performing the request.
        return $_oAPI->request( $this->getAPIParameterArray( $this->arrArgs['Operation'] ), '', $this->arrArgs['cache_duration'] );
                 
    }
    
    
    /**
     * 
     * 'Operation' => 'ItemSearch',    // ItemSearch, ItemLookup, SimilarityLookup
     * @see                http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html
     * @since            2.0.2
     */
    protected function getAPIParameterArray( $sOperation='ItemLookup', $iItemPage=null ) {

        $this->arrArgs = $this->arrArgs + self::$aStructure_ItemLookup;
        $aParams = array(
            'Operation'             => $sOperation,
            'Condition'             => $this->arrArgs['Condition'],    // (optional) Used | Collectible | Refurbished, All
            'IdType'                => $this->arrArgs['IdType'],    // (optional) All IdTypes except ASINx require a SearchIndex to be specified.  SKU | UPC | EAN | ISBN (US only, when search index is Books). UPC is not valid in the CA locale.
            'IncludeReviewsSummary' => "True",        // (optional)
            'ItemId'                => $this->arrArgs['ItemId'],    // (required)  If ItemIdis an ASIN, a SearchIndex cannot be specified in the request.
            // 'RelatedItemPage' => null,    // (optional) This optional parameter is only valid when the RelatedItems response group is used.
            // 'RelationshipType' => null,    // (conditional)    This parameter is required when the RelatedItems response group is used. 
            'SearchIndex'           => $this->arrArgs['SearchIndex'],    // (conditional) see: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/APPNDX_SearchIndexValues.html
            // 'TruncateReviewsAt' => 1000, // (optional)
            // 'VariationPage' => null, // (optional)
            'ResponseGroup'         => 'Large', // (optional)
        );
        
        if ( 'ASIN' === $this->arrArgs['IdType'] ) {
            unset( $aParams['SearchIndex'] );
        }

        $_aArgs = 'Amazon' === $this->arrArgs['MerchantId']
            ? $aParams + array( 'MerchantId' => $this->arrArgs['MerchantId'] )    // (optional) 'Amazon' restrict the returned items only to be soled by Amazon.
            : $aParams;
        return $_aArgs;
    }

}