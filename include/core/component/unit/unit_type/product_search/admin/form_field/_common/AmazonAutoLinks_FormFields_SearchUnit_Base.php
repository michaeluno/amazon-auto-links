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

    /**
     * @param       array   $aUnitOptions
     *
     * @return      array   A list of labels for HTML `<option>` form tags.
     * @remark      Called by the Product Search and Item Look-up unit types.
     * @since       3.5.5
     */
    protected function _getSearchIndex( $aUnitOptions ) {

        // If the unit options is directly given, use it.
        $_sLocale = $this->getElement( $aUnitOptions, 'country' );
        if ( $_sLocale ) {
            return AmazonAutoLinks_Property::getSearchIndexByLocale( $_sLocale );
        }

        // If the current page is the unit creation admin page, retrieve the value from the transient
        $_sTransientID = $this->getElement( $_GET, 'transient_id' );
        if ( $_sTransientID ) {
            $_aInputs = get_transient( $_sTransientID );
            $_sLocale = $this->getElement( $_aInputs, 'country' );
            if ( $_sLocale ) {
                return AmazonAutoLinks_Property::getSearchIndexByLocale( $_sLocale );
            }
        }

        // Check if it is a meta box. If so. use the stored locale.
        $_iPostID = AmazonAutoLinks_WPUtility::getCurrentPostID();
        if ( ! $_iPostID ) {
            return AmazonAutoLinks_Property::getSearchIndexByLocale( $_sLocale );
        }
        $_sLocale = get_post_meta(
            $_iPostID,
            'country', // meta key
            true
        );
        return AmazonAutoLinks_Property::getSearchIndexByLocale( $_sLocale );
    }
  
}