<?php
/**
 * Imports v1 options.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

final class AmazonAutoLinks_ImportV1Options {

    function __construct() {
        $this->oOption = $GLOBALS['oAmazonAutoLinks_Option'];
    }
    
    /**
     * Imports v1 general options
     * 
     * v1 General Option Array Structure
        [general] => Array (
            [supportrate] => 0
            [blacklist] => 
            [blacklist_title] => 
            [blacklist_description] => 
            [donate] => 0
            [cloakquery] => productlink
            [prefetch] => 0
            [enablelog] => 0
            [license] => 
            [license_feedapi] => 
            [capability] => manage_options
            [cleanoptions] => 0
        )
        
        v2 General Option array structure
        'aal_settings' => array(
            'capabilities' => array(
                'setting_page_capability' => 'manage_options',
            ),        
            'product_filters' => array(
                'black_list' => array(
                    'asin' => '',
                    'title' => '',
                    'description' => '',
                ),
            ),
            'support' => array(
                'rate' => 0,            // asked for the first load of the plugin admin page
            ),
            'query' => array(
                'cloak' => 'productlink'
            ),
            ...
        ),        
        
     * Example: $oImportV1Options->importGeneralSettings( $arrV1Options['general'] );
     */
    public function importGeneralSettings( $arrGeneral ) {
        
        $this->oOption->arrOptions[ AmazonAutoLinks_Commons::PageSettingsSlug ]['support']['rate'] = $arrGeneral['supportrate'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Commons::PageSettingsSlug ]['product_filters']['black_list']['asin'] = $arrGeneral['blacklist'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Commons::PageSettingsSlug ]['product_filters']['black_list']['title'] = $arrGeneral['blacklist_title'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Commons::PageSettingsSlug ]['product_filters']['black_list']['description'] = $arrGeneral['blacklist_description'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Commons::PageSettingsSlug ]['query']['cloak'] = $arrGeneral['cloakquery'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Commons::PageSettingsSlug ]['capabilities']['setting_page_capability'] = $arrGeneral['capability'];        
        $this->oOption->arrOptions[ AmazonAutoLinks_Commons::PageSettingsSlug ][ AmazonAutoLinks_Commons::SectionID_License ][ AmazonAutoLinks_Commons::FieldID_LicenseKey ] = isset( $arrGeneral['license'] ) ? $arrGeneral['license'] : '';
        $this->oOption->save();
        
    }
            
    /**
     * Imports v1 units.
        [526753c700367] => Array (
            [id] => 526753c700367
            [unitlabel] => US01
            [country] => US
            [associateid] => miunosoft-20
            [containerformat] => <div class="amazon-auto-links">%items%</div>
            [itemformat] => <a href="%link%" title="%title%: %textdescription%" rel="nofollow">%img%</a><h5><a href="%link%" title="%title%: %textdescription%" rel="nofollow">%title%</a></h5><p>%htmldescription%</p>
            [imgformat] => <img src="%imgurl%" alt="%textdescription%" />
            [imagesize] => 160
            [sortorder] => random
            [feedtypes] => Array ( 
                [bestsellers] => 1
                [hotnewreleases] => 0
                [moverandshakers] => 0
                [toprated] => 0
                [mostwishedfor] => 0
                [giftideas] => 0
            )
            [cacheexpiration] => 43200
            [numitems] => 10
            [nosim] => 0
            [mblang] => en
            [countryurl] => http://www.amazon.com/gp/bestsellers/
            [insert] => Array (
                [postabove_static] => 0
                [postbelow_static] => 0
                [postabove] => 0
                [postbelow] => 0
                [excerptabove] => 0
                [excerptbelow] => 0
                [feedabove] => 0
                [feedbelow] => 0
                [feedexcerptabove] => 0
                [feedexcerptbelow] => 0
            )

            [modifieddate] => 
            [feedurls] => 
            [titlelength] => -1
            [linkstyle] => 1
            [credit] => 1
            [urlcloak] => 0
            [disableonhome] => 0
            [poststobedisabled] => 
            [categories] => Array (
                [US > Beauty] => Array (
                        [feedurl] => http://www.amazon.com/gp/rss/bestsellers/beauty
                        [pageurl] => http://www.amazon.com/Best-Sellers-Beauty/zgbs/beauty/
                )
            )
            [blacklist_categories] => Array ()
            [numberofcolumns] => 5
            [keeprawtitle] => 0
            [titlenumbering] => 0
            [keep_raw_title] => Array (
                [keeprawtitle] => 
                [titlenumbering] => 
            )
            [tag] => 
            [tab100_submitted] => 1
            [prior_unitlabel] => 
            [proceedbutton] => Proceed
            [adtypes] => Array (
                [bestsellers] => Array (
                    [check] => 1
                    [slug] => bestsellers
                )
                [hotnewreleases] => Array (
                    [check] => 0
                    [slug] => new-releases
                )
                [moverandshakers] => Array (
                    [check] => 0
                    [slug] => movers-and-shakers
                )
                [toprated] => Array (
                    [check] => 0
                    [slug] => top-rated
                )
                [mostwishedfor] => Array (
                    [check] => 0
                    [slug] => most-wished-for
                )
                [giftideas] => Array (
                    [check] => 0
                    [slug] => most-gifted
                )
            )
            )
    - v2     
        'count' => 10,
        'column' => 4,
        'country' => 'US',
        'associate_id' => null,
        'image_size' => 160,
        'sort' => 'random',    // date, title, title_descending    
        'keep_raw_title' => false,    // this is for special sorting method.
        'feed_type' => array(
            'bestsellers' => true, 
            'new-releases' => false,
            'movers-and-shakers' => false,
            'top-rated' => false,
            'most-wished-for' => false,
            'most-gifted' => false,    
        ),
        'ref_nosim' => false,
        'title_length' => -1,
        'link_style' => 1,
        'credit_link' => 1,
        'categories' => array(),    
        'categories_exclude' => array(),
        'title' => '',        // won't be used to fetch links. Used to create a unit.
        'template' => '',        // the template name - if multiple templates with a same name are registered, the first found item will be used.
        'template_id' => null,    // the template ID: md5( dir path )
        'template_path' => '',    // the template can be specified by the template path. If this is set, the 'template' key won't take effect.    
        'is_preview' => false,    // used to decide whether the global ASIN black/white list should be used.
        
        @return            interger            The count of imported unites.
     */
    public function importUnits( $arrUnits ) {

        $arrDefaultTemplate = $GLOBALS['oAmazonAutoLinks_Templates']->getPluginDefaultTemplate();
        $strDefaultTemplateID = $arrDefaultTemplate['strID'];

        $intCount = 0;
        foreach( $arrUnits as $arrUnit ) {
            $fSucceed = $this->importUnit( $arrUnit, $strDefaultTemplateID );
            if ( $fSucceed )
                $intCount++;
        }

        return $intCount;
        
    }
    protected function importUnit( $arrV1Unit, $strDefaultTemplateID ) {
    
        $arrV2UnitOptions = array(
            // by the program
            'unit_type' => 'category',    // this must be category since v1 only has the category type.
            'template_id' => $strDefaultTemplateID,    // this determines the template to use
            
            // by the user
            'unit_title' => $arrV1Unit['unitlabel'],
            'count' => $arrV1Unit['numitems'],
            'column' => $arrV1Unit['numberofcolumns'],
            'country' => $arrV1Unit['country'], // 'US',
            'associate_id' => $arrV1Unit['associateid'],
            'image_size' => $arrV1Unit['imagesize'],
            'sort' => $arrV1Unit['sortorder'],    // date, title, title_descending    
            'keep_raw_title' => $arrV1Unit['keep_raw_title']['keeprawtitle'],    // this is for special sorting method.        
            'feed_type' => array(
                'bestsellers' => $arrV1Unit['adtypes']['bestsellers']['check'], 
                'new-releases' => $arrV1Unit['adtypes']['hotnewreleases']['check'],
                'movers-and-shakers' => $arrV1Unit['adtypes']['moverandshakers']['check'],
                'top-rated' => $arrV1Unit['adtypes']['toprated']['check'],
                'most-wished-for' => $arrV1Unit['adtypes']['mostwishedfor']['check'],
                'most-gifted' => $arrV1Unit['adtypes']['giftideas']['check'],
            ),
            'ref_nosim' => $arrV1Unit['nosim'],
            'title_length' => $arrV1Unit['titlelength'],
            'link_style' => $arrV1Unit['linkstyle'],
            'credit_link' => $arrV1Unit['credit'],            
            'categories' => $this->formatCategories( $arrV1Unit['categories'] ),    
            'categories_exclude' => $this->formatCategories( $arrV1Unit['blacklist_categories'] ),
        );
        
        // Insert the post
        $intPostID = AmazonAutoLinks_Option::insertPost( $arrV2UnitOptions, AmazonAutoLinks_Commons::PostTypeSlug, array(), array( 'unit_title' ) );
        if ( ! $intPostID ) {
            return false;
        }
           
        // Insert the term
        $vTerm = wp_insert_term(
            $arrV1Unit['unitlabel'], // the term 
            AmazonAutoLinks_Commons::TagSlug // the taxonomy    
        );
        // if ( ! is_wp_error( $vTerm ) && isset( $vTerm['term_id'], $vTerm['term_taxonomy_id'] ) )        // $vTarm = array('term_id'=>12,'term_taxonomy_id'=>34)) or WP_Error
        
        // The term already exists, in that case, try retreiving the term id from the term name.
        if ( is_wp_error( $vTerm ) ) {
            $vTerm = get_term_by( 'name', $arrV1Unit['unitlabel'], AmazonAutoLinks_Commons::TagSlug, 'ARRAY_A' );            
        }
        
        if ( isset( $vTerm['term_id'] ) )
            wp_set_object_terms( 
                $intPostID,         // object id
                isset( $vTerm['slug'] ) ?  $vTerm['slug'] : $vTerm['term_id'],             // term id or slug
                AmazonAutoLinks_Commons::TagSlug     // taxonomy slug, ( not id )
            );    
        
        // Add an auto-insert definition ( custom post type)
        if ( array_filter( $arrV1Unit['insert'] ) ) // if at least one item is checked,
            AmazonAutoLinks_Option::insertPost( 
                $this->composeAutoInsertOptions( $intPostID, $arrV1Unit['insert'], $arrV1Unit['disableonhome'], trim( $arrV1Unit['poststobedisabled'] ) ), 
                AmazonAutoLinks_Commons::PostTypeSlugAutoInsert 
            );
            
        return true;
        
    }

        
    /**
     * 
     * @remark            The position option for non static hooks will be lost and be converted to 'below'.
     */
    protected function composeAutoInsertOptions( $intPostID, $arrInsert, $fDisableOnHome, $strDisablingPostIDs ) {
        
        /* 
         * v1
            [insert] => Array (
                [postabove_static] => 0
                [postbelow_static] => 0
                [postabove] => 0
                [postbelow] => 0
                [excerptabove] => 0
                [excerptbelow] => 0
                [feedabove] => 0
                [feedbelow] => 0
                [feedexcerptabove] => 0
                [feedexcerptbelow] => 0
            )

         * 
         * v2 
         *     public static $arrStructure_AutoInsertOptions = array(
            'status' => true,        // let toggle on and off
            'unit_ids' => null,    // will be array, e.g. array( 123, 234 )
            'built_in_areas' => array( 'the_content' => true ),
            'filter_hooks' => null,
            'position' => 'below',
            'static_areas' => array( 'wp_insert_post_data' => false ),
            'static_position' => 'below',
            'action_hooks' => null,
            'enable_allowed_area' => 0,
            'enable_post_ids' => null,
            'enable_page_types' => true,
            'enable_post_types' => true,
            'enable_taxonomy' => true,
            'enable_denied_area' => 0,
            'diable_post_ids' => null,
            'disable_page_types' => array( 'is_home' => false, 'is_404' => false, 'is_archive' => false, 'is_search' => false ),
            'disable_post_types' => array(),
            'disable_taxonomy' => array(),    
        );         
    
        - v2 the 'built_in_areas' array
        array(                        
            'the_content'                => __( 'Post / Page Content', 'amazon-auto-links' ),
            'the_excerpt'                => __( 'Excerpt', 'amazon-auto-links' ),
            'comment_text'                => __( 'Comment', 'amazon-auto-links' ),
            'the_content_feed'            => __( 'Feed', 'amazon-auto-links' ),
            'the_excerpt_rss'            => __( 'Feed Excerpt', 'amazon-auto-links' ),
        )
        - v2 the 'static_areas' array
        array(
            'wp_insert_post_data'        => __( 'Post / Page Content on Publish', 'amazon-auto-links' )         
        )        
        */
                
        $arrStaticAreas = array();
        if ( $arrInsert['postabove_static'] )
            $arrStaticAreas['wp_insert_post_data'] = true;
        if ( $arrInsert['postbelow_static'] )
            $arrStaticAreas['wp_insert_post_data'] = true;

        $arrBiltInAreas = array();        
        if ( $arrInsert['postabove'] )
            $arrStaticAreas['the_content'] = true;
        if ( $arrInsert['postbelow'] )
            $arrStaticAreas['the_content'] = true;
        if ( $arrInsert['excerptabove'] )
            $arrStaticAreas['the_excerpt'] = true;
        if ( $arrInsert['excerptbelow'] )
            $arrStaticAreas['the_excerpt'] = true;
        if ( $arrInsert['feedabove'] )
            $arrStaticAreas['the_content_feed'] = true;
        if ( $arrInsert['feedbelow'] )
            $arrStaticAreas['the_content_feed'] = true;
        if ( $arrInsert['feedexcerptabove'] )
            $arrStaticAreas['the_excerpt_rss'] = true;
        if ( $arrInsert['feedexcerptbelow'] )
            $arrStaticAreas['the_excerpt_rss'] = true;
                
        // Deny options
        $fIsDenyOptionEnabled = ( $fDisableOnHome || $strDisablingPostIDs );
                
        $arrAutoInsertOptions = array( 
            'unit_ids' => array( $intPostID ),
            'built_in_areas' => $arrBiltInAreas,
            'position' => 'below',
            'static_areas' => $arrStaticAreas,
            'static_position' => 'below',
            'enable_denied_area' => $fIsDenyOptionEnabled,
            'diable_post_ids' => $strDisablingPostIDs,
            'disable_page_types' => $fDisableOnHome ? array( 'is_home' => true ) : null,
        ) + AmazonAutoLinks_Form_AutoInsert::$arrStructure_AutoInsertOptions;
        
        return $arrAutoInsertOptions;
    }
    
    protected function formatCategories( $arrV1Categories ) {
        
        $arrNewCategories = array();
        foreach ( $arrV1Categories as $strBreadCrumb => $arrV1Category ) {
            $arrNewCategory = $this->formatCategory( $strBreadCrumb, $arrV1Category );
            $arrNewCategories[ md5( $arrNewCategory['feed_url'] ) ] = $arrNewCategory;
        }                
        return $arrNewCategories;
    }
    // V2
     // * md5( $arrCurrentCategory['feed_url'] ) => array(
     // *         'breadcrumb' => 'US > Books',
     // *         'feed_url' => 'http://amazon....',    // the feed url of the category
     // *         'page_url' => 'http://...'        // the page url of the category    

    // V1
     // [US > Beauty] => Array (
        // [feedurl] => http://www.amazon.com/gp/rss/bestsellers/beauty
        // [pageurl] => http://www.amazon.com/Best-Sellers-Beauty/zgbs/beauty/
    // )
    protected function formatCategory( $strBreadCrumb, $arrV1Category ) {
        return array(
            'breadcrumb'    =>    $strBreadCrumb,
            'feed_url'        =>    $arrV1Category['feedurl'],
            'page_url'        =>    $arrV1Category['pageurl'],
        );        
    }
    

}