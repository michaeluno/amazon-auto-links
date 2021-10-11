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
 * Loads the unit types/
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitTypesLoader extends AmazonAutoLinks_UnitTypeLoader_Base {

    /**
     * Stores class names of common form fields among all the unit types.
     */
    public $aFieldClasses = array(
        // 'AmazonAutoLinks_FormFields_Unit_Template', // kept for backward-compatibility -> 4.0.5 Removed completely to avoid the error saying the class not found.
        'AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport', // 4.0.0+
        'AmazonAutoLinks_FormFields_Unit_CommonAdvanced',
        'AmazonAutoLinks_FormFields_Button_Selector',
        'AmazonAutoLinks_FormFields_Unit_Common',    
        'AmazonAutoLinks_FormFields_Unit_Cache',
    );      
    
    /**
     * Stores protected meta key names.
     */    
    public $aProtectedMetaKeys = array(
        'product_filters',      // section id
    );

    /**
     * @param string $sScriptPath the plugin main file path.
     */
    protected function _construct( $sScriptPath ) {

        // Unit types
        new AmazonAutoLinks_UnitTypeLoader_category( $sScriptPath );
        new AmazonAutoLinks_Unit_UnitTypeLoader_ad_widget_search( $sScriptPath ); // 5.0.0
        new AmazonAutoLinks_UnitTypeLoader_search( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_item_lookup( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_url( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_contextual( $sScriptPath );
        new AmazonAutoLinks_UnitTypeLoader_embed( $sScriptPath ); // 4.0.0
        new AmazonAutoLinks_UnitTypeLoader_feed( $sScriptPath );  // 4.0.0
        new AmazonAutoLinks_UnitTypeLoader_scratchpad_payload( $sScriptPath );  // 4.1.0

    }

}