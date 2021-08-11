<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Handles plugin options.
 * 
 * @since       3
 */
class AmazonAutoLinks_Option extends AmazonAutoLinks_Option_Base {

    /**
     * Stores instances by option key.
     * 
     * @since       3
     */
    static public $aInstances = array(
        // key => object
    );
        
    /**
     * Stores default values.
     */
    public $aDefault = array(
    
        'capabilities' => array(
            'setting_page_capability' => 'manage_options',
            'create_units'            => 'edit_pages',
        ),
        'debug' => array(
            'debug_mode' => 0,
        ),

        // [4.6.19+]
        'security' => array(
            'allowed_html' => array(
                array(
                    'tags'  => 'noscript',
                    'attributes' => 'data-*',
                ),
            ),
            'allowed_inline_css_properties' => 'min-height, max-height, min-width, max-width, display, float',
        ),

        'form_options' => array(    // @deprecated 4.6.19 Migrated to the `security` element
            'allowed_html_tags'             => null,
            'allowed_attributes'            => null,
            'allowed_inline_css_properties' => null,
        ),

        'product_filters'       => array(
            'black_list'     => array(
                'asin'        => '',
                'title'       => '',
                'description' => '',
            ),
            'white_list'        => array(
                'asin'        => '',
                'title'       => '',
                'description' => '',
            ),
            'case_sensitive' => 0,
            'no_duplicate'   => 0,    // in 2.0.5.1 changed to 0 from 1.
        ),
        'support' => array(
            'rate'   => 0,            // asked for the first load of the plugin admin page
            'ads'    => false,        // asked for the first load of the plugin admin page
            'review' => 0,            // not implemented yet
            'agreed' => false,        // hidden
        ),
        'cache'    =>    array(

            // 'caching_method'                   => 'database', // 3.12.0 Not implemented yet
            'caching_mode'                     => 'normal',
            
            // 3.4.0+
            'expired_cache_removal_interval'   => array(
                'size'  => 7,
                'unit'  => 86400,   // either 3600, 86400, or 604800
            ),

            // 3.8.12+
            'cache_removal_event_last_run_time' => '',

            // 3.7.3+
            'table_size' => array(
                'products' => '',   // (string|integer) blank string for unlimited. For integer values, mega bytes.
                'requests' => '',   // (string|integer) blank string for unlimited. For integer values, mega bytes.
            ),

            // 4.0.0+
            'compress'  => false, // (boolean) whether to compress caches
        ),
        'query' => array(
            'cloak' => 'productlink'
        ),
        'authentication_keys' => array(
            'access_key'                => '',     // public key
            'access_key_secret'         => '',     // private key
            'api_authentication_status' => false,
            'associates_test_tag'       => '',     // 3.6.7+
            'server_locale'             => null,   // [3.4.4] Added [4.5.0] Removed the default value of 'US' as the value must be checked when checking whether the API keys are set.
        ),            
        // Hidden options
        'template' => array(
            'max_column' => 1,
        ),
        'import_v1_options' => array(
            'dismiss' => false,
        ),
        // 2.2.0+
        'unit_preview'      => array(
            'preview_post_type_label' => AmazonAutoLinks_Registry::NAME,
            'preview_post_type_slug'  => '',
            'visible_to_guests'       => true,
            'searchable'              => false,
        ),
        
        // 3+
        'reset_settings'    => array(
            'reset_on_uninstall'    => false,
        ),
        
        // 3.1.0+
        'external_scripts'  => array(
            'impression_counter_script' => false,
        ),
        
        // 3+ Changed the name from `arrTemplates`.
        // stores information of active templates.   
        // 'templates' => array(),    
    
        // 3.3.0+
        'feed'  => array(
            'use_description_tag_for_rss_product_content' => false,
        ),

        // 3.8.0
        'convert_links' => array(
            'enabled'               => false,
            'where'                 => array(
                'the_content'  => 1,
                'comment_text' => 1,
            ),
            'filter_hooks'   => '',
        ),

        // 3.9.0+
        'widget'    => array(
            'register'    => array(
                'contextual'    => true,
                'by_unit'       => true,
            )
        ),

        // 3.11.0+
        'aalb'      => array(
            'support' => 0,
            'template_conversion_map' => array(),
        ),

        // 4.0.0
        'custom_oembed' => array(
            'enabled'               => true,
            'use_iframe'            => true,
            'external_provider'     => '',
            'override_associates_id_of_url' => false,
            'template_id'           => null,            // (string) will be set via the UI
        ),

        // [4.4.0]
        'paapi_request_counts' => array(
            'enable'           => true,
            'retention_period' => array(
                'size'      => 7,
                'unit'      => 86400,
            ),
        ),

        // 4.6.0+
        'geotargeting'         => array(
            'enable'                => false,
            'non_plugin_links'      => false,
            'api_providers'         => array(
                'cloudflare'     => true,
                'db-ip.com'      => true,
                'geoiplookup.io' => true,
                'geoplugin.net'  => true,
            ),
        ),

        // 4.6.6+ - stores timestamps
        'user_actions' => array(
            'last_saved'  => 0,
            'first_saved' => 0,
        ),

        // 3.4.0+
        'unit_default'  => array(
            'unit_type'                     => null,
            'unit_title'                    => null,
            'cache_duration'                => 86400,  // 60*60*24
            
            'count'                         => 10,
            'column'                        => 4,
            'country'                       => 'US',
            'associate_id'                  => null,
            'image_size'                    => 160,      
            'ref_nosim'                     => false,
            'title_length'                  => -1,
            'description_length'            => 250,     // 3.3.0+  Moved from the search unit types.
            'link_style'                    => 1,
            'credit_link'                   => 0,   // 1 or 0   // 3.5.3+ disabled by default
            'credit_link_type'              => 0,   // 3.2.2+ 0: normal, 1: image

            // @todo not sure about this
            'title'                 => '',      // won't be used to fetch links. Used to create a unit.
            
            'template'              => '',      // the template name - if multiple templates with a same name are registered, the first found item will be used.
            'template_id'           => null,    // the template ID: md5( dir path )
            'template_path'         => '',      // the template can be specified by the template path. If this is set, the 'template' key won't take effect.
            
            'is_preview'            => false,   // for the search unit, true won't be used but just for the code consistency. 
                        
            // stores labels associated with the units (the plugin custom taxonomy). Used by the RSS2 template.
            '_labels'               => array(),    
            
            // this is for fetching by label. AND, IN, NOT IN can be used
            'operator'              => 'AND',
            
            // 3+
            'subimage_size'                 => 100,
            'subimage_max_count'            => 5,
            'customer_review_max_count'     => 2,
            'customer_review_include_extra' => false,
            
            'button_id'                     => null,    // a button (post) id will be assigned
            'button_type'                   => 1,       // 3.1.0 0: normal link, 1: add to cart
            'button_label'                  => '',      // 4.3.0
            'override_button_label'         => false,   // 4.3.0

            'product_filters'               => array(
                'white_list'    => array(
                    'asin'          => '',
                    'title'         => '',
                    'description'   => '',
                ),
                'black_list'    => array(
                    'asin'          => '',
                    'title'         => '',
                    'description'   => '',
                ),
                'case_sensitive'    => 0,   // or 1
                'no_duplicate'      => 0,   // or 1
            ),
            // 3.1.0+
            'skip_no_image'               => false,
           
           
            'width'         => null,
            'width_unit'    => '%',
            'height'        => null,
            'height_unit'   => 'px',

            /**
             * Whether to show an error message.
             *
             * When an error occurs, the error message will be shown and the template is not applied.
             * Currently, no unit option meta box input field for this option.
             *
             * 0: completely hide the error
             * 1: show the error
             * 2: show the error as an HTML comment
             * @since   4.1.0   Changed the type to integer from boolean
             * @since   4.4.0   Changed it to 1 from 2.
             */
            'show_errors'   => 1,

            // 3.2.0+
            'show_now_retrieving_message'   => true,
     
            // 3.2.1+
            '_allowed_ASINs' => array(),
            
            // 3.3.0+
            'highest_content_heading_tag_level' => 5,
            
            // 3.3.0+ (boolean) Whether to fetch similar products. The background routine of retrieving similar products need to set this `false`.
            '_search_similar_products'      => true,        

            // @deprecated 3.9.0    PA-API 5 does not support similarity look-up
            'similar_product_image_size'    => 100,
            'similar_product_max_count'     => 10,
            
            'description_suffix'            => 'read more',

            // 3.5.0+
            '_force_cache_renewal'          => false,

            '_no_pending_items'             => false,
            '_filter_adult_products'        => false,   // 3.9.0+
            '_filter_by_rating'             => array(
                'enabled'   => false,
                'case'      => 'above',
                'amount'    => 0,
            ),
            '_filter_by_discount_rate'      => array(
                'enabled'   => false,
                'case'      => 'above',
                'amount'    => 0,
            ),

            // unknown+
            '_no_outer_container'           => false,

            // 3.6.0+
            'load_with_javascript'          => false,
            '_now_loading_text'             => 'Now loading...',
            /// for widget outputs - helps the output function know what to do with JavaScript loading
            '_widget_option_name'           => null,
            '_widget_number'                => null,

            // 3.7.0+ These are not options that change the output behavior but post meta to store relevant information based on the result.
            // 3.7.7  The default value became an empty string and become `normal` for loaded units without an error.
            '_error'                        => null,

            // 3.7.5+
            '_custom_url_query_string'      => array(),

            // 3.10.0
            '_filter_by_prime_eligibility'  => false,
            '_filter_by_free_shipping'      => false,
            '_filter_by_fba'                => false,
            'preferred_currency'            => null,
            'language'                      => null,

            // 4.0.0
            'product_title'                 => null,    // 4.0.0    Overrides product titles.
            'output_formats'                => array(), // (array) holds item_format, image_format, title_format, unit format for each active template

            // 4.3.0
            'custom_text'                   => '',
        )
        
    );
       
