<?php
/**
 * Provides the definitions of form fields for the category type unit.
 * 
 * @since            2.0.0
 * @remark           The admin page and meta box access it.
 */
abstract class AmazonAutoLinks_Form_Settings_ extends AmazonAutoLinks_Form {
    
    protected $strPageSlug = 'aal_settings';
    
    public function getSections( $strPageSlug='aal_settings' ) {
        
        $strPageSlug = $strPageSlug ? $strPageSlug : $this->strPageSlug;
        return array(
        
            // authentication
            array( 
                'strSectionID'        => 'authentication_keys',
                'strCapability'       => 'manage_options',
                'strPageSlug'         => $strPageSlug,
                'strTabSlug'          => 'authentication',
                'strTitle'            => __( 'AWS Access Key Identifiers', 'amazon-auto-links' ),
                'strDescription'      => sprintf( __( 'For the Search Unit type, credentials are required to perform search requests with Amazon <a href="%1$s" target="_blank">Product Advertising API</a>.', 'amazon-auto-links' ), 'https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html' )
                    . ' ' . sprintf( __( 'The keys can be obtained by logging in to the <a href="%1$s" target="_blank">Amazon Web Services web site</a>.', 'amazon-auto-links' ), 'http://aws.amazon.com/' )
                    . ' ' . sprintf( __( 'The instruction is documented <a href="%1$s" target="_blank">here</a>.', 'amazon-auto-links' ), '?post_type=amazon_auto_links&page=aal_help&tab=notes#How_to_Obtain_Access_Key_and_Secret_Key' ),
            ),

            // general
            array( 
                'strSectionID'       => 'product_filters',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'         => 'general',
                'strTitle'           => __( 'Filters', 'amazon-auto-links' ),
                'strDescription'     => __( 'Set the criteria to filter fetched items.', 'amazon-auto-links' ),
            ),
            array( 
                'strSectionID'       => 'support',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'         => 'general',
                'strTitle'           => __( 'Support', 'amazon-auto-links' ),
                'strDescription'     => __( 'Help the developer!', 'amazon-auto-links' ),
            ),
            array( 
                'strSectionID'       => 'initial_support',
                'strPageSlug'        => $strPageSlug,
                'strTabSlug'         => 'support',
                'strTitle'           => __( 'Amazon Auto Links Plugin Agreements', 'amazon-auto-links' ),
                'strDescription'     => __( 'Please define how you want to support the developer.', 'amazon-auto-links' ),
            ),    
            array(  // 2.2.0+
                'strSectionID'        => 'unit_preview',
                'strPageSlug'         => $strPageSlug,
                'strTabSlug'          => 'general',
                'strTitle'            => __( 'Unit Preview', 'amazon-auto-links' ),
            ),
            array( 
                'strSectionID'        => 'query',
                'strPageSlug'         => $strPageSlug,
                'strTabSlug'          => 'general',
                'strTitle'            => __( 'Custom Query Key', 'amazon-auto-links' ),
            ),
            array( 
                'strSectionID'        => 'cache',
                'strPageSlug'         => $strPageSlug,
                'strTabSlug'          => 'general',
                'strTitle'            => __( 'Caches', 'amazon-auto-links' ),
            ),
            // misc
            array(
                'strSectionID'        => 'capabilities',
                'strCapability'       => 'manage_options',
                'strPageSlug'         => $strPageSlug,
                'strTabSlug'          => 'misc',
                'strTitle'            => __( 'Access Rights', 'amazon-auto-links' ),
                'strDescription'      => __( 'Set the access levels to the plugin setting pages.', 'amazon-auto-links' ),
            ),        
            array(
                'strSectionID'        => 'form_options',
                'strCapability'       => 'manage_options',
                'strPageSlug'         => $strPageSlug,
                'strTabSlug'          => 'misc',
                'strTitle'            => __( 'Form', 'amazon-auto-links' ),
                'strDescription'      => __( 'Set allowed HTML tags etc..', 'amazon-auto-links' ),
            ),                    
            array(
                'strSectionID'        => 'debug',
                'strCapability'       => 'manage_options',
                'strPageSlug'         => $strPageSlug,
                'strTabSlug'          => 'misc',
                'strTitle'            => __( 'Debug', 'amazon-auto-links' ),
                'strDescription'      => __( 'For developers who need to see the internal workings of the plugin.', 'amazon-auto-links' ),
            ),
    
            // reset
            array(
                'strSectionID'        => 'reset_settings',
                'strPageSlug'         => $strPageSlug,
                'strCapability'       => 'manage_options',
                'strTabSlug'          => 'reset',
                'strTitle'            => __( 'Reset Settings', 'amazon-auto-links' ),
                'strDescription'      => __( 'If you get broken options, initialize them by performing reset.', 'amazon-auto-links' ),
            ),
            array(
                'strSectionID'        => 'caches',
                'strPageSlug'         => $strPageSlug,
                'strTabSlug'          => 'reset',
                'strTitle'            => __( 'Caches', 'amazon-auto-links' ),
                'strDescription'      => __( 'If you need to refresh the product link outputs, clear the cashes.', 'amazon-auto-links' ),
            )    
        );
    
    }

