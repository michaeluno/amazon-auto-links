<?php
/**
 * Provides the common methods for creating form fields definitions.
 * 
 * @since           3  
 */
abstract class AmazonAutoLinks_FormFields_SearchUnit_Base extends AmazonAutoLinks_FormFields_Base {

    /**
     * 
     * @access      protected       This class will be extended and this method will be accessed from an extended class.
     */
    protected function _getSearchTypeLabel( $sSearchTypeKey ) {
        switch ( $sSearchTypeKey ) {
            default:
            case 'ItemSearch':
                return __( 'Products', 'amazon-auto-links' );
            case 'ItemLookup':
                return __( 'Item Lookup', 'amazon-auto-links' );
            case 'SimilarityLookup':
                return __( 'Similar Products', 'amazon-auto-links' );
        }
    }  
  
  
}