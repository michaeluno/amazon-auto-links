<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Converts v2 options to v3's.
 * 
 * @since       3
 */
class AmazonAutoLinks_OptionConverter_V2ToV3 extends AmazonAutoLinks_OptionConverter_Base {

    /**
     * Returns a converted options array.
     * @since       3
     * @return      array
     * 
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
     */
    public function get() {
        return $this->aOptions;
    }

}