    /**
     * Returns the formatted options array.
     * @remark  `$sOptionKey` property must be set before calling this method.
     * @return  array
     * @since   4.6.19
     */
    protected function _getFormattedOptions() {

        $_aRawOptions = $this->getRawOptions();

        $this->aDefault[ 'unit_default' ][ 'description_suffix' ] = __( 'read more', 'amazon-auto-links' );
        $this->aDefault[ 'unit_default' ][ 'button_label' ]       = __( 'Buy Now', 'amazon-auto-links' );   // 4.3.0

        // Handle the `security` default values
        $this->aDefault[ 'security' ] = $this->___getDefaultSecurityOptions( $this->aDefault[ 'security' ], $_aRawOptions );

        $_aFormatted  = $this->uniteArrays( $_aRawOptions, $this->aDefault );   // same as parent::_getFormattedOptions();

        // After `this->aOptions` is created. Set the unit default Item Format option.
        $_aFormatted[ 'unit_default' ] = $_aFormatted[ 'unit_default' ]
            + $this->getDefaultOutputFormats();  // needs to check API is connected
        if ( ! $_aFormatted[ 'unit_default' ][ 'override_button_label' ] ) {
            $_aFormatted[ 'unit_default' ][ 'button_label' ] = __( 'Buy Now', 'amazon-auto-links' );
        }

        return $_aFormatted;
    }
        /**
         * @remark This is for backward compatibility with v4.6.18 or below.
         * @param  array $aSecurity
         * @param  array $aRawOptions
         * @return array
         * @since  4.6.19
         */
        private function ___getDefaultSecurityOptions( array $aSecurity, array $aRawOptions ) {
            // If the user has not saved options before v4.6.19, do nothing.
            $_aFormSection      = $this->getElementAsArray( $aRawOptions, array( 'form_options' ) );
            $_sAllowedHTMLTags  = $this->getElement( $_aFormSection, array( 'allowed_html_tags' ) );
            if ( ! $_sAllowedHTMLTags ) {
                return $aSecurity;
            }
            // If the user saved the security settings, do nothing.
            $_aSavedSecurity = $this->getElementAsArray( $aRawOptions, array( 'security', 'allowed_html' ) );
            if ( ! empty( $_aSavedSecurity ) ) {
                return $aSecurity;
            }
            // At this point, the user has previous `form_options` values and hasn't saved the `security` settings yet.
            // In that case, form a default value by merging with the previous `form_options`.
            $aSecurity[ 'allowed_inline_css_properties' ] = $this->getElement(
                $_aFormSection,
                array( 'allowed_inline_css_properties' ),
                $aSecurity[ 'allowed_inline_css_properties' ]
            );
            array_unshift(
                $aSecurity[ 'allowed_html' ],
                array(
                    'tags'       => ( string ) $_sAllowedHTMLTags,
                    'attributes' => $this->getElement( $_aFormSection, array( 'allowed_attributes' ) ),
                )
            );
            return $aSecurity;
        }
       
