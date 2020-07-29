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
 * Provides methods for tools template options.
 * 
 * @since       4.2.0
 */
class AmazonAutoLinks_ToolOption extends AmazonAutoLinks_Option_Base {

    /**
     * Represents the structure of the default tool options.
     * @since       4.2.0
     */
    public $aDefault = array(

        'proxies'       => array(
            'enable'                => false,
            'proxy_list'            => '',
            'unusable'              => '',
            'automatic_updates'     => false,
            'proxy_update_interval' => array(
                'size'      => 1,
                'unit'      => 86400,
            ),
            'update_last_run_time'  => 0,  // ( integer ) with no visible field and updated with the automatic proxy update
        ),

    );

    /**
     * Stores the self instance.
     */
    static public $oSelf;
    
    /**
     * Returns an instance of the self.
     * 
     * @remark      To support PHP 5.2, this method needs to be defined in each extended class 
     * as in static methods, it is not possible to retrieve the extended class name in a base class in PHP 5.2.x.
     * @return      AmazonAutoLinks_ToolOption
     */
    static public function getInstance( $sOptionKey='' ) {
        
        if ( isset( self::$oSelf ) ) {
            return self::$oSelf;
        }
        $sOptionKey = $sOptionKey 
            ? $sOptionKey
            : AmazonAutoLinks_Registry::$aOptionKeys[ 'tools' ];
        
        $_sClassName = __Class__;
        self::$oSelf = new $_sClassName( $sOptionKey );            
        return self::$oSelf;
        
    }

}