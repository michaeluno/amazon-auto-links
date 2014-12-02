<?php
/**
 * Provides the definitions of form fields for the category type unit.
 * 
 * @since            2.0.0
 * @remark            The admin page and meta box access it.
 */
abstract class AmazonAutoLinks_Form_Tag_ extends AmazonAutoLinks_Form {

    protected $strPageSlug = 'aal_add_tag_unit';

    public function getSections( $strPageSlug='' ) {
    
        $strPageSlug = $strPageSlug ? $strPageSlug : $this->strPageSlug;
        return array(
        
            array(
                'strSectionID'        => 'tag',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Add New Unit by Tag and Customer ID', 'amazon-auto-links' ),
            ),        
            array(
                'strSectionID'        => 'tag_auto_insert',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Auto Insert', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'tag_template',
                'strPageSlug'        => $strPageSlug,
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
    public function getFields( $strSectionID='tag', $strPrefix='tag_' ) {
        
        switch( $strSectionID ) {
            case 'tag':
                return $this->getTagFields( $strSectionID, $strPrefix );
            case 'tag_auto_insert':
                return $this->getAutoInsertFields( $strSectionID, $strPrefix );
            case 'tag_template':
                return $this->getTemplateFields( $strSectionID, $strPrefix );                
        }

    }
    
    protected function getTagFields( $strSectionID, $strPrefix ) {
        
        return array(
            array(
                'strFieldID' => $strPrefix . 'unit_title',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Unit Name', 'amazon-auto-links' ),
                'strType' => 'text',
                'strDescription' => 'e.g. <code>My Tag Unit</code>',
                'vValue' => '',    // the previous value should not appear
            ),        
            array(
                'strFieldID' => $strPrefix . 'country',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Country', 'amazon-auto-links' ),
                'strType' => 'select',
                'vLabel' => array(                        
                    // 'AT' => 'AT - ' . __( 'Australia', 'amazon-auto-links' ),
                    // 'CA' => 'CA - ' . __( 'Canada', 'amazon-auto-links' ),
                    // 'CN' => 'CN - ' . __( 'China', 'amazon-auto-links' ),
                    // 'FR' => 'FR - ' . __( 'France', 'amazon-auto-links' ),
                    // 'DE' => 'DE - ' . __( 'Germany', 'amazon-auto-links' ),
                    // 'IT' => 'IT - ' . __( 'Italy', 'amazon-auto-links' ),
                    // 'JP' => 'JP - ' . __( 'Japan', 'amazon-auto-links' ),
                    // 'UK' => 'UK - ' . __( 'United Kingdom', 'amazon-auto-links' ),
                    // 'ES' => 'ES - ' . __( 'Spain', 'amazon-auto-links' ),
                    'US' => 'US - ' . __( 'United States', 'amazon-auto-links' ),
                    // 'IN' => 'IN - ' . __( 'India', 'amazon-auto-links' ),
                    // 'BR' => 'BR - ' . __( 'Brazil', 'amazon-auto-links' ),
                    // 'MX' => 'MX - ' . __( 'Mexico', 'amazon-auto-links' ),
                ),
                'vDefault' => 'US',
                'strDescription' => __( 'Currently only the U.S. locale is supported for this unit type.', 'amazon-auto-links' ),
            ),        
            array(
                'strFieldID' => $strPrefix . 'associate_id',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Associate ID', 'amazon-auto-links' ),
                'strType' => 'text',
                'strDescription' => 'e.g. <code>miunosoft-20</code>',
                'vDefault' => '',
            ),            
            array(
                'strFieldID' => $strPrefix . 'tags',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Tags', 'amazon-auto-links' ),
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 40 : 60,
                'strDescription' => __( 'Enter keywords that represent tags separated by commas.', 'amazon-auto-links' )
                    . ' ' . __( 'If the customer ID is provided, this option is optional and if it is left empty, all products tagged by the customer will be fetched.', 'amazon-auto-links' )
                    . ' ' . __( 'Any upper-case characters will be converted to lower-cases.', 'amazon-auto-links' )
                    . '<br />e.g. <code>wordpress, php</code>',
                'vValue' => '',    // the previous value should not appear
            ),    
            array(
                'strFieldID' => $strPrefix . 'customer_id',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Customer ID', 'amazon-auto-links' ) . ' <span class="description">(' . __( 'optional', 'amazon-auto-links' ) . ')</span>',
                'strType' => 'text',
                'strDescription' => __( 'Enter a 13-character ID of the customer who tagged the products.', 'amazon-auto-links' ) 
                    . ' ' . sprintf( __( 'You can find it by looking at the url of the customer profile page. The format is <code>%1$s</code>.', 'amazon-auto-links' ), 'http://www.amazon.com/gp/pdp/profile/[customer_id]/' )
                    . ' ' . __( 'This is recommended to filter spam tagged products.', 'amazon-auto-links' ) 
                    . ' ' . __( 'If not specified, product links tagged by all customers will be fetched.', 'amazon-auto-links' )
                    . '<br />e.g. <code>AJM38DLD0P3H8</code>' . ' ' . sprintf( __( 'An example of the <a href="%1$s" target="_blank">customer profile page</a>.', 'amazon-auto-links' ), 'http://www.amazon.com/gp/pdp/profile/AJM38DLD0P3H8/' ),
                // 'vAfterInputTag' => "<span class='description optional'>(" . __( 'optional', 'amazon-auto-links' ) . ")</span>",
                // 'vValue' => '',    // the previous value should not appear
            ),    
            array(
                'strFieldID' => $strPrefix . 'feed_type',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Types', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vLabel' => array(    
                    // These keys will be used in a url when generating the feed url.
                    'new'            => __( 'New', 'amazon-auto-links' ),
                    'popular'        => __( 'Popular', 'amazon-auto-links' ),
                    'recent'        => __( 'Recent', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'It is recommended to check only a few for faster page loading.', 'amazon-auto-links' )
                    . '&nbsp;' . __( 'If the customer ID is provided, this option will not take effect.', 'amazon-auto-links' ),
                'vDefault' => array( 'new' => true ),
            ),
            array(
                'strFieldID' => $strPrefix . 'threshold',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Threshhold', 'amazon-auto-links' ),
                'strType' => 'number',
                'vMin' => 1,
                // 'vAfterInputTag' => ' ' . __( 'pixel', 'amazon-auto-links' ),
                'vDelimiter' => '',
                'strDescription' => __( 'This option indicates a threshold for how many times an item must have been tagged in order to appear in the "recent" feed. ', 'amazon-auto-links' )
                    . ' ' . __( 'If the threshold is set to 5, items will not appear in the recency feed until they have been tagged 5 times, and will be bumped up every time they are tagged after that.', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>2</code>',
                'vDefault' => 2,
            ),            
            array(
                'strFieldID' => $strPrefix . 'count',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Number of Items', 'amazon-auto-links' ),
                'strType' => 'number',
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
                'strFieldID' => $strPrefix . 'sort',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Sort Order', 'amazon-auto-links' ),
                'strType' => 'select',
                'vLabel' => array(                        
                    'date'                => __( 'Date', 'amazon-auto-links' ),
                    'title'                => __( 'Title', 'amazon-auto-links' ),
                    'title_descending'    => __( 'Title Descending', 'amazon-auto-links' ),
                    'random'            => __( 'Random', 'amazon-auto-links' ),
                ),
                'vDefault' => 'random',
            ),        
            // array(
                // 'strFieldID' => $strPrefix . 'keep_raw_title',
                // 'strSectionID' => $strSectionID ? $strSectionID : null,
                // 'strTitle' => __( 'Sort Option', 'amazon-auto-links' ),
                // 'strType' => 'checkbox',
                // 'vLabel' => __( 'Keep raw titles.', 'amazon-auto-links' ),
                // 'strDescription' => __( 'If checked, unsanitized titles will be used. This is useful to sort by rank.', 'amazon-auto-links' ),
                // 'vDefault' => false,
            // ),                    
            array(
                'strFieldID' => $strPrefix . 'ref_nosim',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Direct Link Bonus', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription'    => sprintf( __( 'Inserts <code>ref=nosim</code> in the product link url. For more information, visit <a href="%1$s">this page</a>.', 'amazon-auto-links' ), 'https://affiliate-program.amazon.co.uk/gp/associates/help/t5/a21' ),
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
    
    protected function getAutoInsertFields( $strSectionID, $strPrefix ) {
        
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
    
    protected function getTemplateFields( $strSectionID, $strPrefix ) {
        
        $oForm_Template = new AmazonAutoLinks_Form_Template( $this->strPageSlug );
        return $oForm_Template->getTemplateFields( $strSectionID, $strPrefix, true, 'tag' );
        
    }
    
}