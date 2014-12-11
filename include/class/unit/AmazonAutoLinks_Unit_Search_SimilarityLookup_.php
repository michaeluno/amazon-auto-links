<?php
/**
 * Creates Amazon product links by SimilarityLookup. 
 * 
 * @package         Amazon Auto Links
 * @copyright       Copyright (c) 2013, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since            2.0.2
 */

abstract class AmazonAutoLinks_Unit_Search_SimilarityLookup_ extends AmazonAutoLinks_Unit_Search_ {

    /**
     * Represents the array structure of the API request arguments.
     * 
     * @since            2.0.2
     */
    public static $aStructure_SimilarityLookup = array(    
        'Operation'         => 'SimilarityLookup',        
        'Condition'         => 'New',
        'ItemId'            => null,
        'MerchantId'        => null,
        'SimilarityType'    => 'Intersection',
        'ResponseGroup'     => 'Large',
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
     * 'Operation' => 'SimilarityLookup''
     * @see              http://docs.aws.amazon.com/AWSECommerceService/latest/DG/SimilarityLookup.html
     * @since            2.0.3
     */
    protected function getAPIParameterArray( $sOperation='SimilarityLookup', $iItemPage=null ) {

        $this->arrArgs = $this->arrArgs + self::$aStructure_SimilarityLookup;
        return array(
            'Operation'         => $sOperation,
            'MerchantId'        => 'Amazon' === $this->arrArgs['MerchantId'] ? $this->arrArgs['MerchantId'] : null,
            'SimilarityType'    => $this->arrArgs['SimilarityType'],        
            'Condition'         => $this->arrArgs['Condition'],    // (optional) Used | Collectible | Refurbished, All
            'ItemId'            => $this->arrArgs['ItemId'],    // (required)  If ItemIdis an ASIN, a SearchIndex cannot be specified in the request.
            'ResponseGroup'     => 'Large', // (optional)
        );
    }

}