    /**
     * Returns the field array with the given section ID.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     */    
    public function getFields( $strSectionID='', $strPrefix='' ) {
        
        return array_merge( 
            $this->getFieldsOfAuthenticationKeys(),
            $this->getFieldsOfBlackWhiteList(),
            $this->getFieldsOfSupport( 'support' ),
            $this->getFieldsOfSupport( 'initial_support', 'initial_' ),
            $this->getFieldsOfCustomPreviewPostType(),
            $this->getFieldsOfQuery(),
            $this->getFieldsOfCacheSettings(),
            $this->getFieldsOfDebug(),
            $this->getFieldsOfCapabilities(),
            $this->getFieldsOfFormOptions(),
            $this->getFieldsOfResetSettings(),
            $this->getFieldsOfCaches()        
        );

    }
    
    protected function getFieldsOfAuthenticationKeys() {
        return array(
            array(
                'strFieldID' => 'access_key',
                'strSectionID' => 'authentication_keys',
                'strTitle' => __( 'Access Key ID', 'amazon-auto-links' ),
                'strDescription' => __( 'The public key consisting of 20 alphabetic characters.', 'amazon-auto-links' )
                    . ' e.g.<code>022QF06E7MXBSH9DHM02</code>',
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 20 : 40,
            ),
            array(
                'strFieldID' => 'access_key_secret',
                'strSectionID' => 'authentication_keys',
                'strTitle' => __( 'Secret Access Key', 'amazon-auto-links' ),
                'strDescription' => __( 'The private key consisting of 40 alphabetic characters.', 'amazon-auto-links' )
                    . ' e.g.<code>kWcrlUX5JEDGM/LtmEENI/aVmYvHNif5zB+d9+ct</code>',
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 40 : 60,
            ),
            array(  // single button
                'strFieldID' => 'submit_authentication',
                'strSectionID' => 'authentication_keys',
                'strType' => 'submit',
                'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                    . "<div class='right-button'>",
                'strAfterField' => "</div>",
                'vLabelMinWidth' => 0,
                'vLabel' => __( 'Authenticate', 'amazon-auto-links' ),
                'vClassAttribute' => 'button button-primary',
            )        
        );
    }
    protected function getFieldsOfBlackWhiteList() {
        return array(
            array(
                'strFieldID' => 'white_list',
                'strSectionID' => 'product_filters',
                'strTitle' => __( 'White List', 'amazon-auto-links' ),
                'strDescription' => __( 'Enter characters separated by commas.', 'amazon-auto-links' )
                    . ' ' . __( 'Product links that do not contain the white list items will be omitted.', 'amazon-auto-links' ),
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 60 : 80,
                'vLabel' => array(    
                    'asin' => 'ASIN',
                    'title' => __( 'Title', 'amazon-auto-links' ),
                    'description' => __( 'Description', 'amazon-auto-links' ),
                ),
                'vAfterInputTag' => array(
                    'asin' => '<br /><span class="description">e.g. <code>120530902X, BO1000000X</code></span>',
                    'title' => '<br /><span class="description">e.g. <code>Laptop</code></span>',
                    'description' => '<br /><span class="description">e.g. <code>$100</code></span>',
                ),
            ),
            array(
                'strFieldID' => 'black_list',
                'strSectionID' => 'product_filters',
                'strTitle' => __( 'Black List', 'amazon-auto-links' ),
                'strDescription' => __( 'Enter characters separated by commas.', 'amazon-auto-links' )
                    . ' ' . __( 'Product links that contain the black list items will be omitted.', 'amazon-auto-links' ),
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 60 : 80,
                'vLabel' => array(    
                    'asin' => 'ASIN',
                    'title' => __( 'Title', 'amazon-auto-links' ),
                    'description' => __( 'Description', 'amazon-auto-links' ),
                ),
                'vAfterInputTag' => array(
                    'asin' => '<br /><span class="description">e.g. <code>020530902X, BO0000000X</code></span>',
                    'title' => '<br /><span class="description">e.g. <code>xxx, adult</code></span>',
                    'description' => '<br /><span class="description">e.g. <code>xxx, $0.</code></span>',
                ),
            ),
            array(
                'strFieldID' => 'case_sensitive',
                'strSectionID' => 'product_filters',
                'strTitle' => __( 'Case Sensitive', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'If this is on, upper cases and lower cases of characters have to match to find the given string.', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>' . __( 'Off', 'amazon-auto-links' ) . '</code>',
                'vDefault' => AmazonAutoLinks_Option::$arrStructure_Options['aal_settings']['product_filters']['case_sensitive'],                
            ),
            array(
                'strFieldID' => 'no_duplicate',
                'strSectionID' => 'product_filters',
                'strTitle' => __( 'Prevent Duplicates', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'If this is on, the same products that are already loaded will not be displayed among different units', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>' . __( 'On', 'amazon-auto-links' ) . '</code>',
                'vDefault' => AmazonAutoLinks_Option::$arrStructure_Options['aal_settings']['product_filters']['no_duplicate'],                
            ),            
        );        
    }
    protected function getFieldsOfSupport( $strSection='support', $strPrefix='' ) {
        return array(
            array(
                'strFieldID' => $strPrefix . 'rate',
                'strSectionID' => $strSection,
                'strTitle' => __( 'Support Rate', 'amazon-auto-links' ),
                'strDescription' => __( 'The percentage that the associate ID is altered with the plugin developers', 'amazon-auto-links' ),
                'strType' => 'select',
                'vLabel' => array(    
                    30 => '30%',
                    20 => '20%',
                    10 => '10%',
                    0 => '0%',
                ),
                'vAfterInputTag' => $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['support']['rate'] == 0
                    ? "<span class='description upgrade-notice'>" . sprintf( __( 'Please consider upgrading to <a href="%1$s" target="_blank">Pro</a> to help the plugin development if you set it 0%%!', 'amazon-auto-links' ), 'http://en.michaeluno.jp/amazon-auto-links-pro/' ) . "</div>"
                    : '',
                'vDefault' => $strSection == 'support' 
                    ? AmazonAutoLinks_Option::$arrStructure_Options['aal_settings']['support']['rate']
                    : 0,    // for initial_support
            ),
            array(
                'strFieldID' => $strPrefix . 'ads',
                'strSectionID' => $strSection,
                'strTitle' => __( 'Advertisement', 'amazon-auto-links' ),
                'strDescription' => __( 'Display ads in the plugins\'s settings page', 'amazon-auto-links' ),    // syntax fixer '
                'strType' => 'radio',
                'vLabel' => array(    
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'vAfterInputTag' => ! $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['support']['ads']
                    ? "<span class='description upgrade-notice'>" . sprintf( __( 'Please consider upgrading to <a href="%1$s" target="_blank">Pro</a> to help the plugin development if you set it off!', 'amazon-auto-links' ), 'http://en.michaeluno.jp/amazon-auto-links-pro/' ) . "</span>"
                    : '',
                'vDefault' => $strSection == 'support' 
                    ? AmazonAutoLinks_Option::$arrStructure_Options['aal_settings']['support']['ads']
                    : 1, // for initial_support
                'strAfterField'  => "<input type='hidden' name='amazon_auto_links_admin[aal_settings][support][agreed]' value='"
                    . true     // this must be true as this field is shared with both the Plugin Agreement page and the general setting page.
                    . "'>",
            ),    
            array(  // single button
                'strFieldID' => $strPrefix . 'agreed',
                'strSectionID' => $strSection,
                'fIf' => ( $strSection == 'initial_support' ),
                'strType' => 'submit',
                'vRedirect' => $this->getBounceURL(),
                // 'vAfterInputTag' => "<input type='hidden' name='aal_bounce_url' value='" .  . "' />",
                'vLabelMinWidth' => 0,
                'vLabel' => __( 'Proceed', 'amazon-auto-links' ),
                'vClassAttribute' => 'button button-primary',
            )    
            // array(
                // 'strFieldID' => $strPrefix . 'review',
                // 'strSectionID' => 'support',
                // 'strTitle' => __( 'Have Reviewed', 'amazon-auto-links' ),
                // 'strDescription' => sprintf( __( 'Please review the plugin at the <a href="%1$s" target="_blank">WordPress site</a>.', 'amazon-auto-links' ), 'http://wordpress.org/support/view/plugin-reviews/amazon-auto-links?filter=5' ),
                // 'strType' => 'radio',
                // 'vLabel' => array(    
                    // 0 => __( 'No', 'amazon-auto-links' ),
                    // 1 => __( 'Yes', 'amazon-auto-links' ),
                // ),
                // 'vDefault' => AmazonAutoLinks_Option::$arrStructure_Options['aal_settings']['support']['review'],
            // )    
        );        
    }
        protected function getBounceURL() {
                
            // AmazonAutoLinks_WPUtilities::deleteTransient( 'AAL_BounceURL' );
            return AmazonAutoLinks_WPUtilities::getTransient( isset( $_GET['bounce_url'] ) ? $_GET['bounce_url'] : 'AAL_BounceURL' );
            
            // if ( ! isset( $_GET['bounce_url'] ) )
                // return;
            // $oEncode = new AmazonAutoLinks_Encrypt;
            // return $oEncode->decode( $_GET['bounce_url'] );
            
        }
    /**
     * 
     * @since       2.2.0
     * @return      array
     */
    protected function getFieldsOfCustomPreviewPostType() {
        return array(
            array(
                'strSectionID'      => 'unit_preview',
                'strFieldID'        => 'preview_post_type_slug',
                'strTitle'          => __( 'Post Type Slug', 'amazon-auto-links' ),
                'strDescription'    => __( 'Up to 20 characters with small-case alpha numeric characters.', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' )
                    . ': <code>'
                        . AmazonAutoLinks_Commons::PostTypeSlug
                    . '</code>',
                'strType'           => 'text',
                'vDefault'          => AmazonAutoLinks_Commons::PostTypeSlug,
            ),
            array(
                'strSectionID'      => 'unit_preview',
                'strType'           => 'checkbox',
                'strFieldID'        => 'visible_to_guests',
                'strTitle'          => __( 'Visibility', 'amazon-auto-links' ),                
                'vLabel'            => __( 'Visible to non-logged-in users.', 'amazon-auto-links' ),
                'vDefault'          => true,
            ),
            array(
                'strSectionID'      => 'unit_preview',
                'strType'           => 'checkbox',
                'strFieldID'        => 'searchable',
                'strTitle'          => __( 'Searchable', 'amazon-auto-links' ),                
                'vLabel'            => __( 'Possible for the WordPress search form to find the plugin preview pages.', 'amazon-auto-links' ),
                'vDefault'          => false,
            ),            
        );
 
    }
    protected function getFieldsOfQuery() {
        return array(
            array(
                'strFieldID'        => 'cloak',
                'strSectionID'      => 'query',
                'strTitle'          => __( 'Link Style Query Key', 'amazon-auto-links' ),
                'strDescription'    => __( 'Define the query parameter key for the cloaking link style.', 'amazon-auto-links' ) . '<br />'
                    . __( 'Default', 'amazon-auto-links' ) . ': <code>' . AmazonAutoLinks_Option::$arrStructure_Options['aal_settings']['query']['cloak'] . '</code>',
                'strType'           => 'text',
                'vDefault'          => AmazonAutoLinks_Option::$arrStructure_Options['aal_settings']['query']['cloak'],
            ),
            // array(  // single button
                // 'strFieldID' => 'submit_general',
                // 'strSectionID' => 'query',
                // 'strType' => 'submit',
                // 'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                    // . "<div class='right-button'>",
                // 'strAfterField' => "</div>",
                // 'vLabelMinWidth' => 0,
                // 'vLabel' => __( 'Save Changes', 'amazon-auto-links' ),
                // 'vClassAttribute' => 'button button-primary',
            // )    
        );        
    }
    protected function getFieldsOfCacheSettings() {
        return array(
            array(
                'strFieldID' => 'chaching_mode',
                'strSectionID' => 'cache',
                'strTitle' => __( 'Caching Mode', 'amazon-auto-links' ),
                'strType' => 'radio',
                'strCapability' => 'manage_options',
                'vLabel' => array(
                    'normal' => __( 'Normal', 'amazon-auto-links' ) . ' - ' . __( 'relies on WP Cron.', 'amazon-auto-links' ) . '<br />',
                    'intense' => __( 'Intense', 'amazon-auto-links' ) . ' - ' . __( 'relies on the plugin caching method.', 'amazon-auto-links' ) . '<br />',
                ),
                'strDescription' => __( 'The intense mode should only be enabled when the normal mode does not work.', 'amazon-auto-links' ),
                'vDefault' => 'normal',

            ),        
            array(  // single button
                'strFieldID' => 'submit_general',
                'strSectionID' => 'cache',
                'strType' => 'submit',
                'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                    . "<div class='right-button'>",
                'strAfterField' => "</div>",
                'vLabelMinWidth' => 0,
                'vLabel' => __( 'Save Changes', 'amazon-auto-links' ),
                'vClassAttribute' => 'button button-primary',
            )            
        );
    }
    protected function getFieldsOfCapabilities() {
        return array(
            array(
                'strFieldID' => 'setting_page_capability',
                'strSectionID' => 'capabilities',
                'strTitle' => __( 'Capability', 'amazon-auto-links' ),
                'strDescription' => __( 'Select the user role that is allowed to access the plugin setting pages.', 'amazon-auto-links' )
                    . __( 'Default', 'amazon-auto-links' ) . ': ' . __( 'Administrator', 'amazon-auto-links' ),
                'strType' => 'select',
                'strCapability' => 'manage_options',
                'vLabel' => array(                        
                    'manage_options' => __( 'Administrator', 'amazon-auto-links' ),
                    'edit_pages' => __( 'Editor', 'amazon-auto-links' ),
                    'publish_posts' => __( 'Author', 'amazon-auto-links' ),
                    'edit_posts' => __( 'Contributor', 'amazon-auto-links' ),
                    'read' => __( 'Subscriber', 'amazon-auto-links' ),
                ),
                // 'strHelp' => 'This is a test.',
            )
        );        
    }
    protected function getFieldsOfFormOptions() {
        return array(
            array(
                'strFieldID' => 'allowed_html_tags',
                'strSectionID' => 'form_options',
                'strTitle' => __( 'Allowed HTML Tags', 'amazon-auto-links' ),
                'strDescription' => __( 'Enter the allowed HTML tags for the form input, separated by commas. By default, WordPress applies a filter called KSES that strips out certain tags before the user input is saved in the database for security reasons.', 'amazon-auto-links' ) . '<br />'
                    . ' e.g. <code>noscript, style</code>',
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 60 : 80,
                'strCapability' => 'manage_options',
            )
        );        
    }
    protected function getFieldsOfDebug() {
        return array(
            array(
                'strFieldID' => 'debug_mode',
                'strSectionID' => 'debug',
                'strTitle' => __( 'Debug Mode', 'amazon-auto-links' ),
                'strType' => 'radio',
                'strCapability' => 'manage_options',
                'vLabel' => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'vDefault' => 0,
            ),
            array(  // single button
                'strFieldID' => 'submit_misc',
                'strSectionID' => 'debug',
                'strType' => 'submit',
                'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                    . "<div class='right-button'>",
                'strAfterField' => "</div>",
                'vLabelMinWidth' => 0,
                'vLabel' => __( 'Save Changes', 'amazon-auto-links' ),
                'vClassAttribute' => 'button button-primary',
            )    
        );        
    }
    protected function getFieldsOfResetSettings() {
        return array(
            array(    
                'strFieldID' => 'options_to_delete',
                'strSectionID' => 'reset_settings',
                'strTitle' => __( 'Options to Delete', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vDelimiter' => '<br />',
                'vLabel' => array(
                    'all' => __( 'All', 'amazon-auto-links' ), 
                    'general' => __( 'General options', 'amazon-auto-links' ),     
                    'template' => __( 'Template related options', 'amazon-auto-links' ),
                ),
            ),
            // array(  // single button
                // 'strFieldID' => 'submit_reset_settings',
                // 'strSectionID' => 'reset_settings',
                // 'strType' => 'submit',
                // 'strBeforeField' => "<div class='right-button'>",
                // 'strAfterField' => "</div>",
                // 'vLabelMinWidth' => 0,
                // 'vLabel' => __( 'Perform', 'amazon-auto-links' ),
                // 'vClassAttribute' => 'button button-primary',
            // ),
        );        
    }
    protected function getFieldsOfCaches() {
        return array(
            array(    
                'strFieldID' => 'clear_caches',
                'strSectionID' => 'caches',
                'strTitle' => __( 'Clear Caches', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vLabel' => __( 'Clear caches of product links.', 'amazon-auto-links' ),
            ),
            array(  // single button
                'strFieldID' => 'submit_reset_settings',
                'strSectionID' => 'caches',
                'strType' => 'submit',
                'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                    . "<div class='right-button'>",
                'strAfterField' => "</div>",
                'vLabelMinWidth' => 0,
                'vLabel' => __( 'Perform', 'amazon-auto-links' ),
                'vClassAttribute' => 'button button-primary',
            )    
        );        
    }    
    
    
}