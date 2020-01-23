<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Converts v1 options to v3's.
 * 
 * @since       3
 */
class AmazonAutoLinks_OptionConverter_V1ToV3 extends AmazonAutoLinks_OptionConverter_Base {

    /**
     * Returns a converted options array.
     * @since       3
     * @return      array
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
     */
    public function get() {
        
        $this->oOption->arrOptions[ AmazonAutoLinks_Registry::PageSettingsSlug ]['support']['rate'] = $arrGeneral['supportrate'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Registry::PageSettingsSlug ]['product_filters']['black_list']['asin'] = $arrGeneral['blacklist'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Registry::PageSettingsSlug ]['product_filters']['black_list']['title'] = $arrGeneral['blacklist_title'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Registry::PageSettingsSlug ]['product_filters']['black_list']['description'] = $arrGeneral['blacklist_description'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Registry::PageSettingsSlug ]['query']['cloak'] = $arrGeneral['cloakquery'];
        $this->oOption->arrOptions[ AmazonAutoLinks_Registry::PageSettingsSlug ]['capabilities']['setting_page_capability'] = $arrGeneral['capability'];        
        $this->oOption->arrOptions[ AmazonAutoLinks_Registry::PageSettingsSlug ][ AmazonAutoLinks_Registry::SectionID_License ][ AmazonAutoLinks_Registry::FieldID_LicenseKey ] = isset( $arrGeneral['license'] ) ? $arrGeneral['license'] : '';
        $this->oOption->save();
        
        return $this->aOptions;
    }

    /**
     * @since       3
     * @remark      As of v3, the template options were separated.
     * @return      array
     */
    public function getTemplateOptions() {
        return array();        
    }    
    
}
