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
 * Defines the meta box added to the unit definition page.
 */
abstract class AmazonAutoLinks_UnitPostMetaBox_Base extends AmazonAutoLinks_PostMetaBox_Base {

    /**
     * Stores the current post ID.
     * @var int
     * @since   3.7.0
     */
    protected $_iPostID = 0;

    /**
     * Stores the unit type slug(s). 
     * 
     * Each unit type should add the slug with the `aal_filter_unit_types_common_unit_meta_boxes'`
     */
    protected $aUnitTypes = array();

    /**
     * @var AmazonAutoLinks_UnitOption_Base $_oUnitOption
     */
    protected $_oUnitOption;
    
    /**
     * Checks whether the meta box should be registered or not in the loading page.
     */
    protected function _isInThePage() {

        if ( ! parent::_isInThePage() ) {
            return false;
        }
                
        $this->aUnitTypes = empty( $this->aUnitTypes )
            ? apply_filters( 'aal_filter_registered_unit_types', $this->aUnitTypes ) // to allow all unit types so that common unit meta boxes can leave the property empty
            : $this->aUnitTypes;
                
        // At this point, it is TRUE evaluated by the framework.
        // but we need to evaluate it for the plugin.
        
        // Get the post ID.
        $this->_iPostID = AmazonAutoLinks_WPUtility::getCurrentPostID();
        
        // Maybe post-new.php
        if ( ! $this->_iPostID ) {
            return true;
        }

        return in_array(
            ( string ) get_post_meta( $this->_iPostID, 'unit_type', true ),
            $this->aUnitTypes,
            true
        );
        
    }

    /**
     * The APF post meta box factory class does not have the getValue() method which is supported in AdminPage factory class.
     * So define it.
     * @return mixed
     * @since  4.5.0
     */
    public function getValue() {

        if ( ! $this->_iPostID ) {
            return array();
        }
        $_aParameters = func_get_args();
        if ( empty( $_aParameters ) ) {
            return AmazonAutoLinks_PluginUtility::getPostMeta( $this->_iPostID );
        }
        $_aParameters    = $_aParameters + array( null, null );
        $_sMetaKey       = is_array( $_aParameters[ 0 ] )
            ? reset( $_aParameters[ 0 ] )
            : ( string ) $_aParameters[ 0 ];
        $_mDefault       = is_array( $_aParameters[ 0 ] )
            ? $_aParameters[ 1 ]
            : null;
        $_mValue         = AmazonAutoLinks_PluginUtility::getPostMeta( $this->_iPostID, $_sMetaKey, $_mDefault );
        if ( is_array( $_aParameters[ 0 ] ) ) {
            array_shift( $_aParameters[ 0 ] );
            if ( ! isset( $_aParameters[ 0 ][ 0 ] ) ) {
                return $_mValue;
            }
            return AmazonAutoLinks_PluginUtility::getElement( $_mValue, $_aParameters[ 0 ], $_aParameters[ 1 ] );
        }
        array_shift( $_aParameters );
        if ( ! isset( $_aParameters[ 0 ] ) ) {
            return $_mValue;
        }
        return AmazonAutoLinks_PluginUtility::getElement( $_mValue, $_aParameters, null );

    }

    /**
     * Adds form fields.
     * @param  array $aClassNames
     * @since  3.1.0
     */
    protected function _addFieldsByClasses( $aClassNames ) {
        foreach( $aClassNames as $_sClassName ) {
            $_oFields = new $_sClassName( $this );
            foreach( $_oFields->get() as $_aField ) {
                $this->addSettingFields( $_aField );
            }
        }
    }

    /**
     * This is meant to server as a callback function.
     * Call it like `add_action( 'do_meta_boxes', array( $this, 'replyToRemoveLocaleMetaBox' ) );`
     * @param    string $sPostType
     * @callback add_action() do_meta_boxes
     * @since    4.7.0
     */
    public function replyToRemoveLocaleMetaBox( $sPostType ) {
        if ( ! in_array( $sPostType, $this->oProp->aPostTypes, true ) ) {
            return;
        }
        if ( $this->oUtil->hasBeenCalled( __METHOD__ ) ) {
            return;
        }
        remove_meta_box(
            'amazon_auto_links_locale',
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ], // screen: post type slug
            'side'
        );
    }

}