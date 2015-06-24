<?php
/**
 * Provides the definitions of form fields for the category type unit.
 * 
 * @since            2.0.0
 * @remark            The admin page and meta box access it.
 */
abstract class AmazonAutoLinks_Form_Search_ extends AmazonAutoLinks_Form {
    
    protected $strPageSlug = 'aal_add_search_unit';
    
    public function getSections( $strPageSlug='' ) {
        
        $strPageSlug = $strPageSlug ? $strPageSlug : $this->strPageSlug;
        return array(
            array(
                'strSectionID'        => 'search',
                'strTabSlug'        => 'initial_search_settings',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Add New Unit by Search', 'amazon-auto-links' ),
            ),        
            // for the product search type
            array(
                'strSectionID'        => 'search_second',
                'strTabSlug'        => 'search_products',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Add New Unit by Search', 'amazon-auto-links' ),
            ),        
            array(
                'strSectionID'        => 'search_advanced',
                'strTabSlug'        => 'search_products',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Advanced Search Criteria', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'search_auto_insert',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'        => 'search_products',
                'strTitle'            => __( 'Auto Insert', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'search_template',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'        => 'search_products',
                'strTitle'            => __( 'Template', 'amazon-auto-links' ),
            ),
            // for the item lookup search type
            array(
                'strSectionID'        => 'search_item_lookup',
                'strTabSlug'        => 'item_lookup',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Item Look-up', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'search_item_lookup_advanced',
                'strTabSlug'        => 'item_lookup',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Advanced Item Look-up Options', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'search_auto_insert2',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'        => 'item_lookup',
                'strTitle'            => __( 'Auto Insert', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'search_template2',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'        => 'item_lookup',
                'strTitle'            => __( 'Template', 'amazon-auto-links' ),
            ),    
            // for the similarity lookup search type.
            array(
                'strSectionID'        => 'similarity_lookup',
                'strTabSlug'        => 'similarity_lookup',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Item Look-up', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'similarity_lookup_advanced',
                'strTabSlug'        => 'similarity_lookup',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Advanced Item Look-up Options', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'search_auto_insert3',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'        => 'similarity_lookup',
                'strTitle'            => __( 'Auto Insert', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'search_template3',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'        => 'similarity_lookup',
                'strTitle'            => __( 'Template', 'amazon-auto-links' ),
            ),                
        );
    
    }

    /**
     * Returns the field array with the given section ID.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     */    
    public function getFields( $strSectionID='search', $strPrefix='search_' ) {
        
        switch( $strSectionID ) {
            case 'search':
                return $this->getFieldsOfFirstTab( $strSectionID, $strPrefix );
            case 'search_second':
                return $this->getFieldsOfProductSearch( $strSectionID, $strPrefix );
            case 'search_advanced':
                return $this->getFieldsOfAdvanced( $strSectionID, $strPrefix );
            case 'search_item_lookup':
                return $this->getFieldOfItemLookUp( $strSectionID, $strPrefix );
            case 'search_item_lookup_advanced':
                return $this->getFieldOfItemLookUpAdvanced( $strSectionID, $strPrefix );
            case 'similarity_lookup':
                return $this->getFieldOfSimilarityLookUp( $strSectionID, $strPrefix );
            case 'similarity_lookup_advanced':
                return $this->getFieldOfSimilarityLookUpAdvanced( $strSectionID, $strPrefix );
            case 'search_auto_insert':
            case 'search_auto_insert2':
            case 'search_auto_insert3':
                return $this->getFieldsOfAutoInsert( $strSectionID, $strPrefix ); 
            case 'search_template':
                return $this->getFieldsOfTemplate( $strSectionID, $strPrefix, 'search' ); 
            case 'search_template2':
                return $this->getFieldsOfTemplate( $strSectionID, $strPrefix, 'item_lookup' ); 
            case 'search_template3':
                return $this->getFieldsOfTemplate( $strSectionID, $strPrefix, 'similarity_lookup' ); 
            default:
                return array();
        }

    }
    
    /**
     * Returns the field array with the given section ID.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     */        
    public function getFieldsOfFirstTab( $strSectionID='search', $strPrefix='search_' ) {
        
        return array(
            array(
                'strFieldID' => $strPrefix . 'unit_title',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Unit Name', 'amazon-auto-links' ),
                'strType' => 'text',
                'strDescription' => 'e.g. <code>My Search Unit</code>',
                'vValue' => '',    // the previous value should not appear
            ),
            array(
                'strFieldID' => $strPrefix . 'access_key',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Access Key ID', 'amazon-auto-links' ),
                'strDescription' => __( 'The public key consisting of 20 alphabetic characters.', 'amazon-auto-links' )
                    . ' e.g.<code>022QF06E7MXBSH9DHM02</code><br />'
                    . sprintf( __( 'The keys can be obtained by logging in to the <a href="%1$s" target="_blank">Amazon Web Services web site</a>.', 'amazon-auto-links' ), 'http://aws.amazon.com/' )
                    . ' ' . sprintf( __( 'The instruction is documented <a href="%1$s" target="_blank">here</a>.', 'amazon-auto-links' ), '?post_type=amazon_auto_links&page=aal_help&tab=notes#How_to_Obtain_Access_Key_and_Secret_Key' ),
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 40 : 60,
                'fIf' => empty( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['authentication_keys']['access_key'] ),
                'vDefault' => $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['authentication_keys']['access_key'],
            ),
            array(
                'strFieldID' => $strPrefix . 'access_key_secret',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Secret Access Key', 'amazon-auto-links' ),
                'strDescription' => __( 'The private key consisting of 40 alphabetic characters.', 'amazon-auto-links' )
                    . ' e.g.<code>kWcrlUX5JEDGM/LtmEENI/aVmYvHNif5zB+d9+ct</code>',
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 40 : 60,
                'fIf' => empty( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['authentication_keys']['access_key_secret'] ),
                'vDefault' => $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['authentication_keys']['access_key_secret'],
            ),            
            array(
                'strFieldID' => $strPrefix . 'country',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Country', 'amazon-auto-links' ),
                'strType' => 'select',
                'vLabel' => array(                        
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
                    // 'BR' => 'BR - ' . __( 'Brazil', 'amazon-auto-links' ),
                    // 'MX' => 'MX - ' . __( 'Mexico', 'amazon-auto-links' ),
                ),
                'vDefault' => 'US',
            ),                
            array(
                'strFieldID' => $strPrefix . 'associate_id',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Associate ID', 'amazon-auto-links' ),
                'strType' => 'text',
                'strDescription' => 'e.g. <code>miunosoft-20</code>',
            ),        
            array(
                'strFieldID' => $strPrefix . 'Operation',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Types', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    'ItemSearch'        => '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong> - ' . __( 'returns items that satisfy the search criteria in the title and descriptions.', 'amazon-auto-links' ),
                    'ItemLookup'        => '<span class=""><strong>' . __( 'Item Look-up', 'amazon-auto-links' ) . '</strong> - ' . __( 'returns some or all of the item attributes with the given item identifier.', 'amazon-auto-links' ) . '</span>',
                    'SimilarityLookup'    => '<span class=""><strong>' . __( 'Similar Products', 'amazon-auto-links' ) . '</strong> - ' . __( 'returns products that are similar to one or more items specified.', 'amazon-auto-links' ) . '</span>',
                ),
                // 'vDisable' => array(
                    // 'ItemSearch' => false,
                    // 'ItemLookup' => false,
                    // 'SimilarityLookup'    => true,
                // ),
                // 'strDescription' => __( 'Currently the Similar Products type is still work in progress.', 'amazon-auto-links' ),
                'vDefault' => 'ItemSearch', // array( 'ItemSearch' => true ),
            ),
            array(  // single button
                'strFieldID' => $strPrefix . 'submit_initial_options',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strType' => 'submit',
                'fIf' => ! empty( $strSectionID ),
                'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                    . "<div class='right-button'>",
                'strAfterField' => "</div>",
                'vLabelMinWidth' => 0,
                'vLabel' => __( 'Proceed', 'amazon-auto-links' ),
                'vClassAttribute' => 'button button-primary',
                'strAfterField' => ''
                    . "<input type='hidden' name='amazon_auto_links_admin[{$this->strPageSlug}][{$strSectionID}][{$strPrefix}unit_type]' value='search'>"
                    . "<input type='hidden' name='amazon_auto_links_admin[{$this->strPageSlug}][{$strSectionID}][{$strPrefix}transient_id]' value='" . ( $strTransientID = isset( $_GET['transient_id'] ) ? $_GET['transient_id'] : uniqid() ) . "'>"
                    . "<input type='hidden' name='amazon_auto_links_admin[{$this->strPageSlug}][{$strSectionID}][{$strPrefix}mode]' value='1'>"
                    . "<input type='hidden' name='amazon_auto_links_admin[{$this->strPageSlug}][{$strSectionID}][{$strPrefix}bounce_url]' value='" . add_query_arg( array( 'transient_id' => $strTransientID ) + $_GET, admin_url( $GLOBALS['pagenow'] ) ) . "'>",
            )                
        );
        
    }
    
    /**
     * Returns the field array with the given section ID.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     */    
    public function getFieldsOfProductSearch( $strSectionID='search', $strPrefix='search2_' ) {
        
        $arrUnitOptions = isset( $_REQUEST['transient_id'] )
            ? AmazonAutoLinks_WPUtilities::getTransient( 'AAL_CreateUnit_' . $_REQUEST['transient_id'] )
            : ( $GLOBALS['strAmazonAutoLinks_UnitType'] == 'search' && isset( $_GET['post'] ) && $_GET['post'] != 0
                ? $GLOBALS['oAmazonAutoLinks_Option']->getUnitOptionsByPostID( $_GET['post'] )
                : array()
            );
                            
        return array(
            array(
                'strFieldID' => $strPrefix . 'unit_title',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Unit Name', 'amazon-auto-links' ),
                'strType' => 'text',
                'fIf' => isset( $_REQUEST['transient_id'] ),
                'vValue' => isset( $arrUnitOptions['unit_title'] ) ? $arrUnitOptions['unit_title'] : null,
            ),            
            array(
                'strFieldID' => $strPrefix . 'Keywords',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Search Keyword', 'amazon-auto-links' ),
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 40 : 60,
                'strDescription' => __( 'Enter the keyword to search. For multiple items, separate them by commas.', 'amazon-auto-links' ) 
                    . ' e.g. <code>WordPress, PHP</code>',
                'vValue' => '',    // the previous value should not appear
            ),
            array(
                'strFieldID' => $strPrefix . 'search_type',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Search Type', 'amazon-auto-links' ),
                'strType' => 'text',
                'vDisable' => true,
                'vReadOnly' => true,
                'vValue' => isset( $arrUnitOptions['Operation'] ) ? $this->getSearchTypeLabel( $arrUnitOptions['Operation'] ) : null,
            ),                            
            array(
                'strFieldID' => $strPrefix . 'Operation',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Operation', 'amazon-auto-links' ),
                'strType' => 'hidden',
                'vReadOnly' => true,
                'vValue' => isset( $arrUnitOptions['Operation'] ) ? $arrUnitOptions['Operation'] : null,
            ),                
            array(
                'strFieldID' => $strPrefix . 'country',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Locale', 'amazon-auto-links' ),
                'strType' => 'text',
                'vReadOnly' => true,
                'vValue' => isset( $arrUnitOptions['country'] ) ? $arrUnitOptions['country'] : null,    // for the meta box, pass null so it uses the stored value
            ),                    
            array(
                'strFieldID' => $strPrefix . 'associate_id',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Associate ID', 'amazon-auto-links' ),
                'strType' => 'text',
                'strDescription' => 'e.g. <code>miunosoft-20</code>',
                'vValue' => isset( $arrUnitOptions['associate_id'] ) ? $arrUnitOptions['associate_id'] : null,    // for the meta box, pass null so it uses the stored value
            ),        
            array(
                'strFieldID' => $strPrefix . 'SearchIndex',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Category', 'amazon-auto-links' ),            
                'strType' => 'select',
                'vLabel' => AmazonAutoLinks_Properties::getSearchIndexByLocale( isset( $arrUnitOptions['country'] ) ? $arrUnitOptions['country'] : null ),
                'vDefault' => 'All',
                'strDescription' => __( 'Select the category to limit the searching area.', 'amazon-auto-links' )
                    . ' ' . __( 'Since some options do not work with the All index, it is recommended to pick one.', 'amazon-auto-links' ),
            ),    
            array(
                'strFieldID' => $strPrefix . 'count',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Number of Items', 'amazon-auto-links' ),
                'strType' => 'number',
                'vMax' => $GLOBALS['oAmazonAutoLinks_Option']->getMaximumProductLinkCount() ? $GLOBALS['oAmazonAutoLinks_Option']->getMaximumProductLinkCount() : null,
                'vMin' => 1,
                'strDescription' => __( 'The number of product links to display.' ),
                'vDefault' => 10,
            ),            
            array(
                'strFieldID' => $strPrefix . 'image_size',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Image Size', 'amazon-auto-links' ),
                'strType' => 'number',
                'vAfterInputTag' => ' ' . __( 'pixel', 'amazon-auto-links' ),
                'vDelimiter' => '',
                'strDescription' => __( 'The maximum width of the product image in pixel. Set <code>0</code> for no image.', 'amazon-auto-links' )
                    . ' ' . __( 'Max', 'amazon-auto-links' ) . ': <code>500</code> ' 
                    . __( 'Default', 'amazon-auto-links' ) . ': <code>160</code>',                
                'vMax' => 500,
                'vMin' => 0,
                'vDefault' => 160,
            ),        
            array(
                // see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/SortingbyPopularityPriceorCondition.html
                'strFieldID' => $strPrefix . 'Sort',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Sort Order', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    'pricerank'            => "<strong>" . __( 'Price Ascending', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items from the cheapest to the most expensive.', 'amazon-auto-links' ) . '<br />',
                    'inversepricerank'    => "<strong>" . __( 'Price Descending', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items from the most expensive to the cheapest.', 'amazon-auto-links' ) . '<br />',
                    'salesrank'        => "<strong>" . __( 'Sales Rank', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items based on how well they have been sold, from best to worst sellers.', 'amazon-auto-links' ) . '<br />',
                    'relevancerank'        => "<strong>" . __( 'Relevance Rank', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items based on how often the keyword appear in the product description.', 'amazon-auto-links' ) . '<br />',
                    'reviewrank'        => "<strong>" . __( 'Review Rank', 'amazon-auto-links' ) . "</strong> - " . __( 'Sorts items based on how highly rated the item was reviewed by customers where the highest ranked items are listed first and the lowest ranked items are listed last.', 'amazon-auto-links' ) . '<br />',
                ),
                'vDefault' => 'salesrank',
                'strDescription' => __( 'When the search index is selected to All, this option does not take effect.', 'amazon-auto-links' ),
            ),                
            array(
                'strFieldID' => $strPrefix . 'ref_nosim',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Direct Link Bonus', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription'    => sprintf( __( 'Inserts <code>ref=nosim</code> in the link url. For more information, visit <a href="%1$s">this page</a>.', 'amazon-auto-links' ), 'https://affiliate-program.amazon.co.uk/gp/associates/help/t5/a21' ),
                'vDefault' => 0,
            ),        
            array(
                'strFieldID' => $strPrefix . 'title_length',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Title Length', 'amazon-auto-links' ),
                'strType' => 'number',
                'strDescription' => __( 'The allowed character length for the title.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Use it to prevent a broken layout caused by a very long product title. Set -1 for no limit.', 'amazon-auto-links' ) . '<br />'
                    . __( 'Default', 'amazon-auto-links' ) . ": <code>-1</code>",
                'vDefault' => -1,
            ),                
            array(
                'strFieldID' => $strPrefix . 'description_length',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Description Length', 'amazon-auto-links' ),
                'strType' => 'number',
                'strDescription' => __( 'The allowed character length for the description.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Set -1 for no limit.', 'amazon-auto-links' ) . '<br />'
                    . __( 'Default', 'amazon-auto-links' ) . ": <code>250</code>",
                'vDefault' => 250,
            ),        
            array(
                'strFieldID' => $strPrefix . 'link_style',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Link Style', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1    => 'http://www.amazon.<code>[domain-suffix]</code>/<code>[product-name]</code>/dp/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>'
                        . "&nbsp;<span class='description'>(" . __( 'Default', 'amazon-auto-links' ) . ")</span>",
                    2    => 'http://www.amazon.<code>[domain-suffix]</code>/exec/obidos/ASIN/<code>[asin]</code>/<code>[associate-id]</code>/ref=<code>[...]</code>',
                    3    => 'http://www.amazon.<code>[domain-suffix]</code>/gp/product/<code>[asin]</code>/?tag=<code>[associate-id]</code>&ref=<code>[...]</code>',
                    4    => 'http://www.amazon.<code>[domain-suffix]</code>/dp/ASIN/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>',
                    5    => site_url() . '?' . $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['query']['cloak'] . '=<code>[asin]</code>&locale=<code>[...]</code>&tag=<code>[associate-id]</code>'
                ),
                'vDefault' => 1,
            ),        
            array(
                'strFieldID' => $strPrefix . 'credit_link',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Credit Link', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription'    => sprintf( __( 'Inserts the credit link at the end of the unit output.', 'amazon-auto-links' ), '' ),
                'vDefault' => 1,
            ),    
        );
    }

        protected function getSearchTypeLabel( $strSearchTypeKey ) {
            switch ( $strSearchTypeKey ) {
                case 'ItemSearch' :
                    return __( 'Products', 'amazon-auto-links' );
                case 'ItemLookup' :
                    return __( 'Item Lookup', 'amazon-auto-links' );
                case 'SimilarityLookup' :
                    return __( 'Similar Products', 'amazon-auto-links' );                
            }
        }
        protected function getNodeListByCategory( $arrPreviousPageInput ) {
            
            // Determine the locale.
            if ( ! empty( $arrPreviousPageInput ) )     
                $strLocale = $arrPreviousPageInput['country'];
            else if ( $GLOBALS['strAmazonAutoLinks_UnitType'] == 'search' ) 
                $strLocale = get_post_meta( $_GET['post'], 'country', true );            
            else 
                return array( 'error' => 'ERROR' );
    
            // Prepare API object
            $strPublicKey = $GLOBALS['oAmazonAutoLinks_Option']->getAccessPublicKey();
            $strPrivateKey = $GLOBALS['oAmazonAutoLinks_Option']->getAccessPrivateKey();        
            $oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI( $strLocale, $strPublicKey, $strPrivateKey );
            
    
            // Now fetch the category node.
            $arrBrowseNodes = array();
            $arrNodeLabels = array( 0 => __( 'All', 'amazon-auto-links' ) );
            
            foreach( AmazonAutoLinks_Properties::getRootNoeds( $strLocale ) as $arrNodeIDs )     {
                
                $arrResult = $oAmazonAPI->request( 
                    array(
                        "Operation" => "BrowseNodeLookup",
                        "BrowseNodeId" => implode( ',', $arrNodeIDs ),
                    ),
                    $strLocale,
                    30    // 24*3600*7 // set custom cache lifespan, 7 days
                );
                if ( ! isset( $arrResult['BrowseNodes']['BrowseNode'] ) ) continue;
                        
                $arrBrowseNodes = array_merge( $arrResult['BrowseNodes']['BrowseNode'], $arrBrowseNodes );
        
            }
                
            foreach( $arrBrowseNodes as $arrNode ) {
                
                if ( isset( $arrNode['Ancestors'] ) )
                    $arrNode = $arrNode['Ancestors']['BrowseNode'];
                                                    
                $arrNodeLabels[ $arrNode['BrowseNodeId'] ] = $arrNode['Name'];
                
            }
                
// AmazonAutoLinks_Debug::logArray( $arrNodeLabels );    
            
            return $arrNodeLabels;
        }
    
    /**
     * 
     * @remark            The scope is public because the meta box calls it.
     */
    public function getFieldsOfAdvanced( $strSectionID, $strPrefix ) {
             
        $fIsDisabled = ! $GLOBALS['oAmazonAutoLinks_Option']->isAdvancedAllowed();
        $strOpeningTag = $fIsDisabled ? "<div class='upgrade-to-pro' style='margin:0; padding:0; display: inline-block;' title='" . __( 'Please consider upgrading to Pro to use this feature!', 'amazon-auto-links' ) . "'>" : "";
        $strClosingTag = $fIsDisabled ? "</div>" : "";
        
        return array(
            array(
                'strFieldID' => $strPrefix . 'Title',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Title', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'strType' => 'text',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,
                'strDescription' => __( 'Enter keywords which should be matched in the product title. For multiple keywords, separate them by commas.', 'amazon-auto-links' )
                    . ' ' . __( 'If this is set, the Search Keyword option can be empty.', 'amazon-auto-links' ), 
            ),
            array(
                'strFieldID' => $strPrefix . 'additional_attribute',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Additional Attribute', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'strType' => 'text',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,
            ),    
            array(
                'strFieldID' => $strPrefix . 'search_by',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => '', //__( '', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',    
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,                
                'vLabel' => array(
                    'Manufacturer'    => __( 'Manufacturer', 'amazon-auto-links' ),
                    'Author'        => __( 'Author', 'amazon-auto-links' ),
                    'Actor'            => __( 'Actor', 'amazon-auto-links' ),
                    'Composer'        => __( 'Composer', 'amazon-auto-links' ),
                    'Brand'            => __( 'Brand', 'amazon-auto-links' ),
                    'Artist'        => __( 'Artist', 'amazon-auto-links' ),
                    'Conductor'        => __( 'Conductor', 'amazon-auto-links' ),
                    'Director'        => __( 'Director', 'amazon-auto-links' ),
                ),
                'vDefault' => 'Author',
                'strDescription' => __( 'Enter a keyword to narrow down the results with one of the above attributes.', 'amazon-auto-links' )
                    . ' ' . __( 'If this is set, the Search Keyword option can be empty.', 'amazon-auto-links' ), 
            ),             
            // array(
                // 'strFieldID' => $strPrefix . 'Author',
                // 'strSectionID' => $strSectionID ? $strSectionID : null,
                // 'strTitle' => __( 'Author', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                // 'strType' => 'text',
                // 'strDescription' => __( 'Enter keywords to narrow down the results by author.', 'amazon-auto-links' ), 
            // ),                        
            array(
                'strFieldID' => $strPrefix . 'Availability',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Availability', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',        
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,
                'vLabel' => __( 'Filter out most of the items that are unavailable as may products can become unavailable quickly.', 'amazon-auto-links' ),
                'vDefault' => 1,
            ),        
            array(
                'strFieldID' => $strPrefix . 'Condition',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Condition', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',        
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,                
                'vLabel' => array(
                    'New' => __( 'New', 'amazon-auto-links' ),
                    'Used' => __( 'Used', 'amazon-auto-links' ),
                    'Collectible' => __( 'Collectible', 'amazon-auto-links' ),
                    'Refurbished' => __( 'Refurbished', 'amazon-auto-links' ),
                    'All' => __( 'All', 'amazon-auto-links' ),
                ),
                'vDefault' => 'New',
                'strDescription' => __( 'If the search index is All, this option does not take effect.', 'amazon-auto-links' ),
            ),
            array(
                'strFieldID' => $strPrefix . 'MaximumPrice',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Maximum Price', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'strType' => 'number',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',    
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,                
                'vMin' => 1,
                'strDescription' => __( 'Specifies the maximum price of the items in the response. Prices are in terms of the lowest currency denomination, for example, pennies. For example, 3241 represents $32.41.', 'amazon-auto-links' )
                    . ' ' . __( 'This option will not take effect if the Category option is set to <code>All</code> or <code>Blended</code>', 'amazon-auto-links' ),
            ),                        
            array(
                'strFieldID' => $strPrefix . 'MinimumPrice',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Minimum Price', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'strType' => 'number',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',    
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,                
                'vMin' => 1,
                'strDescription' => __( 'Specifies the minimum price of the items to return. Prices are in terms of the lowest currency denomination, for example, pennies, for example, 3241 represents $32.41.', 'amazon-auto-links' )
                    . ' ' . __( 'This option will not take effect if the Category option is set to <code>All</code> or <code>Blended</code>', 'amazon-auto-links' ),
            ),                    
            array(
                'strFieldID' => $strPrefix . 'MinPercentageOff',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Minimum Percentage Off', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'strType' => 'number',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',        
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,
                'vMin' => 1,
                'strDescription' => __( 'Specifies the minimum percentage off for the items to return.', 'amazon-auto-links' ),
            ),    
            array(
                'strFieldID' => $strPrefix . 'BrowseNode',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Browse Node ID', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'strType' => 'number',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',        
                'vBeforeInputTag' => $strOpeningTag,        
                'vAfterInputTag' => $strClosingTag,                
                'vMin' => 1,
                'strDescription' => __( 'If you know the browse node that you are searching, specify it here. It is a positive integer.', 'amazon-auto-links' ),
            ),    
            array(
                'strFieldID' => $strPrefix . 'MerchantId',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Merchant ID', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'strType' => 'text',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',        
                'vBeforeInputTag' => $strOpeningTag,        
                'vAfterInputTag' => $strClosingTag,                
                'strDescription' => __( 'Filter search results and offer listings to only include items sold by Amazon. By default, Product Advertising API returns items sold by various merchants including Amazon. Use the Amazon to limit the response to only items sold by Amazon. Case sensitive. e.g.<code>Amazon</code>', 'amazon-auto-links' ),
            ),                
        );        
        
    }
    
    protected function getFieldsOfAutoInsert( $strSectionID, $strPrefix ) {

        return array(    
            array(
                'strFieldID' => $strPrefix . 'auto_insert',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Enable Auto Insert', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'Set it On to insert product links into post and pages automatically. More advanced options can be configured later.', 'amazon-auto-links' ),
                'vDefault' => 1,
            ),
        
        );
    }
    
    protected function getFieldsOfTemplate( $strSectionID, $strPrefix, $strUnitType='search' ) {
        
        $oForm_Template = new AmazonAutoLinks_Form_Template( $this->strPageSlug );
        return $oForm_Template->getTemplateFields( $strSectionID, $strPrefix, true, $strUnitType );        
                
    }
    
    /**
     * 
     * @remark            The scope is public because it is access from the meta box class as well.
     */
    public function getFieldOfItemLookUp( $strSectionID, $strPrefix ) {
        
        $bIsSearchUnitType = in_array( $GLOBALS['strAmazonAutoLinks_UnitType'], array( 'search', 'item_lookup', 'similarity_lookup ' ) );
        $arrUnitOptions = isset( $_REQUEST['transient_id'] )
            ? AmazonAutoLinks_WPUtilities::getTransient( 'AAL_CreateUnit_' . $_REQUEST['transient_id'] )
            : ( $bIsSearchUnitType && isset( $_GET['post'] ) && $_GET['post'] != 0
                ? $GLOBALS['oAmazonAutoLinks_Option']->getUnitOptionsByPostID( $_GET['post'] )
                : array()
            );
                            
        $bUPCAllowed = ( isset( $arrUnitOptions['country'] ) && $arrUnitOptions['country'] != 'CA' );
        $bISBNAllowed = ( isset( $arrUnitOptions['country'] ) && $arrUnitOptions['country'] == 'US' );

        return array(
            array(
                'strFieldID' => $strPrefix . 'unit_title',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Unit Name', 'amazon-auto-links' ),
                'strType' => 'text',
                'fIf' => isset( $_REQUEST['transient_id'] ),
                'vValue' => isset( $arrUnitOptions['unit_title'] ) ? $arrUnitOptions['unit_title'] : null,
            ),            
            array(
                'strFieldID' => $strPrefix . 'ItemId',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Item ID', 'amazon-auto-links' ),
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 40 : 60,
                'strDescription' => __( 'Enter the ID(s) of the product. For more more than one items, use the <code>,</code> (comma) characters to delimit the items.', 'amazon-auto-links' ) 
                    . ' e.g. <code>B009ZVO3H6, B0043D2DZA</code>',
                'vValue' => '',    // the previous value should not appear
            ),

            array(
                'strFieldID' => $strPrefix . 'IdType',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'ID Type', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(
                    'ASIN' => 'ASIN',
                    'SKU' => 'SKU',
                    'UPC' => '<span class="' . ( $bUPCAllowed ? "" : "disabled" ) . '">UPC <span class="description">(' . __( 'Not available in the CA locale.', 'amazon-auto-links' ) . ')</span></span>',
                    'EAN' => 'EAN',
                    'ISBN' => '<span class="' . ( $bISBNAllowed ? "" : "disabled" ) . '">ISBN <span class="description">(' . __( 'The US locale only, when the search index is Books.', 'amaozn-auto-links' ) .')</span></span>',
                ),
                'vDisable' => array(
                    'ASIN' => false,
                    'SKU' => false,
                    'UPC' => $bUPCAllowed ? false : true,
                    'EAN' => false,
                    'ISBN' => $bISBNAllowed ? false : true,
                ),
                'vDefault' => 'ASIN',
            ),            
            array(
                'strFieldID' => $strPrefix . 'search_type',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Search Type', 'amazon-auto-links' ),
                'strType' => 'text',
                'vDisable' => true,
                'vReadOnly' => true,
                'vValue' => isset( $arrUnitOptions['Operation'] ) ? $this->getSearchTypeLabel( $arrUnitOptions['Operation'] ) : null,
            ),                            
            array(
                'strFieldID' => $strPrefix . 'Operation',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Operation', 'amazon-auto-links' ),
                'strType' => 'hidden',
                'vReadOnly' => true,
                'vValue' => isset( $arrUnitOptions['Operation'] ) ? $arrUnitOptions['Operation'] : null,
            ),
            array(
                'strFieldID' => $strPrefix . 'country',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Locale', 'amazon-auto-links' ),
                'strType' => 'text',
                'vReadOnly' => true,
                'vValue' => isset( $arrUnitOptions['country'] ) ? $arrUnitOptions['country'] : null,    // for the meta box, pass null so it uses the stored value
            ),                    
            array(
                'strFieldID' => $strPrefix . 'associate_id',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Associate ID', 'amazon-auto-links' ),
                'strType' => 'text',
                'strDescription' => 'e.g. <code>miunosoft-20</code>',
                'vValue' => isset( $arrUnitOptions['associate_id'] ) ? $arrUnitOptions['associate_id'] : null,    // for the meta box, pass null so it uses the stored value
            ),        
            array(
                'strFieldID' => $strPrefix . 'SearchIndex',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Categories', 'amazon-auto-links' ),            
                'strType' => 'select',
                'vLabel' => AmazonAutoLinks_Properties::getSearchIndexByLocale( isset( $arrUnitOptions['country'] ) ? $arrUnitOptions['country'] : null ),
                'vDefault' => 'All',
                'vValue' => isset( $arrUnitOptions['SearchIndex'] ) ? $arrUnitOptions['SearchIndex'] : null,    // for the meta box, pass null so it uses the stored value
                'strDescription' => __( 'Select the category to limit the searching area.', 'amazon-auto-links' )
                    . ' ' . __( 'If the above ID Type is ISBN, this will be automatically set to Books.', 'amazon-auto-links' )
                    . ' ' . __( 'If the ID Type is ASIN this option will not take effect.', 'amazon-auto-links' ),
            ),        
            array(
                'strFieldID' => $strPrefix . 'image_size',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Image Size', 'amazon-auto-links' ),
                'strType' => 'number',
                'vAfterInputTag' => ' ' . __( 'pixel', 'amazon-auto-links' ),
                'vDelimiter' => '',
                'strDescription' => __( 'The maximum width of the product image in pixel. Set <code>0</code> for no image.', 'amazon-auto-links' )
                    . ' ' . __( 'Max', 'amazon-auto-links' ) . ': <code>500</code> ' 
                    . __( 'Default', 'amazon-auto-links' ) . ': <code>160</code>',                
                'vMax' => 500,
                'vMin' => 0,
                'vDefault' => 160,
            ),                
            array(
                'strFieldID' => $strPrefix . 'ref_nosim',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Direct Link Bonus', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription'    => sprintf( __( 'Inserts <code>ref=nosim</code> in the link url. For more information, visit <a href="%1$s">this page</a>.', 'amazon-auto-links' ), 'https://affiliate-program.amazon.co.uk/gp/associates/help/t5/a21' ),
                'vDefault' => 0,
            ),        
            array(
                'strFieldID' => $strPrefix . 'title_length',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Title Length', 'amazon-auto-links' ),
                'strType' => 'number',
                'strDescription' => __( 'The allowed character length for the title.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Use it to prevent a broken layout caused by a very long product title. Set -1 for no limit.', 'amazon-auto-links' ) . '<br />'
                    . __( 'Default', 'amazon-auto-links' ) . ": <code>-1</code>",
                'vDefault' => -1,
            ),                
            array(
                'strFieldID' => $strPrefix . 'description_length',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Description Length', 'amazon-auto-links' ),
                'strType' => 'number',
                'strDescription' => __( 'The allowed character length for the description.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Set -1 for no limit.', 'amazon-auto-links' ) . '<br />'
                    . __( 'Default', 'amazon-auto-links' ) . ": <code>250</code>",
                'vDefault' => 250,
            ),        
            array(
                'strFieldID' => $strPrefix . 'link_style',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Link Style', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1    => 'http://www.amazon.<code>[domain-suffix]</code>/<code>[product-name]</code>/dp/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>'
                        . "&nbsp;<span class='description'>(" . __( 'Default', 'amazon-auto-links' ) . ")</span>",
                    2    => 'http://www.amazon.<code>[domain-suffix]</code>/exec/obidos/ASIN/<code>[asin]</code>/<code>[associate-id]</code>/ref=<code>[...]</code>',
                    3    => 'http://www.amazon.<code>[domain-suffix]</code>/gp/product/<code>[asin]</code>/?tag=<code>[associate-id]</code>&ref=<code>[...]</code>',
                    4    => 'http://www.amazon.<code>[domain-suffix]</code>/dp/ASIN/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>',
                    5    => site_url() . '?' . $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['query']['cloak'] . '=<code>[asin]</code>&locale=<code>[...]</code>&tag=<code>[associate-id]</code>'
                ),
                'vDefault' => 1,
            ),        
            array(
                'strFieldID' => $strPrefix . 'credit_link',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Credit Link', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription'    => sprintf( __( 'Inserts the credit link at the end of the unit output.', 'amazon-auto-links' ), '' ),
                'vDefault' => 1,
            ),    
        );
        
    }
    
    /**
     * 
     * @remark            The scope is public because the meta box calls it.
     */
    public function getFieldOfItemLookUpAdvanced( $strSectionID, $strPrefix ) {
             
        $fIsDisabled = ! $GLOBALS['oAmazonAutoLinks_Option']->isAdvancedAllowed();
        $strOpeningTag = $fIsDisabled ? "<div class='upgrade-to-pro' style='margin:0; padding:0; display: inline-block;' title='" . __( 'Please consider upgrading to Pro to use this feature!', 'amazon-auto-links' ) . "'>" : "";
        $strClosingTag = $fIsDisabled ? "</div>" : "";
        
        return array(
            array(
                'strFieldID' => $strPrefix . 'MerchantId',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Merchant ID', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'strType' => 'radio',
                'vLabel' => array(
                    'All'    => 'All',    // not that the API will not accept the value All so do appropriate sanitization when performing the API request.
                    'Amazon' => 'Amazon',
                ),
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,
                'strDescription' => __( 'Select <code>Amazon</code> if you only want to see items sold by Amazon; otherwise, <code>All</code>.', 'amazon-auto-links' ),
                'vDefault' => 'All',
            ),
            array(
                'strFieldID' => $strPrefix . 'Condition',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Condition', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vDisable' => $fIsDisabled,
                'vClassAttribute' => $fIsDisabled ? 'disabled' : '',        
                'vBeforeInputTag' => $strOpeningTag,
                'vAfterInputTag' => $strClosingTag,                
                'vLabel' => array(
                    'New' => __( 'New', 'amazon-auto-links' ),
                    'Used' => __( 'Used', 'amazon-auto-links' ),
                    'Collectible' => __( 'Collectible', 'amazon-auto-links' ),
                    'Refurbished' => __( 'Refurbished', 'amazon-auto-links' ),
                    'All' => __( 'All', 'amazon-auto-links' ),
                ),
                'vDefault' => 'New',
                'strDescription' => __( 'If the search index is All, this option does not take effect.', 'amazon-auto-links' ),
            )                
        );        
        
    }
    
    /**
     * 
     * @remark            The scope is public because the meta box calls it.
     */
    public function getFieldOfSimilarityLookUp( $strSectionID, $strPrefix ) {
        
        $bIsSearchUnitType = in_array( $GLOBALS['strAmazonAutoLinks_UnitType'], array( 'search', 'item_lookup', 'similarity_lookup ' ) );
        $arrUnitOptions = isset( $_REQUEST['transient_id'] )
            ? AmazonAutoLinks_WPUtilities::getTransient( 'AAL_CreateUnit_' . $_REQUEST['transient_id'] )
            : ( $bIsSearchUnitType && isset( $_GET['post'] ) && $_GET['post'] != 0
                ? $GLOBALS['oAmazonAutoLinks_Option']->getUnitOptionsByPostID( $_GET['post'] )
                : array()
            );
                
        return array(
            array(
                'strFieldID' => $strPrefix . 'unit_title',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Unit Name', 'amazon-auto-links' ),
                'strType' => 'text',
                'fIf' => isset( $_REQUEST['transient_id'] ),
                'vValue' => isset( $arrUnitOptions['unit_title'] ) ? $arrUnitOptions['unit_title'] : null,
            ),            
            array(
                'strFieldID' => $strPrefix . 'ItemId',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Item ASIN', 'amazon-auto-links' ),
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 40 : 60,
                'strDescription' => __( 'Enter the ASIN(s) of the product. For more more than one items, use the <code>,</code> (comma) characters to delimit the items.', 'amazon-auto-links' ) 
                    . ' e.g. <code>B009ZVO3H6</code>',
                'vValue' => $strSectionID ? '' : null,    // the previous value should not appear
            ),        
            array(
                'strFieldID' => $strPrefix . 'SimilarityType',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Similarity Type', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    'Intersection'        => __( 'Intersection', 'amazon-auto-links' ) . ' - ' . __( 'returns the intersection of items that are similar to all of the ASINs specified', 'amazon-auto-links' ),
                    'Random'        => __( 'Random', 'amazon-auto-links' ) . ' - ' . __( 'returns the union of randomly picked items that are similar to all of the ASINs specified.', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'The maximum of only ten items can be retrieved.' , 'amazon-auto-links' ),
                'vDefault' => 'Intersection',
            ),
            array(
                'strFieldID' => $strPrefix . 'count',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Number of Items', 'amazon-auto-links' ),
                'strType' => 'number',
                'vMax' => 10,    // regardless of whether it's pro or not, the max is 10.
                'vMin' => 1,
                'strDescription' => __( 'The number of product links to display. This unit type cannot display more than 10 items.' ),
                'vDefault' => 10,
            ),                
            array(
                'strFieldID' => $strPrefix . 'search_type',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Search Type', 'amazon-auto-links' ),
                'strType' => 'text',
                'vDisable' => true,
                'vReadOnly' => true,
                'vValue' => isset( $arrUnitOptions['Operation'] ) ? $this->getSearchTypeLabel( $arrUnitOptions['Operation'] ) : __( 'Similar Products', 'amazon-auto-links' ),
            ),                            
            array(
                'strFieldID' => $strPrefix . 'Operation',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Operation', 'amazon-auto-links' ),
                'strType' => 'hidden',
                'vReadOnly' => true,
                'vValue' => isset( $arrUnitOptions['Operation'] ) ? $arrUnitOptions['Operation'] : 'SimilarityLookup',
            ),
            array(
                'strFieldID' => $strPrefix . 'country',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Locale', 'amazon-auto-links' ),
                'strType' => 'text',
                'vReadOnly' => true,
                'vValue' => isset( $arrUnitOptions['country'] ) ? $arrUnitOptions['country'] : null,    // for the meta box, pass null so it uses the stored value
            ),                    
            array(
                'strFieldID' => $strPrefix . 'associate_id',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Associate ID', 'amazon-auto-links' ),
                'strType' => 'text',
                'strDescription' => 'e.g. <code>miunosoft-20</code>',
                'vValue' => isset( $arrUnitOptions['associate_id'] ) ? $arrUnitOptions['associate_id'] : null,    // for the meta box, pass null so it uses the stored value
            ),            
            array(
                'strFieldID' => $strPrefix . 'image_size',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Image Size', 'amazon-auto-links' ),
                'strType' => 'number',
                'vAfterInputTag' => ' ' . __( 'pixel', 'amazon-auto-links' ),
                'vDelimiter' => '',
                'strDescription' => __( 'The maximum width of the product image in pixel. Set <code>0</code> for no image.', 'amazon-auto-links' )
                    . ' ' . __( 'Max', 'amazon-auto-links' ) . ': <code>500</code> ' 
                    . __( 'Default', 'amazon-auto-links' ) . ': <code>160</code>',                
                'vMax' => 500,
                'vMin' => 0,
                'vDefault' => 160,
            ),                
            array(
                'strFieldID' => $strPrefix . 'ref_nosim',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Direct Link Bonus', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription'    => sprintf( __( 'Inserts <code>ref=nosim</code> in the link url. For more information, visit <a href="%1$s">this page</a>.', 'amazon-auto-links' ), 'https://affiliate-program.amazon.co.uk/gp/associates/help/t5/a21' ),
                'vDefault' => 0,
            ),        
            array(
                'strFieldID' => $strPrefix . 'title_length',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Title Length', 'amazon-auto-links' ),
                'strType' => 'number',
                'strDescription' => __( 'The allowed character length for the title.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Use it to prevent a broken layout caused by a very long product title. Set -1 for no limit.', 'amazon-auto-links' ) . '<br />'
                    . __( 'Default', 'amazon-auto-links' ) . ": <code>-1</code>",
                'vDefault' => -1,
            ),                
            array(
                'strFieldID' => $strPrefix . 'description_length',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Description Length', 'amazon-auto-links' ),
                'strType' => 'number',
                'strDescription' => __( 'The allowed character length for the description.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Set -1 for no limit.', 'amazon-auto-links' ) . '<br />'
                    . __( 'Default', 'amazon-auto-links' ) . ": <code>250</code>",
                'vDefault' => 250,
            ),        
            array(
                'strFieldID' => $strPrefix . 'link_style',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Link Style', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1    => 'http://www.amazon.<code>[domain-suffix]</code>/<code>[product-name]</code>/dp/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>'
                        . "&nbsp;<span class='description'>(" . __( 'Default', 'amazon-auto-links' ) . ")</span>",
                    2    => 'http://www.amazon.<code>[domain-suffix]</code>/exec/obidos/ASIN/<code>[asin]</code>/<code>[associate-id]</code>/ref=<code>[...]</code>',
                    3    => 'http://www.amazon.<code>[domain-suffix]</code>/gp/product/<code>[asin]</code>/?tag=<code>[associate-id]</code>&ref=<code>[...]</code>',
                    4    => 'http://www.amazon.<code>[domain-suffix]</code>/dp/ASIN/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>',
                    5    => site_url() . '?' . $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['query']['cloak'] . '=<code>[asin]</code>&locale=<code>[...]</code>&tag=<code>[associate-id]</code>'
                ),
                'vDefault' => 1,
            ),        
            array(
                'strFieldID' => $strPrefix . 'credit_link',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Credit Link', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription'    => sprintf( __( 'Inserts the credit link at the end of the unit output.', 'amazon-auto-links' ), '' ),
                'vDefault' => 1,
            ),    
        );        
        
    }

    /**
     * 
     * @remark            The scope is public because the meta box calls it.
     */
    public function getFieldOfSimilarityLookUpAdvanced( $strSectionID, $strPrefix ) {
        return $this->getFieldOfItemLookUpAdvanced( $strSectionID, $strPrefix );        
    }
    
}