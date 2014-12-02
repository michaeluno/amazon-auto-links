<?php
/**
 * Provides the definitions of form fields for the category type unit.
 * 
 * @since            2.0.0
 * @remark            The admin page and meta box access it.
 */
abstract class AmazonAutoLinks_Form_Category_ extends AmazonAutoLinks_Form {

    protected $strPageSlug = 'aal_add_category_unit';
    
    public function getSections( $strPageSlug='' ) {
    
        $strPageSlug = $strPageSlug ? $strPageSlug : $this->strPageSlug;
        return array(

            array(
                'strSectionID'        => 'category',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'        => 'set_category_unit_options',
                'strTitle'            => __( 'Add New Unit by Category', 'amazon-auto-links' ),
            ),        
            array(
                'strSectionID'        => 'category_auto_insert',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'        => 'set_category_unit_options',
                'strTitle'            => __( 'Auto Insert', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'category_template',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'        => 'set_category_unit_options',
                'strTitle'            => __( 'Template', 'amazon-auto-links' ),
            )                        
        );
    
    }
    
    /**
     * Returns the field array with the given section ID.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     */    
    public function getFields( $strSectionID='category', $strPrefix='category_' ) {
        
        switch( $strSectionID ) {
            case 'category':
                return $this->getCategoryFields( $strSectionID, $strPrefix );
            case 'category_auto_insert':
                return $this->getAutoInsertFields( $strSectionID, $strPrefix );
            case 'category_template':
                return $this->getTemplateFields( $strSectionID, $strPrefix );                
        }

    }
    

    public function getCategoryFields( $strSectionID='category', $strPrefix='category_' ) {
        
        return array(
            array(
                'strFieldID' => $strPrefix . 'unit_title',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Unit Name', 'amazon-auto-links' ),
                'strType' => 'text',
                'strDescription' => 'e.g. <code>My Unit</code>',
                'vValue' => '',    // the previous value should not appear
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
                    'BR' => 'BR - ' . __( 'Brazil', 'amazon-auto-links' ),
                    'MX' => 'MX - ' . __( 'Mexico', 'amazon-auto-links' ),
                ),
                'vDefault' => 'US',
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
                'strFieldID' => $strPrefix . 'count',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Number of Items', 'amazon-auto-links' ),
                'strType' => 'number',
                'strDescription' => __( 'The number of product links to display.', 'amazon-auto-links' ),
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
            array(
                'strFieldID' => $strPrefix . 'keep_raw_title',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Sort Option', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vLabel' => __( 'Keep raw titles.', 'amazon-auto-links' ),
                'strDescription' => __( 'If checked, unsanitized titles will be used. This is useful to sort by rank.', 'amazon-auto-links' ),
                'vDefault' => false,
            ),                
            array(
                'strFieldID' => $strPrefix . 'feed_type',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Types', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vLabel' => array(    
                    // These keys will be used in a url when generating the feed url.
                    'bestsellers'            => __( 'Best Sellers', 'amazon-auto-links' ),
                    'new-releases'            => __( 'Hot New Releases', 'amazon-auto-links' ),
                    'movers-and-shakers'    => __( 'Mover and Shakers', 'amazon-auto-links' ),
                    'top-rated'                => __( 'Top Rated', 'amazon-auto-links' ),
                    'most-wished-for'        => __( 'Most Wished For', 'amazon-auto-links' ),
                    'most-gifted'            => __( 'Gift Ideas', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'It is recommended to check only a few for faster page loading.', 'amazon-auto-links' )
                    . '&nbsp;' . __( 'Some of the types other than Best Sellers are not supported in some locales.', 'amazon-auto-links' ),
                'vDefault' => array( 'bestsellers' => true ),
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
        $arrFields =  $oForm_Template->getTemplateFields( $strSectionID, $strPrefix, false, 'category' );
        $arrFields[] =     array(  // single button
            'strFieldID' => $strPrefix . 'submit_initial_options',
            'strSectionID' => $strSectionID,
            'strType' => 'submit',
            'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                . "<div class='right-button'>",
            'strAfterField' => "</div>",
            'vLabelMinWidth' => 0,
            'vLabel' => __( 'Proceed', 'amazon-auto-links' ),
            'vClassAttribute' => 'button button-primary',
            'strAfterField' => ''
                . '<input type="hidden" name="amazon_auto_links_admin[aal_add_category_unit][category][category_unit_type]" value="category">'
                . '<input type="hidden" name="amazon_auto_links_admin[aal_add_category_unit][category][category_transient_id]" value="' . ( $strTransientID = isset( $_GET['transient_id'] ) ? $_GET['transient_id'] : uniqid() ) . '">'
                . '<input type="hidden" name="amazon_auto_links_admin[aal_add_category_unit][category][category_mode]" value="1">'
                . '<input type="hidden" name="amazon_auto_links_admin[aal_add_category_unit][category][category_bounce_url]" value="' . add_query_arg( array( 'transient_id' => $strTransientID ) + $_GET, admin_url( $GLOBALS['pagenow'] ) ) . '">',
            'vRedirect'    => add_query_arg( array( 'tab' => 'select_categories', 'transient_id' => $strTransientID ) + $_GET, admin_url( $GLOBALS['pagenow'] ) ),
        );
        
        return $arrFields;
        
        // return array(
            // array(
                // 'strFieldID' => $strPrefix . 'template_id',
                // 'strSectionID' => $strSectionID,
                // 'strType' => 'select',            
                // 'strDescription'    => __( 'Sets a default template for this unit.', 'amazon-auto-links' ),
                // 'vLabel'            => $GLOBALS['oAmazonAutoLinks_Templates']->getTemplateArrayForSelectLabel(),
                // 'strType'            => 'select',
                // 'vDefault'            => $GLOBALS['oAmazonAutoLinks_Templates']->getPluginDefaultTemplateID( 'category' ),    // // defined in the 'unit_type' field
            // ),                
            // array(  // single button
                // 'strFieldID' => $strPrefix . 'submit_initial_options',
                // 'strSectionID' => $strSectionID,
                // 'strType' => 'submit',
                // 'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                    // . "<div class='right-button'>",
                // 'strAfterField' => "</div>",
                // 'vLabelMinWidth' => 0,
                // 'vLabel' => __( 'Proceed', 'amazon-auto-links' ),
                // 'vClassAttribute' => 'button button-primary',
                // 'strAfterField' => ''
                    // . '<input type="hidden" name="amazon_auto_links_admin[aal_add_category_unit][category][category_unit_type]" value="category">'
                    // . '<input type="hidden" name="amazon_auto_links_admin[aal_add_category_unit][category][category_transient_id]" value="' . ( $strTransientID = isset( $_GET['transient_id'] ) ? $_GET['transient_id'] : uniqid() ) . '">'
                    // . '<input type="hidden" name="amazon_auto_links_admin[aal_add_category_unit][category][category_mode]" value="1">'
                    // . '<input type="hidden" name="amazon_auto_links_admin[aal_add_category_unit][category][category_bounce_url]" value="' . add_query_arg( array( 'transient_id' => $strTransientID ) + $_GET, admin_url( $GLOBALS['pagenow'] ) ) . '">',
                // 'vRedirect'    => add_query_arg( array( 'tab' => 'select_categories', 'transient_id' => $strTransientID ) + $_GET, admin_url( $GLOBALS['pagenow'] ) ),
            // )            
        
        // );
        
    }
}