    /**
     * Returns the instance of the class.
     * 
     * This is to ensure only one instance exists.
     * 
     * @since      3
     * @return     AmazonAutoLinks_Option
     * @param      string $sOptionKey
     * @filter     aal_filter_option_class_name
     */
    static public function getInstance( $sOptionKey='' ) {
        
        $sOptionKey = $sOptionKey 
            ? $sOptionKey
            : AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ];
        
        if ( isset( self::$aInstances[ $sOptionKey ] ) ) {
            return self::$aInstances[ $sOptionKey ];
        }
        $_sClassName = apply_filters( 
            AmazonAutoLinks_Registry::HOOK_SLUG . '_filter_option_class_name',
            __CLASS__ 
        );
        self::$aInstances[ $sOptionKey ] = new $_sClassName( $sOptionKey );
        return self::$aInstances[ $sOptionKey ];
        
    }         
            
            
    /**
     * 
     * @remark      The array contains the concatenation character(.) 
     * so it cannot be done in the declaration.
     * @since       unknown
     * @since       3.4.0       Moved from `AmazonAutoLinks_UnitOption_Base`. Removed the static scope.
     * @since       4.3.0       Renamed from `getDefaultItemFormat()`.
     * @return      array
     */
    public function getDefaultOutputFormats() {

        $_bAPIConnected = ( boolean ) $this->isPAAPIConnectedByAnyLocale();
        return array(
            'unit_format' => '%text%' . PHP_EOL
                . '%products%',  // 4.3.0
            'item_format' => $_bAPIConnected
                ? $this->getDefaultItemFormatConnected()
                : $this->getDefaultItemFormatDisconnected(),
            'image_format' => '<div class="amazon-product-thumbnail" style="max-width:%image_size%px; max-height:%image_size%px; width:%image_size%px;">' . PHP_EOL
                . '    <a href="%href%" title="%title_text%: %description_text%" rel="nofollow noopener" target="_blank">' . PHP_EOL
                . '        <img src="%src%" alt="%description_text%" style="max-height:%image_size%px;" />' . PHP_EOL
                . '    </a>' . PHP_EOL
                . '</div>',
            'title_format' => '<h5 class="amazon-product-title">' . PHP_EOL
                . '<a href="%href%" title="%title_text%: %description_text%" rel="nofollow noopener" target="_blank">%title_text%</a>' . PHP_EOL
                . '</h5>',
        );
        
    }

    /**
     * @since   3.8.0
     * @return  string
     */
    public function getDefaultItemFormatConnected() {
        return '<div class="amazon-auto-links-product">' . PHP_EOL
            . '    <div class="amazon-auto-links-product-image" style="min-width: %image_size%px;">' . PHP_EOL
            . '        %image%' . PHP_EOL
            . '        %image_set%' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '    <div class="amazon-auto-links-product-body">' . PHP_EOL
            . '        %title%' . PHP_EOL
            . '        %rating% %prime% %price%' . PHP_EOL
            . '        %description%' . PHP_EOL
            . '        %disclaimer%' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '</div>';
    }
    /**
     * @since   3.8.0
     * @return  string
     */
    public function getDefaultItemFormatDisconnected() {
        return '<div class="amazon-auto-links-product">' . PHP_EOL
            . '    <div class="amazon-auto-links-product-image" style="min-width: %image_size%px;">' . PHP_EOL
            . '        %image%' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '    <div class="amazon-auto-links-product-body">' . PHP_EOL
            . '        %title%' . PHP_EOL
            . '        %rating% %prime% %price%' . PHP_EOL
            . '        %description%' . PHP_EOL
            . '        %disclaimer%' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '</div>';
    }

    /**
     * @since    4.2.0
     * @return   boolean
     */
    public function isAdvancedProxyOptionSupported() {
        return false;
    }

    /**
     * @return false
     * @since  4.5.0
     */
    public function isAdvancedWebPageDumperOptionSupported() {
        return false;
    }

    /**
     * @return  boolean
     * @param   integer|null $iNumberOfUnits
     */
    public function isUnitLimitReached( $iNumberOfUnits=null ) {
        
        if ( ! isset( $iNumberOfUnits ) ) {
            $_oNumberOfUnits = AmazonAutoLinks_WPUtility::countPosts( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );
            $iNumberOfUnits  = $_oNumberOfUnits->publish 
                + $_oNumberOfUnits->private 
                + $_oNumberOfUnits->trash;
        } 
        return ( boolean ) ( $iNumberOfUnits >= 3 );
        
    }    
    public function getRemainedAllowedUnits( $iNumberOfUnits=null ) {
        
        if ( ! isset( $iNumberOfUnits ) ) {
            $_oNumberOfUnits   = AmazonAutoLinks_WPUtility::countPosts( 
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            );
            $iNumberOfUnits   = $_oNumberOfUnits->publish 
                + $_oNumberOfUnits->private 
                + $_oNumberOfUnits->trash;
        } 
        
        return 3 - $iNumberOfUnits;
        
    }
    public function getMaximumNumberOfCategories() {
        return 3;
    }
    public function isReachedCategoryLimit( $iNumberOfCategories ) {
        return ( boolean ) ( $iNumberOfCategories >= 3 );
    }    
    public function getMaximumProductLinkCount() {
        return 10;
    }    
    public function getMaxSupportedColumnNumber(){
        return apply_filters( 
            'aal_filter_max_column_number', 
            $this->aOptions[ 'template' ][ 'max_column' ]
        );                    
    }
    
    public function isAdvancedAllowed() {
        return false;
    }
    public function isAdvancedContextualAllowed() {
        return false;
    }
    
    public function canExport() {
        return false;
    }
    public function isSupported() {
        return false;
    }

    /**
     * @return false
     * @since  4.4.0
     */
    public function isPAAPIRequestCountChartDateRangeSupported() {
        return false;
    }
    
    /**
     * @since       3.3.0
     * @return      boolean
     */
    public function canCloneUnits() {
        return false;
    }

    /**
     * @since       3.7.5
     * @return      boolean
     */
    public function canAddQueryStringToProductLinks() {
        return false;
    }
    
    /**
     * Checks whether the API keys are set and it has been verified.
     * @since       3
     * @deprecated  4.5.0       Use `getPAAPIStatus()`.
     * @see         getPAAPIStatus()
     * @return      boolean     true if connected; otherwise false.
     * @todo There are sill lines using this method.
     */
    public function isAPIConnected() {
        // @deprecated 4.5.0
        // return ( boolean ) $this->get( 'authentication_keys', 'api_authentication_status' );
        return true === $this->isPAAPIConnectedByAnyLocale();
    }

    /**
     * Checks whether the API keys are set.
     *
     * This is not checking the connectivity as connectivity can vary even the keys are set
     * such as when too many requests are made.
     *
     * @param   string      $sLocale        The locale to check. If this value is given, the method checks if the PA-API keys are set for this locale
     * and returns false if the keys are not for that locale.
     * @return  boolean
     * @since   3.9.2
     * @since   4.0.1       Added the `$sLocale` parameter.
     * @deprecated 4.5.0    Use
     * @see isPAAPIKeySet()
     */
    public function isAPIKeySet( $sLocale='' ) {
        return $this->isPAAPIKeySet( $sLocale );
    }
    
    /**
     * Checks whether the plugin debug mode is on.
     * @return      boolean
     */
    public function isDebug() {
        return ( boolean ) $this->get( 'debug', 'debug_mode' );
    }
    
    /**
     * 
     * @since       2.2.0
     * @return      boolean
     */
    public function isCustomPreviewPostTypeSet()  {
        
        $_sPreviewPostTypeSlug = $this->get( 'unit_preview', 'preview_post_type_slug' );
        if ( ! $_sPreviewPostTypeSlug ) {
            return false;
        }
        return AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] !== $_sPreviewPostTypeSlug;
        
    }    
    
    /**
     * 
     * @since       2.2.0
     * @return      boolean
     */
    public function isPreviewVisible() {
        
        if ( $this->get( 'unit_preview', 'visible_to_guests' ) ) {
            return true;
        }
        return ( boolean ) is_user_logged_in();
        
    }

    /**
     * @return bool
     * @since   3.5.0
     */
    public function isAdvancedProductFiltersAllowed() {
        return false;
    }

    /**
     * @param  string $sLocale
     * @return string
     * @since  4.5.0
     */
    public function getAssociateID( $sLocale ) {

        $sLocale        = strtoupper( $sLocale );
        $_nsAssociateID = $this->get( array( 'associates', $sLocale, 'associate_id' ) );
        if ( ! empty( $_nsAssociateID ) ) {
            return trim( $_nsAssociateID );
        }
        // For backward compatibility with below 4.5.0.
        if ( $this->get( array( 'unit_default', 'country' ), '' ) === $sLocale ) {
            $_sUnitDefaultAssociateID = $this->get( array( 'unit_default', 'associate_id' ), '' );
            if ( $_sUnitDefaultAssociateID ) {
                return trim( $_sUnitDefaultAssociateID );
            }
        }
        if ( $this->get( array( 'authentication_keys', 'server_locale' ), '' ) === $sLocale ) {
            return trim( ( string ) $this->get( array( 'authentication_keys', 'associates_test_tag' ) ) );
        }
        return trim( $this->get( array( 'custom_oembed', 'associates_ids', $sLocale ), '' ) );

    }
    /**
     * @param  string  $sLocale
     * @return boolean|null null: untested. true: connected, false: disconnected.
     * @since  4.5.0
     */
    public function getPAAPIStatus( $sLocale='' ) {

        $sLocale = empty( $sLocale ) ? $this->getMainLocale() : strtoupper( $sLocale );

        /**
         * Possible values:
         *  - null - the user hasn't saved the settings
         *  - '' (empty string)   the user saved the settings but did not fill the options of this locale
         *  - 1 (string number) - connected
         *  - 0 (string number) - disconnected (error occurred in tests)
         */
        $_bnStatus = $this->get( array( 'associates', $sLocale, 'paapi', 'status' ) );
        if ( strlen( $_bnStatus ) ) {
            return ( boolean ) $_bnStatus;
        }
        // For backward-compatibility with below 4.5.0
        $_sLocale  = $this->get( array( 'authentication_keys', 'server_locale' ) );
        if ( $_sLocale !== $sLocale ) {
            return null;
        }
        $_bnStatus = $this->get(
            array( 'authentication_keys', 'api_authentication_status' )
        );
        return null === $_bnStatus ? null : ( boolean ) $_bnStatus;

    }

    /**
     * Checks whether any locale connects with PA-API.
     * Unlike getPAAPIStatus() that check only the main or given locale, this returns true when any of the locale is connected to PA-API
     * @return false
     * @since  4.5.0
     */
    public function isPAAPIConnectedByAnyLocale() {
        $_bnConnected = $this->getObjectCache( __METHOD__ );
        if ( isset( $_bnConnected ) ) {
            return $_bnConnected;
        }

        $_bnConnected = $this->getPAAPIStatus();
        if ( $_bnConnected ) {
            $this->setObjectCache( __METHOD__, true );
            return true;
        }

        $_bConnected  = false;
        foreach( $this->getAsArray( $this->get( array( 'associates' ) ) ) as $_sLocale => $_aLocale ) {
            if ( $this->getElement( $_aLocale, array( 'paapi', 'status' ) ) ) {
                $_bConnected = true;
                break;
            }
        }
        $this->setObjectCache( __METHOD__, $_bConnected );
        return $_bConnected;
    }

    /**
     * @param  string $sLocale
     * @return string
     * @since  4.5.0
     */
    public function getPAAPIAccessKey( $sLocale ) {
        $_nsAccessKey = $this->get( array( 'associates', $sLocale, 'paapi', 'access_key' ) );
        if ( null !== $_nsAccessKey ) {
            return ( string ) $_nsAccessKey;
        }
        return $sLocale === $this->get( array( 'authentication_keys', 'server_locale' ), '' )
            ? ( string ) $this->get( array( 'authentication_keys', 'access_key' ), '' )
            : '';
    }
    /**
     * @param  string $sLocale
     * @return string
     * @since  4.5.0
     */
    public function getPAAPISecretKey( $sLocale ) {
        $_nsAccessKey = $this->get( array( 'associates', $sLocale, 'paapi', 'secret_key' ) );
        if ( null !== $_nsAccessKey ) {
            return ( string ) $_nsAccessKey;
        }
        return $sLocale === $this->get( array( 'authentication_keys', 'server_locale' ), '' )
            ? ( string ) $this->get( array( 'authentication_keys', 'access_key_secret' ), '' )
            : '';
    }

    /**
     * @return string The locale code consisting of the two characters.
     * @since  4.5.0
     */
    public function getMainLocale() {
        $_nsLocale = $this->get( array( 'associates', 'locale' ) );
        if ( null !== $_nsLocale ) {
            return strtoupper( $_nsLocale );
        }
        // For backward compatibility with below 4.5.0.
        $_sTestLocale = $this->get( array( 'authentication_keys', 'server_locale' ) );
        if ( ! empty( $_sTestLocale ) ) {
            return strtoupper( $_sTestLocale );
        }
        return strtoupper( $this->get( array( 'unit_default', 'country' ), 'US' ) );
    }

    /**
     * @param  string  $sLocale
     * @return boolean
     * @since  4.5.0
     */
    public function isPAAPIKeySet( $sLocale='' ) {

        $_sLocale = empty( $sLocale ) ? $this->getMainLocale() : $sLocale;
        $_aPAAPI  = $this->getAsArray( $this->get( array( 'associates', $_sLocale, 'paapi' ) ) );
        if ( $this->getElement( $_aPAAPI, array( 'access_key' ) ) && $this->getElement( $_aPAAPI, array( 'secret_key' ) ) ) {
            return true;
        }

        // For backward compatibility with below 4.5.0.

        $_sPublicKey = $this->get( 'authentication_keys', 'access_key' );
        $_sSecretKey = $this->get( 'authentication_keys', 'access_key_secret' );
        $_bKeysSet   = ( boolean ) ( $_sPublicKey && $_sSecretKey );

        if ( ! $sLocale ) {
            return $_bKeysSet;
        }

        // At this point, the locale is specified.
        $_sStoredLocale = strtoupper( ( string ) $this->get( 'authentication_keys', 'server_locale' ) );
        $sLocale        = strtoupper( ( string ) $sLocale );
        if ( $sLocale !== $_sStoredLocale ) {
            return false;
        }
        return $_bKeysSet;

    }

}