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
 * A base class for post meta boxes that define buttons of a button type and of the `button` post type.
 * @since 5.2.0
 */
abstract class AmazonAutoLinks_Button_ButtonType_PostMetaBox_Base extends AmazonAutoLinks_AdminPageFramework_MetaBox {

    /**
     * @var   string
     * @since 5.2.0
     */
    protected $_sButtonType = '';

    /**
     * @var   array Stores field definition class names.
     * @since 5.2.0
     */
    protected $_aFieldClasses = array();

    public function start() {

        // Register custom field types
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );

        add_action( "set_up_" . $this->oProp->sClassName, array( $this, 'replyToInsertCustomStyleTag' ) );

    }

    /**
     * @since 5.2.0
     */
    public function setUp() {
        foreach( $this->_aFieldClasses as $_sClassName ) {
            $_oFields = new $_sClassName( $this );
            foreach( $_oFields->get() as $_aField ) {
                $this->addSettingFields( $_aField );
            }
        }
    }

    /**
     * @callback add_action() set_up_{instantiated class name}
     */
    public function replyToInsertCustomStyleTag() {

        if ( $this->oUtil->hasBeenCalled( __METHOD__ ) ) {
            return;
        }

        add_action( 'admin_head', array( $this, 'replyToPrintCustomStyleTag' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'replyToSetScripts' ) );

    }

    /**
     *
     * @callback add_action() admin_head
     */
    public function replyToPrintCustomStyleTag() {
        // echo "<style type='text/css' id='amazon-auto-links-button-style'>" . PHP_EOL
        //         . '.amazon-auto-links-button {}' . PHP_EOL
        //     . "</style>";
    }

    /**
     * @callback add_action() admin_enqueue_scripts
     */
    public function replyToSetScripts() {}

    /**
     * Checks whether the meta box should be registered or not in the loading page.
     */
    protected function _isInThePage() {

        if ( ! parent::_isInThePage() ) {
            return false;
        }
        
        // At this point, it is TRUE evaluated by the framework. But we need to evaluate it for the plugin.
        return $this->_shouldLoad();

    }

    /**
     * @return boolean
     * @since  5.2.0
     */
    protected function _shouldLoad() {
        if ( ! empty( $_GET[ 'button_type' ] ) && $this->_sButtonType === $_GET[ 'button_type' ] ) {
            return true;
        }
        if ( ! empty( $_REQUEST[ '_button_type' ] ) && $this->_sButtonType === $_REQUEST[ '_button_type' ] ) {
            return true;
        }
        return get_post_meta( AmazonAutoLinks_WPUtility::getCurrentPostID(), '_button_type', true ) === $this->_sButtonType;;
    }
        
}